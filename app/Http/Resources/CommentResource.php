<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'comment' => $this->comment,
            'user' => $this->user,
            'created_at' => $this->created_at->diffForHumans(),
            'likes' => $this->likes,
            'created_real' => $this->created_at,
            'updated_real' => $this->updated_at,
        ];
    }
}
