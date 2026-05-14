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

        // Kumpulkan bahan_id yang ada di kulkas dan belum expired
        $bahanDiKulkas = $fridgeItems->filter(function ($item) {
            if (!$item->expired_date) return true;
            $diff = Carbon::now()->startOfDay()
                ->diffInDays(Carbon::parse($item->expired_date)->startOfDay(), false);
            return $diff > 0;
        })->pluck('bahan_id')->unique()->values();

        // Rekomendasi resep berdasarkan bahan di kulkas
        $reseps = Resep::with('bahans')->where('is_published', 1)->get();

        $rekomendasi = $reseps->map(function ($resep) use ($bahanDiKulkas) {
            $totalBahan = $resep->bahans->count();
            if ($totalBahan === 0) return null;

            $bahanResepIds = $resep->bahans->pluck('id');
            $bahanAda      = $bahanResepIds->intersect($bahanDiKulkas)->count();
            $bahanKurang   = $resep->bahans->filter(fn($b) => !$bahanDiKulkas->contains($b->id));

            return [
                'id'           => $resep->id,
                'title'        => $resep->title,
                'thumbnail'    => $resep->thumbnail,
                'total_bahan'  => $totalBahan,
                'bahan_ada'    => $bahanAda,
                'bahan_kurang' => $bahanKurang->map(fn($b) => $b->nama)->values(),
                'lengkap'      => $bahanAda === $totalBahan,
            ];
        })
        ->filter()
        ->sortByDesc('bahan_ada')
        ->values()
        ->take(5);

        $grouped = $fridgeItems->groupBy('bahan_id')->map(function ($items) {
            $bahan       = $items->first()->bahan;
            $hasExpiry   = $bahan->expired_expectancy_day !== null;
            $statusFinal = 'tersedia';

            foreach ($items as $item) {
                if ($item->expired_date) {
                    $diff = Carbon::now()->startOfDay()
                        ->diffInDays(Carbon::parse($item->expired_date)->startOfDay(), false);
                    if ($diff <= 0) {
                        $statusFinal = 'expired';
                    } elseif ($diff <= 3 && $statusFinal !== 'expired') {
                        $statusFinal = 'hampir-habis';
                    }
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
            'bought_date'  => 'required|date',
            'expired_date' => 'nullable|date|after_or_equal:bought_date',
        ]);

        UserFridge::create([
            'user_id'      => Auth::id() ?? 2,
            'bahan_id'     => $request->bahan_id,
            'jumlah'       => $request->jumlah,
            'bought_date'  => $request->bought_date,
            'expired_date' => $request->expired_date,
        ]);

        return redirect()->route('kulkas.index')
            ->with('success', 'Bahan berhasil ditambahkan ke kulkas!');
    }

    /**
     * Simpan bahan baru yang belum ada di DB (dari input manual user).
     * Dipanggil via AJAX dari halaman tambah bahan.
     */
    public function storeBahanBaru(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:100']);

        $existing = Bahan::whereRaw('LOWER(nama) = ?', [strtolower($request->nama)])->first();
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
            'nama'                   => ucwords(strtolower($request->nama)),
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
        $item = UserFridge::where('id', $id)
            ->where('user_id', Auth::id() ?? 2)
            ->firstOrFail();
        $item->delete();

        return redirect()->route('kulkas.index')
            ->with('success', 'Pembelian berhasil dihapus.');
    }
}