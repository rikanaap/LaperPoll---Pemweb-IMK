<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserFollow;

class UserFollowSeeder extends Seeder
{
    public function run(): void
    {
        $follows = [
            // Harmoni follow Ikbal
            ['user_id' => 3, 'to_user_id' => 2],
            // Harmoni follow Ihsan
            ['user_id' => 3, 'to_user_id' => 4],
            // Harmoni follow Iqbal
            ['user_id' => 3, 'to_user_id' => 5],

            // Ikbal follow Harmoni
            ['user_id' => 2, 'to_user_id' => 3],
            // Ikbal follow Ihsan
            ['user_id' => 2, 'to_user_id' => 4],

            // Ihsan follow Harmoni
            ['user_id' => 4, 'to_user_id' => 3],
            // Ihsan follow Ikbal
            ['user_id' => 4, 'to_user_id' => 2],

            // Iqbal follow Harmoni
            ['user_id' => 5, 'to_user_id' => 3],
            // Iqbal follow Ikbal
            ['user_id' => 5, 'to_user_id' => 2],
        ];

        foreach ($follows as $follow) {
            UserFollow::create($follow);
        }
    }
}