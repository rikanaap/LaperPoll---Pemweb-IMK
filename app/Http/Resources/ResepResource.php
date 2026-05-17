<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResepResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $bahanParam = $request->query('bahan') ?? $request->input('bahan');
        
        $inputBahanIds = $bahanParam ? collect(explode(',', $bahanParam))
            ->map(fn($id) => (int) trim($id))
            ->filter()
            ->toArray() : [];

        $isSearchingByBahan = !empty($inputBahanIds);

        $totalBahan = (int) ($this->bahans_count ?? ($this->relationLoaded('bahans') ? $this->bahans->count() : 0));
        
        $matchedBahan = isset($this->matched_bahan_count) 
            ? (int) $this->matched_bahan_count 
            : ($this->relationLoaded('bahans') ? $this->bahans->whereIn('id', $inputBahanIds)->count() : 0);
            
        $matchPercentage = $totalBahan > 0 ? round(($matchedBahan / $totalBahan) * 100) : 0;

        $missingBahans = [];
        if ($isSearchingByBahan && $this->relationLoaded('bahans')) {
            $missingBahans = $this->bahans->filter(function ($bahan) use ($inputBahanIds) {
                return !in_array($bahan->id, $inputBahanIds);
            })->map(function ($bahan) {
                return [
                    'id' => $bahan->id,
                    'nama' => $bahan->nama,
                ];
            })->values()->all();
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'thumbnail' => $this->thumbnail ? asset('storage/' . $this->thumbnail) : null,
            'cook_duration' => $this->cook_duration,
            'rating' => $this->current_star ?? 5.0,
            'views' => $this->views_count ?? 0,
            'search_by_bahan' => $isSearchingByBahan,
            'total_bahan_count' => $totalBahan,
            'matched_bahan_count' => $matchedBahan,
            'match_percentage' => $matchPercentage,
            'missing_bahans' => $missingBahans,
            'author' => [
                'name' => $this->user?->name ?? 'User LaperPoll',
                'avatar' => $this->user?->avatar ? asset('storage/' . $this->user->avatar) : null,
            ],
            'created_at' => $this->created_at ? $this->created_at->isoFormat('D MMMM YYYY') : null,
        ];
    }
}