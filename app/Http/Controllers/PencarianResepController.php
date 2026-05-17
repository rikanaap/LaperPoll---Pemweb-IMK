<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Services\ResepService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PencarianResepController extends Controller
{
    public function __construct(
        protected ResepService $resepService
    ) {}

    public function index(Request $request): View
    {
        $bahans = Bahan::query()
            ->orderBy('nama')
            ->get()
            ->groupBy(function ($bahan) {
                return strtoupper(substr($bahan->nama, 0, 1));
            });

        $keyword = $request->query('q');
        $bahanParam = $request->query('bahan');
        
        $bahanIds = $bahanParam ? collect(explode(',', $bahanParam))
            ->map(fn($id) => (int) trim($id))
            ->filter()
            ->toArray() : [];

        if ($request->has('bahan') || $request->has('q')) {
            $reseps = $this->resepService->searchByBahans($bahanIds, $keyword, 10);
            
            return view('pages.pencarian-resep.filter-resep.index', compact('bahans', 'keyword', 'bahanIds', 'reseps'));
        }

        return view('pages.pencarian-resep.index', compact('bahans'));
    }
}
