@extends('layouts.app')

@section('title', 'LaperPoll')

@push('styles')

@endpush

@push('links')
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush

@section('content')
<div class="flex flex-col gap-5 bg-[#FFF8F5]">
    <x-navbar></x-navbar>
    <div class="relative w-full">
        <x-svg-comp name="cup" class="absolute opacity-[0.55] rorate-[3deg]"></x-svg-comp>
        <x-svg-comp name="pan" class="absolute top-10 right-0 opacity-[0.55] rorate-[-3deg]"></x-svg-comp>
        <div class="flex flex-col gap-2 items-center">
            <div class="flex flex-col gap-1 items-center">
                <h1 class="font-poppins text-orange-normal-active font-medium text-[0.8rem]/[120%]">Akses Fitur Lainnya</h1>
                <p class="font-poppins font-medium text-[0.5rem]/[120%] text-orange-dark-active">Banyak hal lainnya yang bisa kamu gunakan di aplikasi ini</p>
            </div>
            <div class="flex flex-row gap-[0.6rem]">
                @foreach ( $features as $feature )
                @if($feature['locked'] && !$user)
                <a href="{{ route('auth.sign-in') }}">
                    <div class="flex flex-col gap-[0.5rem] justify-center items-center">
                        <div class="resep-logo-locked w-[2.8rem] h-[2.8rem] rounded-[0.5rem]]">
                            <span class="material-icons-round text-2 text-accent-dark">lock</span>
                        </div>
                        <p class="font-jakarta text-[0.4rem]/[120%] font-semibold text-accent-dark-active">{{ $feature['name'] }}</p>
                    </div>
                </a>
                @else
                <a href="{{ $feature['link'] }}">
                    <div class="flex flex-col gap-[0.5rem] justify-center items-center">
                        <div class="resep-logo w-[2.8rem] h-[2.8rem] rounded-[0.5rem]]">
                            <span class="material-icons-round text-2 text-accent-dark">{{ $feature['icon'] }}</span>
                        </div>
                        <p class="font-jakarta text-[0.4rem]/[120%] font-semibold text-accent-dark-active">{{ $feature['name'] }}</p>
                    </div>
                </a>
                @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Resep Favorit Pengguna -->
    <div class="flex flex-col items-start self-stretch pl-[1.5rem] pr-[0.75rem] gap-[0.4rem]">
        <div class="flex justify-between items-center self-stretch">
            <h1 class="font-poppins text-[0.7rem]/120% text-orange-normal-active font-medium">Resep Favorit Pengguna</h1>
            <!-- TODO: Ganti Routing  -->
            <a href="{{ route('main-menu.index', ['m' => 'favorit']) }}" class="px-[0.25rem] py-[0.4rem] rounded-[0.25rem] bg-orange-light-active text-accent-normal font-poppins text-[0.6rem]/[120%] font-medium">Lihat Semua</a>
        </div>
        <div class="flex flex-row items-center gap-[0.3rem] overflow-x-scroll w-full">
            @foreach ($reseps_favorit as $resep_f)
            <x-card-resep-landing :resep="$resep_f" :index="0"></x-card-resep-landing>
            @endforeach
        </div>
    </div>

    <!-- Resep Hari Ini -->
    <div class="flex flex-col items-start self-stretch pl-[1.5rem] pr-[0.75rem] gap-[0.4rem]">
        <div class="flex justify-between items-center self-stretch">
            <h1 class="font-poppins text-[0.7rem]/120% text-orange-normal-active font-medium">Resep Hari Ini</h1>
            <!-- TODO: Ganti Routing  -->
            <a href="{{ route('main-menu.index', ['m' => 'hari_ini']) }}" class="px-[0.25rem] py-[0.4rem] rounded-[0.25rem] bg-orange-light-active text-accent-normal font-poppins text-[0.6rem]/[120%] font-medium">Lihat Semua</a>
        </div>
        <div class="flex flex-row items-center gap-[0.3rem] overflow-x-scroll w-full">
            @foreach ($reseps_hari as $resep_h)
            <x-card-resep-landing :resep="$resep_h" :index="0"></x-card-resep-landing>
            @endforeach
        </div>
    </div>

    <!-- Rekomendasi Bahan -->
    <div class="relative w-full h-fit">
        <x-svg-comp name="wisk" class="absolute top-3 left-5 opacity-[0.7] rorate-[3deg]"></x-svg-comp>
        <x-svg-comp name="cup-2" class="absolute bottom-0 right-2 opacity-[0.7] rorate-[-3deg]"></x-svg-comp>
        <div class="py-[0.5rem] px-[1.25rem]">
            <div class="flex py-[0.75rem] flex-col justify-center items-center gap-[0.6rem] self-stretch rounded-[0.5rem] bg-[#FFF2ED]">
                <div class="flex flex-col justify-center items-center gap-[0.25rem]">
                    <h1 class="font-poppins text-[0.8rem]/[120%] text-orange-normal-active font-medium">Rekomendasi Bahan</h1>
                    <p class="font-poppins text-[0.5rem]/[120%] text-orange-dark-active font-medium">Pilih bahan yang mau digunakan kamu hari ini!</p>
                </div>
                <div class="flex justify-center items-center content-center gap-[0.3rem] self-stretch flex-wrap">
                    <!-- Card Bahan -->
                    @foreach ($bahans as $index=>$bahan)
                    <x-card-bahan-landing :bahan="$bahan" :index="$index"></x-card-bahan-landing>
                    @endforeach
                </div>
                <a href="{{ route('pencarian.resep') }}" class="px-[0.4rem] py-[0.4rem] rounded-[0.3rem] bg-orange-light-active text-accent-normal font-poppins text-[0.45rem]/[120%] font-medium">Lihat Semua</a>
            </div>
        </div>
    </div>

    <!-- Pendapat Pengguna Lain -->
    <div class="relative w-full h-fit">
        <x-svg-comp name="spatula" class="absolute top-24 left-12 rorate-[3deg]"></x-svg-comp>
        <x-svg-comp name="leaf" class="absolute bottom-0 right-12 z-10 rorate-[-3deg]"></x-svg-comp>
        <div class="flex py-[0.5rem] px-[1.25rem] flex-col items-center gap-[0.6rem] self-stretch">
            <div class="flex flex-col justify-center items-center gap-[0.25rem]">
                <h1 class="font-poppins text-[0.8rem]/[120%] text-orange-normal-active font-medium">Pendapat Pengguna Lain</h1>
                <p class="font-poppins w-[12rem] text-[0.5rem]/[120%] text-center text-orange-dark-active font-medium">Lihat apa yang pengguna lain katakan terhadap aplikasi kami</p>
            </div>
            <div class="relative w-fit">
                <x-svg-comp name="fork" class="absolute top-10 right-0 z-10 rorate-[3deg]"></x-svg-comp>
                <x-svg-comp name="spoon" class="absolute bottom-10 left-0 z-10 rorate-[3deg]"></x-svg-comp>
                <div class="flex flex-col gap-[0.3rem] w-full h-fit">
                    <div class="rotate-[-3deg] flex w-[15rem] p-[0.62rem] gap-[0.62rem] rounded-[0.3rem] border-[1px] border-solid border-[#F7C9B0] bg-white">
                        <img src="{{ asset('assets/images/Image_DummyProfile.png') }}" alt="Profil Foto" class="w-[2.25rem] h-[2.25rem] aspect-square content-center items-center rounded-[3rem] border-[1px] border-solid border-[#EC4448]">
                        <div class="flex flex-col content-center gap-[0.2rem] w-full">
                            <div class="flex w-full items-center justify-between">
                                <div class="flex flex-col gap-[0.06rem] self-stretch">
                                    <p class="text-black font-jakarta text-[0.48rem]/[120%] font-semibold">{{ $comments[0]['name'] }}</p>
                                    <p class="text-black font-jakarta text-[0.45rem]/[120%] font-normal">{{ $comments[0]['username'] }}</p>
                                </div>
                                <div class="flex w-fit p-[0.2rem] items-center gap-[0.25rem] rounded-[0.6rem] bg-[#B84100]">
                                    <span class="material-icons-round text-[0.5rem] text-secondary-light">star</span>
                                    <p class="text-secondary-light text-[0.45rem] font-normal">{{ $comments[0]['rating'] }}</>
                                </div>
                            </div>
                            <p class="self-stretch text-black font-jakarta text-justify text-[0.45rem]/[120%] font-normal">{{ $comments[0]['comment'] }}</p>
                        </div>
                    </div>
                    <div class=" rotate-[3deg] flex w-[15rem] p-[0.62rem] gap-[0.62rem] rounded-[0.3rem] border-[1px] border-solid border-[#F7C9B0] bg-white">
                        <img src="{{ asset('assets/images/Image_DummyProfile.png') }}" alt="Profil Foto" class="w-[2.25rem] h-[2.25rem] aspect-square content-center items-center rounded-[3rem] border-[1px] border-solid border-[#EC4448]">
                        <div class="flex flex-col content-center gap-[0.2rem] w-full">
                            <div class="flex w-full items-center justify-between">
                                <div class="flex flex-col gap-[0.06rem] self-stretch">
                                    <p class="text-black font-jakarta text-[0.48rem]/[120%] font-semibold">{{ $comments[1]['name'] }}</p>
                                    <p class="text-black font-jakarta text-[0.45rem]/[120%] font-normal">{{ $comments[1]['username'] }}</p>
                                </div>
                                <div class="flex w-fit p-[0.2rem] items-center gap-[0.25rem] rounded-[0.6rem] bg-[#B84100]">
                                    <span class="material-icons-round text-[0.5rem] text-secondary-light">star</span>
                                    <p class="text-secondary-light text-[0.45rem] font-normal">{{ $comments[1]['rating'] }}</>
                                </div>
                            </div>
                            <p class="self-stretch text-black font-jakarta text-justify text-[0.45rem]/[120%] font-normal">{{ $comments[1]['comment'] }}</p>
                        </div>
                    </div>
                    <div class=" rotate-[-3deg] flex w-[15rem] p-[0.62rem] gap-[0.62rem] rounded-[0.3rem] border-[1px] border-solid border-[#F7C9B0] bg-white">
                        <img src="{{ asset('assets/images/Image_DummyProfile.png') }}" alt="Profil Foto" class="w-[2.25rem] h-[2.25rem] aspect-square content-center items-center rounded-[3rem] border-[1px] border-solid border-[#EC4448]">
                        <div class="flex flex-col content-center gap-[0.2rem] w-full">
                            <div class="flex w-full items-center justify-between">
                                <div class="flex flex-col gap-[0.06rem] self-stretch">
                                    <p class="text-black font-jakarta text-[0.48rem]/[120%] font-semibold">{{ $comments[2]['name'] }}</p>
                                    <p class="text-black font-jakarta text-[0.45rem]/[120%] font-normal">{{ $comments[2]['username'] }}</p>
                                </div>
                                <div class="flex w-fit p-[0.2rem] items-center gap-[0.25rem] rounded-[0.6rem] bg-[#B84100]">
                                    <span class="material-icons-round text-[0.5rem] text-secondary-light">star</span>
                                    <p class="text-secondary-light text-[0.45rem] font-normal">{{ $comments[2]['rating'] }}</>
                                </div>
                            </div>
                            <p class="self-stretch text-black font-jakarta text-justify text-[0.45rem]/[120%] font-normal">{{ $comments[2]['comment'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pertanyaan Pengguna Lain -->
    <div class="relative w-full h-fit">
        <x-svg-comp name="cheese-grater" class="absolute top-0 right-0 rorate-[3deg]"></x-svg-comp>
        <x-svg-comp name="chili" class="absolute bottom-0 left-1.5 rorate-[-3deg]"></x-svg-comp>
        <div class="flex py-[0.5rem] px-[1.25rem] flex-col items-center gap-[0.8rem] self-stretch">
            <div class="flex flex-col justify-center items-center gap-[0.25rem]">
                <h1 class="font-poppins text-[0.8rem]/[120%] text-orange-normal-active font-medium">Pertanyaan Pengguna Lain</h1>
                <p class="font-poppins w-[12rem] text-[0.5rem]/[120%] text-center text-orange-dark-active font-medium">Pertanyaan yang sering ditanyakan oleh pengguna lain saat menggunakan aplikasi</p>
            </div>
            <div class="flex flex-col gap-2 w-[20rem]">
                <!-- FAQ CARD      -->
                @foreach ($faqs as $faq )
                <div id="faq-card" class="rounded-[0.2rem] border-l-solid border-l-[1px] border-l-[#B84100] py-[0.5rem] px-[0.9rem] flex justify-between gap-1 overflow-hidden bg-[#FEE7C3]">
                    <div class="flex w-full flex-col gap-1 justify-center">
                        <p class="text-[0.45rem] font-medium font-poppins text-black">{{ $faq['q'] }}</p>
                        <p class="faq-answer text-[0.45rem] text-black font-light leading-relaxed hidden">{{ $faq['a'] }}</p>
                    </div>
                    <button class="w-fit h-fit p-[0.2rem] aspect-square flex justify-center items-center bg-[#B84100] rounded-full">
                        <span class="faq-icon material-icons-round text-[0.8rem] text-white">keyboard_arrow_down</span>
                    </button>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="flex py-[0.75rem] gap-[1.25rem] flex-col items-center self-stretch bg-[#F4F5F7]">
        <div class="flex flex-col items-center gap-[0.2rem]">
            <img src="{{ asset('assets/images/Logo_Laperpoll.png') }}" alt="Logo Laperpoll" class="w-[4rem]">
            <h1 class="italic text-[0.5rem]/[120%] text-secondary-normal font-jakarta font-medium">"Laper Banget?" Nyari Resep ya Laperpoll aja</h1>
        </div>
        <div class="pt-[0.75rem] flex flex-col items-center gap-1">
            <p class="text-black text-center font-jakarta text-[0.5rem]/[120%] font-semibold">Hubungi kami melalui</p>
            <div class="flex gap-3 items-center">
                <div class="flex gap-[0.3rem] items-center">
                    <span class="material-icons-round text-[0.8rem] text-black">phone</span>
                    <p class="text-black text-center font-jakarta text-[0.45rem]/[120%] font-normal">62-899-0042</p>
                </div>
                <div class="flex gap-[0.3rem] items-center">
                    <i class="bi bi-instagram text-[0.8rem] w-fit"></i>
                    <p class="text-black text-center font-jakarta text-[0.45rem]/[120%] font-normal">62-899-0042</p>
                </div>
            </div>
        </div>
        <p class="text-black text-[0.45rem]/[120%] font-jakarta text-center font-normal">© 2025 LaperPoll | All Right Reserved</p>
    </div>
</div>

@endsection
@push('scripts')
<script src="{{ asset('js/pages/landing-page.js') }}"></script>
@endpush