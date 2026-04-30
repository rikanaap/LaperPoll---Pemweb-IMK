<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            UserSeeder::class,
            BahanSeeder::class,
            FilterSeeder::class,
            ResepSeeder::class,
            UserFollowSeeder::class,
            LangkahResepSeeder::class,
            ResepAttachmentSeeder::class,
            ResepBahanSeeder::class,
            FeedbackSeeder::class,
            FavoriteSeeder::class,
            UserCartSeeder::class,
            UserFridgeSeeder::class,
            MealPlannerSeeder::class,
            MealPlannerDetailSeeder::class
        ]);
    }
}
