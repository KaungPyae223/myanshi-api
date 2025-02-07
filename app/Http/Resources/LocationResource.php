<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LocationResource extends JsonResource
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
            'slug' => $this->slug,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'full_address' => $this->full_address,
            'location_image' => $this->locationImage
                ? (Str::startsWith($this->locationImage->image_path, ['http://', 'https://'])
                    ? $this->locationImage->image_path
                    : asset('storage/location_images/' . $this->locationImage->image_path))
                : null,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
