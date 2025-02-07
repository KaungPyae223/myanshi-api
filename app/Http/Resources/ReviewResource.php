<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ReviewResource extends JsonResource
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
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_image' => $this->customerImage
                ? (Str::startsWith($this->customerImage->image_path, ['http://', 'https://'])
                    ? $this->customerImage->image_path
                    : asset('storage/customer_images/' . $this->customerImage->image_path))
                : null,
            'review_title' => $this->review_title,
            'review_description' => $this->review_description,
            'review_image' => $this->reviewImage
                ? (Str::startsWith($this->reviewImage->image_path, ['http://', 'https://'])
                    ? $this->reviewImage->image_path
                    : asset('storage/review_images/' . $this->reviewImage->image_path))
                : null,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
