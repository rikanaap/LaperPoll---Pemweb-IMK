<?php

namespace App\Services;

use App\Models\Resep;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ResepService
{
    public function searchByBahans(
        array $bahanIds,
        int $perPage = 10
    ): LengthAwarePaginator {

        $bahanIds = collect($bahanIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->toArray();

        return Resep::query()
            ->where('is_published', true)

            ->whereHas('bahans', function ($query) use ($bahanIds) {
                $query->whereIn('bahans.id', $bahanIds);
            })

            ->with([
                'user:id,name',
                'bahans:id,nama',
            ])

            ->withCount([
                'bahans as matched_bahan_count' => function ($query) use ($bahanIds) {
                    $query->whereIn('bahans.id', $bahanIds);
                },

                'bahans as total_bahan_count',
            ])

            ->orderByDesc('matched_bahan_count')
            ->latest()

            ->paginate($perPage);
    }
}