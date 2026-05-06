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
                @php
                $features = [
                ['name' => 'Kulkas Digital', 'icon' => 'inventory_2', 'link' => ''],
                ['name' => 'Nota Belanja', 'icon' => 'shopping_cart', 'link' => ''],
                ['name' => 'Meal Planner', 'icon' => 'calendar_month', 'link' => ''],
                ['name' => 'Swiper Search', 'icon' => 'swipe', 'link' => ''],
                ];
                @endphp
                @foreach ( $features as $feature )
                <div class="flex flex-col gap-[0.5rem] justify-center items-center">
                    <div class="resep-logo w-[2.8rem] h-[2.8rem] rounded-[0.5rem]]">
                        <span class="material-icons-round text-2 text-accent-dark">{{ $feature['icon'] }}</span>
                    </div>
                    <p class="font-jakarta text-[0.4rem]/[120%] font-semibold text-accent-dark-active">{{ $feature['name'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Resep Favorit Pengguna -->
    <div class="flex flex-col items-start self-stretch pl-[1.5rem] pr-[0.75rem] gap-[0.4rem]">
        <div class="flex justify-between items-center self-stretch">
            <h1 class="font-poppins text-[0.7rem]/120% text-orange-normal-active font-medium">Resep Favorit Pengguna</h1>
            <button class="px-[0.25rem] py-[0.4rem] rounded-[0.25rem] bg-orange-light-active text-accent-normal font-poppins text-[0.6rem]/[120%] font-medium">Lihat Semua</button>
        </div>
        <div class="flex flex-row items-center gap-[0.3rem] overflow-x-scroll w-full">
            <div class="p-[0.5rem] gap-[0.5rem] min-w-[7rem] max-w-[7rem] items-center flex-col flex rounded-[0.5rem] border-[0.67px] border-solid border-[#F2E2D9] bg-white">
                <img src="https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=200&h=160&fit=crop" alt="" class="w-full aspect-square rounded-[0.5rem]">
                <div class="flex flex-col gap-[0.1rem] items-start">
                    <h2 class="text-black overflow-ellipsis font-jakarta text-[0.5rem]/[120%] font-semibold">Spageti</h2>
                    <div class="flex gap-[0.18rem]">
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] font-light text-black">watch_later</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">25 mins</>
                        </div>
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] text-black">menu_book</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">Bahan Tersedia</>
                        </div>
                    </div>
                    <div class="flex gap-[0.125rem]">
                        <p class="text-black text-[0.4rem]/[120%] font-medium font-jakarta">@foodnice</p>
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" width="6" height="6" viewBox="0 0 6 6" fill="none">
                                <path d="M3 0L3.39891 0.481395L3.92705 0.146831L4.15768 0.727933L4.76336 0.572949L4.80312 1.19688L5.42705 1.23664L5.27207 1.84232L5.85317 2.07295L5.51861 2.60109L6 3L5.51861 3.39891L5.85317 3.92705L5.27207 4.15768L5.42705 4.76336L4.80312 4.80312L4.76336 5.42705L4.15768 5.27207L3.92705 5.85317L3.39891 5.51861L3 6L2.60109 5.51861L2.07295 5.85317L1.84232 5.27207L1.23664 5.42705L1.19688 4.80312L0.572949 4.76336L0.727933 4.15768L0.146831 3.92705L0.481395 3.39891L0 3L0.481395 2.60109L0.146831 2.07295L0.727933 1.84232L0.572949 1.23664L1.19688 1.19688L1.23664 0.572949L1.84232 0.727933L2.07295 0.146831L2.60109 0.481395L3 0Z" fill="#0186FF" />
                            </svg>
                            <span class="material-icons-round text-[0.125rem] text-white absolute top-1/2 left-1/2 right-1/2 bottom-1/2">check</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-[0.5rem] gap-[0.5rem] min-w-[7rem] max-w-[7rem] items-center flex-col flex rounded-[0.5rem] border-[0.67px] border-solid border-[#F2E2D9] bg-white">
                <img src="https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=200&h=160&fit=crop" alt="" class="w-full aspect-square rounded-[0.5rem]">
                <div class="flex flex-col gap-[0.1rem] items-start">
                    <h2 class="text-black overflow-ellipsis font-jakarta text-[0.5rem]/[120%] font-semibold">Spageti</h2>
                    <div class="flex gap-[0.18rem]">
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] font-light text-black">watch_later</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">25 mins</>
                        </div>
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] text-black">menu_book</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">Bahan Tersedia</>
                        </div>
                    </div>
                    <div class="flex gap-[0.125rem]">
                        <p class="text-black text-[0.4rem]/[120%] font-medium font-jakarta">@foodnice</p>
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" width="6" height="6" viewBox="0 0 6 6" fill="none">
                                <path d="M3 0L3.39891 0.481395L3.92705 0.146831L4.15768 0.727933L4.76336 0.572949L4.80312 1.19688L5.42705 1.23664L5.27207 1.84232L5.85317 2.07295L5.51861 2.60109L6 3L5.51861 3.39891L5.85317 3.92705L5.27207 4.15768L5.42705 4.76336L4.80312 4.80312L4.76336 5.42705L4.15768 5.27207L3.92705 5.85317L3.39891 5.51861L3 6L2.60109 5.51861L2.07295 5.85317L1.84232 5.27207L1.23664 5.42705L1.19688 4.80312L0.572949 4.76336L0.727933 4.15768L0.146831 3.92705L0.481395 3.39891L0 3L0.481395 2.60109L0.146831 2.07295L0.727933 1.84232L0.572949 1.23664L1.19688 1.19688L1.23664 0.572949L1.84232 0.727933L2.07295 0.146831L2.60109 0.481395L3 0Z" fill="#0186FF" />
                            </svg>
                            <span class="material-icons-round text-[0.125rem] text-white absolute top-1/2 left-1/2 right-1/2 bottom-1/2">check</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-[0.5rem] gap-[0.5rem] min-w-[7rem] max-w-[7rem] items-center flex-col flex rounded-[0.5rem] border-[0.67px] border-solid border-[#F2E2D9] bg-white">
                <img src="https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=200&h=160&fit=crop" alt="" class="w-full aspect-square rounded-[0.5rem]">
                <div class="flex flex-col gap-[0.1rem] items-start">
                    <h2 class="text-black overflow-ellipsis font-jakarta text-[0.5rem]/[120%] font-semibold">Spageti</h2>
                    <div class="flex gap-[0.18rem]">
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] font-light text-black">watch_later</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">25 mins</>
                        </div>
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] text-black">menu_book</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">Bahan Tersedia</>
                        </div>
                    </div>
                    <div class="flex gap-[0.125rem]">
                        <p class="text-black text-[0.4rem]/[120%] font-medium font-jakarta">@foodnice</p>
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" width="6" height="6" viewBox="0 0 6 6" fill="none">
                                <path d="M3 0L3.39891 0.481395L3.92705 0.146831L4.15768 0.727933L4.76336 0.572949L4.80312 1.19688L5.42705 1.23664L5.27207 1.84232L5.85317 2.07295L5.51861 2.60109L6 3L5.51861 3.39891L5.85317 3.92705L5.27207 4.15768L5.42705 4.76336L4.80312 4.80312L4.76336 5.42705L4.15768 5.27207L3.92705 5.85317L3.39891 5.51861L3 6L2.60109 5.51861L2.07295 5.85317L1.84232 5.27207L1.23664 5.42705L1.19688 4.80312L0.572949 4.76336L0.727933 4.15768L0.146831 3.92705L0.481395 3.39891L0 3L0.481395 2.60109L0.146831 2.07295L0.727933 1.84232L0.572949 1.23664L1.19688 1.19688L1.23664 0.572949L1.84232 0.727933L2.07295 0.146831L2.60109 0.481395L3 0Z" fill="#0186FF" />
                            </svg>
                            <span class="material-icons-round text-[0.125rem] text-white absolute top-1/2 left-1/2 right-1/2 bottom-1/2">check</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-[0.5rem] gap-[0.5rem] min-w-[7rem] max-w-[7rem] items-center flex-col flex rounded-[0.5rem] border-[0.67px] border-solid border-[#F2E2D9] bg-white">
                <img src="https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=200&h=160&fit=crop" alt="" class="w-full aspect-square rounded-[0.5rem]">
                <div class="flex flex-col gap-[0.1rem] items-start">
                    <h2 class="text-black overflow-ellipsis font-jakarta text-[0.5rem]/[120%] font-semibold">Spageti</h2>
                    <div class="flex gap-[0.18rem]">
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] font-light text-black">watch_later</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">25 mins</>
                        </div>
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] text-black">menu_book</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">Bahan Tersedia</>
                        </div>
                    </div>
                    <div class="flex gap-[0.125rem]">
                        <p class="text-black text-[0.4rem]/[120%] font-medium font-jakarta">@foodnice</p>
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" width="6" height="6" viewBox="0 0 6 6" fill="none">
                                <path d="M3 0L3.39891 0.481395L3.92705 0.146831L4.15768 0.727933L4.76336 0.572949L4.80312 1.19688L5.42705 1.23664L5.27207 1.84232L5.85317 2.07295L5.51861 2.60109L6 3L5.51861 3.39891L5.85317 3.92705L5.27207 4.15768L5.42705 4.76336L4.80312 4.80312L4.76336 5.42705L4.15768 5.27207L3.92705 5.85317L3.39891 5.51861L3 6L2.60109 5.51861L2.07295 5.85317L1.84232 5.27207L1.23664 5.42705L1.19688 4.80312L0.572949 4.76336L0.727933 4.15768L0.146831 3.92705L0.481395 3.39891L0 3L0.481395 2.60109L0.146831 2.07295L0.727933 1.84232L0.572949 1.23664L1.19688 1.19688L1.23664 0.572949L1.84232 0.727933L2.07295 0.146831L2.60109 0.481395L3 0Z" fill="#0186FF" />
                            </svg>
                            <span class="material-icons-round text-[0.125rem] text-white absolute top-1/2 left-1/2 right-1/2 bottom-1/2">check</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-[0.5rem] gap-[0.5rem] min-w-[7rem] max-w-[7rem] items-center flex-col flex rounded-[0.5rem] border-[0.67px] border-solid border-[#F2E2D9] bg-white">
                <img src="https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=200&h=160&fit=crop" alt="" class="w-full aspect-square rounded-[0.5rem]">
                <div class="flex flex-col gap-[0.1rem] items-start">
                    <h2 class="text-black overflow-ellipsis font-jakarta text-[0.5rem]/[120%] font-semibold">Spageti</h2>
                    <div class="flex gap-[0.18rem]">
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] font-light text-black">watch_later</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">25 mins</>
                        </div>
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] text-black">menu_book</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">Bahan Tersedia</>
                        </div>
                    </div>
                    <div class="flex gap-[0.125rem]">
                        <p class="text-black text-[0.4rem]/[120%] font-medium font-jakarta">@foodnice</p>
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" width="6" height="6" viewBox="0 0 6 6" fill="none">
                                <path d="M3 0L3.39891 0.481395L3.92705 0.146831L4.15768 0.727933L4.76336 0.572949L4.80312 1.19688L5.42705 1.23664L5.27207 1.84232L5.85317 2.07295L5.51861 2.60109L6 3L5.51861 3.39891L5.85317 3.92705L5.27207 4.15768L5.42705 4.76336L4.80312 4.80312L4.76336 5.42705L4.15768 5.27207L3.92705 5.85317L3.39891 5.51861L3 6L2.60109 5.51861L2.07295 5.85317L1.84232 5.27207L1.23664 5.42705L1.19688 4.80312L0.572949 4.76336L0.727933 4.15768L0.146831 3.92705L0.481395 3.39891L0 3L0.481395 2.60109L0.146831 2.07295L0.727933 1.84232L0.572949 1.23664L1.19688 1.19688L1.23664 0.572949L1.84232 0.727933L2.07295 0.146831L2.60109 0.481395L3 0Z" fill="#0186FF" />
                            </svg>
                            <span class="material-icons-round text-[0.125rem] text-white absolute top-1/2 left-1/2 right-1/2 bottom-1/2">check</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-[0.5rem] gap-[0.5rem] min-w-[7rem] max-w-[7rem] items-center flex-col flex rounded-[0.5rem] border-[0.67px] border-solid border-[#F2E2D9] bg-white">
                <img src="https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=200&h=160&fit=crop" alt="" class="w-full aspect-square rounded-[0.5rem]">
                <div class="flex flex-col gap-[0.1rem] items-start">
                    <h2 class="text-black overflow-ellipsis font-jakarta text-[0.5rem]/[120%] font-semibold">Spageti</h2>
                    <div class="flex gap-[0.18rem]">
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] font-light text-black">watch_later</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">25 mins</>
                        </div>
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] text-black">menu_book</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">Bahan Tersedia</>
                        </div>
                    </div>
                    <div class="flex gap-[0.125rem]">
                        <p class="text-black text-[0.4rem]/[120%] font-medium font-jakarta">@foodnice</p>
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" width="6" height="6" viewBox="0 0 6 6" fill="none">
                                <path d="M3 0L3.39891 0.481395L3.92705 0.146831L4.15768 0.727933L4.76336 0.572949L4.80312 1.19688L5.42705 1.23664L5.27207 1.84232L5.85317 2.07295L5.51861 2.60109L6 3L5.51861 3.39891L5.85317 3.92705L5.27207 4.15768L5.42705 4.76336L4.80312 4.80312L4.76336 5.42705L4.15768 5.27207L3.92705 5.85317L3.39891 5.51861L3 6L2.60109 5.51861L2.07295 5.85317L1.84232 5.27207L1.23664 5.42705L1.19688 4.80312L0.572949 4.76336L0.727933 4.15768L0.146831 3.92705L0.481395 3.39891L0 3L0.481395 2.60109L0.146831 2.07295L0.727933 1.84232L0.572949 1.23664L1.19688 1.19688L1.23664 0.572949L1.84232 0.727933L2.07295 0.146831L2.60109 0.481395L3 0Z" fill="#0186FF" />
                            </svg>
                            <span class="material-icons-round text-[0.125rem] text-white absolute top-1/2 left-1/2 right-1/2 bottom-1/2">check</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resep Hari Ini -->
    <div class="flex flex-col items-start self-stretch pl-[1.5rem] pr-[0.75rem] gap-[0.4rem]">
        <div class="flex justify-between items-center self-stretch">
            <h1 class="font-poppins text-[0.7rem]/120% text-orange-normal-active font-medium">Resep Hari Ini</h1>
            <button class="px-[0.25rem] py-[0.4rem] rounded-[0.25rem] bg-orange-light-active text-accent-normal font-poppins text-[0.6rem]/[120%] font-medium">Lihat Semua</button>
        </div>
        <div class="flex flex-row items-center gap-[0.3rem] overflow-x-scroll w-full">
            <div class="p-[0.5rem] gap-[0.5rem] min-w-[7rem] max-w-[7rem] items-center flex-col flex rounded-[0.5rem] border-[0.67px] border-solid border-[#F2E2D9] bg-white">
                <img src="https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=200&h=160&fit=crop" alt="" class="w-full aspect-square rounded-[0.5rem]">
                <div class="flex flex-col gap-[0.1rem] items-start">
                    <h2 class="text-black overflow-ellipsis font-jakarta text-[0.5rem]/[120%] font-semibold">Spageti</h2>
                    <div class="flex gap-[0.18rem]">
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] font-light text-black">watch_later</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">25 mins</>
                        </div>
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] text-black">menu_book</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">Bahan Tersedia</>
                        </div>
                    </div>
                    <div class="flex gap-[0.125rem]">
                        <p class="text-black text-[0.4rem]/[120%] font-medium font-jakarta">@foodnice</p>
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" width="6" height="6" viewBox="0 0 6 6" fill="none">
                                <path d="M3 0L3.39891 0.481395L3.92705 0.146831L4.15768 0.727933L4.76336 0.572949L4.80312 1.19688L5.42705 1.23664L5.27207 1.84232L5.85317 2.07295L5.51861 2.60109L6 3L5.51861 3.39891L5.85317 3.92705L5.27207 4.15768L5.42705 4.76336L4.80312 4.80312L4.76336 5.42705L4.15768 5.27207L3.92705 5.85317L3.39891 5.51861L3 6L2.60109 5.51861L2.07295 5.85317L1.84232 5.27207L1.23664 5.42705L1.19688 4.80312L0.572949 4.76336L0.727933 4.15768L0.146831 3.92705L0.481395 3.39891L0 3L0.481395 2.60109L0.146831 2.07295L0.727933 1.84232L0.572949 1.23664L1.19688 1.19688L1.23664 0.572949L1.84232 0.727933L2.07295 0.146831L2.60109 0.481395L3 0Z" fill="#0186FF" />
                            </svg>
                            <span class="material-icons-round text-[0.125rem] text-white absolute top-1/2 left-1/2 right-1/2 bottom-1/2">check</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-[0.5rem] gap-[0.5rem] min-w-[7rem] max-w-[7rem] items-center flex-col flex rounded-[0.5rem] border-[0.67px] border-solid border-[#F2E2D9] bg-white">
                <img src="https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=200&h=160&fit=crop" alt="" class="w-full aspect-square rounded-[0.5rem]">
                <div class="flex flex-col gap-[0.1rem] items-start">
                    <h2 class="text-black overflow-ellipsis font-jakarta text-[0.5rem]/[120%] font-semibold">Spageti</h2>
                    <div class="flex gap-[0.18rem]">
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] font-light text-black">watch_later</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">25 mins</>
                        </div>
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] text-black">menu_book</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">Bahan Tersedia</>
                        </div>
                    </div>
                    <div class="flex gap-[0.125rem]">
                        <p class="text-black text-[0.4rem]/[120%] font-medium font-jakarta">@foodnice</p>
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" width="6" height="6" viewBox="0 0 6 6" fill="none">
                                <path d="M3 0L3.39891 0.481395L3.92705 0.146831L4.15768 0.727933L4.76336 0.572949L4.80312 1.19688L5.42705 1.23664L5.27207 1.84232L5.85317 2.07295L5.51861 2.60109L6 3L5.51861 3.39891L5.85317 3.92705L5.27207 4.15768L5.42705 4.76336L4.80312 4.80312L4.76336 5.42705L4.15768 5.27207L3.92705 5.85317L3.39891 5.51861L3 6L2.60109 5.51861L2.07295 5.85317L1.84232 5.27207L1.23664 5.42705L1.19688 4.80312L0.572949 4.76336L0.727933 4.15768L0.146831 3.92705L0.481395 3.39891L0 3L0.481395 2.60109L0.146831 2.07295L0.727933 1.84232L0.572949 1.23664L1.19688 1.19688L1.23664 0.572949L1.84232 0.727933L2.07295 0.146831L2.60109 0.481395L3 0Z" fill="#0186FF" />
                            </svg>
                            <span class="material-icons-round text-[0.125rem] text-white absolute top-1/2 left-1/2 right-1/2 bottom-1/2">check</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-[0.5rem] gap-[0.5rem] min-w-[7rem] max-w-[7rem] items-center flex-col flex rounded-[0.5rem] border-[0.67px] border-solid border-[#F2E2D9] bg-white">
                <img src="https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=200&h=160&fit=crop" alt="" class="w-full aspect-square rounded-[0.5rem]">
                <div class="flex flex-col gap-[0.1rem] items-start">
                    <h2 class="text-black overflow-ellipsis font-jakarta text-[0.5rem]/[120%] font-semibold">Spageti</h2>
                    <div class="flex gap-[0.18rem]">
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] font-light text-black">watch_later</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">25 mins</>
                        </div>
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] text-black">menu_book</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">Bahan Tersedia</>
                        </div>
                    </div>
                    <div class="flex gap-[0.125rem]">
                        <p class="text-black text-[0.4rem]/[120%] font-medium font-jakarta">@foodnice</p>
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" width="6" height="6" viewBox="0 0 6 6" fill="none">
                                <path d="M3 0L3.39891 0.481395L3.92705 0.146831L4.15768 0.727933L4.76336 0.572949L4.80312 1.19688L5.42705 1.23664L5.27207 1.84232L5.85317 2.07295L5.51861 2.60109L6 3L5.51861 3.39891L5.85317 3.92705L5.27207 4.15768L5.42705 4.76336L4.80312 4.80312L4.76336 5.42705L4.15768 5.27207L3.92705 5.85317L3.39891 5.51861L3 6L2.60109 5.51861L2.07295 5.85317L1.84232 5.27207L1.23664 5.42705L1.19688 4.80312L0.572949 4.76336L0.727933 4.15768L0.146831 3.92705L0.481395 3.39891L0 3L0.481395 2.60109L0.146831 2.07295L0.727933 1.84232L0.572949 1.23664L1.19688 1.19688L1.23664 0.572949L1.84232 0.727933L2.07295 0.146831L2.60109 0.481395L3 0Z" fill="#0186FF" />
                            </svg>
                            <span class="material-icons-round text-[0.125rem] text-white absolute top-1/2 left-1/2 right-1/2 bottom-1/2">check</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-[0.5rem] gap-[0.5rem] min-w-[7rem] max-w-[7rem] items-center flex-col flex rounded-[0.5rem] border-[0.67px] border-solid border-[#F2E2D9] bg-white">
                <img src="https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=200&h=160&fit=crop" alt="" class="w-full aspect-square rounded-[0.5rem]">
                <div class="flex flex-col gap-[0.1rem] items-start">
                    <h2 class="text-black overflow-ellipsis font-jakarta text-[0.5rem]/[120%] font-semibold">Spageti</h2>
                    <div class="flex gap-[0.18rem]">
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] font-light text-black">watch_later</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">25 mins</>
                        </div>
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] text-black">menu_book</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">Bahan Tersedia</>
                        </div>
                    </div>
                    <div class="flex gap-[0.125rem]">
                        <p class="text-black text-[0.4rem]/[120%] font-medium font-jakarta">@foodnice</p>
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" width="6" height="6" viewBox="0 0 6 6" fill="none">
                                <path d="M3 0L3.39891 0.481395L3.92705 0.146831L4.15768 0.727933L4.76336 0.572949L4.80312 1.19688L5.42705 1.23664L5.27207 1.84232L5.85317 2.07295L5.51861 2.60109L6 3L5.51861 3.39891L5.85317 3.92705L5.27207 4.15768L5.42705 4.76336L4.80312 4.80312L4.76336 5.42705L4.15768 5.27207L3.92705 5.85317L3.39891 5.51861L3 6L2.60109 5.51861L2.07295 5.85317L1.84232 5.27207L1.23664 5.42705L1.19688 4.80312L0.572949 4.76336L0.727933 4.15768L0.146831 3.92705L0.481395 3.39891L0 3L0.481395 2.60109L0.146831 2.07295L0.727933 1.84232L0.572949 1.23664L1.19688 1.19688L1.23664 0.572949L1.84232 0.727933L2.07295 0.146831L2.60109 0.481395L3 0Z" fill="#0186FF" />
                            </svg>
                            <span class="material-icons-round text-[0.125rem] text-white absolute top-1/2 left-1/2 right-1/2 bottom-1/2">check</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-[0.5rem] gap-[0.5rem] min-w-[7rem] max-w-[7rem] items-center flex-col flex rounded-[0.5rem] border-[0.67px] border-solid border-[#F2E2D9] bg-white">
                <img src="https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=200&h=160&fit=crop" alt="" class="w-full aspect-square rounded-[0.5rem]">
                <div class="flex flex-col gap-[0.1rem] items-start">
                    <h2 class="text-black overflow-ellipsis font-jakarta text-[0.5rem]/[120%] font-semibold">Spageti</h2>
                    <div class="flex gap-[0.18rem]">
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] font-light text-black">watch_later</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">25 mins</>
                        </div>
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] text-black">menu_book</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">Bahan Tersedia</>
                        </div>
                    </div>
                    <div class="flex gap-[0.125rem]">
                        <p class="text-black text-[0.4rem]/[120%] font-medium font-jakarta">@foodnice</p>
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" width="6" height="6" viewBox="0 0 6 6" fill="none">
                                <path d="M3 0L3.39891 0.481395L3.92705 0.146831L4.15768 0.727933L4.76336 0.572949L4.80312 1.19688L5.42705 1.23664L5.27207 1.84232L5.85317 2.07295L5.51861 2.60109L6 3L5.51861 3.39891L5.85317 3.92705L5.27207 4.15768L5.42705 4.76336L4.80312 4.80312L4.76336 5.42705L4.15768 5.27207L3.92705 5.85317L3.39891 5.51861L3 6L2.60109 5.51861L2.07295 5.85317L1.84232 5.27207L1.23664 5.42705L1.19688 4.80312L0.572949 4.76336L0.727933 4.15768L0.146831 3.92705L0.481395 3.39891L0 3L0.481395 2.60109L0.146831 2.07295L0.727933 1.84232L0.572949 1.23664L1.19688 1.19688L1.23664 0.572949L1.84232 0.727933L2.07295 0.146831L2.60109 0.481395L3 0Z" fill="#0186FF" />
                            </svg>
                            <span class="material-icons-round text-[0.125rem] text-white absolute top-1/2 left-1/2 right-1/2 bottom-1/2">check</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-[0.5rem] gap-[0.5rem] min-w-[7rem] max-w-[7rem] items-center flex-col flex rounded-[0.5rem] border-[0.67px] border-solid border-[#F2E2D9] bg-white">
                <img src="https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=200&h=160&fit=crop" alt="" class="w-full aspect-square rounded-[0.5rem]">
                <div class="flex flex-col gap-[0.1rem] items-start">
                    <h2 class="text-black overflow-ellipsis font-jakarta text-[0.5rem]/[120%] font-semibold">Spageti</h2>
                    <div class="flex gap-[0.18rem]">
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] font-light text-black">watch_later</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">25 mins</>
                        </div>
                        <div class="flex gap-[0.1rem]">
                            <span class="material-icons-round text-[0.45rem] text-black">menu_book</span>
                            <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">Bahan Tersedia</>
                        </div>
                    </div>
                    <div class="flex gap-[0.125rem]">
                        <p class="text-black text-[0.4rem]/[120%] font-medium font-jakarta">@foodnice</p>
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" width="6" height="6" viewBox="0 0 6 6" fill="none">
                                <path d="M3 0L3.39891 0.481395L3.92705 0.146831L4.15768 0.727933L4.76336 0.572949L4.80312 1.19688L5.42705 1.23664L5.27207 1.84232L5.85317 2.07295L5.51861 2.60109L6 3L5.51861 3.39891L5.85317 3.92705L5.27207 4.15768L5.42705 4.76336L4.80312 4.80312L4.76336 5.42705L4.15768 5.27207L3.92705 5.85317L3.39891 5.51861L3 6L2.60109 5.51861L2.07295 5.85317L1.84232 5.27207L1.23664 5.42705L1.19688 4.80312L0.572949 4.76336L0.727933 4.15768L0.146831 3.92705L0.481395 3.39891L0 3L0.481395 2.60109L0.146831 2.07295L0.727933 1.84232L0.572949 1.23664L1.19688 1.19688L1.23664 0.572949L1.84232 0.727933L2.07295 0.146831L2.60109 0.481395L3 0Z" fill="#0186FF" />
                            </svg>
                            <span class="material-icons-round text-[0.125rem] text-white absolute top-1/2 left-1/2 right-1/2 bottom-1/2">check</span>
                        </div>
                    </div>
                </div>
            </div>
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
                    <div class="flex flex-col gap-[0.25rem] justify-center min-h-[4.5rem] max-h-[4.5rem] py-[0.4rem] px-[0.5rem] items-center rounded-[0.5rem] border-solid border-[0.67px] border-[#F7C9B0] bg-primary-light">
                        <div class="resep-logo w-[2.8rem] h-[2.8rem] rounded-[0.4rem]">
                            <span class="material-icons-round text-2 text-accent-dark">lunch_dining</span>
                        </div>
                        <p class="text-center self-stretch font-jakarta text-[0.45rem]/[120%] font-normal">Tomat</p>
                    </div>
                    <div class="flex flex-col gap-[0.25rem] justify-center min-h-[4.5rem] max-h-[4.5rem] py-[0.4rem] px-[0.5rem] items-center rounded-[0.5rem] border-solid border-[0.67px] border-[#F7C9B0] bg-primary-light">
                        <div class="resep-logo w-[2.8rem] h-[2.8rem] rounded-[0.4rem]">
                            <span class="material-icons-round text-2 text-accent-dark">lunch_dining</span>
                        </div>
                        <p class="text-center self-stretch font-jakarta text-[0.45rem]/[120%] font-normal">Tomat</p>
                    </div>
                    <div class="flex flex-col gap-[0.25rem] justify-center min-h-[4.5rem] max-h-[4.5rem] py-[0.4rem] px-[0.5rem] items-center rounded-[0.5rem] border-solid border-[0.67px] border-[#F7C9B0] bg-primary-light">
                        <div class="resep-logo w-[2.8rem] h-[2.8rem] rounded-[0.4rem]">
                            <span class="material-icons-round text-2 text-accent-dark">lunch_dining</span>
                        </div>
                        <p class="text-center self-stretch font-jakarta text-[0.45rem]/[120%] font-normal">Tomat</p>
                    </div>
                    <div class="flex flex-col gap-[0.25rem] justify-center min-h-[4.5rem] max-h-[4.5rem] py-[0.4rem] px-[0.5rem] items-center rounded-[0.5rem] border-solid border-[0.67px] border-[#F7C9B0] bg-primary-light">
                        <div class="resep-logo w-[2.8rem] h-[2.8rem] rounded-[0.4rem]">
                            <span class="material-icons-round text-2 text-accent-dark">lunch_dining</span>
                        </div>
                        <p class="text-center self-stretch font-jakarta text-[0.45rem]/[120%] font-normal">Tomat</p>
                    </div>
                    <div class="flex flex-col gap-[0.25rem] justify-center min-h-[4.5rem] max-h-[4.5rem] py-[0.4rem] px-[0.5rem] items-center rounded-[0.5rem] border-solid border-[0.67px] border-[#F7C9B0] bg-primary-light">
                        <div class="resep-logo w-[2.8rem] h-[2.8rem] rounded-[0.4rem]">
                            <span class="material-icons-round text-2 text-accent-dark">lunch_dining</span>
                        </div>
                        <p class="text-center self-stretch font-jakarta text-[0.45rem]/[120%] font-normal">Tomat</p>
                    </div>
                    <div class="flex flex-col gap-[0.25rem] justify-center min-h-[4.5rem] max-h-[4.5rem] py-[0.4rem] px-[0.5rem] items-center rounded-[0.5rem] border-solid border-[0.67px] border-[#F7C9B0] bg-primary-light">
                        <div class="resep-logo w-[2.8rem] h-[2.8rem] rounded-[0.4rem]">
                            <span class="material-icons-round text-2 text-accent-dark">lunch_dining</span>
                        </div>
                        <p class="text-center self-stretch font-jakarta text-[0.45rem]/[120%] font-normal">Tomat</p>
                    </div>
                    <div class="flex flex-col gap-[0.25rem] justify-center min-h-[4.5rem] max-h-[4.5rem] py-[0.4rem] px-[0.5rem] items-center rounded-[0.5rem] border-solid border-[0.67px] border-[#F7C9B0] bg-primary-light">
                        <div class="resep-logo w-[2.8rem] h-[2.8rem] rounded-[0.4rem]">
                            <span class="material-icons-round text-2 text-accent-dark">lunch_dining</span>
                        </div>
                        <p class="text-center self-stretch font-jakarta text-[0.45rem]/[120%] font-normal">Tomat</p>
                    </div>
                    <div class="flex flex-col gap-[0.25rem] justify-center min-h-[4.5rem] max-h-[4.5rem] py-[0.4rem] px-[0.5rem] items-center rounded-[0.5rem] border-solid border-[0.67px] border-[#F7C9B0] bg-primary-light">
                        <div class="resep-logo w-[2.8rem] h-[2.8rem] rounded-[0.4rem]">
                            <span class="material-icons-round text-2 text-accent-dark">lunch_dining</span>
                        </div>
                        <p class="text-center self-stretch font-jakarta text-[0.45rem]/[120%] font-normal">Tomat</p>
                    </div>
                    <div class="flex flex-col gap-[0.25rem] justify-center min-h-[4.5rem] max-h-[4.5rem] py-[0.4rem] px-[0.5rem] items-center rounded-[0.5rem] border-solid border-[0.67px] border-[#F7C9B0] bg-primary-light">
                        <div class="resep-logo w-[2.8rem] h-[2.8rem] rounded-[0.4rem]">
                            <span class="material-icons-round text-2 text-accent-dark">lunch_dining</span>
                        </div>
                        <p class="text-center self-stretch font-jakarta text-[0.45rem]/[120%] font-normal">Tomat</p>
                    </div>
                    <div class="flex flex-col gap-[0.25rem] justify-center min-h-[4.5rem] max-h-[4.5rem] py-[0.4rem] px-[0.5rem] items-center rounded-[0.5rem] border-solid border-[0.67px] border-[#F7C9B0] bg-primary-light">
                        <div class="resep-logo w-[2.8rem] h-[2.8rem] rounded-[0.4rem]">
                            <span class="material-icons-round text-2 text-accent-dark">lunch_dining</span>
                        </div>
                        <p class="text-center self-stretch font-jakarta text-[0.45rem]/[120%] font-normal">Tomat</p>
                    </div>
                </div>
                <button class="px-[0.4rem] py-[0.4rem] rounded-[0.3rem] bg-orange-light-active text-accent-normal font-poppins text-[0.45rem]/[120%] font-medium">Lihat Semua</button>
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
                                    <p class="text-black font-jakarta text-[0.48rem]/[120%] font-semibold">Bambang Tri Hartanto</p>
                                    <p class="text-black font-jakarta text-[0.45rem]/[120%] font-normal">@bang_tri</p>
                                </div>
                                <div class="flex w-fit p-[0.2rem] items-center gap-[0.25rem] rounded-[0.6rem] bg-[#B84100]">
                                    <span class="material-icons-round text-[0.5rem] text-secondary-light">star</span>
                                    <p class="text-secondary-light text-[0.45rem] font-normal">4.3/5</>
                                </div>
                            </div>
                            <p class="self-stretch text-black font-jakarta text-justify text-[0.45rem]/[120%] font-normal">Sangat membantu dalam mencari resep masakan. Tampilan menarik dan mudah digunakan.</p>
                        </div>
                    </div>
                    <div class=" rotate-[3deg] flex w-[15rem] p-[0.62rem] gap-[0.62rem] rounded-[0.3rem] border-[1px] border-solid border-[#F7C9B0] bg-white">
                        <img src="{{ asset('assets/images/Image_DummyProfile.png') }}" alt="Profil Foto" class="w-[2.25rem] h-[2.25rem] aspect-square content-center items-center rounded-[3rem] border-[1px] border-solid border-[#EC4448]">
                        <div class="flex flex-col content-center gap-[0.2rem] w-full">
                            <div class="flex w-full items-center justify-between">
                                <div class="flex flex-col gap-[0.06rem] self-stretch">
                                    <p class="text-black font-jakarta text-[0.48rem]/[120%] font-semibold">Bambang Tri Hartanto</p>
                                    <p class="text-black font-jakarta text-[0.45rem]/[120%] font-normal">@bang_tri</p>
                                </div>
                                <div class="flex w-fit p-[0.2rem] items-center gap-[0.25rem] rounded-[0.6rem] bg-[#B84100]">
                                    <span class="material-icons-round text-[0.5rem] text-secondary-light">star</span>
                                    <p class="text-secondary-light text-[0.45rem] font-normal">4.3/5</>
                                </div>
                            </div>
                            <p class="self-stretch text-black font-jakarta text-justify text-[0.45rem]/[120%] font-normal">Sangat membantu dalam mencari resep masakan. Tampilan menarik dan mudah digunakan.</p>
                        </div>
                    </div>
                    <div class=" rotate-[-3deg] flex w-[15rem] p-[0.62rem] gap-[0.62rem] rounded-[0.3rem] border-[1px] border-solid border-[#F7C9B0] bg-white">
                        <img src="{{ asset('assets/images/Image_DummyProfile.png') }}" alt="Profil Foto" class="w-[2.25rem] h-[2.25rem] aspect-square content-center items-center rounded-[3rem] border-[1px] border-solid border-[#EC4448]">
                        <div class="flex flex-col content-center gap-[0.2rem] w-full">
                            <div class="flex w-full items-center justify-between">
                                <div class="flex flex-col gap-[0.06rem] self-stretch">
                                    <p class="text-black font-jakarta text-[0.48rem]/[120%] font-semibold">Bambang Tri Hartanto</p>
                                    <p class="text-black font-jakarta text-[0.45rem]/[120%] font-normal">@bang_tri</p>
                                </div>
                                <div class="flex w-fit p-[0.2rem] items-center gap-[0.25rem] rounded-[0.6rem] bg-[#B84100]">
                                    <span class="material-icons-round text-[0.5rem] text-secondary-light">star</span>
                                    <p class="text-secondary-light text-[0.45rem] font-normal">4.3/5</>
                                </div>
                            </div>
                            <p class="self-stretch text-black font-jakarta text-justify text-[0.45rem]/[120%] font-normal">Sangat membantu dalam mencari resep masakan. Tampilan menarik dan mudah digunakan.</p>
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
                <div class="rounded-[0.2rem] border-l-solid border-l-[1px] border-l-[#B84100] py-[0.5rem] px-[0.9rem] flex justify-between gap-1 overflow-hidden bg-[#FEE7C3]">
                    <div class="flex w-full flex-col gap-1 justify-center">
                        <p class="text-[0.45rem] font-medium font-poppins text-black">Apa itu laperpoll?</p>
                        <p class="text-[0.45rem] text-black font-light leading-relaxed">
                            Laperpoll adalah aplikasi resep masakan berbasis komunitas yang memudahkan kamu menemukan resep sesuai bahan yang tersedia di rumah. Didukung fitur Kulkas Digital, Meal Planner, dan Swiper Search yang intuitif.
                        </p>
                    </div>
                    <button class="w-fit h-fit p-[0.2rem] aspect-square flex justify-center items-center bg-[#B84100] rounded-full">
                        <span class="material-icons-round text-[0.8rem] text-white">keyboard_arrow_down</span>
                    </button>
                </div>
                <div class="rounded-[0.2rem] border-l-solid border-l-[1px] border-l-[#B84100] py-[0.5rem] px-[0.9rem] flex justify-between gap-1 overflow-hidden bg-[#FEE7C3]">
                    <div class="flex w-full flex-col gap-1 justify-center">
                        <p class="text-[0.45rem] font-medium font-poppins text-black">Bagaimana cara menambahkan resep baru ke aplikasi?</p>
                        <p class="text-[0.45rem] text-black font-light leading-relaxed hidden">
                            Laperpoll adalah aplikasi resep masakan berbasis komunitas yang memudahkan kamu menemukan resep sesuai bahan yang tersedia di rumah. Didukung fitur Kulkas Digital, Meal Planner, dan Swiper Search yang intuitif.
                        </p>
                    </div>
                    <button class="w-fit h-fit p-[0.2rem] aspect-square flex justify-center items-center bg-[#B84100] rounded-full">
                        <span class="material-icons-round text-[0.8rem] text-white">keyboard_arrow_down</span>
                    </button>
                </div>
                <div class="rounded-[0.2rem] border-l-solid border-l-[1px] border-l-[#B84100] py-[0.5rem] px-[0.9rem] flex justify-between gap-1 overflow-hidden bg-[#FEE7C3]">
                    <div class="flex w-full flex-col gap-1 justify-center">
                        <p class="text-[0.45rem] font-medium font-poppins text-black">Apakah aplikasi ini bisa digunakan secara offline?</p>
                        <p class="text-[0.45rem] text-black font-light leading-relaxed hidden">
                            Laperpoll adalah aplikasi resep masakan berbasis komunitas yang memudahkan kamu menemukan resep sesuai bahan yang tersedia di rumah. Didukung fitur Kulkas Digital, Meal Planner, dan Swiper Search yang intuitif.
                        </p>
                    </div>
                    <button class="w-fit h-fit p-[0.2rem] aspect-square flex justify-center items-center bg-[#B84100] rounded-full">
                        <span class="material-icons-round text-[0.8rem] text-white">keyboard_arrow_down</span>
                    </button>
                </div>
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
@endpush