<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [

            [
                'name' => 'Min Khant Aung',
                'email' => 'minkhantaung@gmail.com',
                'password' => Hash::make('password123'),
                'phone_number' => '09-222233344',
                'date_of_birth' => '1998-03-22',
                'gender' => 'male',
                'address' => 'No. 34, Mandalay, Myanmar',
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kaung Pyae',
                'email' => 'kaungpyae@gmail.com',
                'password' => Hash::make('password456'),
                'phone_number' => '09-333344455',
                'date_of_birth' => '2000-07-10',
                'gender' => 'male',
                'address' => 'No. 56, Yangon, Myanmar',
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Aung Ko',
                'email' => 'aungko@gmail.com',
                'password' => Hash::make('password789'),
                'phone_number' => '09-444455566',
                'date_of_birth' => '1992-12-30',
                'gender' => 'male',
                'address' => 'No. 78, Bangkok, Thailand',
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Maung Maung',
                'email' => 'maungmaung@gmail.com',
                'password' => Hash::make('password'),
                'phone_number' => '09-111122233',
                'date_of_birth' => '1995-08-15',
                'gender' => 'male',
                'address' => 'No. 12, Naypyidaw, Myanmar',
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sandar Myint',
                'email' => 'sandarmyint@example.com',
                'password' => Hash::make('asdfasdf'),
                'phone_number' => '09-555566677',
                'date_of_birth' => '1996-05-25',
                'gender' => 'female',
                'address' => 'No. 90, Taunggyi, Myanmar',
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        User::insert($users);

        $insertedUsers = User::all();
        $userImages = [
            'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=1780&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
            'https://images.unsplash.com/photo-1527980965255-d3b416303d12?q=80&w=1780&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
            'https://plus.unsplash.com/premium_photo-1689977807477-a579eda91fa2?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
            'https://images.unsplash.com/photo-1628157588553-5eeea00af15c?q=80&w=1780&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
            'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        ];

        foreach ($insertedUsers as $index => $user) {
            UserImage::create([
                'user_id' => $user->id,
                'image_path' => $userImages[$index] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
