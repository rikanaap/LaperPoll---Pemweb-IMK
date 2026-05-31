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

    // ──────────────────────────────────────────────────────────
    // Read
    // ──────────────────────────────────────────────────────────

    /**
     * Ambil daftar resep dengan filter, search, dan pagination.
     *
     * @param array{search: ?string, status: ?bool, filter_id: ?int} $filters
     */
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

    /**
     * Ambil semua filter/kategori untuk dropdown.
     */
    public function getAllFilters(): Collection
    {
        return Filter::orderBy('title')->get();
    }

    // ──────────────────────────────────────────────────────────
    // Write
    // ──────────────────────────────────────────────────────────

    /**
     * Toggle status published sebuah resep.
     * Return true jika sekarang published, false jika di-unpublish.
     */
    public function togglePublish(Resep $resep): bool
    {
        $resep->update(['is_published' => ! $resep->is_published]);

        return $resep->is_published;
    }

    /**
     * Hapus resep beserta thumbnail-nya dari storage.
     */
    public function deleteResep(Resep $resep): string
    {
        $title = $resep->title;

        // Hapus thumbnail dari storage jika ada
        if ($resep->thumbnail && Storage::exists($resep->thumbnail)) {
            Storage::delete($resep->thumbnail);
        }

        $resep->delete(); // cascade ke langkahs, bahans, dsb via migration

        return $title;
    }
}