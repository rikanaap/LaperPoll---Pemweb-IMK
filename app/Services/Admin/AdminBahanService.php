<?php

namespace App\Services\Admin;

use App\Models\Bahan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminBahanService
{
    private const PER_PAGE = 15;

    public function getPaginatedBahans(array $filters): LengthAwarePaginator
    {
        return Bahan::withCount('reseps')
            ->when($filters['search'], fn ($q, $search) =>
                $q->where('nama', 'like', "%{$search}%")
            )
            ->when($filters['expired'] === 'yes', fn ($q) =>
                $q->whereNotNull('expired_expectancy_day')
            )
            ->when($filters['expired'] === 'no', fn ($q) =>
                $q->whereNull('expired_expectancy_day')
            )
            ->orderBy('nama')
            ->paginate(self::PER_PAGE)
            ->withQueryString();
    }

    public function createBahan(array $data): Bahan
    {
        return Bahan::create([
            'nama'                   => $data['nama'],
            'expired_expectancy_day' => $data['expired_expectancy_day'] ?? null,
        ]);
    }

    public function updateBahan(Bahan $bahan, array $data): Bahan
    {
        $bahan->update([
            'nama'                   => $data['nama'],
            'expired_expectancy_day' => $data['expired_expectancy_day'] ?? null,
        ]);

        return $bahan->fresh();
    }

    /**
     * Hapus bahan.
     * Cek dulu apakah bahan masih dipakai di resep — kalau iya, tolak.
     *
     * @throws \RuntimeException
     */
    public function deleteBahan(Bahan $bahan): string
    {
        $usedCount = $bahan->reseps()->count();

        if ($usedCount > 0) {
            throw new \RuntimeException(
                "Bahan \"{$bahan->nama}\" tidak bisa dihapus karena masih digunakan di {$usedCount} resep."
            );
        }

        $nama = $bahan->nama;
        $bahan->delete();

        return $nama;
    }
}