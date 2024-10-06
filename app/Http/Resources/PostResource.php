<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'comment' => $this->comment,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'category' => [
                'name' => $this->category ? $this->category->name : null,
                'icon' => $this->category ? $this->category->name : null,
            ],
            'subcategory' => [
                'name' => $this->subcategory ? $this->subcategory->name : null,
                'icon' => $this->subcategory ? $this->subcategory->icon : null,
            ],
            'images' => $this->images,
            'comments' => $this->comments->loadMissing('user'),
            'created_at' => $this->created_at,
            'location' => $this->location_short,
            'likes' => $this->likes,
            'valid_until' => $this->valid_until ? date('d/m/Y H:i', strtotime($this->valid_until)) : null, // Carbon::parse($this->valid_until)->diffForHumans() : null,
        ];
    }
}
