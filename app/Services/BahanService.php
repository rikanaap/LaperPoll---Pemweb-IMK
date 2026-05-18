<?php

namespace App\Services;

use App\Models\Bahan;
use Illuminate\Support\Collection;

class BahanService
{
   
    public function getAllGroupedByLetter(): Collection
    {
        return Bahan::query()
            ->orderBy('nama')
            ->get()
            ->groupBy(fn(Bahan $bahan) => strtoupper(substr($bahan->nama, 0, 1)));
    }


   
    public function getByIds(array $ids): Collection
    {
        if (empty($ids)) {
            return collect();
        }

        return Bahan::query()
            ->whereIn('id', $ids)
            ->orderBy('nama')
            ->get(['id', 'nama']);
    }

 
    public function parseIdsFromParam(?string $param): array
    {
        if (blank($param)) {
            return [];
        }

        $cleaned = preg_replace('/[^0-9,]/', '', $param);

        return collect(explode(',', $cleaned))
            ->map(fn(string $id) => (int) trim($id))
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }
}