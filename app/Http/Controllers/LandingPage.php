<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\Resep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandingPage extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $reseps_hari = Resep::with('user.verify', 'attachments')->whereDate('created_at', today())->orderBy('created_at', 'desc')->take(8)->get();
        $reseps_favorit = Resep::with('user.verify', 'attachments')->withCount('favoritedBy')->orderBy('favorited_by_count', 'desc')->take(8)->get();
        $bahans = Bahan::get();
        $features = [
            ['name' => 'Kulkas Digital', 'icon' => 'inventory_2', 'link' => route('kulkas.index'), "locked" => true],
            ['name' => 'Nota Belanja', 'icon' => 'shopping_cart', 'link' => route('nota.index'), "locked" => true],
            ['name' => 'Meal Planner', 'icon' => 'calendar_month', 'link' => route('meal-planner.index'), "locked" => true],
            ['name' => 'Swiper Search', 'icon' => 'swipe', 'link' => route('swipe.rasa'), "locked" => false],
        ];
        $comments = [
            [
                'name' => 'Bambang Tri Hartanto',
                'username' => '@bang_tri',
                'rating' => '4.8/5',
                'comment' => 'Sangat membantu dalam mencari resep masakan. Tampilan menarik dan mudah digunakan.',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'username' => '@ct_nur',
                'rating' => '4.5/5',
                'comment' => 'Fiturnya lengkap banget! Sekarang gak perlu bingung lagi mau masak apa setiap hari di rumah.',
            ],
            [
                'name' => 'Ahmad Fauzi',
                'username' => '@ahmad_fauzi',
                'rating' => '5.0/5',
                'comment' => 'Desain aplikasinya bersih, responsif, dan resep-resepnya sangat akurat. Sukses terus!',
            ],
        ];
        $faqs = [
            [
                'q' => 'Apa itu laperpoll?',
                'a' => 'Laperpoll adalah aplikasi resep masakan berbasis komunitas yang memudahkan kamu menemukan resep sesuai bahan yang tersedia di rumah. Didukung fitur Kulkas Digital, Meal Planner, dan Swiper Search yang intuitif.'
            ],
            [
                'q' => 'Bagaimana cara menambahkan resep baru ke aplikasi?',
                'a' => 'Kamu bisa menambahkan resep baru dengan menekan ikon profil, pilih opsi "Tambah Resep", kemudian isi detail nama masakan, bahan-bahan, takaran, langkah pembuatan, serta unggah foto terbaik hasil masakanmu.'
            ],
            [
                'q' => 'Apa itu fitur Swipe Search?',
                'a' => 'Swipe Search adalah metode pencarian resep interaktif yang seru! Kamu bisa mencari ide masakan hanya dengan melakukan geser (swipe) layar ponselmu—geser kanan jika kamu menyukai resepnya untuk disimpan, atau geser kiri untuk melihat rekomendasi resep berikutnya.'
            ],
            [
                'q' => 'Bagaimana cara kerja fitur Kulkas Digital?',
                'a' => 'Kamu cukup memasukkan daftar bahan makanan yang saat ini ada di dalam kulkas aslimu ke dalam aplikasi. Sistem Laperpoll akan otomatis merekomendasikan resep masakan yang bisa dibuat langsung dari bahan-bahan tersebut.'
            ],
            [
                'q' => 'Apa itu fitur Nota Belanja?',
                'a' => 'Fitur Nota Belanja otomatis mencatat seluruh bahan makanan yang kamu butuhkan berdasarkan resep-resep yang sudah kamu jadwalkan di Meal Planner. Fitur ini bikin belanja mingguanmu jadi lebih terstruktur dan bebas dari lupa!'
            ]
        ];
        return view('index', compact('user', 'features', 'reseps_hari', 'faqs', 'reseps_favorit', 'bahans', 'comments'));
    }
}
