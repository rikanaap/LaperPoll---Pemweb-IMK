<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Filter;

class FilterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $filters = [

            // LEVEL 1 = Jenis Makanan
            [
                'title' => 'Makanan Berat',
                'level' => 1,
                'description' => 'Menu utama untuk makan besar'
            ],
            [
                'title' => 'Camilan',
                'level' => 1,
                'description' => 'Makanan ringan atau snack'
            ],
            [
                'title' => 'Minuman',
                'level' => 1,
                'description' => 'Berbagai jenis minuman'
            ],
            [
                'title' => 'Dessert',
                'level' => 1,
                'description' => 'Makanan penutup manis'
            ],

            // LEVEL 2 = Metode Masak
            [
                'title' => 'Goreng',
                'level' => 2,
                'description' => 'Dimasak dengan minyak'
            ],
            [
                'title' => 'Rebus',
                'level' => 2,
                'description' => 'Dimasak dengan air mendidih'
            ],
            [
                'title' => 'Kukus',
                'level' => 2,
                'description' => 'Dimasak dengan uap panas'
            ],
            [
                'title' => 'Panggang',
                'level' => 2,
                'description' => 'Dimasak dengan oven atau bara'
            ],
            [
                'title' => 'Tumis',
                'level' => 2,
                'description' => 'Dimasak dengan sedikit minyak'
            ],

            // LEVEL 3 = Rasa / Preferensi
            [
                'title' => 'Pedas',
                'level' => 3,
                'description' => 'Memiliki cita rasa pedas'
            ],
            [
                'title' => 'Manis',
                'level' => 3,
                'description' => 'Memiliki cita rasa manis'
            ],
            [
                'title' => 'Gurih',
                'level' => 3,
                'description' => 'Memiliki cita rasa gurih'
            ],
            [
                'title' => 'Asin',
                'level' => 3,
                'description' => 'Memiliki cita rasa asin'
            ],
            [
                'title' => 'Sehat',
                'level' => 3,
                'description' => 'Menu sehat dan bergizi'
            ],
            [
                'title' => 'Cepat Saji',
                'level' => 3,
                'description' => 'Bisa dibuat cepat'
            ],
        ];

        foreach ($filters as $filter) {
            Filter::create($filter);
        }
    }
}
