<?php

namespace App\Services;

use App\Models\Resep;
use Illuminate\Support\Collection;

class SwipeResepService
{
    public function filterRecipesBySwipe(array $filterIds): Collection
    {
        $filterIds = array_map('intval', $filterIds);

        return Resep::query()
            ->whereHas('filters', fn($q) => $q->whereIn('filters.id', $filterIds))
            ->with(['user:id,name', 'filters:id,title'])
            ->get()
            ->map(function (Resep $resep) use ($filterIds) {
                $resepFilterIds = $resep->filters
                    ->pluck('id')
                    ->map(fn($id) => (int) $id)
                    ->toArray();

                $resep->match_count = count(array_intersect($resepFilterIds, $filterIds));

                return $resep;
            })
            ->sortByDesc('views_count')
            ->sortByDesc('match_count')
            ->values();
    }
}