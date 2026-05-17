<?php

namespace App\Http\Controllers;

use App\Models\MealPlanner;
use App\Models\MealPlannerDetail;
use App\Models\Resep;
use App\Models\UserCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MealPlannerController extends Controller
{
    // ── Halaman utama ──────────────────────────────────────────────────
    public function index()
    {
        return view('pages.meal_planner.index');
    }

    // ── GET /api/meal-planner?start=2026-05-16&end=2026-05-22 ─────────
    public function getData(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end'   => 'required|date|after_or_equal:start',
        ]);

        $userId = Auth::id();
        $start  = Carbon::parse($request->start)->startOfDay();
        $end    = Carbon::parse($request->end)->endOfDay();

        $planners = MealPlanner::with(['details.resep'])
            ->where('user_id', $userId)
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn($p) => Carbon::parse($p->tanggal)->format('Y-m-d'));

        $result = [];
        $cur = $start->copy();
        while ($cur <= $end) {
            $iso     = $cur->toDateString();
            $planner = $planners->get($iso);

            $meals = ['SA' => null, 'SI' => null, 'MA' => null];
            if ($planner) {
                foreach ($planner->details as $detail) {
                    $resep = $detail->resep;
                    if (!$resep) continue;
                    $meals[$detail->meal_time] = [
                        'detail_id' => $detail->id,
                        'resep_id'  => $resep->id,
                        'nama'      => $resep->title,
                        'kalori'    => (int)($resep->calorie ?? 0),
                        'durasi'    => $resep->cook_duration,
                        'thumbnail' => $resep->thumbnail
                            ? asset('storage/' . $resep->thumbnail)
                            : null,
                    ];
                }
            }

            $result[] = [
                'tanggal'      => $iso,
                'planner_id'   => $planner?->id,
                'max_calorie'  => (int)($planner?->max_calorie ?? 0),
                'total_kalori' => collect($meals)->sum(fn($m) => $m['kalori'] ?? 0),
                'meals'        => $meals,
            ];

            $cur->addDay();
        }

        return response()->json($result);
    }

    // ── POST /api/meal-planner/kalori ─────────────────────────────────
    public function setKalori(Request $request)
    {
        $request->validate([
            'tanggal'     => 'required|date',
            'max_calorie' => 'required|integer|min:100|max:9999',
        ]);

        $userId  = Auth::id();
        $planner = MealPlanner::firstOrCreate(
            ['user_id' => $userId, 'tanggal' => $request->tanggal],
            ['max_calorie' => $request->max_calorie]
        );
        $planner->update(['max_calorie' => $request->max_calorie]);

        return response()->json([
            'success'     => true,
            'planner_id'  => $planner->id,
            'max_calorie' => (int)$planner->max_calorie,
        ]);
    }

    // ── POST /api/meal-planner/tambah ─────────────────────────────────
    public function tambahResep(Request $request)
    {
        $request->validate([
            'tanggal'   => 'required|date',
            'meal_time' => 'required|in:SA,SI,MA',
            'resep_id'  => 'required|exists:reseps,id',
        ]);

        $userId = Auth::id();

        DB::transaction(function () use ($request, $userId) {
            $planner = MealPlanner::firstOrCreate(
                ['user_id' => $userId, 'tanggal' => $request->tanggal],
                ['max_calorie' => null]
            );

            // Replace slot yang sama
            MealPlannerDetail::where('meal_planner_id', $planner->id)
                ->where('meal_time', $request->meal_time)
                ->delete();

            MealPlannerDetail::create([
                'meal_planner_id' => $planner->id,
                'resep_id'        => $request->resep_id,
                'meal_time'       => $request->meal_time,
            ]);
        });

        $resep = Resep::find($request->resep_id);

        return response()->json([
            'success'   => true,
            'resep_id'  => $resep->id,
            'nama'      => $resep->title,
            'kalori'    => (int)($resep->calorie ?? 0),
            'thumbnail' => $resep->thumbnail ? asset('storage/' . $resep->thumbnail) : null,
        ]);
    }

    // ── DELETE /api/meal-planner/detail/{id} ──────────────────────────
    public function hapusDetail($id)
    {
        $detail = MealPlannerDetail::whereHas(
            'mealPlanner', fn($q) => $q->where('user_id', Auth::id())
        )->findOrFail($id);

        $detail->delete();

        return response()->json(['success' => true]);
    }

    // ── POST /api/meal-planner/generate-nota ──────────────────────────
    // FIX: pakai updateOrCreate biar tidak duplicate entry di user_cart
    public function generateNota(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end'   => 'required|date|after_or_equal:start',
        ]);

        $userId = Auth::id();

        // Kumpulkan semua resep_id dalam range
        $resepIds = MealPlannerDetail::whereHas('mealPlanner', fn($q) =>
            $q->where('user_id', $userId)
              ->whereBetween('tanggal', [$request->start, $request->end])
        )->pluck('resep_id')->unique();

        if ($resepIds->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada resep di rentang tanggal ini.',
            ], 422);
        }

        // Agregasi gram per bahan dari semua resep
        $bahanNeeds = DB::table('resep_bahan')
            ->whereIn('resep_id', $resepIds)
            ->select('bahan_id', DB::raw('SUM(gram_total) as total_gram'))
            ->groupBy('bahan_id')
            ->get();

        DB::transaction(function () use ($userId, $bahanNeeds) {
            foreach ($bahanNeeds as $need) {
                // updateOrCreate = aman dari duplicate entry (user_id + bahan_id unique)
                // Kalau sudah ada (apapun is_done-nya) → update gram_total
                // Kalau belum ada → buat baru
                UserCart::updateOrCreate(
                    [
                        'user_id'  => $userId,
                        'bahan_id' => $need->bahan_id,
                    ],
                    [
                        'gram_total' => $need->total_gram,
                        'is_done'    => 0,  // reset ke belum dibeli
                    ]
                );
            }
        });

        return response()->json([
            'success'  => true,
            'redirect' => route('nota.index', [
                'start' => $request->start,
                'end'   => $request->end,
            ]),
        ]);
    }
}