<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserFollow;

class UserFollowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
       public function run(): void
    {
        UserFollow::create([
            'user_id' => 3, // Harmoni
            'to_user_id' => 2, // Ikbal
        ]);
    }
}
