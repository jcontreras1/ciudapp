<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncidentResource extends JsonResource
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
            'description' => $this->description,
            'posts' => PostResource::collection($this->posts),
            'status' => $this->status,
            'history' => IncidentHistoryResource::collection($this->history),
            'user' => new BasicUserResource($this->user),
            'comments' => IncidentCommentResource::collection($this->comments),
            'postOriginal' => $this->postOriginal ? new PostResource($this->postOriginal) : null,
        ];
    }
}
