<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Resep;
use App\Models\Filter;
use App\Models\ResepFilter;

class ResepFilterSeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            [
                'resep' => 'Nasi Goreng Spesial',
                'filters' => [
                    'Goreng',
                    'Gurih',
                ]
            ],

            [
                'resep' => 'Mie Goreng Jawa',
                'filters' => [
                    'Goreng',
                    'Manis',
                ]
            ],

            [
                'resep' => 'Telur Dadar Crispy',
                'filters' => [
                    'Goreng',
                    'Gurih',
                ]
            ],

            [
                'resep' => 'Ayam Kecap Pedas',
                'filters' => [
                    'Pedas',
                    'Manis',
                ]
            ],

            [
                'resep' => 'Capcay Sayur',
                'filters' => [
                    'Sehat',
                    'Tumis',
                ]
            ],

        ];

        foreach ($data as $item) {

            $resep = Resep::where(
                'title',
                $item['resep']
            )->first();

            if (!$resep) {
                continue;
            }

            foreach ($item['filters'] as $filterTitle) {

                $filter = Filter::where(
                    'title',
                    $filterTitle
                )->first();

                if (!$filter) {
                    continue;
                }

                ResepFilter::updateOrCreate(
                    [
                        'resep_id' => $resep->id,
                        'filters_id' => $filter->id,
                    ]
                );
            }
        }
    }
}