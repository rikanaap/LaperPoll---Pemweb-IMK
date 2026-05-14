<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchResepRequest;
use App\Http\Resources\ResepResource;
use App\Models\Bahan;
use App\Services\ResepService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ResepApiController extends Controller
{
    public function __construct(
        protected ResepService $resepService
    ) {}

    public function search(SearchResepRequest $request): JsonResponse 
    {
        $validated = $request->validated();

        $bahanIds = collect($validated['bahan_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->toArray();

        $reseps = $this->resepService->searchByBahans($bahanIds, 10);

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
        if (!$request->filled('ids')) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter ids wajib diisi.',
                'data'    => [],
            ], 422);
        }

        $ids = collect(explode(',', $request->ids))
            ->map(fn ($id) => (int) trim($id))
            ->filter()
            ->values()
            ->toArray();

        $cacheKey = 'bahans_by_ids_' . md5(json_encode($ids));

        $bahans = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($ids) {
            return Bahan::query()
                ->whereIn('id', $ids)
                ->orderBy('nama')
                ->get(['id', 'nama']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data bahan.',
            'total'   => $bahans->count(),
            'data'    => $bahans,
        ]);
    }
}