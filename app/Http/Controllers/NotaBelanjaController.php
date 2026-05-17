<?php

namespace App\Http\Controllers;

use App\Models\UserCart;
use App\Models\MealPlanner;
use App\Models\MealPlannerDetail;
use App\Models\Bahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotaBelAnjaController extends Controller
{
    // ── Halaman utama ──────────────────────────────────────────────────
    // GET /nota-belanja?start=2026-05-16&end=2026-05-22
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Ambil range dari query string (dikirim dari meal planner generate, atau filter manual)
        $start = $request->query('start')
            ? Carbon::parse($request->query('start'))->startOfDay()
            : null;
        $end   = $request->query('end')
            ? Carbon::parse($request->query('end'))->endOfDay()
            : null;

        // Ambil semua item cart user (belum selesai)
        $cartItems = UserCart::with('bahan')
            ->where('user_id', $userId)
            ->orderBy('is_done', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        // Kalau ada range, ambil juga resep yang terlibat untuk ditampilkan di header
        $resepDalamRange = collect();
        if ($start && $end) {
            $resepDalamRange = MealPlannerDetail::with('resep')
                ->whereHas('mealPlanner', function ($q) use ($userId, $start, $end) {
                    $q->where('user_id', $userId)
                      ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()]);
                })
                ->get()
                ->pluck('resep')
                ->unique('id')
                ->values();
        }

        // Grouping per kategori
        $katMap = [
            'karbohidrat' => 'KARBOHIDRAT',
            'protein'     => 'PROTEIN',
            'sayuran'     => 'SAYURAN',
            'bumbu'       => 'BUMBU',
            'lainnya'     => 'LAINNYA',
        ];

        $grouped = $cartItems->groupBy(function ($item) use ($katMap) {
            // Kalau bahan punya kategori → pakai, kalau tidak → LAINNYA
            $kat = strtolower($item->bahan->kategori ?? 'lainnya');
            return $katMap[$kat] ?? 'LAINNYA';
        });

        // Pastikan urutan kategori
        $katOrder  = ['KARBOHIDRAT', 'PROTEIN', 'SAYURAN', 'BUMBU', 'LAINNYA'];
        $groupedOrdered = collect($katOrder)->mapWithKeys(function ($kat) use ($grouped) {
            return [$kat => $grouped->get($kat, collect())];
        })->filter(fn($items) => $items->isNotEmpty());

        $totalItem  = $cartItems->count();
        $doneItem   = $cartItems->where('is_done', 1)->count();

        return view('pages.nota_belanja.index', compact(
            'cartItems',
            'groupedOrdered',
            'totalItem',
            'doneItem',
            'start',
            'end',
            'resepDalamRange'
        ));
    }

    // ── AJAX: centang/uncentang item (tandai sudah dibeli) ─────────────
    // POST /api/nota-belanja/toggle/{id}
    public function toggle($id)
    {
        $item = UserCart::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $item->update(['is_done' => !$item->is_done]);

        return response()->json([
            'success' => true,
            'is_done' => $item->is_done,
            'id'      => $item->id,
        ]);
    }

    // ── AJAX: hapus semua item yang sudah dibeli (is_done = 1) ────────
    // REVISI 1: tombol ini muncul kalau ada minimal 1 yang sudah dibeli
    // DELETE /api/nota-belanja/hapus-selesai
    public function hapusSelesai()
    {
        $deleted = UserCart::where('user_id', Auth::id())
            ->where('is_done', 1)
            ->delete();

        return response()->json([
            'success' => true,
            'deleted' => $deleted,
        ]);
    }

    // ── AJAX: hapus satu item dari nota ───────────────────────────────
    // DELETE /api/nota-belanja/{id}
    public function destroy($id)
    {
        UserCart::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail()
            ->delete();

        return response()->json(['success' => true]);
    }

    // ── AJAX: ambil data nota berdasarkan filter tanggal ──────────────
    // REVISI 2: filter tanggal dari meal planner atau manual
    // GET /api/nota-belanja?start=...&end=...
    public function getData(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end'   => 'required|date|after_or_equal:start',
        ]);

        $userId = Auth::id();
        $start  = $request->start;
        $end    = $request->end;

        // Ambil bahan_id dari meal planner dalam range
        $bahanIds = DB::table('resep_bahan')
            ->whereIn('resep_id', function ($q) use ($userId, $start, $end) {
                $q->select('meal_planner_detail.resep_id')
                  ->from('meal_planner_detail')
                  ->join('meal_planner', 'meal_planner.id', '=', 'meal_planner_detail.meal_planner_id')
                  ->where('meal_planner.user_id', $userId)
                  ->whereBetween('meal_planner.tanggal', [$start, $end]);
            })
            ->select('bahan_id', DB::raw('SUM(gram_total) as total_gram'))
            ->groupBy('bahan_id')
            ->get();

        // Merge ke cart (kalau belum ada, tambah; kalau sudah ada, update)
        DB::transaction(function () use ($userId, $bahanIds) {
            foreach ($bahanIds as $need) {
                $existing = UserCart::where('user_id', $userId)
                    ->where('bahan_id', $need->bahan_id)
                    ->where('is_done', 0)
                    ->first();

                if ($existing) {
                    $existing->update(['gram_total' => $need->total_gram]);
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

        // Return data cart terbaru
        $items = UserCart::with('bahan')
            ->where('user_id', $userId)
            ->orderBy('is_done')
            ->orderBy('created_at')
            ->get()
            ->map(fn($i) => [
                'id'        => $i->id,
                'nama'      => $i->bahan->nama,
                'gram'      => $i->gram_total,
                'is_done'   => $i->is_done,
                'kategori'  => strtoupper($i->bahan->kategori ?? 'LAINNYA'),
            ]);

        return response()->json(['success' => true, 'items' => $items]);
    }
}