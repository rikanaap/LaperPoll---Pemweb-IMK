<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResepFilterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [

            // ======================================
            // RESEP 1
            // Pedas + Gurih
            // ======================================
            [
                'resep_id' => 1,
                'filters_id' => 1,
            ],

            [
                'resep_id' => 1,
                'filters_id' => 3,
            ],

            // ======================================
            // RESEP 2
            // Manis
            // ======================================
            [
                'resep_id' => 2,
                'filters_id' => 2,
            ],

            // ======================================
            // RESEP 3
            // Sehat
            // ======================================
            [
                'resep_id' => 3,
                'filters_id' => 5,
            ],

            // ======================================
            // RESEP 4
            // Pedas + Asin
            // ======================================
            [
                'resep_id' => 4,
                'filters_id' => 1,
            ],

            [
                'resep_id' => 4,
                'filters_id' => 4,
            ],

        ];

        DB::table('resep_filters')
            ->insert($data);
    }
}