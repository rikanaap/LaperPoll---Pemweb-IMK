<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResepSwipeResource;
use App\Services\SwipeResepService;
use App\Models\Filter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SwipeResepApiController extends Controller
{
    public function __construct(
        protected SwipeResepService $swipeService
    ) {}

    public function getRasa(): JsonResponse
    {
        $data = Filter::query()
            ->where('level', 3)
            ->select(['id', 'title', 'description'])
            ->inRandomOrder()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data rasa',
            'data'    => $data,
        ]);
    }

    public function filterSwipe(Request $request): JsonResponse
    {
        $filterIds = $this->parseFilterIds($request->query('filters'));

        if (empty($filterIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Filter rasa wajib diisi dan harus valid.',
                'data'    => [],
            ], 422);
        }

        $reseps = $this->swipeService->filterRecipesBySwipe($filterIds);

        $selectedFilters = Filter::query()
            ->whereIn('id', $filterIds)
            ->select(['id', 'title'])
            ->get()
            ->map(fn($f) => ['id' => $f->id, 'title' => $f->title])
            ->values();

        return response()->json([
            'success'          => true,
            'message'          => 'Berhasil mengambil rekomendasi resep',
            'selected_filters' => $selectedFilters,
            'total_result'     => $reseps->count(),
            'data'             => ResepSwipeResource::collection($reseps),
        ]);
    }

    private function parseFilterIds(mixed $raw): array
    {
        if (empty($raw)) {
            return [];
        }

        return collect(is_string($raw) ? explode(',', $raw) : (array) $raw)
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->toArray();
    }
}