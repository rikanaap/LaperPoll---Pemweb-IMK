<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LangkahResep;
use App\Models\Resep;

class LangkahResepSeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            'Nasi Goreng Spesial' => [
                ['order' => 1, 'desc' => 'Panaskan minyak di wajan dengan api sedang.'],
                ['order' => 2, 'desc' => 'Tumis bawang merah dan bawang putih hingga harum.'],
                ['order' => 3, 'desc' => 'Masukkan telur lalu orak-arik hingga matang.'],
                ['order' => 4, 'desc' => 'Masukkan nasi, kecap manis, garam, dan merica. Aduk rata.'],
                ['order' => 5, 'desc' => 'Sajikan selagi hangat dengan taburan bawang goreng.'],
            ],

            'Mie Goreng Jawa' => [
                ['order' => 1, 'desc' => 'Rebus mie hingga matang lalu tiriskan.'],
                ['order' => 2, 'desc' => 'Tumis bawang putih hingga harum.'],
                ['order' => 3, 'desc' => 'Masukkan mie, kecap manis, dan garam. Aduk rata.'],
                ['order' => 4, 'desc' => 'Tambahkan telur orak-arik, masak hingga matang.'],
                ['order' => 5, 'desc' => 'Sajikan dengan taburan bawang goreng dan acar.'],
            ],

            'Telur Dadar Crispy' => [
                ['order' => 1, 'desc' => 'Kocok telur dengan garam, merica, dan daun bawang.'],
                ['order' => 2, 'desc' => 'Panaskan minyak banyak dengan api sedang.'],
                ['order' => 3, 'desc' => 'Tuang kocokan telur tipis-tipis ke minyak panas.'],
                ['order' => 4, 'desc' => 'Goreng hingga renyah kecoklatan lalu tiriskan.'],
            ],

            'Ayam Kecap Pedas' => [
                ['order' => 1, 'desc' => 'Bersihkan ayam dan beri perasan jeruk nipis, diamkan 10 menit.'],
                ['order' => 2, 'desc' => 'Haluskan bawang merah, bawang putih, dan cabai merah.'],
                ['order' => 3, 'desc' => 'Tumis bumbu halus hingga harum dan matang.'],
                ['order' => 4, 'desc' => 'Masukkan ayam, tambahkan kecap manis dan air secukupnya.'],
                ['order' => 5, 'desc' => 'Masak dengan api kecil hingga ayam empuk dan bumbu meresap.'],
                ['order' => 6, 'desc' => 'Tambahkan tomat dan daun bawang, masak sebentar lalu sajikan.'],
            ],

            'Capcay Sayur' => [
                ['order' => 1, 'desc' => 'Potong semua sayuran sesuai selera.'],
                ['order' => 2, 'desc' => 'Tumis bawang putih hingga harum.'],
                ['order' => 3, 'desc' => 'Masukkan wortel terlebih dahulu karena paling keras.'],
                ['order' => 4, 'desc' => 'Tambahkan kubis dan sawi, aduk rata.'],
                ['order' => 5, 'desc' => 'Bumbui dengan garam, merica, dan saus tiram. Sajikan.'],
            ],

            'Soto Ayam' => [
                ['order' => 1, 'desc' => 'Rebus ayam dengan air hingga matang, angkat dan suwir.'],
                ['order' => 2, 'desc' => 'Haluskan bawang merah, bawang putih, kunyit, dan kemiri.'],
                ['order' => 3, 'desc' => 'Tumis bumbu halus bersama serai dan daun salam hingga harum.'],
                ['order' => 4, 'desc' => 'Masukkan bumbu ke dalam kaldu ayam, didihkan.'],
                ['order' => 5, 'desc' => 'Sajikan dengan suwiran ayam, tauge, telur, dan bawang goreng.'],
            ],

            'Rendang Daging' => [
                ['order' => 1, 'desc' => 'Haluskan semua bumbu rendang: bawang, cabai, jahe, kunyit, lengkuas.'],
                ['order' => 2, 'desc' => 'Masukkan daging, bumbu halus, dan santan ke dalam wajan.'],
                ['order' => 3, 'desc' => 'Masak dengan api sedang sambil terus diaduk agar santan tidak pecah.'],
                ['order' => 4, 'desc' => 'Kecilkan api setelah santan menyusut, terus masak hingga kering kecoklatan.'],
                ['order' => 5, 'desc' => 'Sajikan rendang yang kering dan hitam kecoklatan dengan nasi putih.'],
            ],

            'Opor Ayam' => [
                ['order' => 1, 'desc' => 'Haluskan bumbu: bawang merah, bawang putih, kemiri, ketumbar.'],
                ['order' => 2, 'desc' => 'Tumis bumbu halus bersama serai, daun salam, dan daun jeruk.'],
                ['order' => 3, 'desc' => 'Masukkan potongan ayam, aduk rata dengan bumbu.'],
                ['order' => 4, 'desc' => 'Tuang santan, masak dengan api kecil hingga ayam matang.'],
                ['order' => 5, 'desc' => 'Koreksi rasa, sajikan dengan taburan bawang goreng.'],
            ],

            'Nasi Uduk' => [
                ['order' => 1, 'desc' => 'Cuci beras hingga bersih.'],
                ['order' => 2, 'desc' => 'Masak beras dengan santan, serai, daun salam, dan garam.'],
                ['order' => 3, 'desc' => 'Aduk sesekali agar santan tidak gosong di dasar.'],
                ['order' => 4, 'desc' => 'Kukus nasi hingga matang sempurna.'],
                ['order' => 5, 'desc' => 'Sajikan dengan lauk pelengkap seperti ayam goreng dan sambal.'],
            ],

            'Gado-gado' => [
                ['order' => 1, 'desc' => 'Rebus kentang, wortel, buncis, dan tauge secara terpisah.'],
                ['order' => 2, 'desc' => 'Haluskan kacang tanah goreng, cabai, bawang putih, dan gula merah.'],
                ['order' => 3, 'desc' => 'Encerkan saus kacang dengan air matang, tambahkan kecap dan jeruk limau.'],
                ['order' => 4, 'desc' => 'Tata sayuran rebus dan tahu di piring.'],
                ['order' => 5, 'desc' => 'Siram dengan saus kacang, sajikan dengan kerupuk.'],
            ],

            'Pisang Goreng Crispy' => [
                ['order' => 1, 'desc' => 'Kupas pisang dan belah menjadi dua memanjang.'],
                ['order' => 2, 'desc' => 'Buat adonan tepung terigu dengan air, garam, dan sedikit gula.'],
                ['order' => 3, 'desc' => 'Celupkan pisang ke dalam adonan tepung.'],
                ['order' => 4, 'desc' => 'Goreng dalam minyak panas hingga keemasan dan renyah.'],
            ],

            'Tahu Crispy' => [
                ['order' => 1, 'desc' => 'Potong tahu menjadi dadu atau segitiga.'],
                ['order' => 2, 'desc' => 'Bumbui dengan garam, merica, dan bawang putih halus.'],
                ['order' => 3, 'desc' => 'Balut tahu dengan tepung maizena.'],
                ['order' => 4, 'desc' => 'Goreng dalam minyak panas hingga kuning keemasan.'],
            ],

            'Tempe Mendoan' => [
                ['order' => 1, 'desc' => 'Iris tempe tipis-tipis melebar.'],
                ['order' => 2, 'desc' => 'Buat adonan tepung dengan bumbu: bawang putih, ketumbar, garam, dan daun bawang.'],
                ['order' => 3, 'desc' => 'Celupkan tempe ke adonan tepung yang agak encer.'],
                ['order' => 4, 'desc' => 'Goreng sebentar dengan minyak panas, jangan terlalu kering.'],
            ],

            'Bakwan Sayur' => [
                ['order' => 1, 'desc' => 'Iris tipis wortel, kol, dan daun bawang.'],
                ['order' => 2, 'desc' => 'Campur sayuran dengan tepung terigu, telur, garam, dan merica.'],
                ['order' => 3, 'desc' => 'Tambahkan air secukupnya hingga adonan kental.'],
                ['order' => 4, 'desc' => 'Goreng sesendok adonan dalam minyak panas hingga matang.'],
            ],

            'Onde-onde' => [
                ['order' => 1, 'desc' => 'Rebus kacang hijau kupas hingga lunak, haluskan dan beri gula.'],
                ['order' => 2, 'desc' => 'Campur tepung ketan dengan air panas dan garam, uleni hingga kalis.'],
                ['order' => 3, 'desc' => 'Pipihkan adonan, isi dengan kacang hijau, bulatkan.'],
                ['order' => 4, 'desc' => 'Gulingkan bola ketan di atas wijen hingga seluruh permukaan tertutup.'],
                ['order' => 5, 'desc' => 'Goreng dalam minyak hangat dengan api kecil hingga matang.'],
            ],

            'Es Teh Manis' => [
                ['order' => 1, 'desc' => 'Seduh teh dengan air panas, biarkan 3-5 menit.'],
                ['order' => 2, 'desc' => 'Tambahkan gula pasir, aduk hingga larut.'],
                ['order' => 3, 'desc' => 'Dinginkan di kulkas atau tambahkan es batu. Sajikan.'],
            ],

            'Jus Alpukat' => [
                ['order' => 1, 'desc' => 'Belah alpukat, ambil dagingnya.'],
                ['order' => 2, 'desc' => 'Blender alpukat bersama susu kental manis dan sedikit susu cair.'],
                ['order' => 3, 'desc' => 'Tambahkan es batu, blender sebentar lagi.'],
                ['order' => 4, 'desc' => 'Tuang ke gelas, tambahkan sirop coklat jika suka.'],
            ],

            'Es Cincau Hijau' => [
                ['order' => 1, 'desc' => 'Siapkan cincau hijau yang sudah dicetak, potong dadu.'],
                ['order' => 2, 'desc' => 'Rebus gula merah dengan air hingga menjadi sirop kental.'],
                ['order' => 3, 'desc' => 'Siapkan santan dengan tambahan sedikit garam.'],
                ['order' => 4, 'desc' => 'Susun cincau di gelas, tuang santan dan sirop gula merah.'],
                ['order' => 5, 'desc' => 'Tambahkan es batu. Sajikan segera.'],
            ],

            'Klepon' => [
                ['order' => 1, 'desc' => 'Campur tepung ketan dengan air daun suji dan sedikit garam.'],
                ['order' => 2, 'desc' => 'Uleni hingga kalis dan tidak lengket di tangan.'],
                ['order' => 3, 'desc' => 'Ambil sedikit adonan, pipihkan, isi dengan gula merah, bulatkan.'],
                ['order' => 4, 'desc' => 'Rebus bola ketan dalam air mendidih hingga mengapung.'],
                ['order' => 5, 'desc' => 'Angkat, gulingkan di atas kelapa parut. Sajikan.'],
            ],

            'Bubur Sumsum' => [
                ['order' => 1, 'desc' => 'Larutkan tepung beras dengan santan dan sedikit garam.'],
                ['order' => 2, 'desc' => 'Masak di atas api kecil sambil terus diaduk hingga mengental.'],
                ['order' => 3, 'desc' => 'Rebus gula merah dengan air dan daun pandan hingga menjadi kuah.'],
                ['order' => 4, 'desc' => 'Sajikan bubur dengan siram kuah gula merah di atasnya.'],
            ],

            'Puding Coklat' => [
                ['order' => 1, 'desc' => 'Campurkan agar-agar, gula, coklat bubuk, dan susu dalam panci.'],
                ['order' => 2, 'desc' => 'Masak dengan api sedang sambil terus diaduk hingga mendidih.'],
                ['order' => 3, 'desc' => 'Tuang ke cetakan, biarkan dingin pada suhu ruang.'],
                ['order' => 4, 'desc' => 'Masukkan ke kulkas minimal 1 jam hingga set sempurna.'],
                ['order' => 5, 'desc' => 'Sajikan dengan saus vanilla atau topping sesuai selera.'],
            ],

            'Ayam Bakar Bumbu Rujak' => [
                ['order' => 1, 'desc' => 'Haluskan bumbu rujak: cabai, bawang, kemiri, dan gula merah.'],
                ['order' => 2, 'desc' => 'Lumuri ayam dengan bumbu halus, diamkan 30 menit.'],
                ['order' => 3, 'desc' => 'Kukus ayam berbumbu selama 20 menit agar empuk.'],
                ['order' => 4, 'desc' => 'Bakar ayam di atas bara api sambil dioles sisa bumbu.'],
                ['order' => 5, 'desc' => 'Sajikan dengan lalapan dan sambal.'],
            ],

            'Ikan Goreng Sambal' => [
                ['order' => 1, 'desc' => 'Bersihkan ikan, lumuri dengan jeruk nipis, garam, dan kunyit.'],
                ['order' => 2, 'desc' => 'Goreng ikan dalam minyak panas hingga garing kecoklatan.'],
                ['order' => 3, 'desc' => 'Haluskan cabai merah, bawang merah, tomat, dan garam untuk sambal.'],
                ['order' => 4, 'desc' => 'Tumis sambal hingga matang, koreksi rasa.'],
                ['order' => 5, 'desc' => 'Sajikan ikan goreng dengan sambal dan lalapan segar.'],
            ],

            'Tumis Kangkung' => [
                ['order' => 1, 'desc' => 'Cuci kangkung bersih, petik bagian yang muda.'],
                ['order' => 2, 'desc' => 'Tumis bawang putih dan cabai rawit hingga harum.'],
                ['order' => 3, 'desc' => 'Masukkan kangkung, tambahkan garam dan saus tiram.'],
                ['order' => 4, 'desc' => 'Masak sebentar dengan api besar agar tidak layu berlebihan. Sajikan.'],
            ],

            'Sop Buntut' => [
                ['order' => 1, 'desc' => 'Rebus buntut sapi dengan air hingga mendidih, buang air pertama.'],
                ['order' => 2, 'desc' => 'Rebus kembali buntut dengan air bersih, jahe, dan bawang.'],
                ['order' => 3, 'desc' => 'Masak hingga buntut empuk selama ±1,5 jam.'],
                ['order' => 4, 'desc' => 'Tambahkan wortel, kentang, dan bumbu lainnya.'],
                ['order' => 5, 'desc' => 'Koreksi rasa, sajikan dengan taburan seledri dan bawang goreng.'],
            ],

            'Pecel Lele' => [
                ['order' => 1, 'desc' => 'Bersihkan lele, lumuri dengan kunyit, garam, dan jeruk nipis.'],
                ['order' => 2, 'desc' => 'Goreng lele dalam minyak panas hingga garing.'],
                ['order' => 3, 'desc' => 'Haluskan bumbu sambal: cabai, bawang, tomat, dan terasi.'],
                ['order' => 4, 'desc' => 'Tumis sambal hingga matang dan harum.'],
                ['order' => 5, 'desc' => 'Sajikan lele goreng dengan sambal, lalapan, dan nasi.'],
            ],

            'Nasi Kuning' => [
                ['order' => 1, 'desc' => 'Cuci beras bersih, rendam sebentar.'],
                ['order' => 2, 'desc' => 'Masak beras dengan santan, kunyit, serai, daun salam, dan garam.'],
                ['order' => 3, 'desc' => 'Aduk hingga santan terserap, lalu kukus hingga matang.'],
                ['order' => 4, 'desc' => 'Bentuk nasi kuning sesuai selera, sajikan dengan lauk pelengkap.'],
            ],

            'Semur Daging' => [
                ['order' => 1, 'desc' => 'Potong daging sapi, rebus sebentar untuk menghilangkan bau.'],
                ['order' => 2, 'desc' => 'Tumis bawang merah, bawang putih, jahe, dan pala hingga harum.'],
                ['order' => 3, 'desc' => 'Masukkan daging, tambahkan kecap manis, garam, dan air.'],
                ['order' => 4, 'desc' => 'Masak dengan api kecil hingga daging empuk dan kuah mengental.'],
                ['order' => 5, 'desc' => 'Koreksi rasa, sajikan dengan nasi putih hangat.'],
            ],

            'Lodeh Sayur' => [
                ['order' => 1, 'desc' => 'Potong semua sayuran: labu siam, kacang panjang, tempe.'],
                ['order' => 2, 'desc' => 'Haluskan bawang merah, bawang putih, cabai, dan kemiri.'],
                ['order' => 3, 'desc' => 'Tumis bumbu halus bersama serai dan daun salam.'],
                ['order' => 4, 'desc' => 'Masukkan sayuran, tuang santan, masak hingga matang.'],
                ['order' => 5, 'desc' => 'Koreksi rasa dengan garam dan gula. Sajikan.'],
            ],
        ];

        foreach ($data as $title => $langkahs) {
            $resep = Resep::where('title', $title)->first();
            if (! $resep) continue;

            foreach ($langkahs as $langkah) {
                LangkahResep::firstOrCreate(
                    [
                        'resep_id'   => $resep->id,
                        'step_order' => $langkah['order'],
                    ],
                    [
                        'description'   => $langkah['desc'],
                        'step_duration' => null,
                    ]
                );
            }
        }
    }
}