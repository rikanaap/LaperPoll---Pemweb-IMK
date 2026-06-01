<?php

namespace App\Services\Admin;

use App\Models\Filter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AdminFilterService
{
    private const PER_PAGE = 15;

    // ──────────────────────────────────────────────────────────
    // Read
    // ──────────────────────────────────────────────────────────

    public function getPaginatedFilters(array $filters): LengthAwarePaginator
    {
        return Filter::withCount('reseps')
            ->when($filters['search'], fn ($q, $search) =>
                // ✅ wrap orWhere dalam group agar tidak bocor ke kondisi lain
                $q->where(fn ($q) =>
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                )
            )
            ->when($filters['level'], fn ($q, $level) =>
                $q->where('level', $level)
            )
            ->orderBy('level')
            ->orderBy('title')
            ->paginate(self::PER_PAGE)
            ->withQueryString();
    }

    public function getAvailableLevels(): Collection
    {
        return Filter::whereNotNull('level')
            ->distinct()
            ->orderBy('level')
            ->pluck('level');
    }

    // ──────────────────────────────────────────────────────────
    // Write
    // ──────────────────────────────────────────────────────────

    public function createFilter(array $data): Filter
    {
        return Filter::create([
            'title'       => $data['title'],
            'level'       => $data['level'] ?? null,
            'description' => $data['description'] ?? null,
        ]);
    }

    public function updateFilter(Filter $filter, array $data): Filter
    {
        $filter->update([
            'title'       => $data['title'],
            'level'       => $data['level'] ?? null,
            'description' => $data['description'] ?? null,
        ]);

        return $filter->fresh();
    }

    /**
     * Hapus filter.
     * Tolak kalau masih dipakai resep sebagai main_filter atau filter tag.
     *
     * @throws \RuntimeException
     */
    public function deleteFilter(Filter $filter): string
    {
        $usedAsMain = $filter->mainReseps()->count();
        $usedAsTag  = $filter->reseps()->count();
        $total      = $usedAsMain + $usedAsTag;

        if ($total > 0) {
            throw new \RuntimeException(
                "Filter \"{$filter->title}\" tidak bisa dihapus karena masih digunakan di {$total} resep."
            );
        }

        $title = $filter->title;
        $filter->delete();

        return $title;
    }
}