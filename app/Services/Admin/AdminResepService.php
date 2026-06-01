<?php

namespace App\Services\Admin;

use App\Models\Filter;
use App\Models\Resep;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class AdminResepService
{
    private const PER_PAGE = 15;

    public function getPaginatedReseps(array $filters): LengthAwarePaginator
    {
        return Resep::with(['user', 'mainFilter'])
            ->when($filters['search'], fn ($q, $search) =>
                $q->where('title', 'like', "%{$search}%")
            )
            ->when(! is_null($filters['status']), fn ($q) =>
                $q->where('is_published', $filters['status'])
            )
            ->when($filters['filter_id'], fn ($q, $filterId) =>
                $q->where('main_filter_id', $filterId)
            )
            ->latest()
            ->paginate(self::PER_PAGE)
            ->withQueryString();
    }

    public function getResepDetail(Resep $resep): Resep
    {
        return $resep->load([
            'user',
            'mainFilter',
            'filters',
            'bahans',
            'langkahs.langkahBahans.resepBahan.bahan',
            'feedbacks.user',
            'feedbacks.photos',
            'attachments',
        ]);
    }

    public function getAllFilters(): Collection
    {
        return Filter::orderBy('title')->get();
    }

    /**
     * Toggle publish. Return true = sekarang published.
     */
    public function togglePublish(Resep $resep): bool
    {
        $resep->update(['is_published' => ! $resep->is_published]);

        return $resep->fresh()->is_published; // ✅ ambil nilai terbaru dari DB
    }

    /**
     * Hapus resep + thumbnail dari storage.
     * Return title resep untuk flash message.
     */
    public function deleteResep(Resep $resep): string
    {
        $title = $resep->title;

        if ($resep->thumbnail && Storage::disk('public')->exists($resep->thumbnail)) {
            Storage::disk('public')->delete($resep->thumbnail);
        }

        $resep->delete();

        return $title;
    }
}