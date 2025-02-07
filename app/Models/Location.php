<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    /** @use HasFactory<\Database\Factories\LocationFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'email',
        'phone_number',
        'full_address',
    ];

    public function locationImage()
    {
        return $this->hasOne(LocationImage::class, 'location_id', 'id');
    }
}
