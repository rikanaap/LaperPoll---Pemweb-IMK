<?php

namespace App\Services;

use App\Models\Resep;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ResepService
{

    public function searchByBahans(array $bahanIds, ?string $keyword = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Resep::query()
            ->with(['user', 'bahans'])
            ->withCount('bahans')
            ->where('is_published', true);

        if (filled($keyword)) {
            $query->where('title', 'like', '%' . $keyword . '%');
        }

        if (filled($bahanIds)) {
            
            $query->withCount([
                'bahans as matched_bahan_count' => fn($q) => $q->whereIn('bahans.id', $bahanIds),
            ]);
     
            $query->whereHas('bahans', fn($q) => $q->whereIn('bahans.id', $bahanIds));

            $safeIds = implode(',', array_map('intval', $bahanIds));
            $query->orderByRaw("
                (
                    SELECT COUNT(*) FROM resep_bahan
                    WHERE resep_bahan.resep_id = reseps.id
                    AND resep_bahan.bahan_id IN ({$safeIds})
                ) /
                NULLIF(
                    (SELECT COUNT(*) FROM resep_bahan WHERE resep_bahan.resep_id = reseps.id),
                    0
                ) DESC
            ");
        }

        return $query->latest()->paginate($perPage);
    }
}