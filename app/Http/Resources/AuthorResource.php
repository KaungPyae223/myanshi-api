<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'position' => $this->position,
            'detail' => $this->detail,
            'facebook' => $this->facebook,
            'tiktok' => $this->tiktok,
            'youtube' => $this->youtube,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
