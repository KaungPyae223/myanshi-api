<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\LocationImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'title' => 'MyanShi Yangon',
                'slug' => Str::slug('MyanShi Yangon'),
                'email' => 'yangon@myanshi.com',
                'phone_number' => '09-123456789',
                'full_address' => 'No. 45, Bogyoke Road, Yangon, Myanmar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'MyanShi Mandalay',
                'slug' => Str::slug('MyanShi Mandalay'),
                'email' => 'mandalay@myanshi.com',
                'phone_number' => '09-987654321',
                'full_address' => 'No. 23, 78th Street, Mandalay, Myanmar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'MyanShi Naypyidaw',
                'slug' => Str::slug('MyanShi Naypyidaw'),
                'email' => 'naypyidaw@myanshi.com',
                'phone_number' => '09-555666777',
                'full_address' => 'Junction Center, Naypyidaw, Myanmar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Location::insert($locations);

        $insertedLocations = Location::all();

        $images = [
            'https://upload.wikimedia.org/wikipedia/commons/thumb/f/ff/Food_court_edo_japan_la_belle_province_basha.jpg/375px-Food_court_edo_japan_la_belle_province_basha.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a1/Sushi_Yasuda.JPG/375px-Sushi_Yasuda.JPG',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/9/97/Sushi_Azabu_NYC.jpg/375px-Sushi_Azabu_NYC.jpg',
        ];


        foreach ($insertedLocations as $index => $location) {
            LocationImage::create([
                'location_id' => $location->id,
                'image_path' => $images[$index] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
