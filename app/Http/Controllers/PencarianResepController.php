<?php

namespace App\Http\Controllers;

use App\Services\BahanService;
use App\Services\ResepService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PencarianResepController extends Controller
{
    public function __construct(
        protected BahanService $bahanService,
        protected ResepService $resepService,
    ) {}

    public function index(Request $request): View
    {
        $bahans  = $this->bahanService->getAllGroupedByLetter();
        $keyword = $request->query('q');
        $bahanIds = $this->bahanService->parseIdsFromParam($request->query('bahan'));

        $isFiltering = $request->hasAny(['bahan', 'q']);

        if ($isFiltering) {
            $reseps = $this->resepService->searchByBahans($bahanIds, $keyword);

            return view('pages.pencarian-resep.filter-resep.index', compact(
                'bahans',
                'keyword',
                'bahanIds',
                'reseps',
            ));
        }

        return view('pages.pencarian-resep.index', compact('bahans'));
    }
}