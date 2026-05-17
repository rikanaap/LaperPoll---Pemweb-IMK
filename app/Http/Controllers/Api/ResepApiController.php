<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchResepRequest;
use App\Http\Resources\ResepResource;
use App\Models\Bahan;
use App\Services\ResepService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResepApiController extends Controller
{
    public function __construct(
        protected ResepService $resepService
    ) {}

    public function search(SearchResepRequest $request): JsonResponse 
    {
        $validated = $request->validated();
        $keyword = $validated['q'] ?? null;
        
        $bahanParam = $request->query('bahan') ?? $request->input('bahan');
        $bahanIds = [];

        if ($bahanParam && trim($bahanParam) !== '') {
            $cleanBahanParam = preg_replace('/[^0-9,]/', '', $bahanParam);
            $bahanIds = collect(explode(',', $cleanBahanParam))
                ->map(fn($id) => (int) trim($id))
                ->filter()
                ->values()
                ->toArray();
        }

        $reseps = $this->resepService->searchByBahans($bahanIds, $keyword, 10);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data resep.',
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

    public function getBahansByIds(Request $request): JsonResponse 
    {
        $idsParam = $request->query('ids') ?? $request->input('ids');

        if (!$idsParam) {
            return response()->json(['success' => true, 'total' => 0, 'data' => []]);
        }

        if (is_array($idsParam)) {
            $idsParam = implode(',', $idsParam);
        }

        $cleanParam = preg_replace('/[^0-9,]/', '', $idsParam);
        
        $ids = collect(explode(',', $cleanParam))
            ->map(fn ($id) => (int) trim($id))
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        if (empty($ids)) {
            return response()->json(['success' => true, 'total' => 0, 'data' => []]);
        }

        $bahans = Bahan::query()
            ->whereIn('id', $ids)
            ->orderBy('nama')
            ->get(['id', 'nama']);

        return response()->json([
            'success' => true,
            'total'   => $bahans->count(),
            'data'    => $bahans,
        ]);
    }
}