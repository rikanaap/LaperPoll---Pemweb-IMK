<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResepResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $bahanIds = $this->resolveInputBahanIds($request);
        $isSearchingByBahan = filled($bahanIds);

        $totalBahan   = (int) ($this->bahans_count ?? 0);
        $matchedBahan = (int) ($this->matched_bahan_count ?? 0);
        $matchPercent = $totalBahan > 0 ? round(($matchedBahan / $totalBahan) * 100) : 0;

        $missingBahans = $isSearchingByBahan && $this->relationLoaded('bahans')
            ? $this->bahans
                ->filter(fn($bahan) => !in_array($bahan->id, $bahanIds))
                ->map(fn($bahan) => ['id' => $bahan->id, 'nama' => $bahan->nama])
                ->values()
                ->all()
            : [];

        return [
            'id'                  => $this->id,
            'detail_url'          => route('detail.resep', $this->id),
            'title'               => $this->title,
            'description'         => $this->description,
            'slug'                => $this->slug,
            'thumbnail'             => $this->thumbnail_url,
            'cook_duration'       => $this->cook_duration,
            'rating'              => $this->current_star ?? 5.0,
            'views'               => $this->views_count ?? 0,
            'search_by_bahan'     => $isSearchingByBahan,
            'total_bahan_count'   => $totalBahan,
            'matched_bahan_count' => $matchedBahan,
            'match_percentage'    => $matchPercent,
            'missing_bahans'      => $missingBahans,
            'author'              => [
                'name'   => $this->user?->name ?? 'User LaperPoll',
                'avatar' => $this->user?->avatar ? asset('storage/' . $this->user->avatar) : null,
            ],
            'created_at' => $this->created_at?->isoFormat('D MMMM YYYY'),
        ];
    }

   
    private function resolveInputBahanIds(Request $request): array
    {
        $param = $request->query('bahan') ?? $request->input('bahan');

        if (blank($param)) {
            return [];
        }

        return collect(explode(',', $param))
            ->map(fn($id) => (int) trim($id))
            ->filter()
            ->toArray();
    }
}