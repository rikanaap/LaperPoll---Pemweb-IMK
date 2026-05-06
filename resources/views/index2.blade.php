@extends('layout.app')

@section('title', 'LaperPoll')

@push('styles')

@endpush

@push('links')
<script src="https://cdn.tailwindcss.com"></script>
@endpush

@section('content')
@include('components.navbar')
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laperpoll - Laper Banget? Nyari Resep ya Laperpoll aja</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&family=Pacifico&display=swap" rel="stylesheet">
</head>

<body class="font-sans text-brown min-h-screen">

    {{-- ========================
         NAVBAR
    ======================== --}}
    <nav class="sticky top-0 z-50 bg-cream/90 backdrop-blur-sm border-b border-peach px-4 py-3 flex items-center justify-between">
        <button class="p-2 rounded-full hover:bg-peach transition-colors" aria-label="Search">
            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.35-4.35" />
            </svg>
        </button>

        <h1 class="font-display text-2xl text-primary tracking-wide">laperpoll</h1>

        <button class="w-9 h-9 rounded-full overflow-hidden border-2 border-primary/30 hover:border-primary transition-colors" aria-label="Profile">
            <img src="https://ui-avatars.com/api/?name=U&background=E8673A&color=fff&size=40" alt="Avatar" class="w-full h-full object-cover">
        </button>
    </nav>

    <main class="max-w-md mx-auto pb-12">

        {{-- ========================
             HERO BANNER — Akses Fitur Lainnya
        ======================== --}}
        <section class="mx-4 mt-5 rounded-2xl bg-gradient-to-br from-secondary/30 to-primary/10 p-5 relative overflow-hidden">
            {{-- Decorative blobs --}}
            <div class="absolute -top-6 -right-6 w-24 h-24 rounded-full bg-secondary/20"></div>
            <div class="absolute -bottom-4 -left-4 w-16 h-16 rounded-full bg-primary/10"></div>

            <h2 class="font-extrabold text-base text-brown relative z-10">Akses Fitur Lainnya</h2>
            <p class="text-xs text-brown/60 mt-0.5 mb-4 relative z-10">Banyak hal lainnya yang bisa kamu gunakan di aplikasi ini</p>

            <div class="grid grid-cols-4 gap-2 relative z-10">
                @php
                $features = [
                ['icon' => '🧊', 'label' => 'Kulkas Digital'],
                ['icon' => '🛒', 'label' => 'Nota Belanja'],
                ['icon' => '📅', 'label' => 'Meal Planner'],
                ['icon' => '🔍', 'label' => 'Swiper Search'],
                ];
                @endphp

                @foreach ($features as $feature)
                <button class="flex flex-col items-center gap-1 card-hover">
                    <div class="w-14 h-14 rounded-2xl bg-white shadow-sm flex items-center justify-center text-2xl">
                        {{ $feature['icon'] }}
                    </div>
                    <span class="text-[10px] font-semibold text-brown/70 text-center leading-tight">{{ $feature['label'] }}</span>
                </button>
                @endforeach
            </div>
        </section>

        {{-- ========================
             RESEP FAVORIT PENGGUNA
        ======================== --}}
        <section class="mt-7">
            <div class="flex items-center justify-between px-4 mb-3">
                <h2 class="font-extrabold text-base text-brown">Resep Favorit Pengguna</h2>
                <a href="#" class="text-xs font-bold text-primary bg-primary/10 px-3 py-1 rounded-full hover:bg-primary/20 transition-colors">Lihat Semua</a>
            </div>

            <div class="flex gap-3 overflow-x-auto scroll-hide px-4 pb-2">
                @php
                $favoriteRecipes = [
                ['name' => 'Spaghetti Bolognese', 'time' => '25 mins', 'img' => 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=200&h=160&fit=crop'],
                ['name' => 'Pancake Blueberry Inggris', 'time' => '25 mins', 'img' => 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=200&h=160&fit=crop'],
                ['name' => 'Pancake Blueberry Inggris', 'time' => '25 mins', 'img' => 'https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=200&h=160&fit=crop'],
                ['name' => 'Pancake Blueberry Inggris', 'time' => '25 mins', 'img' => 'https://images.unsplash.com/photo-1484723091739-30990a2b64f9?w=200&h=160&fit=crop'],
                ];
                @endphp

                @foreach ($favoriteRecipes as $recipe)
                <div class="flex-none w-36 card-hover cursor-pointer">
                    <div class="w-full h-28 rounded-2xl overflow-hidden">
                        <img src="{{ $recipe['img'] }}" alt="{{ $recipe['name'] }}" class="w-full h-full object-cover">
                    </div>
                    <div class="mt-2 px-0.5">
                        <p class="text-xs font-bold text-brown leading-snug line-clamp-2">{{ $recipe['name'] }}</p>
                        <div class="flex items-center gap-1 mt-1">
                            <svg class="w-3 h-3 text-primary/60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M12 6v6l4 2" />
                            </svg>
                            <span class="text-[10px] text-brown/50">{{ $recipe['time'] }}</span>
                            <span class="text-[10px] text-brown/50 ml-1">■ Bahan Tersedia</span>
                        </div>
                        <p class="text-[10px] text-primary mt-0.5 font-semibold">@foodieuk ✓</p>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        {{-- ========================
             RESEP HARI INI
        ======================== --}}
        <section class="mt-7">
            <div class="flex items-center justify-between px-4 mb-3">
                <h2 class="font-extrabold text-base text-brown">Resep Hari Ini</h2>
                <a href="#" class="text-xs font-bold text-primary bg-primary/10 px-3 py-1 rounded-full hover:bg-primary/20 transition-colors">Lihat Semua</a>
            </div>

            <div class="flex gap-3 overflow-x-auto scroll-hide px-4 pb-2">
                @php
                $todayRecipes = [
                ['name' => 'Pancake Blueberry Inggris', 'time' => '25 mins', 'img' => 'https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=200&h=160&fit=crop'],
                ['name' => 'Pancake Blueberry Inggris', 'time' => '25 mins', 'img' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=200&h=160&fit=crop'],
                ['name' => 'Pancake Blueberry Inggris', 'time' => '25 mins', 'img' => 'https://images.unsplash.com/photo-1484723091739-30990a2b64f9?w=200&h=160&fit=crop'],
                ['name' => 'Pancake Blueberry Inggris', 'time' => '25 mins', 'img' => 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=200&h=160&fit=crop'],
                ];
                @endphp

                @foreach ($todayRecipes as $recipe)
                <div class="flex-none w-36 card-hover cursor-pointer">
                    <div class="w-full h-28 rounded-2xl overflow-hidden">
                        <img src="{{ $recipe['img'] }}" alt="{{ $recipe['name'] }}" class="w-full h-full object-cover">
                    </div>
                    <div class="mt-2 px-0.5">
                        <p class="text-xs font-bold text-brown leading-snug line-clamp-2">{{ $recipe['name'] }}</p>
                        <div class="flex items-center gap-1 mt-1">
                            <svg class="w-3 h-3 text-primary/60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M12 6v6l4 2" />
                            </svg>
                            <span class="text-[10px] text-brown/50">{{ $recipe['time'] }}</span>
                            <span class="text-[10px] text-brown/50 ml-1">■ Bahan Tersedia</span>
                        </div>
                        <p class="text-[10px] text-primary mt-0.5 font-semibold">@foodieuk ✓</p>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        {{-- ========================
             REKOMENDASI BAHAN
        ======================== --}}
        <section class="mt-8 mx-4 rounded-3xl bg-gradient-to-b from-peach to-cream p-5">
            <div class="text-center mb-4">
                <h2 class="font-extrabold text-base text-brown">Rekomendasi Bahan</h2>
                <p class="text-xs text-brown/50 mt-0.5">Pilih bahan yang mau digunakan kamu hari ini!</p>
            </div>

            @php
            $ingredients = [
            ['name' => 'Tomat', 'emoji' => '🍅'],
            ['name' => 'Jahe', 'emoji' => '🫚'],
            ['name' => 'Cabe Merah', 'emoji' => '🌶️'],
            ['name' => 'Kunyit', 'emoji' => '🟡'],
            ['name' => 'Lengkuas', 'emoji' => '🌿'],
            ['name' => 'Daun Salam', 'emoji' => '🍃'],
            ['name' => 'Serai', 'emoji' => '🌾'],
            ['name' => 'Kemiri', 'emoji' => '🥜'],
            ['name' => 'Ketumbar', 'emoji' => '🌰'],
            ['name' => 'Lada Hitam', 'emoji' => '⚫'],
            ];
            @endphp

            <div class="grid grid-cols-5 gap-3">
                @foreach ($ingredients as $ingredient)
                <button class="flex flex-col items-center gap-1.5 card-hover group">
                    <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-xl group-hover:bg-primary/10 transition-colors">
                        {{ $ingredient['emoji'] }}
                    </div>
                    <span class="text-[10px] font-semibold text-brown/70 text-center leading-tight">{{ $ingredient['name'] }}</span>
                </button>
                @endforeach
            </div>

            <div class="text-center mt-4">
                <a href="#" class="inline-block text-xs font-bold text-primary border border-primary/30 px-5 py-1.5 rounded-full hover:bg-primary hover:text-white transition-all">Lihat Semua</a>
            </div>
        </section>

        {{-- ========================
             PENDAPAT PENGGUNA LAIN
        ======================== --}}
        <section class="mt-8 mx-4">
            <div class="text-center mb-4">
                <h2 class="font-extrabold text-base text-brown">Pendapat Pengguna Lain</h2>
                <p class="text-xs text-brown/50 mt-0.5">Lihat apa yang pengguna lain katakan<br>terhadap aplikasi ini</p>
            </div>

            @php
            $reviews = [
            [
            'name' => 'Bambang Tri Hartanto',
            'handle' => '@bang_tri',
            'rating' => 4.5,
            'text' => 'Sangat membantu dalam mencari resep masakan. Tampilan menarik dan mudah digunakan.',
            'avatar' => 'BT',
            ],
            [
            'name' => 'Citra Kirana',
            'handle' => '@citra_k',
            'rating' => 4.8,
            'text' => 'Aplikasi masak yang lengkap! Resepnya mudah diikuti dan banyak tips memasak.',
            'avatar' => 'CK',
            ],
            [
            'name' => 'Kevin Julia',
            'handle' => '@kevin_j',
            'rating' => 4.5,
            'text' => 'Desainnya keren dan modern. Fitur meal planner sangat membantu mengatur menu harian.',
            'avatar' => 'KJ',
            ],
            ];
            @endphp

            <div class="flex flex-col gap-3">
                @foreach ($reviews as $review)
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-peach card-hover">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center text-white text-xs font-bold">
                                {{ $review['avatar'] }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-brown leading-tight">{{ $review['name'] }}</p>
                                <p class="text-[10px] text-brown/40">{{ $review['handle'] }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 bg-secondary/20 px-2 py-0.5 rounded-full">
                            <span class="star text-xs">★</span>
                            <span class="text-xs font-bold text-brown">{{ $review['rating'] }}</span>
                        </div>
                    </div>
                    <p class="text-xs text-brown/70 leading-relaxed">{{ $review['text'] }}</p>
                </div>
                @endforeach
            </div>
        </section>

        {{-- ========================
             FAQ — Pertanyaan Pengguna Lain
        ======================== --}}
        <section class="mt-8 mx-4">
            <div class="text-center mb-4">
                <h2 class="font-extrabold text-base text-brown">Pertanyaan Pengguna Lain</h2>
                <p class="text-xs text-brown/50 mt-0.5">Pertanyaan yang sering ditanyakan oleh<br>pengguna lain saat menggunakan aplikasi</p>
            </div>

            @php
            $faqs = [
            ['q' => 'Apa itu Laperpoll?', 'open' => true],
            ['q' => 'Bagaimana cara menambahkan resep baru ke aplikasi?', 'open' => false],
            ['q' => 'Apakah aplikasi ini bisa digunakan secara offline?', 'open' => false],
            ['q' => 'Bisakah saya menyimpan resep favorit saya?', 'open' => false],
            ['q' => 'Apakah aplikasi ini menyediakan video tutorial memasak?', 'open' => false],
            ['q' => 'Bagaimana cara mencari resep berdasarkan bahan yang saya miliki?', 'open' => false],
            ];
            @endphp

            <div class="flex flex-col gap-2" x-data="{ open: 0 }">
                @foreach ($faqs as $i => $faq)
                <div
                    class="rounded-2xl border {{ $faq['open'] ? 'border-primary/40 bg-primary/5' : 'border-peach bg-white' }} overflow-hidden"
                    x-data="{ expanded: {{ $faq['open'] ? 'true' : 'false' }} }">
                    <button
                        class="w-full flex items-center justify-between px-4 py-3 text-left"
                        x-on:click="expanded = !expanded"
                        aria-expanded="expanded">
                        <span class="text-xs font-bold text-brown">{{ $faq['q'] }}</span>
                        <svg
                            class="w-4 h-4 text-primary flex-none transition-transform duration-200"
                            x-bind:class="expanded ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>

                    <div x-show="expanded" x-collapse class="px-4 pb-3">
                        @if ($faq['open'])
                        <p class="text-xs text-brown/60 leading-relaxed">
                            Laperpoll adalah aplikasi resep masakan berbasis komunitas yang memudahkan kamu menemukan resep sesuai bahan yang tersedia di rumah. Didukung fitur Kulkas Digital, Meal Planner, dan Swiper Search yang intuitif.
                        </p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        {{-- ========================
             FOOTER
        ======================== --}}
        <footer class="mt-12 text-center px-4">
            <h2 class="font-display text-3xl text-primary">laperpoll</h2>
            <p class="text-xs italic text-brown/50 mt-1">"Laper Banget? Nyari Resep ya Laperpoll aja"</p>

            <div class="mt-4 border-t border-peach pt-4">
                <p class="text-xs font-semibold text-brown/60 mb-2">Hubungi kami melalui</p>
                <div class="flex items-center justify-center gap-4">
                    <a href="tel:+62" class="flex items-center gap-1 text-[10px] text-brown/50 hover:text-primary transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.88 13.4 19.79 19.79 0 0 1 1.82 4.76 2 2 0 0 1 3.8 2.6h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 10a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                        </svg>
                        62-996-2031-420
                    </a>
                    <a href="mailto:laperpoll@email.id" class="flex items-center gap-1 text-[10px] text-brown/50 hover:text-primary transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="20" height="16" x="2" y="4" rx="2" />
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                        </svg>
                        @laperpoll.id
                    </a>
                </div>
            </div>

            <p class="text-[10px] text-brown/30 mt-4">© 2025 LaporPoll | All Right Reserved</p>
        </footer>

    </main>

    {{-- Alpine.js for FAQ accordion + collapse plugin --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>
@endsection

@push('scripts')

@endpush
@endsection