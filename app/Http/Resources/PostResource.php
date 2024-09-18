<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
                'name' => $this->category->name,
                'icon' => $this->category->icon,
            ],
            'subcategory' => [
                'name' => $this->subcategory->name,
                'icon' => $this->subcategory->icon,
            ],
            'images' => $this->images,
            'comments' => $this->comments,
            'created_at' => $this->created_at,
        ];
    }
}
