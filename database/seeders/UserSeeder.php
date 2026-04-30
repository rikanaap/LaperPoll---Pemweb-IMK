<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin LaperPoll',
            'email' => 'admin@laperpoll.com',
            'password' => 'password123',
            'is_admin' => true,
            'profile_photo' => null,
        ]);

        // User tetap
        User::create([
            'name' => 'Ikbal Miftahudin',
            'email' => 'ikbal@gmail.com',
            'password' => 'password123',
            'is_admin' => false,
            'profile_photo' => null,
        ]);

        User::create([
            'name' => 'Harmoni Natanael',
            'email' => 'harmoni@gmail.com',
            'password' => 'password123',
            'is_admin' => false,
            'profile_photo' => null,
        ]);

        // Dummy user faker
        User::factory(10)->create();
    }
}
