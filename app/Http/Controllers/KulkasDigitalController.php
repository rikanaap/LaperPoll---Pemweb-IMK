<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\UserFridge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KulkasDigitalController extends Controller
{
    /**
     * Halaman utama kulkas digital
     */
    public function index()
    {
        $fridgeItems = UserFridge::with('bahan')
            ->where('user_id', Auth::id())
            ->orderBy('bahan_id')
            ->orderBy('bought_date', 'desc')
            ->get();

        $grouped = $fridgeItems->groupBy('bahan_id')->map(function ($items) {
            $bahan = $items->first()->bahan;
            $hasExpiry = $bahan->expired_expectancy_day !== null;

            $statusFinal = 'tersedia';

            foreach ($items as $item) {
                if ($item->expired_date) {
                    $diff = Carbon::now()->startOfDay()
                        ->diffInDays(Carbon::parse($item->expired_date)->startOfDay(), false);

                    if ($diff <= 3) {
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
                            ? Carbon::parse($item->bought_date)->format('d M Y')
                            : null,
                        'expired_date' => $item->expired_date
                            ? Carbon::parse($item->expired_date)->format('d M Y')
                            : null,
                        'sisa_hari'    => $diff,
                    ];
                })->values(),
            ];
        })->values();

        $bahans = Bahan::orderBy('nama')->get();

        return view('pages.kulkas_digital.index', compact('grouped', 'bahans'));
    }

    /**
     * Halaman form tambah bahan
     */
    public function tambah()
    {
        $bahans = Bahan::orderBy('nama')->get();

        return view('pages.kulkas_digital.tambah', compact('bahans'));
    }

    /**
     * Simpan bahan baru ke kulkas user
     */
    public function store(Request $request)
    {
        $request->validate([
            'bahan_id'     => 'required|exists:bahans,id',
            'jumlah'       => 'required|string|max:50',
            'bought_date'  => 'required|date',
            'expired_date' => 'nullable|date|after_or_equal:bought_date',
        ]);

        UserFridge::create([
            'user_id'      => Auth::id(),
            'bahan_id'     => $request->bahan_id,
            'jumlah'       => $request->jumlah,
            'bought_date'  => $request->bought_date,
            'expired_date' => $request->expired_date,
        ]);

        return redirect()
            ->route('kulkas.index')
            ->with('success', 'Bahan berhasil ditambahkan ke kulkas!');
    }

    /**
     * Hapus satu pembelian dari kulkas
     */
    public function destroy($id)
    {
        $item = UserFridge::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $item->delete();

        return redirect()
            ->route('kulkas.index')
            ->with('success', 'Pembelian berhasil dihapus.');
    }

    /**
     * API search bahan (buat autocomplete / dropdown tambah bahan)
     */
    public function searchBahan(Request $request)
    {
        $q = $request->get('q', '');

        $bahans = Bahan::where('nama', 'like', "%{$q}%")
            ->orderBy('nama')
            ->get(['id', 'nama', 'expired_expectancy_day']);

        return response()->json($bahans);
    }
}