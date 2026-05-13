<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResepResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $matchPercentage = 0;
        if ($this->total_bahan_count > 0) {
            $matchPercentage = round(($this->matched_bahan_count / $this->total_bahan_count) * 100);
        }

        $selectedBahanIds = collect($request->input('bahan_ids', []))
            ->map(fn ($id) => (int) $id)
            ->toArray();

        $missingBahans = $this->bahans
            ->whereNotIn('id', $selectedBahanIds)
            ->values()
            ->map(fn ($bahan) => [
                'id' => $bahan->id,
                'nama' => $bahan->nama,
            ]);

        return [
            'id'                  => $this->id,
            'title'               => $this->title,
            'description'         => $this->description,
            'thumbnail'           => $this->thumbnail ? asset('storage/' . $this->thumbnail) : null,
            'cook_duration'       => $this->cook_duration,
            'calorie'             => $this->calorie,
            'rating'              => (float) ($this->current_star ?? 0),
            'views'               => (int) ($this->views_count ?? 0),
            'match_percentage'    => $matchPercentage,
            'matched_bahan_count' => $this->matched_bahan_count,
            'total_bahan_count'   => $this->total_bahan_count,
            'missing_bahans'      => $missingBahans,
            'author'              => [
                'name' => $this->user?->name,
            ],
            'created_at'          => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'          => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}