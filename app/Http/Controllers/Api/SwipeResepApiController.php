<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Filter;
use App\Services\SwipeResepService;
use App\Http\Resources\ResepSwipeResource;

class SwipeResepApiController extends Controller
{
    protected $swipeService;

    public function __construct(SwipeResepService $swipeService)
    {
        $this->swipeService = $swipeService;
    }

    public function getRasa(): JsonResponse
    {
        $rasa = Filter::query()
            ->where('level', 3)
            ->select(['id', 'title', 'description'])
            ->inRandomOrder()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data rasa',
            'data' => $rasa
        ]);
    }

    public function filterSwipe(Request $request): JsonResponse 
    {
        $rawFilters = $request->query('filters');

        if (!$rawFilters) {
            return response()->json([
                'success' => false,
                'message' => 'Filter rasa wajib diisi',
                'data' => []
            ], 422);
        }

        $filters = is_string($rawFilters) ? explode(',', $rawFilters) : $rawFilters;
        
        $cleanFilters = collect($filters)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->toArray();

        if (empty($cleanFilters)) {
            return response()->json([
                'success' => false,
                'message' => 'Filter tidak valid',
                'data' => []
            ], 422);
        }

        $reseps = $this->swipeService->filterRecipesBySwipe($cleanFilters);

        $selectedFiltersData = Filter::query()
            ->whereIn('id', $cleanFilters)
            ->select(['id', 'title'])
            ->get()
            ->map(fn($f) => [
                'id' => $f->id,
                'title' => $f->title
            ])
            ->toArray();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil rekomendasi resep',
            'selected_filters' => $selectedFiltersData,
            'total_result' => $reseps->count(),
            'data' => ResepSwipeResource::collection($reseps)
        ]);
    }
}