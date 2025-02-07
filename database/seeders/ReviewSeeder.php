<?php

namespace Database\Seeders;

use App\Models\CustomerImage;
use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reviews = [
            [
                'customer_name' => 'သန်းထွဋ်',
                'customer_email' => 'thanthtut@example.com',
                'review_title' => 'အရသာအရမ်းကောင်း',
                'review_description' => 'စူရှီအရသာကလည်းသန့်ရှင်းပြီး အရမ်းကိုကြိုက်ပါတယ်။',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_name' => 'မင်းခန့်အောင်',
                'customer_email' => 'minkhantaung@example.com',
                'review_title' => 'စူရှီချစ်သူများအတွက်အကောင်းဆုံးနေရာ',
                'review_description' => 'စူရှီချစ်သူတိုင်းလာသင့်တဲ့နေရာပါ။',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_name' => 'ခင်ဝင့်ဝါ',
                'customer_email' => 'khinewinwa@example.com',
                'review_title' => 'စားပြီးတစ်ခေါက်ထပ်လာချင်တယ်',
                'review_description' => 'စူရှီအရသာအမှန်ကိုခံစားရတာကြိုက်ပါတယ်။',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_name' => 'အောင်သင်းထွန်း',
                'customer_email' => 'aungthinthun@example.com',
                'review_title' => 'ဝန်ဆောင်မှုအရမ်းပြည့်စုံ',
                'review_description' => 'ဝန်ထမ်းတွေတတ်တတ်ကြွကြွနဲ့ ဝန်ဆောင်မှုကောင်းပါတယ်။',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_name' => 'ရွှေစံမြင့်',
                'customer_email' => 'shwesaemyint@example.com',
                'review_title' => 'အရသာမြောက်သောစူရှီ',
                'review_description' => 'တကယ်ကိုစားပြီးစိတ်တိုင်းကျခဲ့တယ်။',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Review::insert($reviews);

        $insertedReviews = Review::all();

        $reviewImages = [
            'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0c/Vancouver_sushi_pieces_dllu.jpg/800px-Vancouver_sushi_pieces_dllu.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c0/Spider_Roll_2010.jpg/1024px-Spider_Roll_2010.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Golden_Maki_Rainbow_Roll_sushi.jpg/1024px-Golden_Maki_Rainbow_Roll_sushi.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e0/Golden_Maki_Vegetarian_Dragon_sushi_roll.jpg/1024px-Golden_Maki_Vegetarian_Dragon_sushi_roll.jpg',
            'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Golden_Maki_Rainbow_Roll_sushi.jpg/330px-Golden_Maki_Rainbow_Roll_sushi.jpg',
        ];


        $customerImages = [
            'https://images.unsplash.com/photo-1738762389087-35bcc2b03b2d?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxmZWF0dXJlZC1waG90b3MtZmVlZHwxOXx8fGVufDB8fHx8fA%3D%3D',
            'https://images.unsplash.com/photo-1733343397198-c28b11617d45?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxmZWF0dXJlZC1waG90b3MtZmVlZHw5Nnx8fGVufDB8fHx8fA%3D%3D',
            'https://images.unsplash.com/photo-1738830986230-57029d6ef4f8?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxmZWF0dXJlZC1waG90b3MtZmVlZHw2fHx8ZW58MHx8fHx8',
            'https://images.unsplash.com/photo-1738566061847-c8fa0e3992ad?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxmZWF0dXJlZC1waG90b3MtZmVlZHw0NHx8fGVufDB8fHx8fA%3D%3D',
            'https://images.unsplash.com/photo-1737412358025-160a0c22e6c5?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxmZWF0dXJlZC1waG90b3MtZmVlZHw1NXx8fGVufDB8fHx8fA%3D%3D',
        ];

        foreach ($insertedReviews as $index => $review) {
            ReviewImage::create([
                'review_id' => $review->id,
                'image_path' => $reviewImages[$index] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            CustomerImage::create([
                'review_id' => $review->id,
                'image_path' => $customerImages[$index] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
