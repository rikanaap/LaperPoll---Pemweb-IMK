<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchResepRequest;
use App\Http\Resources\ResepResource;
use App\Services\BahanService;
use App\Services\ResepService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResepApiController extends Controller
{
    public function __construct(
        protected ResepService $resepService,
        protected BahanService $bahanService,
    ) {}

    public function search(SearchResepRequest $request): JsonResponse
    {
        $keyword  = $request->validated('q');
        $bahanIds = $this->bahanService->parseIdsFromParam($request->validated('bahan'));

        $reseps = $this->resepService->searchByBahans($bahanIds, $keyword);

        return response()->json([
            'success'    => true,
            'message'    => 'Berhasil mengambil data resep.',
            'pagination' => [
                'current_page'   => $reseps->currentPage(),
                'last_page'      => $reseps->lastPage(),
                'per_page'       => $reseps->perPage(),
                'total'          => $reseps->total(),
                'has_more_pages' => $reseps->hasMorePages(),
            ],
            'data' => ResepResource::collection($reseps),
        ]);
    }

    public function renderCards(Request $request): JsonResponse
    {
        $resepsData = $request->input('reseps', []);

        $html = collect($resepsData)
            ->map(fn(array $resep) => view('components.pencarian-resep.resep-card', ['resep' => $resep])->render())
            ->implode('');

        return response()->json([
            'success' => true,
            'html'    => $html,
        ]);
    }

    public function getBahansByIds(Request $request): JsonResponse
    {
        $bahanIds = $this->bahanService->parseIdsFromParam($request->query('ids'));

        if (empty($bahanIds)) {
            return response()->json(['success' => true, 'total' => 0, 'data' => []]);
        }

        $bahans = $this->bahanService->getByIds($bahanIds);

        return response()->json([
            'success' => true,
            'total'   => $bahans->count(),
            'data'    => $bahans,
        ]);
    }
}