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
                ['order' => 1, 'duration' => '00:02:00', 'desc' => 'Panaskan minyak goreng di wajan dengan api sedang.'],
                ['order' => 2, 'duration' => '00:03:00', 'desc' => 'Tumis bawang merah dan bawang putih hingga harum dan keemasan.'],
                ['order' => 3, 'duration' => '00:03:00', 'desc' => 'Masukkan telur, orak-arik hingga matang dan tercampur rata.'],
                ['order' => 4, 'duration' => '00:08:00', 'desc' => 'Masukkan nasi putih, tambahkan kecap manis, garam, dan merica. Aduk rata hingga nasi tidak menggumpal.'],
                ['order' => 5, 'duration' => null,        'desc' => 'Angkat dan sajikan selagi hangat dengan taburan bawang goreng dan irisan cabai.'],
            ],

            'Mie Goreng Jawa' => [
                ['order' => 1, 'duration' => '00:05:00', 'desc' => 'Rebus mie dalam air mendidih hingga matang al dente, tiriskan dan sisihkan.'],
                ['order' => 2, 'duration' => '00:03:00', 'desc' => 'Panaskan minyak, tumis bawang putih cincang hingga keemasan dan harum.'],
                ['order' => 3, 'duration' => '00:03:00', 'desc' => 'Tambahkan telur, orak-arik sebentar lalu aduk bersama bumbu.'],
                ['order' => 4, 'duration' => '00:05:00', 'desc' => 'Masukkan mie, tambahkan kecap manis, garam, dan sedikit merica. Aduk rata.'],
                ['order' => 5, 'duration' => null,        'desc' => 'Sajikan dengan taburan bawang goreng, irisan tomat, dan acar mentimun.'],
            ],

            'Telur Dadar Crispy' => [
                ['order' => 1, 'duration' => '00:03:00', 'desc' => 'Kocok telur dalam mangkuk bersama garam, merica, dan irisan daun bawang hingga rata.'],
                ['order' => 2, 'duration' => '00:02:00', 'desc' => 'Panaskan minyak agak banyak di wajan dengan api sedang-besar.'],
                ['order' => 3, 'duration' => '00:02:00', 'desc' => 'Tuang kocokan telur tipis-tipis ke minyak panas, biarkan menyebar.'],
                ['order' => 4, 'duration' => '00:05:00', 'desc' => 'Goreng hingga pinggiran telur kecoklatan dan renyah, balik sekali lalu tiriskan.'],
            ],

            'Ayam Kecap Pedas' => [
                ['order' => 1, 'duration' => '00:10:00', 'desc' => 'Bersihkan potongan ayam, lumuri dengan perasan jeruk nipis dan sedikit garam. Diamkan 10 menit agar bau amis hilang.'],
                ['order' => 2, 'duration' => '00:05:00', 'desc' => 'Haluskan bawang merah, bawang putih, dan cabai merah menggunakan ulekan atau blender.'],
                ['order' => 3, 'duration' => '00:05:00', 'desc' => 'Panaskan minyak, tumis bumbu halus bersama daun salam hingga harum dan matang.'],
                ['order' => 4, 'duration' => '00:05:00', 'desc' => 'Masukkan potongan ayam, aduk rata dengan bumbu hingga semua permukaan ayam terlapisi.'],
                ['order' => 5, 'duration' => '00:20:00', 'desc' => 'Tambahkan kecap manis dan air secukupnya, masak dengan api kecil hingga ayam empuk dan bumbu meresap.'],
                ['order' => 6, 'duration' => '00:03:00', 'desc' => 'Masukkan irisan tomat dan daun bawang, aduk sebentar. Koreksi rasa lalu sajikan.'],
            ],

            'Capcay Sayur' => [
                ['order' => 1, 'duration' => '00:07:00', 'desc' => 'Cuci dan potong semua sayuran: wortel serong, kubis kasar, sawi potong 3 cm, dan bawang putih cincang.'],
                ['order' => 2, 'duration' => '00:03:00', 'desc' => 'Panaskan minyak, tumis bawang putih hingga harum dan keemasan.'],
                ['order' => 3, 'duration' => '00:05:00', 'desc' => 'Masukkan wortel terlebih dahulu karena paling lama matangnya, tumis 2-3 menit.'],
                ['order' => 4, 'duration' => '00:05:00', 'desc' => 'Tambahkan kubis dan sawi, aduk rata. Tuang sedikit air agar sayuran tidak gosong.'],
                ['order' => 5, 'duration' => '00:03:00', 'desc' => 'Bumbui dengan garam, merica, dan saus tiram. Aduk rata, koreksi rasa lalu sajikan.'],
            ],

            'Soto Ayam' => [
                ['order' => 1, 'duration' => '00:25:00', 'desc' => 'Rebus ayam dalam air mendidih hingga matang dan kaldu keluar. Angkat ayam, suwir-suwir dagingnya.'],
                ['order' => 2, 'duration' => '00:07:00', 'desc' => 'Haluskan bumbu: bawang merah, bawang putih, kunyit, kemiri, dan jahe menggunakan ulekan.'],
                ['order' => 3, 'duration' => '00:05:00', 'desc' => 'Tumis bumbu halus bersama serai geprek, daun salam, dan daun jeruk hingga benar-benar harum.'],
                ['order' => 4, 'duration' => '00:15:00', 'desc' => 'Masukkan tumisan bumbu ke dalam kaldu ayam, didihkan kembali. Tambahkan garam dan gula, koreksi rasa.'],
                ['order' => 5, 'duration' => null,        'desc' => 'Sajikan kuah soto panas dengan suwiran ayam, tauge, irisan telur rebus, dan taburan bawang goreng.'],
            ],

            'Rendang Daging' => [
                ['order' => 1, 'duration' => '00:10:00', 'desc' => 'Haluskan bumbu rendang: cabai merah, bawang merah, bawang putih, jahe, kunyit, dan lengkuas.'],
                ['order' => 2, 'duration' => '00:05:00', 'desc' => 'Potong daging sapi menjadi potongan sedang, masukkan ke dalam wajan bersama bumbu halus dan santan.'],
                ['order' => 3, 'duration' => '00:30:00', 'desc' => 'Masak dengan api sedang sambil sesekali diaduk agar santan tidak pecah. Tambahkan serai, daun jeruk, dan daun kunyit.'],
                ['order' => 4, 'duration' => '01:10:00', 'desc' => 'Setelah santan menyusut dan berminyak, kecilkan api. Terus masak sambil diaduk hingga daging kecoklatan dan kering.'],
                ['order' => 5, 'duration' => null,        'desc' => 'Sajikan rendang hitam kecoklatan yang kering dan kaya rempah bersama nasi putih hangat.'],
            ],

            'Opor Ayam' => [
                ['order' => 1, 'duration' => '00:07:00', 'desc' => 'Haluskan bumbu opor: bawang merah, bawang putih, kemiri sangrai, ketumbar, dan kunyit.'],
                ['order' => 2, 'duration' => '00:05:00', 'desc' => 'Panaskan minyak, tumis bumbu halus bersama serai geprek, daun salam, dan daun jeruk hingga harum dan matang.'],
                ['order' => 3, 'duration' => '00:05:00', 'desc' => 'Masukkan potongan ayam ke dalam tumisan bumbu, aduk rata hingga seluruh permukaan ayam terlumuri bumbu.'],
                ['order' => 4, 'duration' => '00:30:00', 'desc' => 'Tuang santan encer, masak dengan api sedang. Setelah mendidih masukkan santan kental, kecilkan api dan masak hingga ayam matang.'],
                ['order' => 5, 'duration' => null,        'desc' => 'Koreksi rasa dengan garam dan gula. Angkat dan sajikan dengan taburan bawang goreng.'],
            ],

            'Nasi Uduk' => [
                ['order' => 1, 'duration' => '00:05:00', 'desc' => 'Cuci beras hingga air cucian jernih, tiriskan.'],
                ['order' => 2, 'duration' => '00:03:00', 'desc' => 'Masukkan beras ke dalam rice cooker atau panci, tambahkan santan, serai geprek, daun salam, dan garam secukupnya.'],
                ['order' => 3, 'duration' => '00:20:00', 'desc' => 'Masak hingga air menyusut sambil sesekali diaduk agar santan tidak gosong di dasar.'],
                ['order' => 4, 'duration' => '00:20:00', 'desc' => 'Pindahkan ke kukusan, kukus nasi selama 20 menit hingga matang sempurna dan pulen.'],
                ['order' => 5, 'duration' => null,        'desc' => 'Sajikan nasi uduk hangat dengan lauk seperti ayam goreng, telur balado, tempe orek, dan sambal kacang.'],
            ],

            'Gado-gado' => [
                ['order' => 1, 'duration' => '00:20:00', 'desc' => 'Rebus kentang, wortel, buncis, dan tauge secara terpisah hingga matang. Goreng tahu dan tempe hingga keemasan.'],
                ['order' => 2, 'duration' => '00:10:00', 'desc' => 'Haluskan bumbu kacang: kacang tanah goreng, cabai merah, bawang putih, dan gula merah menggunakan ulekan.'],
                ['order' => 3, 'duration' => '00:05:00', 'desc' => 'Encerkan bumbu kacang dengan air matang hangat hingga kekentalan yang pas. Tambahkan kecap manis, garam, dan perasan jeruk limau.'],
                ['order' => 4, 'duration' => '00:05:00', 'desc' => 'Tata semua sayuran rebus, tahu, dan tempe goreng di atas piring saji.'],
                ['order' => 5, 'duration' => null,        'desc' => 'Siram dengan saus kacang, tambahkan telur rebus, kerupuk, dan emping. Sajikan segera.'],
            ],

            'Pisang Goreng Crispy' => [
                ['order' => 1, 'duration' => '00:05:00', 'desc' => 'Kupas pisang kepok yang matang, belah dua memanjang atau pipihkan sedikit.'],
                ['order' => 2, 'duration' => '00:03:00', 'desc' => 'Campurkan tepung terigu, tepung beras, garam, gula pasir, dan air es hingga menjadi adonan encer yang halus.'],
                ['order' => 3, 'duration' => '00:02:00', 'desc' => 'Celupkan potongan pisang ke dalam adonan tepung hingga seluruh permukaan tertutup rata.'],
                ['order' => 4, 'duration' => '00:08:00', 'desc' => 'Goreng pisang dalam minyak panas sedang hingga berwarna keemasan dan renyah, balik sekali agar matang merata.'],
            ],

            'Tahu Crispy' => [
                ['order' => 1, 'duration' => '00:05:00', 'desc' => 'Potong tahu menjadi bentuk dadu atau segitiga sesuai selera. Tekan perlahan dengan tisu dapur untuk mengurangi kadar air.'],
                ['order' => 2, 'duration' => '00:05:00', 'desc' => 'Lumuri tahu dengan campuran garam, merica, dan bawang putih yang dihaluskan. Diamkan 5 menit agar bumbu meresap.'],
                ['order' => 3, 'duration' => '00:03:00', 'desc' => 'Gulingkan tahu berbumbu ke dalam tepung maizena hingga seluruh permukaan tertutup tipis dan rata.'],
                ['order' => 4, 'duration' => '00:10:00', 'desc' => 'Goreng tahu dalam minyak panas yang banyak hingga berwarna kuning keemasan dan tekstur luar renyah.'],
            ],

            'Tempe Mendoan' => [
                ['order' => 1, 'duration' => '00:05:00', 'desc' => 'Iris tempe tipis-tipis melebar sekitar 3-4mm mengikuti serat tempe agar tidak mudah hancur.'],
                ['order' => 2, 'duration' => '00:05:00', 'desc' => 'Buat adonan: campurkan tepung terigu, air, bawang putih halus, ketumbar bubuk, garam, dan irisan daun bawang hingga rata.'],
                ['order' => 3, 'duration' => '00:02:00', 'desc' => 'Celupkan irisan tempe satu per satu ke dalam adonan tepung yang agak encer.'],
                ['order' => 4, 'duration' => '00:05:00', 'desc' => 'Goreng tempe dalam minyak panas sedang sebentar saja sekitar 2-3 menit, tidak perlu sampai kering agar tetap lembut di dalam.'],
            ],

            'Bakwan Sayur' => [
                ['order' => 1, 'duration' => '00:08:00', 'desc' => 'Iris tipis wortel, kol, dan daun bawang. Pastikan ukurannya seragam agar matang merata.'],
                ['order' => 2, 'duration' => '00:05:00', 'desc' => 'Campurkan semua sayuran dengan tepung terigu, telur, garam, merica, dan kaldu bubuk.'],
                ['order' => 3, 'duration' => '00:03:00', 'desc' => 'Tuang air sedikit demi sedikit sambil diaduk hingga adonan kental dan semua sayuran tertutup tepung.'],
                ['order' => 4, 'duration' => '00:10:00', 'desc' => 'Ambil adonan sesendok makan, goreng dalam minyak panas sedang hingga kedua sisi kecoklatan dan matang.'],
            ],

            'Onde-onde' => [
                ['order' => 1, 'duration' => '00:20:00', 'desc' => 'Rebus kacang hijau kupas dengan air hingga benar-benar lunak. Tiriskan, haluskan, dan campur dengan gula pasir. Bentuk bulatan kecil sebagai isian.'],
                ['order' => 2, 'duration' => '00:10:00', 'desc' => 'Campurkan tepung ketan dengan air panas dan garam sedikit demi sedikit, uleni hingga adonan kalis dan tidak lengket di tangan.'],
                ['order' => 3, 'duration' => '00:15:00', 'desc' => 'Ambil adonan ketan seukuran bola pingpong, pipihkan, letakkan isian kacang hijau di tengah, tutup dan bulatkan dengan rapi.'],
                ['order' => 4, 'duration' => '00:03:00', 'desc' => 'Basahi permukaan bola ketan sedikit dengan air, gulingkan di atas wijen hingga seluruh permukaan tertutup rapat.'],
                ['order' => 5, 'duration' => '00:12:00', 'desc' => 'Goreng onde-onde dalam minyak yang baru hangat dengan api kecil, sambil sesekali dibalik hingga mengembang, berwarna keemasan, dan matang merata.'],
            ],

            'Es Teh Manis' => [
                ['order' => 1, 'duration' => '00:05:00', 'desc' => 'Seduh 1-2 kantong teh celup atau 1 sendok teh daun teh dalam segelas air panas mendidih. Biarkan 3-5 menit.'],
                ['order' => 2, 'duration' => '00:02:00', 'desc' => 'Angkat kantong teh, tambahkan gula pasir sesuai selera, aduk hingga benar-benar larut.'],
                ['order' => 3, 'duration' => null,        'desc' => 'Tuang teh ke dalam gelas berisi es batu yang banyak. Sajikan segera selagi dingin menyegarkan.'],
            ],

            'Jus Alpukat' => [
                ['order' => 1, 'duration' => '00:05:00', 'desc' => 'Belah alpukat matang menjadi dua, buang bijinya, keruk daging alpukat dengan sendok ke dalam blender.'],
                ['order' => 2, 'duration' => '00:03:00', 'desc' => 'Tambahkan susu kental manis, susu cair dingin, dan gula pasir ke dalam blender bersama alpukat.'],
                ['order' => 3, 'duration' => '00:02:00', 'desc' => 'Masukkan beberapa bongkah es batu, blender selama 30-45 detik hingga halus dan creamy.'],
                ['order' => 4, 'duration' => null,        'desc' => 'Tuang ke dalam gelas, tambahkan sirop coklat atau milo di atasnya jika suka. Sajikan segera.'],
            ],

            'Es Cincau Hijau' => [
                ['order' => 1, 'duration' => '00:05:00', 'desc' => 'Ambil cincau hijau yang sudah dicetak dan didinginkan, potong menjadi dadu-dadu kecil yang rapi.'],
                ['order' => 2, 'duration' => '00:10:00', 'desc' => 'Rebus gula merah bersama air dan 1 lembar daun pandan hingga gula larut sempurna menjadi sirop kental kecoklatan.'],
                ['order' => 3, 'duration' => '00:03:00', 'desc' => 'Campur santan segar dengan sedikit garam dan daun pandan, panaskan sebentar lalu dinginkan.'],
                ['order' => 4, 'duration' => '00:03:00', 'desc' => 'Susun potongan cincau hijau di dasar gelas saji, tuang santan secukupnya, lalu siram dengan sirop gula merah.'],
                ['order' => 5, 'duration' => null,        'desc' => 'Tambahkan es batu yang banyak. Sajikan segera agar cincau tidak mencair dan tetap segar.'],
            ],

            'Klepon' => [
                ['order' => 1, 'duration' => '00:07:00', 'desc' => 'Campurkan tepung ketan dengan air daun suji pandan (untuk warna hijau alami) dan sejumput garam. Uleni perlahan.'],
                ['order' => 2, 'duration' => '00:10:00', 'desc' => 'Uleni adonan tepung ketan hingga kalis, tidak lengket, dan mudah dibentuk. Tambahkan air sedikit demi sedikit jika terlalu kering.'],
                ['order' => 3, 'duration' => '00:15:00', 'desc' => 'Ambil adonan seukuran kelereng, pipihkan, isi dengan serutan gula merah padat di tengahnya, tutup rapat dan bulatkan.'],
                ['order' => 4, 'duration' => '00:10:00', 'desc' => 'Rebus bola-bola ketan dalam air mendidih hingga mengapung ke permukaan, tanda sudah matang. Angkat dengan serok.'],
                ['order' => 5, 'duration' => null,        'desc' => 'Gulingkan klepon yang masih panas di atas kelapa parut yang sudah dikukus dan diberi sedikit garam. Sajikan segera.'],
            ],

            'Bubur Sumsum' => [
                ['order' => 1, 'duration' => '00:05:00', 'desc' => 'Larutkan tepung beras dengan santan encer sedikit demi sedikit sambil diaduk agar tidak bergerindil. Tambahkan garam dan daun pandan.'],
                ['order' => 2, 'duration' => '00:15:00', 'desc' => 'Masak larutan tepung di atas api kecil sambil terus diaduk searah agar tidak gosong hingga mengental dan matang sempurna.'],
                ['order' => 3, 'duration' => '00:10:00', 'desc' => 'Di panci terpisah, rebus gula merah dengan air dan daun pandan hingga larut dan menghasilkan kuah kental kecoklatan.'],
                ['order' => 4, 'duration' => null,        'desc' => 'Sendok bubur sumsum putih ke dalam mangkuk saji, siram dengan kuah gula merah di atasnya. Sajikan hangat.'],
            ],

            'Puding Coklat' => [
                ['order' => 1, 'duration' => '00:05:00', 'desc' => 'Campurkan bubuk agar-agar, gula pasir, dan coklat bubuk dalam panci. Aduk rata sebelum ditambahkan cairan.'],
                ['order' => 2, 'duration' => '00:12:00', 'desc' => 'Tuang susu cair ke dalam campuran bubuk, aduk rata, masak di atas api sedang sambil terus diaduk hingga mendidih dan semua larut.'],
                ['order' => 3, 'duration' => '00:15:00', 'desc' => 'Angkat dari kompor, tuang ke dalam cetakan puding yang sudah dibasahi air. Biarkan uap panas keluar di suhu ruang.'],
                ['order' => 4, 'duration' => '01:00:00', 'desc' => 'Setelah cukup dingin dan mulai mengeras di tepi, masukkan ke dalam lemari es minimal 1 jam hingga puding set sempurna.'],
                ['order' => 5, 'duration' => null,        'desc' => 'Keluarkan puding dari cetakan, sajikan dengan saus vanilla atau topping sesuai selera.'],
            ],

            'Ayam Bakar Bumbu Rujak' => [
                ['order' => 1, 'duration' => '00:10:00', 'desc' => 'Haluskan bumbu rujak: cabai merah, bawang merah, bawang putih, kemiri sangrai, dan gula merah menggunakan ulekan.'],
                ['order' => 2, 'duration' => '00:30:00', 'desc' => 'Lumuri potongan ayam dengan bumbu rujak halus secara merata, diamkan minimal 30 menit agar bumbu benar-benar meresap.'],
                ['order' => 3, 'duration' => '00:20:00', 'desc' => 'Kukus ayam berbumbu di atas api sedang selama 20 menit agar daging matang dan empuk sebelum dibakar.'],
                ['order' => 4, 'duration' => '00:15:00', 'desc' => 'Bakar ayam di atas bara api atau panggangan sambil dioles sisa bumbu rujak setiap beberapa menit agar tidak kering.'],
                ['order' => 5, 'duration' => null,        'desc' => 'Angkat ayam bakar yang sudah berwarna kecoklatan harum, sajikan dengan lalapan segar, sambal, dan nasi hangat.'],
            ],

            'Ikan Goreng Sambal' => [
                ['order' => 1, 'duration' => '00:10:00', 'desc' => 'Bersihkan ikan, buang isi perut dan insang. Lumuri seluruh permukaan ikan dengan perasan jeruk nipis, kunyit halus, dan garam. Diamkan 10 menit.'],
                ['order' => 2, 'duration' => '00:12:00', 'desc' => 'Goreng ikan dalam minyak panas yang banyak dengan api sedang-besar hingga garing kecoklatan di semua sisi. Tiriskan.'],
                ['order' => 3, 'duration' => '00:05:00', 'desc' => 'Haluskan bahan sambal: cabai merah, cabai rawit, bawang merah, tomat, dan terasi bakar.'],
                ['order' => 4, 'duration' => '00:08:00', 'desc' => 'Tumis sambal halus dengan sedikit minyak hingga matang, harum, dan warna berubah gelap. Tambahkan garam dan gula, koreksi rasa.'],
                ['order' => 5, 'duration' => null,        'desc' => 'Tata ikan goreng di piring, sajikan dengan sambal merah di sampingnya, lengkap dengan lalapan timun, kemangi, dan tomat.'],
            ],

            'Tumis Kangkung' => [
                ['order' => 1, 'duration' => '00:05:00', 'desc' => 'Cuci kangkung bersih di bawah air mengalir, petik batang dan daun mudanya, buang bagian batang yang terlalu keras.'],
                ['order' => 2, 'duration' => '00:03:00', 'desc' => 'Panaskan minyak di wajan dengan api besar, tumis bawang putih geprek dan cabai rawit iris hingga harum.'],
                ['order' => 3, 'duration' => '00:05:00', 'desc' => 'Masukkan kangkung sekaligus, tambahkan garam, saus tiram, dan sedikit gula. Aduk cepat dengan api besar.'],
                ['order' => 4, 'duration' => null,        'desc' => 'Masak hanya 2-3 menit agar kangkung tidak overcooked dan tetap hijau segar. Angkat dan sajikan segera.'],
            ],

            'Sop Buntut' => [
                ['order' => 1, 'duration' => '00:10:00', 'desc' => 'Rebus buntut sapi dengan air hingga mendidih selama 5 menit, buang air rebusan pertama untuk menghilangkan bau dan lemak berlebih.'],
                ['order' => 2, 'duration' => '01:30:00', 'desc' => 'Rebus kembali buntut dengan air bersih yang banyak bersama jahe geprek, bawang putih, dan sedikit garam hingga buntut empuk.'],
                ['order' => 3, 'duration' => '00:20:00', 'desc' => 'Masukkan wortel dan kentang yang sudah dipotong, masak hingga sayuran matang empuk dan kaldu semakin kaya rasa.'],
                ['order' => 4, 'duration' => '00:05:00', 'desc' => 'Koreksi rasa dengan garam dan merica. Tambahkan pala bubuk untuk aroma khas sop buntut.'],
                ['order' => 5, 'duration' => null,        'desc' => 'Sajikan sop buntut panas dalam mangkuk dengan taburan seledri cincang, bawang goreng, dan sambal di sisi mangkuk.'],
            ],

            'Pecel Lele' => [
                ['order' => 1, 'duration' => '00:10:00', 'desc' => 'Bersihkan ikan lele, buang insang dan isi perut, belah punggungnya agar bumbu meresap. Lumuri dengan kunyit, garam, dan jeruk nipis.'],
                ['order' => 2, 'duration' => '00:15:00', 'desc' => 'Goreng lele dalam minyak panas yang cukup banyak dengan api sedang hingga garing dan kuning kecoklatan di semua sisi.'],
                ['order' => 3, 'duration' => '00:05:00', 'desc' => 'Haluskan bahan sambal pecel: cabai merah, bawang merah, bawang putih, tomat, terasi, dan sedikit gula merah.'],
                ['order' => 4, 'duration' => '00:08:00', 'desc' => 'Tumis sambal halus dengan sedikit minyak bekas menggoreng hingga matang dan aromanya keluar. Koreksi rasa.'],
                ['order' => 5, 'duration' => null,        'desc' => 'Tata lele goreng di atas piring, sajikan bersama sambal, lalapan segar (timun, kemangi, kubis), dan nasi putih hangat.'],
            ],

            'Nasi Kuning' => [
                ['order' => 1, 'duration' => '00:05:00', 'desc' => 'Cuci beras hingga bersih, rendam dalam air bersih selama 15-30 menit agar nasi lebih pulen.'],
                ['order' => 2, 'duration' => '00:05:00', 'desc' => 'Masukkan beras ke panci bersama santan, kunyit yang dihaluskan, serai geprek, daun salam, dan garam.'],
                ['order' => 3, 'duration' => '00:20:00', 'desc' => 'Masak dengan api sedang sambil sesekali diaduk hingga santan terserap sempurna oleh beras.'],
                ['order' => 4, 'duration' => '00:20:00', 'desc' => 'Pindahkan nasi ke kukusan yang sudah dipanaskan, kukus selama 20 menit hingga nasi kuning matang sempurna dan pulen.'],
                ['order' => 5, 'duration' => null,        'desc' => 'Cetak nasi kuning dalam wadah kerucut atau bentuk sesuai selera. Sajikan dengan pelengkap seperti ayam goreng, telur, tempe orek, dan serundeng.'],
            ],

            'Semur Daging' => [
                ['order' => 1, 'duration' => '00:10:00', 'desc' => 'Potong daging sapi menjadi dadu besar. Rebus sebentar dalam air mendidih selama 5 menit, buang air rebusan untuk mengurangi bau.'],
                ['order' => 2, 'duration' => '00:07:00', 'desc' => 'Tumis bawang merah, bawang putih, jahe, pala, dan cengkeh hingga harum dan bumbu matang.'],
                ['order' => 3, 'duration' => '00:05:00', 'desc' => 'Masukkan daging ke dalam tumisan bumbu, aduk rata. Tambahkan kecap manis yang banyak, garam, gula, dan air secukupnya.'],
                ['order' => 4, 'duration' => '00:45:00', 'desc' => 'Masak dengan api kecil sambil ditutup hingga daging benar-benar empuk dan kuah semur mengental kecoklatan. Aduk sesekali.'],
                ['order' => 5, 'duration' => null,        'desc' => 'Koreksi rasa, pastikan kuah cukup manis gurih. Angkat dan sajikan semur dengan nasi putih hangat.'],
            ],

            'Lodeh Sayur' => [
                ['order' => 1, 'duration' => '00:10:00', 'desc' => 'Potong semua bahan: labu siam dadu sedang, kacang panjang 3cm, tempe dadu, dan haluskan bumbu: bawang merah, bawang putih, cabai, dan kemiri.'],
                ['order' => 2, 'duration' => '00:07:00', 'desc' => 'Tumis bumbu halus bersama serai geprek, daun salam, dan lengkuas geprek hingga benar-benar harum dan matang.'],
                ['order' => 3, 'duration' => '00:05:00', 'desc' => 'Masukkan semua potongan sayuran dan tempe, aduk rata bersama tumisan bumbu selama 2 menit.'],
                ['order' => 4, 'duration' => '00:20:00', 'desc' => 'Tuang santan encer, masak hingga mendidih lalu masukkan santan kental. Kecilkan api, masak hingga sayuran empuk dan kuah mengental.'],
                ['order' => 5, 'duration' => null,        'desc' => 'Koreksi rasa dengan garam dan gula merah. Sajikan lodeh hangat dengan nasi putih dan lauk pelengkap.'],
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
                        'step_duration' => $langkah['duration'],
                    ]
                );
            }
        }
    }
}