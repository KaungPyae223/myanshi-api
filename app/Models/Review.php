<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_email',
        'review_title',
        'review_description',
    ];

    public function reviewImage()
    {
        return $this->hasOne(ReviewImage::class);
    }

    public function customerImage()
    {
        return $this->hasOne(CustomerImage::class);
    }
}
