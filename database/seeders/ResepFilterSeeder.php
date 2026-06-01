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
            'Nasi Goreng Spesial'     => ['Goreng', 'Gurih'],
            'Mie Goreng Jawa'         => ['Goreng', 'Manis'],
            'Telur Dadar Crispy'      => ['Goreng', 'Gurih'],
            'Ayam Kecap Pedas'        => ['Pedas', 'Manis'],
            'Capcay Sayur'            => ['Tumis', 'Sehat'],
            'Soto Ayam'               => ['Rebus', 'Gurih'],
            'Rendang Daging'          => ['Pedas', 'Gurih'],
            'Opor Ayam'               => ['Rebus', 'Gurih'],
            'Nasi Uduk'               => ['Gurih', 'Cepat Saji'],
            'Gado-gado'               => ['Sehat', 'Gurih'],
            'Pisang Goreng Crispy'    => ['Goreng', 'Manis'],
            'Tahu Crispy'             => ['Goreng', 'Gurih'],
            'Tempe Mendoan'           => ['Goreng', 'Gurih'],
            'Bakwan Sayur'            => ['Goreng', 'Gurih'],
            'Onde-onde'               => ['Manis', 'Gurih'],
            'Es Teh Manis'            => ['Manis', 'Cepat Saji'],
            'Jus Alpukat'             => ['Manis', 'Sehat'],
            'Es Cincau Hijau'         => ['Manis', 'Sehat'],
            'Klepon'                  => ['Manis', 'Gurih'],
            'Bubur Sumsum'            => ['Manis', 'Gurih'],
            'Puding Coklat'           => ['Manis'],
            'Ayam Bakar Bumbu Rujak'  => ['Panggang', 'Pedas', 'Manis'],
            'Ikan Goreng Sambal'      => ['Goreng', 'Pedas'],
            'Tumis Kangkung'          => ['Tumis', 'Gurih'],
            'Sop Buntut'              => ['Rebus', 'Gurih'],
            'Pecel Lele'              => ['Goreng', 'Pedas'],
            'Nasi Kuning'             => ['Gurih', 'Cepat Saji'],
            'Semur Daging'            => ['Manis', 'Gurih'],
            'Lodeh Sayur'             => ['Rebus', 'Gurih', 'Sehat'],
        ];

        foreach ($data as $title => $filters) {
            $resep = Resep::where('title', $title)->first();
            if (! $resep) continue;

            foreach ($filters as $filterTitle) {
                $filter = Filter::where('title', $filterTitle)->first();
                if (! $filter) continue;

                ResepFilter::updateOrCreate([
                    'resep_id'   => $resep->id,
                    'filters_id' => $filter->id,
                ]);
            }
        }
    }
}