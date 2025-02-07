<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    /** @use HasFactory<\Database\Factories\BlogFactory> */
    use HasFactory;
    protected $fillable=[
        'author_id',
        'short_description',
        'content',
        'image',
    ];

    public function author() {
        return $this->belongsTo(Author::class);
    }
}
