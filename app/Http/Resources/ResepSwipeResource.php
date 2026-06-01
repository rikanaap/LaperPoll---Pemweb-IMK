<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ResepSwipeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'detail_url'    => route('detail.resep', $this->id),
            'title'         => $this->title ?? 'Tanpa Judul',
            'description'   => $this->description,
            'thumbnail' => $this->thumbnail_url,
            'current_star'  => round((float) ($this->current_star ?? 0), 1),
            'views_count'   => (int) ($this->views_count ?? 0),
            'cook_duration' => $this->cook_duration,
            'match_count'   => (int) ($this->match_count ?? 0),
            'user'          => [
                'name' => $this->whenLoaded('user', fn() => $this->user->name, 'Unknown'),
            ],
            'filters' => $this->whenLoaded('filters', fn() =>
                $this->filters->map(fn($f) => ['id' => $f->id, 'title' => $f->title])
            , []),
        ];
    }
}