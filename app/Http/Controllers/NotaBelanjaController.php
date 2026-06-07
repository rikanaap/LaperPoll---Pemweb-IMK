<?php

namespace App\Http\Controllers;

use App\Models\UserCart;
use App\Models\MealPlannerDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotaBelanjaController extends Controller
{
    /**
     * GET /nota-belanja?start=2026-05-16&end=2026-05-22
     *
     * Halaman utama nota belanja.
     * Cukup baca dari user_cart — generate sudah dilakukan oleh MealPlannerController@generateNota.
     * Filter ?start & ?end hanya untuk menampilkan label range + resep yang terlibat.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $start = $request->query('start')
            ? Carbon::parse($request->query('start'))->startOfDay()
            : null;
        $end   = $request->query('end')
            ? Carbon::parse($request->query('end'))->endOfDay()
            : null;

        // Semua item cart user (belum dibeli dulu, sudah dibeli belakangan)
        $cartItems = UserCart::with('bahan')
            ->where('user_id', $userId)
            ->orderBy('is_done', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        // Resep dalam range (untuk label info di header)
        $resepDalamRange = collect();
        if ($start && $end) {
            $resepDalamRange = MealPlannerDetail::with('resep')
                ->whereHas('mealPlanner', fn($q) =>
                    $q->where('user_id', $userId)
                      ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
                )
                ->get()
                ->pluck('resep')
                ->filter()
                ->unique('id')
                ->values();
        }

        // Grouping per kategori — fallback ke LAINNYA kalau kolom kategori belum ada
        // FIX: lengkapi katMap dengan BUAH dan MINUMAN
        $katMap = [
            'karbohidrat' => 'KARBOHIDRAT',
            'protein'     => 'PROTEIN',
            'sayuran'     => 'SAYURAN',
            'buah'        => 'BUAH',
            'bumbu'       => 'BUMBU',
            'minuman'     => 'MINUMAN',
            'lainnya'     => 'LAINNYA',
        ];

        $grouped = $cartItems->groupBy(function ($item) use ($katMap) {
            $kat = strtolower($item->bahan->kategori ?? '');
            return $katMap[$kat] ?? 'LAINNYA';
        });

        // FIX: tambah BUAH dan MINUMAN ke urutan tampil
        $katOrder = ['KARBOHIDRAT', 'PROTEIN', 'SAYURAN', 'BUAH', 'BUMBU', 'MINUMAN', 'LAINNYA'];
        $groupedOrdered = collect($katOrder)
            ->mapWithKeys(fn($k) => [$k => $grouped->get($k, collect())])
            ->filter(fn($items) => $items->isNotEmpty());

        $totalItem = $cartItems->count();
        $doneItem  = $cartItems->where('is_done', 1)->count();

        return view('pages.nota_belanja.index', compact(
            'cartItems', 'groupedOrdered',
            'totalItem', 'doneItem',
            'start', 'end', 'resepDalamRange'
        ));
    }

    /**
     * PATCH /api/nota-belanja/toggle/{id}
     * Toggle is_done satu item.
     */
    public function toggle($id)
    {
        $item = UserCart::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $item->update(['is_done' => !$item->is_done]);

        return response()->json([
            'success' => true,
            'is_done' => (bool) $item->is_done,
            'id'      => $item->id,
        ]);
    }

    /**
     * DELETE /api/nota-belanja/hapus-selesai
     * Hapus semua item is_done = 1 milik user.
     */
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

}