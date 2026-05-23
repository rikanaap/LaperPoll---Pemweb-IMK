<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\Resep;
use App\Models\UserFridge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KulkasDigitalController extends Controller
{
    public function index()
    {
        $userId = Auth::id() ?? 2;

        $fridgeItems = UserFridge::with('bahan')
            ->where('user_id', $userId)
            ->orderBy('bahan_id')
            ->orderBy('bought_date', 'desc')
            ->get();

        // Bahan yang ADA dan belum expired
        $bahanDiKulkas = $fridgeItems->filter(function ($item) {
            if (!$item->expired_date) return true;
            $sisa = Carbon::now()->startOfDay()
                ->diffInDays(Carbon::parse($item->expired_date)->startOfDay(), false);
            return $sisa > 0;
        })->pluck('bahan_id')->unique()->values();

        // Stok gram per bahan_id (jumlah sekarang sudah integer gram)
        $stokGram = [];
        foreach ($fridgeItems as $item) {
            $bid = $item->bahan_id;
            $stokGram[$bid] = ($stokGram[$bid] ?? 0) + (int)$item->jumlah;
        }

        // ── REKOMENDASI RESEP ─────────────────────────────────────────
        $rekomendasi = collect();
        if ($bahanDiKulkas->isNotEmpty()) {
            $reseps = Resep::with('bahans')->where('is_published', 1)->get();

            $rekomendasi = $reseps->map(function ($resep) use ($bahanDiKulkas, $stokGram) {
                $totalBahan    = $resep->bahans->count();
                if ($totalBahan === 0) return null;

                $bahanResepIds = $resep->bahans->pluck('id');
                $bahanAda      = $bahanResepIds->intersect($bahanDiKulkas)->count();
                if ($bahanAda === 0) return null;

                $bahanDetail = $resep->bahans->map(function ($b) use ($bahanDiKulkas, $stokGram) {
                    $butuh       = $b->pivot->gram_total ?? 0;
                    $punya       = $stokGram[$b->id] ?? 0;
                    $adaDiKulkas = $bahanDiKulkas->contains($b->id);
                    $cukup       = $adaDiKulkas && ($punya === 0 || $punya >= $butuh);

                    return [
                        'id'    => $b->id,
                        'nama'  => $b->nama,
                        'butuh' => $butuh,
                        'punya' => $punya,
                        'ada'   => $adaDiKulkas,
                        'cukup' => $cukup,
                    ];
                })->values();

                $bahanKurang = $bahanDetail->filter(fn($b) => !$b['cukup'])->values();
                $lengkap     = $bahanKurang->isEmpty();

                return [
                    'id'            => $resep->id,
                    'title'         => $resep->title,
                    'thumbnail'     => $resep->thumbnail,
                    'calorie'       => $resep->calorie,
                    'cook_duration' => $resep->cook_duration,
                    'total_bahan'   => $totalBahan,
                    'bahan_ada'     => $bahanAda,
                    'bahan_detail'  => $bahanDetail,
                    'bahan_kurang'  => $bahanKurang,
                    'bahan_ids'     => $bahanResepIds->values(),
                    'lengkap'       => $lengkap,
                ];
            })
            ->filter()
            ->sortByDesc(fn($r) => [$r['lengkap'] ? 1 : 0, $r['bahan_ada']])
            ->values()
            ->take(6);
        }

        // ── GROUPING KARTU BAHAN ──────────────────────────────────────
        $grouped = $fridgeItems->groupBy('bahan_id')->map(function ($items) use ($stokGram) {
            $bahan       = $items->first()->bahan;
            $hasExpiry   = $bahan->expired_expectancy_day !== null;
            $statusFinal = 'tersedia';

            foreach ($items as $item) {
                if ($item->expired_date) {
                    $diff = Carbon::now()->startOfDay()
                        ->diffInDays(Carbon::parse($item->expired_date)->startOfDay(), false);
                    if ($diff <= 0)                                    $statusFinal = 'expired';
                    elseif ($diff <= 3 && $statusFinal === 'tersedia') $statusFinal = 'hampir-habis';
                }
            }

            return [
                'bahan_id'   => $bahan->id,
                'nama'       => $bahan->nama,
                'has_expiry' => $hasExpiry,
                'status'     => $statusFinal,
                'stok_gram'  => $stokGram[$bahan->id] ?? 0,
                'pembelian'  => $items->map(function ($item) {
                    $diff = $item->expired_date
                        ? Carbon::now()->startOfDay()
                            ->diffInDays(Carbon::parse($item->expired_date)->startOfDay(), false)
                        : null;
                    return [
                        'id'           => $item->id,
                        'jumlah'       => $item->jumlah,  // integer gram
                        'bought_date'  => $item->bought_date
                            ? Carbon::parse($item->bought_date)->format('d M Y') : null,
                        'expired_date' => $item->expired_date
                            ? Carbon::parse($item->expired_date)->format('d M Y') : null,
                        'sisa_hari'    => $diff,
                    ];
                })->values(),
            ];
        })->values();

        return view('pages.kulkas_digital.index', compact('grouped', 'rekomendasi'));
    }

    public function tambah()
    {
        $bahans = Bahan::orderBy('nama')->get();
        return view('pages.kulkas_digital.tambah', compact('bahans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bahan_id'     => 'required|exists:bahans,id',
            'jumlah'       => 'required|integer|min:1|max:99999',  // gram
            'date_mode'    => 'required|in:beli,expired',
            'bought_date'  => 'required_if:date_mode,beli|nullable|date',
            'expired_date' => 'required_if:date_mode,expired|nullable|date',
        ]);

        $boughtDate  = null;
        $expiredDate = null;

        if ($request->date_mode === 'beli') {
            $boughtDate = $request->bought_date;
            $bahan = Bahan::find($request->bahan_id);
            if ($bahan && $bahan->expired_expectancy_day) {
                $expiredDate = Carbon::parse($boughtDate)
                    ->addDays($bahan->expired_expectancy_day)->format('Y-m-d');
            }
        } else {
            $expiredDate = $request->expired_date;
            $boughtDate  = Carbon::now()->format('Y-m-d');
        }

        UserFridge::create([
            'user_id'      => Auth::id() ?? 2,
            'bahan_id'     => $request->bahan_id,
            'jumlah'       => $request->jumlah,  // integer gram
            'bought_date'  => $boughtDate,
            'expired_date' => $expiredDate,
        ]);

        // Redirect ke kulkas dengan flag toast (bukan flash blade)
        return redirect()->route('kulkas.index')->with('toast', 'Bahan berhasil ditambahkan ke kulkas!');
    }

    /**
     * AJAX POST — kurangi stok gram bahan setelah konfirmasi masak.
     * Logika FIFO per bahan:
     *   - Kurangi gram dari pembelian terlama dulu
     *   - Kalau gram habis → hapus row itu, lanjut ke pembelian berikutnya
     *   - Kalau gram resep lebih sedikit dari stok → sisakan, tidak dihapus
     */
    public function pakaiResep(Request $request)
    {
        $request->validate([
            'bahan_ids'   => 'required|array',
            'bahan_ids.*' => 'integer|exists:bahans,id',
            'gram_dipakai'=> 'required|array',       // { bahan_id: gram_dibutuhkan }
            'resep_id'    => 'required|integer|exists:reseps,id',
        ]);

        $userId      = Auth::id() ?? 2;
        $gramDipakai = $request->gram_dipakai;  // ['1' => 120, '2' => 250, ...]

        foreach ($request->bahan_ids as $bahanId) {
            $sisaDipakai = (int)($gramDipakai[$bahanId] ?? 0);

            if ($sisaDipakai <= 0) {
                // Gram tidak diketahui (0) → hapus row terlama saja (FIFO lama)
                $item = UserFridge::where('user_id', $userId)
                    ->where('bahan_id', $bahanId)
                    ->orderBy('bought_date', 'asc')
                    ->first();
                if ($item) $item->delete();
                continue;
            }

            // FIFO: kurangi gram dari pembelian terlama dulu
            $items = UserFridge::where('user_id', $userId)
                ->where('bahan_id', $bahanId)
                ->orderBy('bought_date', 'asc')
                ->get();

            foreach ($items as $item) {
                if ($sisaDipakai <= 0) break;

                $stokItem = (int)$item->jumlah;

                if ($stokItem <= $sisaDipakai) {
                    // Habis semua dari pembelian ini
                    $sisaDipakai -= $stokItem;
                    $item->delete();
                } else {
                    // Masih ada sisa → kurangi gramnya
                    $item->update(['jumlah' => $stokItem - $sisaDipakai]);
                    $sisaDipakai = 0;
                }
            }
        }

        return response()->json([
            'success'  => true,
            'redirect' => route('detail.resep', ['id' => $request->resep_id]),
        ]);
    }

    /**
     * AJAX POST — simpan bahan baru ke master data jika belum ada.
     */
    public function storeBahanBaru(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:100']);

        $existing = Bahan::whereRaw('LOWER(nama) = ?', [strtolower(trim($request->nama))])->first();
        if ($existing) {
            return response()->json([
                'id'                     => $existing->id,
                'nama'                   => $existing->nama,
                'has_expiry'             => $existing->expired_expectancy_day !== null,
                'expired_expectancy_day' => $existing->expired_expectancy_day,
                'status'                 => 'existing',
            ]);
        }

        $bahan = Bahan::create([
            'nama'                   => ucwords(strtolower(trim($request->nama))),
            'expired_expectancy_day' => null,
        ]);

        return response()->json([
            'id'                     => $bahan->id,
            'nama'                   => $bahan->nama,
            'has_expiry'             => false,
            'expired_expectancy_day' => null,
            'status'                 => 'created',
        ]);
    }

    public function destroy($id)
    {
        UserFridge::where('id', $id)
            ->where('user_id', Auth::id() ?? 2)
            ->firstOrFail()
            ->delete();

        return redirect()->route('kulkas.index')
            ->with('toast', 'Pembelian berhasil dihapus.');
    }
}