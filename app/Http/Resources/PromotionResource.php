<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $original_price = $this->price;
        $discount = $this->discount;

        $discount_price = $original_price * $discount/100;

        return [

            "discount_percent" => $this->discount,
            "start_date" => $this->start_date,
            "end_date" => $this->end_date,
            "discount_price" => $discount_price,
            "original_price" => $original_price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ];
    }
}
