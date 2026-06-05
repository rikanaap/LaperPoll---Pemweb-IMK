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
        // BUG FIX: hapus ?? 2 — route sudah middleware('auth'), Auth::id() tidak akan null
        $userId = Auth::id();

        $fridgeItems = UserFridge::with('bahan')
            ->where('user_id', $userId)
            ->orderBy('bahan_id')
            ->orderBy('bought_date', 'desc')
            ->get();

        // ── Item valid: belum expired (sisa > 0) ─────────────────────
        $validItems = $fridgeItems->filter(function ($item) {
            if (!$item->expired_date) return true;
            // BUG FIX: $casts sudah 'date' → $item->expired_date sudah Carbon, tidak perlu Carbon::parse()
            $sisa = Carbon::now()->startOfDay()
                ->diffInDays($item->expired_date->startOfDay(), false);
            return $sisa > 0;
        });

        $bahanDiKulkas = $validItems->pluck('bahan_id')->unique()->values();

        // BUG FIX: stokGram hanya dari item valid (tidak termasuk yang expired)
        $stokGram = [];
        foreach ($validItems as $item) {
            $bid = $item->bahan_id;
            $stokGram[$bid] = ($stokGram[$bid] ?? 0) + (int)$item->jumlah;
        }

        // ── REKOMENDASI RESEP ─────────────────────────────────────────
        $rekomendasi = collect();
        if ($bahanDiKulkas->isNotEmpty()) {
            $reseps = Resep::with('bahans')->where('is_published', 1)->get();

            $rekomendasi = $reseps->map(function ($resep) use ($bahanDiKulkas, $stokGram) {
                $totalBahan = $resep->bahans->count();
                if ($totalBahan === 0) return null;

                $bahanResepIds = $resep->bahans->pluck('id');
                $bahanAda      = $bahanResepIds->intersect($bahanDiKulkas)->count();
                if ($bahanAda === 0) return null;

                $bahanDetail = $resep->bahans->map(function ($b) use ($bahanDiKulkas, $stokGram) {
                    $butuh       = $b->pivot->gram_total ?? 0;
                    $punya       = $stokGram[$b->id] ?? 0;
                    $adaDiKulkas = $bahanDiKulkas->contains($b->id);

                    // BUG FIX: $punya === 0 bukan berarti cukup — artinya stok kosong.
                    // Cukup = ada di kulkas DAN punya stok > 0 DAN (tidak ada gram_total ATAU cukup gramnya)
                    $cukup = $adaDiKulkas && $punya > 0 && ($butuh === 0 || $punya >= $butuh);

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
            $bahan     = $items->first()->bahan;
            $hasExpiry = $bahan->expired_expectancy_day !== null;

            // BUG FIX: mulai dari 'expired' (terburuk), upgrade ke atas sesuai pembelian terbaik yang ada
            // Sebelumnya: mulai dari 'tersedia', bisa di-overwrite ke expired tapi tidak bisa balik ke tersedia
            // Ini penting untuk kasus: 1 pembelian expired + 1 pembelian fresh → harus 'tersedia'
            $statusFinal = 'expired';
            foreach ($items as $item) {
                if (!$item->expired_date) {
                    // Tidak ada tanggal expired → pasti tersedia, stop
                    $statusFinal = 'tersedia';
                    break;
                }
                // BUG FIX: $casts sudah 'date', tidak perlu Carbon::parse()
                $diff = Carbon::now()->startOfDay()
                    ->diffInDays($item->expired_date->startOfDay(), false);

                if ($diff > 3) {
                    // Ada pembelian yang masih jauh dari expired → tersedia, stop
                    $statusFinal = 'tersedia';
                    break;
                } elseif ($diff > 0) {
                    // Hampir habis — upgrade dari expired, tapi tidak perlu break (mungkin ada yang lebih baik)
                    $statusFinal = 'hampir-habis';
                }
                // diff <= 0 → tetap expired, lanjut cek pembelian berikutnya
            }

            return [
                'bahan_id'   => $bahan->id,
                'nama'       => $bahan->nama,
                'has_expiry' => $hasExpiry,
                'status'     => $statusFinal,
                'stok_gram'  => $stokGram[$bahan->id] ?? 0,
                'pembelian'  => $items->map(function ($item) {
                    // BUG FIX: tidak perlu Carbon::parse() karena $casts sudah 'date'
                    $diff = $item->expired_date
                        ? Carbon::now()->startOfDay()
                            ->diffInDays($item->expired_date->startOfDay(), false)
                        : null;
                    return [
                        'id'           => $item->id,
                        'jumlah'       => $item->jumlah,
                        'bought_date'  => $item->bought_date
                            ? $item->bought_date->format('d M Y') : null,
                        'expired_date' => $item->expired_date
                            ? $item->expired_date->format('d M Y') : null,
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

        // Kirim stok kulkas user untuk notifikasi duplikat di JS
        $stokKulkas = UserFridge::where('user_id', Auth::id())
            ->selectRaw('bahan_id, SUM(jumlah) as total_gram')
            ->groupBy('bahan_id')
            ->pluck('total_gram', 'bahan_id');

        return view('pages.kulkas_digital.tambah', compact('bahans', 'stokKulkas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bahan_id'     => 'required|exists:bahans,id',
            'jumlah'       => 'required|integer|min:1|max:99999',
            'date_mode'    => 'required|in:beli,expired',
            'bought_date'  => 'required_if:date_mode,beli|nullable|date|before_or_equal:today',
            'expired_date' => 'required_if:date_mode,expired|nullable|date|after:yesterday',
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

        // BUG FIX: hapus ?? 2
        UserFridge::create([
            'user_id'      => Auth::id(),
            'bahan_id'     => $request->bahan_id,
            'jumlah'       => $request->jumlah,
            'bought_date'  => $boughtDate,
            'expired_date' => $expiredDate,
        ]);

        return redirect()->route('kulkas.index')->with('toast', 'Bahan berhasil ditambahkan ke kulkas!');
    }

    /**
     * AJAX POST — kurangi stok gram bahan setelah konfirmasi masak.
     * Logika FIFO per bahan:
     *   - Validasi dulu bahwa bahan memang milik user yang login
     *   - Kurangi gram dari pembelian terlama dulu
     *   - Kalau gram habis → hapus row itu, lanjut ke pembelian berikutnya
     *   - Kalau gram resep lebih sedikit dari stok → sisakan, tidak dihapus
     */
    public function pakaiResep(Request $request)
    {
        $request->validate([
            'bahan_ids'    => 'required|array',
            'bahan_ids.*'  => 'integer|exists:bahans,id',
            'gram_dipakai' => 'required|array',
            'resep_id'     => 'required|integer|exists:reseps,id',
        ]);

        // BUG FIX: hapus ?? 2
        $userId      = Auth::id();
        $gramDipakai = $request->gram_dipakai;

        // BUG FIX: validasi kepemilikan — hanya proses bahan yang memang ada di kulkas user ini
        $bahanMilikUser = UserFridge::where('user_id', $userId)
            ->whereIn('bahan_id', $request->bahan_ids)
            ->pluck('bahan_id')
            ->toArray();

        foreach ($request->bahan_ids as $bahanId) {
            // Skip bahan yang bukan milik user ini
            if (!in_array($bahanId, $bahanMilikUser)) continue;

            $sisaDipakai = (int)($gramDipakai[$bahanId] ?? 0);

            if ($sisaDipakai <= 0) {
                // Gram tidak diketahui (0) → hapus row terlama saja (FIFO)
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
                    $sisaDipakai -= $stokItem;
                    $item->delete();
                } else {
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
        // BUG FIX: hapus ?? 2
        UserFridge::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail()
            ->delete();

        return redirect()->route('kulkas.index')
            ->with('toast', 'Pembelian berhasil dihapus.');
    }
}