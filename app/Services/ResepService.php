<?php

namespace App\Services;

use App\Models\Resep;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ResepService
{
    /**
     * Mencari resep berdasarkan bahan dan kata kunci tekstual.
     */
    public function searchByBahans(array $bahanIds, ?string $keyword = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Resep::query()
            ->with(['user', 'bahans'])
            ->withCount('bahans')
            ->where('is_published', true);

        // 1. Jalankan Filter Keyword (Jika ada)
        if (!empty($keyword)) {
            $query->where('title', 'like', '%' . $keyword . '%');
        }

        // 2. Jalankan Logika Utama Pencarian Berdasarkan Bahan (Reverse Recipe Search)
        if (!empty($bahanIds)) {
            // Hitung jumlah bahan yang cocok langsung via SQL subquery
            $query->withCount(['bahans as matched_bahan_count' => function ($q) use ($bahanIds) {
                $q->whereIn('bahans.id', $bahanIds);
            }]);

            // Filter: Hanya tampilkan resep yang mengandung minimal salah satu bahan pilihan
            $query->whereHas('bahans', function ($q) use ($bahanIds) {
                $q->whereIn('bahans.id', $bahanIds);
            });

            // Sorting level SQL: Mengurutkan dari persentase kecocokan tertinggi
            $idsString = implode(',', array_map('intval', $bahanIds));
            $query->orderByRaw("
                (SELECT COUNT(*) FROM resep_bahan WHERE resep_bahan.resep_id = reseps.id AND resep_bahan.bahan_id IN ($idsString)) / 
                (SELECT CASE WHEN COUNT(*) = 0 THEN 1 ELSE COUNT(*) END FROM resep_bahan WHERE resep_bahan.resep_id = reseps.id) DESC
            ");
        }

        // 3. Eksekusi Paginasi bawaan Laravel setelah di-sorting oleh Database
        return $query->latest()->paginate($perPage);
    }
}