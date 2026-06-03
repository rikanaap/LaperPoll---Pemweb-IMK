<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name'          => 'Admin LaperPoll',
            'email'         => 'admin@laperpoll.com',
            'password'      => Hash::make('password123'),
            'is_admin'      => true,
            'profile_photo' => null,
        ]);

        // Ikbal
        User::create([
            'name'          => 'Ikbal Miftahudin',
            'email'         => 'ikbal@gmail.com',
            'password'      => Hash::make('password123'),
            'is_admin'      => false,
            'profile_photo' => null,
        ]);

        // Harmoni
        User::create([
            'name'          => 'Harmoni Natanael',
            'email'         => 'harmoni@gmail.com',
            'password'      => Hash::make('password123'),
            'is_admin'      => false,
            'profile_photo' => null,
        ]);

        // Ihsan
        User::create([
            'name'          => 'Muhammad Ihsan Ansori',
            'email'         => 'ihsan@gmail.com',
            'password'      => Hash::make('password123'),
            'is_admin'      => false,
            'profile_photo' => null,
        ]);

        // Iqbal
        User::create([
            'name'          => 'Muhamad Iqbal Ramadhan',
            'email'         => 'iqbal@gmail.com',
            'password'      => Hash::make('password123'),
            'is_admin'      => false,
            'profile_photo' => null,
        ]);

        // Dummy users faker
        User::factory(10)->create();
    }
}