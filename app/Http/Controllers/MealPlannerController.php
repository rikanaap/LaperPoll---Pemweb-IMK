<?php

namespace App\Http\Controllers;

use App\Models\MealPlanner;
use App\Models\MealPlannerDetail;
use App\Models\Resep;
use App\Models\UserCart;
use App\Models\ResepBahan;
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

    // ── AJAX: ambil data meal planner berdasarkan range tanggal ────────
    // GET /api/meal-planner?start=2026-05-16&end=2026-05-22
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
            ->keyBy(fn($p) => $p->tanggal->format('Y-m-d'));

        // Bangun response per tanggal dalam range
        $result = [];
        $cur = $start->copy();
        while ($cur <= $end) {
            $iso     = $cur->toDateString();
            $planner = $planners[$iso] ?? null;

            $meals = ['SA' => null, 'SI' => null, 'MA' => null];
            if ($planner) {
                foreach ($planner->details as $detail) {
                    $resep = $detail->resep;
                    $meals[$detail->meal_time] = [
                        'detail_id' => $detail->id,
                        'resep_id'  => $resep->id,
                        'nama'      => $resep->title,
                        'kalori'    => $resep->calorie ?? 0,
                        'durasi'    => $resep->cook_duration,
                        'thumbnail' => $resep->thumbnail
                            ? asset('storage/' . $resep->thumbnail)
                            : null,
                    ];
                }
            }

            $result[] = [
                'tanggal'     => $iso,
                'planner_id'  => $planner?->id,
                'max_calorie' => $planner?->max_calorie,
                'total_kalori'=> collect($meals)->sum(fn($m) => $m['kalori'] ?? 0),
                'meals'       => $meals,
            ];

            $cur->addDay();
        }

        return response()->json($result);
    }

    // ── AJAX: simpan/update target kalori harian ───────────────────────
    // POST /api/meal-planner/kalori
    // body: { tanggal, max_calorie }
    public function setKalori(Request $request)
    {
        $request->validate([
            'tanggal'     => 'required|date',
            'max_calorie' => 'required|integer|min:100|max:9999',
        ]);

        $userId = Auth::id();

        $planner = MealPlanner::firstOrCreate(
            ['user_id' => $userId, 'tanggal' => $request->tanggal],
            ['max_calorie' => $request->max_calorie]
        );

        $planner->update(['max_calorie' => $request->max_calorie]);

        return response()->json([
            'success'     => true,
            'planner_id'  => $planner->id,
            'max_calorie' => $planner->max_calorie,
        ]);
    }

    // ── AJAX: tambah resep ke slot meal planner ────────────────────────
    // POST /api/meal-planner/tambah
    // body: { tanggal, meal_time (SA/SI/MA), resep_id }
    public function tambahResep(Request $request)
    {
        $request->validate([
            'tanggal'   => 'required|date',
            'meal_time' => 'required|in:SA,SI,MA',
            'resep_id'  => 'required|exists:reseps,id',
        ]);

        $userId = Auth::id();

        DB::transaction(function () use ($request, $userId) {
            // Pastikan baris meal_planner untuk tanggal ini ada
            $planner = MealPlanner::firstOrCreate(
                ['user_id' => $userId, 'tanggal' => $request->tanggal],
                ['max_calorie' => null]
            );

            // Hapus slot yang sama kalau sudah ada (replace)
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
            'success'  => true,
            'resep_id' => $resep->id,
            'nama'     => $resep->title,
            'kalori'   => $resep->calorie ?? 0,
            'thumbnail'=> $resep->thumbnail ? asset('storage/' . $resep->thumbnail) : null,
        ]);
    }

    // ── AJAX: hapus satu slot resep ────────────────────────────────────
    // DELETE /api/meal-planner/detail/{id}
    public function hapusDetail($id)
    {
        $userId = Auth::id();

        $detail = MealPlannerDetail::whereHas('mealPlanner', fn($q) => $q->where('user_id', $userId))
            ->findOrFail($id);

        $detail->delete();

        return response()->json(['success' => true]);
    }

    // ── AJAX: generate nota belanja dari range tanggal ─────────────────
    // POST /api/meal-planner/generate-nota
    // body: { start, end }
    public function generateNota(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end'   => 'required|date|after_or_equal:start',
        ]);

        $userId = Auth::id();

        // Ambil semua resep_id dalam range
        $resepIds = MealPlannerDetail::whereHas('mealPlanner', function ($q) use ($userId, $request) {
            $q->where('user_id', $userId)
              ->whereBetween('tanggal', [$request->start, $request->end]);
        })->pluck('resep_id')->unique();

        if ($resepIds->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tidak ada resep di range ini.'], 422);
        }

        // Kumpulkan kebutuhan bahan dari semua resep (aggregate gram_total)
        $bahanNeeds = DB::table('resep_bahan')
            ->whereIn('resep_id', $resepIds)
            ->select('bahan_id', DB::raw('SUM(gram_total) as total_gram'))
            ->groupBy('bahan_id')
            ->get();

        DB::transaction(function () use ($userId, $bahanNeeds) {
            foreach ($bahanNeeds as $need) {
                $existing = UserCart::where('user_id', $userId)
                    ->where('bahan_id', $need->bahan_id)
                    ->where('is_done', 0)
                    ->first();

                if ($existing) {
                    $existing->increment('gram_total', $need->total_gram);
                } else {
                    UserCart::create([
                        'user_id'    => $userId,
                        'bahan_id'   => $need->bahan_id,
                        'gram_total' => $need->total_gram,
                        'is_done'    => 0,
                    ]);
                }
            }
        });

        return response()->json([
            'success'  => true,
            'redirect' => route('nota.index'),
        ]);
    }
}