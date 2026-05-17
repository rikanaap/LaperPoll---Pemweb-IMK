<?php

namespace App\Services;

use App\Models\Resep;
use Illuminate\Support\Collection;

class SwipeResepService
{
    /**
     * Mengambil rekomendasi resep berdasarkan kecocokan ID filter rasa.
     * Aman dari SQL Column Ambiguity dan 100% kompatibel dengan API Resource.
     */
    public function filterRecipesBySwipe(array $cleanFilters): Collection
    {
        return Resep::query()
            ->whereHas('filters', function ($query) use ($cleanFilters) {
                $query->whereIn('filters.id', $cleanFilters);
            })
            ->with([
                'user:id,name',
                'filters:id,title'
            ])
            ->get()
            ->map(function ($resep) use ($cleanFilters) {
                // Ambil semua ID filter yang dimiliki resep saat ini
                $resepFilterIds = $resep->filters->pluck('id')->toArray();
                
                // Cari irisan (intersection) untuk menghitung berapa banyak rasa yang cocok
                $matchedIds = array_intersect($resepFilterIds, $cleanFilters);
                
                // Suntik properti match_count secara dinamis ke dalam model instance
                $resep->match_count = count($matchedIds);
                
                return $resep;
            })
            // Urutkan dari yang paling cocok (match_count tertinggi), lalu popularitas (views tertinggi)
            ->sortByDesc('views_count')
            ->sortByDesc('match_count')
            ->values();
    }
}