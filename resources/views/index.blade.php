@extends('layouts.app')

@section('title', 'LaperPoll')

@push('styles')
<style>
    @media (min-width: 768px) {
        body {
            padding: 0 !important;
        }
    }
</style>

@endpush

@push('links')
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('css/pages/landing-page.css') }}">
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
@endpush

@section('content')
<div class="flex flex-col gap-5 md:gap-[2rem] bg-[#FFF8F5]">
    <x-navbar></x-navbar>
    <div id="features-section" class="relative w-full">
        <x-svg-comp name="cup" class="absolute opacity-[0.55] rorate-[3deg] md:left-10 w-[65px] h-[58px] md:w-[150px] md:h-[116px]"></x-svg-comp>
        <x-svg-comp name="pan" class="absolute top-20 right-0 opacity-[0.55] w-[79px] h-[49px] md:w-[253px] md:h-[82px]"></x-svg-comp>
        <div class="flex flex-col gap-2 items-center md:gap-[1.2rem]">
            <div class="flex flex-col gap-1 items-center md:gap-[0.5rem]">
                <h1 class="font-poppins text-orange-normal-active font-medium text-[0.8rem]/[120%] md:text-[2rem] md:font-light">Akses Fitur Lainnya</h1>
                <p class="font-poppins font-medium text-[0.5rem]/[120%] text-orange-dark-active md:text-[1rem]">Banyak hal lainnya yang bisa kamu gunakan di aplikasi ini</p>
            </div>
            <div class="flex flex-row gap-[0.6rem]">
                @foreach ( $features as $feature )
                @if($feature['locked'] && !$user)
                <a href="{{ route('auth.sign-in') }}">
                    <div class="flex flex-col gap-[0.5rem] justify-center items-center">
                        <div class="resep-logo-locked w-[2.8rem] h-[2.8rem] rounded-[0.5rem]] md:w-[6rem] md:h-[6rem]">
                            <span class="material-icons-round !text-2 md:!text-[3rem] text-accent-dark">lock</span>
                        </div>
                        <p class="font-jakarta text-[0.4rem]/[120%] font-semibold text-accent-dark-active md:text-[0.8rem]">{{ $feature['name'] }}</p>
                    </div>
                </a>
                @else
                <a href="{{ $feature['link'] }}">
                    <div class="flex flex-col gap-[0.5rem] justify-center items-center">
                        <div class="resep-logo w-[2.8rem] h-[2.8rem] rounded-[0.5rem]] md:w-[6rem] md:h-[6rem]">
                            <!-- TODO: UBAH BIAR DINAMIS -->
                            <span class="material-icons-round !text-2 md:!text-[3rem] text-accent-dark">{{ $feature['icon'] }}</span>
                        </div>
                        <p class="font-jakarta text-[0.4rem]/[120%] font-semibold text-accent-dark-active md:text-[0.8rem]">{{ $feature['name'] }}</p>
                    </div>
                </a>
                @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Resep Favorit Pengguna -->
    <div id="favorit-section" class="flex flex-col items-start self-stretch pl-[1.5rem] pr-[0.75rem] gap-[0.4rem]">
        <div class="flex justify-between items-center self-stretch">
            <h1 class="font-poppins text-[0.7rem]/120% text-orange-normal-active font-medium md:text-[2rem] md:font-light">Resep Favorit Pengguna</h1>
            <!-- TODO: Ganti Routing  -->
            <a href="{{ route('main-menu.index', ['m' => 'favorit']) }}" class="px-[0.25rem] py-[0.4rem] rounded-[0.25rem] bg-orange-light-active text-accent-normal font-poppins text-[0.6rem]/[120%] font-medium md:text-[1rem] md:font-normal md:px-[1rem] md:py-[0.4rem]">Lihat Semua</a>
        </div>
        <div class="flex flex-row items-center gap-[0.3rem] overflow-x-scroll w-full">
            @foreach ($reseps_favorit as $resep_f)
            <x-card-resep-landing :resep="$resep_f" :index="0"></x-card-resep-landing>
            @endforeach
        </div>
    </div>

    <!-- Resep Hari Ini -->
    <div id="hari-ini-section" class="flex flex-col items-start self-stretch pl-[1.5rem] pr-[0.75rem] gap-[0.4rem]">
        <div class="flex justify-between items-center self-stretch">
            <h1 class="font-poppins text-[0.7rem]/120% text-orange-normal-active font-medium md:text-[2rem] md:font-light">Resep {{ $resep_hari_caption  }}</h1>
            <a href="{{ route('main-menu.index', ['m' => 'hari_ini']) }}" class="px-[0.25rem] py-[0.4rem] rounded-[0.25rem] bg-orange-light-active text-accent-normal font-poppins text-[0.6rem]/[120%] font-medium md:text-[1rem] md:font-normal md:px-[1rem] md:py-[0.4rem]">Lihat Semua</a>
        </div>
        <div class="flex flex-row items-center gap-[0.3rem] overflow-x-scroll w-full">
            @foreach ($reseps_hari as $resep_h)
            <x-card-resep-landing :resep="$resep_h" :index="0"></x-card-resep-landing>
            @endforeach
        </div>
    </div>

    <!-- Rekomendasi Bahan -->
    <div id="rekomendasi-section" class="relative w-full h-fit">
        <x-svg-comp name="wisk" class="absolute top-3 left-5 opacity-[0.7] w-[37px] h-[64px] md:w-[62px] md:h-[129px]"></x-svg-comp>
        <x-svg-comp name="cup-2" class="absolute bottom-0 right-2 opacity-[0.7] w-[43px] h-[67px] md:w-[89px] md:h-[101px]"></x-svg-comp>
        <div class="py-[0.5rem] px-[4rem]">
            <div class="flex py-[0.75rem] flex-col justify-center items-center gap-[0.6rem] md:gap-[1rem] self-stretch rounded-[0.5rem] bg-[#FFF2ED]">
                <div class="flex flex-col justify-center items-center gap-[0.25rem] md:gap-[0.3rem]">
                    <h1 class="font-poppins text-[0.8rem]/[120%] text-orange-normal-active font-medium md:text-[2rem] md:font-normal">Rekomendasi Bahan</h1>
                    <p class="font-poppins text-[0.5rem]/[120%] text-orange-dark-active font-medium md:text-[1rem] md:font-semibold">Pilih bahan yang mau digunakan kamu hari ini!</p>
                </div>
                <div class="flex justify-center items-center content-center gap-[0.3rem] self-stretch flex-wrap">
                    <!-- Card Bahan -->
                    @foreach ($bahans as $index=>$bahan)
                    <x-card-bahan-landing :bahan="$bahan" :index="$index"></x-card-bahan-landing>
                    @endforeach
                </div>
                <a href="{{ route('pencarian.resep') }}" class="px-[0.4rem] py-[0.4rem] rounded-[0.3rem] bg-orange-light-active text-accent-normal font-poppins text-[0.45rem]/[120%] font-medium md:text-[1rem] md:px-[0.6rem] md:py-[0.75rem]">Lihat Semua</a>
            </div>
        </div>
    </div>

    <!-- Pendapat Pengguna Lain -->
    <div class="flex self-stretch flex-col md:flex-row md:gap-4">
        <div id="pendapat-section" class="relative w-full h-fit">
            <x-svg-comp name="spatula" class="absolute top-24 left-12 w-[22px] h-[53px]"></x-svg-comp>
            <x-svg-comp name="leaf" class="absolute bottom-0 right-12 z-10 w-[54px] h-[40px]"></x-svg-comp>
            <div class="flex py-[0.5rem] px-[1.25rem] flex-col items-center gap-[0.6rem] self-stretch">
                <div class="flex flex-col justify-center items-center gap-[0.25rem] md:gap-[0.3rem]">
                    <h1 class="font-poppins text-[0.8rem]/[120%] text-orange-normal-active font-medium md:text-[2rem]">Pendapat Pengguna Lain</h1>
                    <p class="font-poppins w-[12rem] text-[0.5rem]/[120%] text-center text-orange-dark-active font-medium md:text-[1rem] md:w-[24rem]">Lihat apa yang pengguna lain katakan terhadap aplikasi kami</p>
                </div>
                <div class="relative w-fit">
                    <x-svg-comp name="fork" class="absolute top-10 right-0 z-10 w-[16px] h-[34px]"></x-svg-comp>
                    <x-svg-comp name="spoon" class="absolute bottom-10 left-0 z-10 w-[16px] h-[31px]"></x-svg-comp>
                    <div class="flex flex-col gap-[0.3rem] w-full h-fit">
                        <a href="{{  route('profile.public', ['id' => $comments[0]['id'] ] ) }}" class="rotate-[-3deg] flex w-[15rem] md:w-[25rem] p-[0.62rem] gap-[0.62rem] rounded-[0.3rem] border-[1px] border-solid border-[#F7C9B0] bg-white">
                            <img src="{{ asset('assets/images/Image_DummyProfile.png') }}" alt="Profil Foto" class="w-[2.25rem] h-[2.25rem] aspect-square content-center items-center rounded-[3rem] border-[1px] border-solid border-[#EC4448]">
                            <div class="flex flex-col content-center gap-[0.2rem] w-full">
                                <div class="flex w-full items-center justify-between">
                                    <div class="flex flex-col gap-[0.06rem] self-stretch">
                                        <p class="text-black font-jakarta text-[0.48rem]/[120%] font-semibold md:text-[0.8rem]">{{ $comments[0]['name'] }}</p>
                                        <p class="text-black font-jakarta text-[0.45rem]/[120%] font-normal md:text-[0.6rem]">{{ $comments[0]['username'] }}</p>
                                    </div>
                                    <div class="flex w-fit p-[0.2rem] items-center gap-[0.25rem] rounded-[0.6rem] bg-[#B84100]">
                                        <span class="material-icons-round text-[0.5rem] text-secondary-light">star</span>
                                        <p class="text-secondary-light text-[0.45rem] font-normal md:text-[0.55rem]">{{ $comments[0]['rating'] }}</>
                                    </div>
                                </div>
                                <p class="self-stretch text-black font-jakarta text-justify text-[0.45rem]/[120%] font-normal md:text-[0.7rem]">{{ $comments[0]['comment'] }}</p>
                            </div>
                        </a>
                        <a href="{{  route('profile.public', ['id' => $comments[1]['id'] ] ) }}" class=" rotate-[3deg] flex w-[15rem] md:w-[25rem] p-[0.62rem] gap-[0.62rem] rounded-[0.3rem] border-[1px] border-solid border-[#F7C9B0] bg-white">
                            <img src="{{ asset('assets/images/Image_DummyProfile.png') }}" alt="Profil Foto" class="w-[2.25rem] h-[2.25rem] aspect-square content-center items-center rounded-[3rem] border-[1px] border-solid border-[#EC4448]">
                            <div class="flex flex-col content-center gap-[0.2rem] w-full">
                                <div class="flex w-full items-center justify-between">
                                    <div class="flex flex-col gap-[0.06rem] self-stretch">
                                        <p class="text-black font-jakarta text-[0.48rem]/[120%] font-semibold md:text-[0.8rem]">{{ $comments[1]['name'] }}</p>
                                        <p class="text-black font-jakarta text-[0.45rem]/[120%] font-normal md:text-[0.7rem]">{{ $comments[1]['username'] }}</p>
                                    </div>
                                    <div class="flex w-fit p-[0.2rem] items-center gap-[0.25rem] rounded-[0.6rem] bg-[#B84100]">
                                        <span class="material-icons-round text-[0.5rem] text-secondary-light">star</span>
                                        <p class="text-secondary-light text-[0.45rem] font-normal md:text-[0.55rem]">{{ $comments[1]['rating'] }}</>
                                    </div>
                                </div>
                                <p class="self-stretch text-black font-jakarta text-justify text-[0.45rem]/[120%] font-normal md:text-[0.7rem]">{{ $comments[1]['comment'] }}</p>
                            </div>
                        </a>
                        <a href="{{  route('profile.public', ['id' => $comments[2]['id'] ] ) }}" class=" rotate-[-3deg] flex w-[15rem] md:w-[25rem] p-[0.62rem] gap-[0.62rem] rounded-[0.3rem] border-[1px] border-solid border-[#F7C9B0] bg-white">
                            <img src="{{ asset('assets/images/Image_DummyProfile.png') }}" alt="Profil Foto" class="w-[2.25rem] h-[2.25rem] aspect-square content-center items-center rounded-[3rem] border-[1px] border-solid border-[#EC4448]">
                            <div class="flex flex-col content-center gap-[0.2rem] w-full">
                                <div class="flex w-full items-center justify-between">
                                    <div class="flex flex-col gap-[0.06rem] self-stretch">
                                        <p class="text-black font-jakarta text-[0.48rem]/[120%] font-semibold md:text-[0.8rem]">{{ $comments[2]['name'] }}</p>
                                        <p class="text-black font-jakarta text-[0.45rem]/[120%] font-normal md:text-[0.6rem]">{{ $comments[2]['username'] }}</p>
                                    </div>
                                    <div class="flex w-fit p-[0.2rem] items-center gap-[0.25rem] rounded-[0.6rem] bg-[#B84100]">
                                        <span class="material-icons-round text-[0.5rem] text-secondary-light">star</span>
                                        <p class="text-secondary-light text-[0.45rem] font-normal md:text-[0.55rem]">{{ $comments[2]['rating'] }}</>
                                    </div>
                                </div>
                                <p class="self-stretch text-black font-jakarta text-justify text-[0.45rem]/[120%] font-normal md:text-[0.7rem]">{{ $comments[2]['comment'] }}</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="vertical-line !bg-orange-300 sm:hidden md:flex"></div>
        <!-- Pertanyaan Pengguna Lain -->
        <div id="faq-section" class="relative w-full h-fit">
            <x-svg-comp name="cheese-grater" class="absolute top-0 right-0 w-[41px] h-[66px]"></x-svg-comp>
            <x-svg-comp name="chili" class="absolute bottom-0 left-1.5 w-[34px] h-[64px]"></x-svg-comp>
            <div class="flex py-[0.5rem] px-[1.25rem] flex-col items-center gap-[0.8rem] self-stretch">
                <div class="flex flex-col justify-center items-center gap-[0.25rem]">
                    <h1 class="font-poppins text-[0.8rem]/[120%] text-orange-normal-active font-medium md:text-[2rem]">Pertanyaan Pengguna Lain</h1>
                    <p class="font-poppins w-[12rem] text-[0.5rem]/[120%] text-center text-orange-dark-active font-medium md:text-[1rem] md:w-[24rem]">Pertanyaan yang sering ditanyakan oleh pengguna lain saat menggunakan aplikasi</p>
                </div>
                <div class="flex flex-col gap-2 w-[20rem]">
                    <!-- FAQ CARD      -->
                    @foreach ($faqs as $faq )
                    <div id="faq-card" class="rounded-[0.2rem] border-l-solid border-l-[1px] border-l-[#B84100] py-[0.5rem] px-[0.9rem] flex justify-between gap-1 overflow-hidden bg-[#FEE7C3]">
                        <div class="flex w-full flex-col gap-1 justify-center">
                            <p class="text-[0.45rem] font-medium font-poppins text-black md:text-[0.8rem]">{{ $faq['q'] }}</p>
                            <p class="faq-answer text-[0.45rem] text-black font-light leading-relaxed hidden md:text-[0.65rem] md:font-normal">{{ $faq['a'] }}</p>
                        </div>
                        <button class="w-fit h-fit p-[0.2rem] aspect-square flex justify-center items-center bg-[#B84100] rounded-full">
                            <span class="faq-icon material-icons-round text-[0.8rem] text-white">keyboard_arrow_down</span>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <div id="footer-section" class="flex py-[0.75rem] gap-[1.25rem] flex-col items-center self-stretch bg-[#F4F5F7]">
        <div class="flex flex-col items-center gap-[0.2rem]">
            <img src="{{ asset('assets/images/Logo_Laperpoll.png') }}" alt="Logo Laperpoll" class="w-[4rem] md:w-[5rem]">
            <h1 class="italic text-[0.5rem]/[120%] text-secondary-normal font-jakarta font-medium md:text-[1rem]">"Laper Banget?" Nyari Resep ya Laperpoll aja</h1>
        </div>
        <div class="pt-[0.75rem] flex flex-col items-center gap-1">
            <p class="text-black text-center font-jakarta text-[0.5rem]/[120%] font-semibold md:text-[1rem]">Hubungi kami melalui</p>
            <div class="flex gap-3 items-center">
                <div class="flex gap-[0.3rem] items-center">
                    <span class="material-icons-round text-[0.8rem] text-black">phone</span>
                    <p class="text-black text-center font-jakarta text-[0.45rem]/[120%] font-normal md:text-[0.8rem]">62-899-0042</p>
                </div>
                <div class="flex gap-[0.3rem] items-center">
                    <i class="bi bi-instagram text-[0.8rem] w-fit"></i>
                    <p class="text-black text-center font-jakarta text-[0.45rem]/[120%] font-normal md:text-[0.8rem]">@laperpoll.id</p>
                </div>
            </div>
        </div>
        <p class="text-black text-[0.45rem]/[120%] font-jakarta text-center font-normal md:text-[0.8rem]">© 2025 LaperPoll | All Right Reserved</p>
    </div>
</div>

@endsection
@push('scripts')
<script>
    function updateBahanVisibility() {
        const cards = document.querySelectorAll('.card-bahan-pointer');
        const width = window.innerWidth;

        // Sesuaikan batas index per breakpoint
        let maxVisible;
        if (width < 768) {
            maxVisible = 8; // mobile: tampilkan 8
        } else if (width < 1280) {
            maxVisible = 12; // tablet: tampilkan 12
        } else {
            maxVisible = 22; // desktop: tampilkan semua
        }

        cards.forEach(card => {
            const index = parseInt(card.dataset.index);
            if (index >= maxVisible) {
                card.classList.add('hidden');
            } else {
                card.classList.remove('hidden');
            }
        });
    }

    // Jalankan saat load dan saat resize
    updateBahanVisibility();
    window.addEventListener('resize', updateBahanVisibility);
</script>
<script src="{{ asset('js/pages/landing-page.js') }}"></script>
@endpush