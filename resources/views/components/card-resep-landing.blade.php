<div class="p-[0.5rem] gap-[0.5rem] min-w-[7rem] max-w-[7rem] items-center flex-col flex rounded-[0.5rem] border-[0.67px] border-solid border-[#F2E2D9] bg-white">
    <img src="https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=200&h=160&fit=crop" alt="" class="w-full aspect-square rounded-[0.5rem]">
    <div class="flex flex-col gap-[0.1rem] items-start">
        <h2 class="text-black overflow-ellipsis font-jakarta text-[0.5rem]/[120%] font-semibold">{{ $resep->title  }}</h2>
        <div class="flex gap-[0.18rem]">
            <div class="flex gap-[0.1rem]">
                <span class="material-icons-round text-[0.45rem] font-light text-black">watch_later</span>
                <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">{{ $resep->cook_duration }}</>
            </div>
            <div class="flex gap-[0.1rem]">
                <span class="material-icons-round text-[0.45rem] text-black">menu_book</span>
                <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black">Bahan Tersedia</>
            </div>
        </div>
        <div class="flex gap-[0.125rem]">
            <p class="text-black text-[0.4rem]/[120%] font-medium font-jakarta">{{ $resep->user->name }}</p>
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" width="6" height="6" viewBox="0 0 6 6" fill="none">
                    <path d="M3 0L3.39891 0.481395L3.92705 0.146831L4.15768 0.727933L4.76336 0.572949L4.80312 1.19688L5.42705 1.23664L5.27207 1.84232L5.85317 2.07295L5.51861 2.60109L6 3L5.51861 3.39891L5.85317 3.92705L5.27207 4.15768L5.42705 4.76336L4.80312 4.80312L4.76336 5.42705L4.15768 5.27207L3.92705 5.85317L3.39891 5.51861L3 6L2.60109 5.51861L2.07295 5.85317L1.84232 5.27207L1.23664 5.42705L1.19688 4.80312L0.572949 4.76336L0.727933 4.15768L0.146831 3.92705L0.481395 3.39891L0 3L0.481395 2.60109L0.146831 2.07295L0.727933 1.84232L0.572949 1.23664L1.19688 1.19688L1.23664 0.572949L1.84232 0.727933L2.07295 0.146831L2.60109 0.481395L3 0Z" fill="#0186FF" />
                </svg>
                @if($resep->user && $resep->user->verify)
                <span class="material-icons-round text-[0.125rem] text-white absolute top-1/2 left-1/2 right-1/2 bottom-1/2">check</span>
                @endif
            </div>
        </div>
    </div>
</div>