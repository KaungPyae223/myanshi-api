<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
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
            'image' => $this->image,
            'tik_tok_link' => $this->tik_tok_link,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
