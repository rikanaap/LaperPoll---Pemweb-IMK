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
    /**
     * Parse string jumlah ("250 gram", "2 kg", "6 butir") → integer gram.
     * Jika tidak bisa di-parse (misal "secukupnya"), return 0.
     */
    private static function parseGram(string $jumlah): int
    {
        $jumlah = strtolower(trim($jumlah));
        // Ambil angka di awal
        if (!preg_match('/^([\d.,]+)\s*(.*)$/', $jumlah, $m)) return 0;
        $angka  = (float) str_replace(',', '.', $m[1]);
        $satuan = trim($m[2]);

        return match(true) {
            str_contains($satuan, 'kg')   => (int) round($angka * 1000),
            str_contains($satuan, 'gram') => (int) round($angka),
            str_contains($satuan, 'g')    => (int) round($angka),
            str_contains($satuan, 'ml')   => (int) round($angka),
            str_contains($satuan, 'l')    => (int) round($angka * 1000),
            default                       => 0,  // butir, bungkus, dll → tidak bisa dibandingkan gram
        };
    }

    public function index()
    {
        $userId = Auth::id() ?? 2;

        $fridgeItems = UserFridge::with('bahan')
            ->where('user_id', $userId)
            ->orderBy('bahan_id')
            ->orderBy('bought_date', 'desc')
            ->get();

        // Bahan yang ADA dan belum expired → dipakai untuk rekomendasi resep
        $bahanDiKulkas = $fridgeItems->filter(function ($item) {
            if (!$item->expired_date) return true;
            $sisa = Carbon::now()->startOfDay()
                ->diffInDays(Carbon::parse($item->expired_date)->startOfDay(), false);
            return $sisa > 0;
        })->pluck('bahan_id')->unique()->values();

        // Stok gram per bahan_id (total semua pembelian)
        $stokGram = [];
        foreach ($fridgeItems as $item) {
            $bid = $item->bahan_id;
            $stokGram[$bid] = ($stokGram[$bid] ?? 0) + self::parseGram($item->jumlah);
        }

        // ── REKOMENDASI RESEP ─────────────────────────────────────────────
        // Hanya muncul jika ada ≥1 bahan di kulkas yang belum expired
        $rekomendasi = collect();
        if ($bahanDiKulkas->isNotEmpty()) {
            $reseps = Resep::with('bahans')->where('is_published', 1)->get();

            $rekomendasi = $reseps->map(function ($resep) use ($bahanDiKulkas, $stokGram) {
                $totalBahan    = $resep->bahans->count();
                if ($totalBahan === 0) return null;

                $bahanResepIds = $resep->bahans->pluck('id');
                $bahanAda      = $bahanResepIds->intersect($bahanDiKulkas)->count();
                if ($bahanAda === 0) return null;

                // Detail per bahan: nama, butuh (gram), punya (gram), cukup atau tidak
                $bahanDetail = $resep->bahans->map(function ($b) use ($bahanDiKulkas, $stokGram) {
                    $butuh       = $b->pivot->gram_total ?? 0;
                    $punya       = $stokGram[$b->id] ?? 0;
                    $adaDiKulkas = $bahanDiKulkas->contains($b->id);
                    // Cukup jika: ada di kulkas DAN (stok 0 = satuan non-gram, anggap cukup) ATAU punya >= butuh
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

                // Bahan yang kurang = tidak ada di kulkas ATAU gram kurang
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

        // ── GROUPING KARTU BAHAN ──────────────────────────────────────────
        $grouped = $fridgeItems->groupBy('bahan_id')->map(function ($items) {
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
                'pembelian'  => $items->map(function ($item) {
                    $diff = $item->expired_date
                        ? Carbon::now()->startOfDay()
                            ->diffInDays(Carbon::parse($item->expired_date)->startOfDay(), false)
                        : null;
                    return [
                        'id'           => $item->id,
                        'jumlah'       => $item->jumlah,
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
            'jumlah'       => 'required|string|max:50',
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
            'jumlah'       => $request->jumlah,
            'bought_date'  => $boughtDate,
            'expired_date' => $expiredDate,
        ]);

        return redirect()->route('kulkas.index')
            ->with('success', 'Bahan berhasil ditambahkan ke kulkas!');
    }

    /**
     * AJAX POST — kurangi stok bahan setelah konfirmasi masak.
     * Request JSON: { bahan_ids: [1,2,...], resep_id: 5 }
     */
    public function pakaiResep(Request $request)
    {
        $request->validate([
            'bahan_ids'   => 'required|array',
            'bahan_ids.*' => 'integer|exists:bahans,id',
            'resep_id'    => 'required|integer|exists:reseps,id',
        ]);

        $userId = Auth::id() ?? 2;

        foreach ($request->bahan_ids as $bahanId) {
            // FIFO — hapus batch pembelian paling lama
            $item = UserFridge::where('user_id', $userId)
                ->where('bahan_id', $bahanId)
                ->orderBy('bought_date', 'asc')
                ->first();
            if ($item) $item->delete();
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
            ->with('success', 'Pembelian berhasil dihapus.');
    }
}