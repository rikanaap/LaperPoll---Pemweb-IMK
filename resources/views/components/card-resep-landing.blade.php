<a href="{{ route('detail.resep', $resep->id) }}" style="text-decoration:none;color:inherit;">
    <div class="p-[0.5rem] gap-[0.5rem] min-w-[7rem] max-w-[7rem] md:min-w-[15rem] md:max-w-[15rem] md:p-[1rem] items-center flex-col flex rounded-[0.5rem] border-[0.67px] border-solid border-[#F2E2D9] bg-white">
        @php $img = $resep->attachments->first(fn($a) => in_array($a->mimetype, ['image/jpeg', 'image/png'])) @endphp
        <img src="{{ $img ? asset($img->path) : 'https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=200&h=160&fit=crop' }}" alt="" class="w-full aspect-square rounded-[0.5rem]">
        <div class="flex flex-col gap-[0.1rem] items-start md:self-stretch">
            <h2 class="text-black overflow-ellipsis font-jakarta text-[0.5rem]/[120%] font-semibold md:text-[1rem]">{{ $resep->title  }}</h2>
            <div class="flex gap-[0.18rem]">
                <div class="flex gap-[0.1rem]">
                    <span class="material-icons-round text-[0.45rem] font-light text-black md:text-[0.8rem]">watch_later</span>
                    <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black md:text-[0.8rem]">{{ $resep->cook_duration }}</>
                </div>
                <div class="flex gap-[0.1rem] items-center md:gap-[0.3rem]">
                    <span class="material-icons-round text-[0.45rem] text-black md:text-[0.8rem]">menu_book</span>
                    <p class="font-jakarta text-[0.4rem]/[120%] font-medium text-black md:text-[0.8rem]">Bahan Tersedia</>
                </div>
            </div>
            <div class="flex gap-[0.125rem] md:gap-[0.3]">
                <p class="text-black text-[0.4rem]/[120%] font-medium font-jakarta md:text-[0.8rem]">{{'@'.$resep->user->name }}</p>
                <div class="relative flex items-center justify-center w-[0.65rem] h-[0.65rem] md:w-[1.1rem] md:h-[1.1rem] flex-shrink-0">
                    <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6" fill="none">
                        <path d="M3 0L3.39891 0.481395L3.92705 0.146831L4.15768 0.727933L4.76336 0.572949L4.80312 1.19688L5.42705 1.23664L5.27207 1.84232L5.85317 2.07295L5.51861 2.60109L6 3L5.51861 3.39891L5.85317 3.92705L5.27207 4.15768L5.42705 4.76336L4.80312 4.80312L4.76336 5.42705L4.15768 5.27207L3.92705 5.85317L3.39891 5.51861L3 6L2.60109 5.51861L2.07295 5.85317L1.84232 5.27207L1.23664 5.42705L1.19688 4.80312L0.572949 4.76336L0.727933 4.15768L0.146831 3.92705L0.481395 3.39891L0 3L0.481395 2.60109L0.146831 2.07295L0.727933 1.84232L0.572949 1.23664L1.19688 1.19688L1.23664 0.572949L1.84232 0.727933L2.07295 0.146831L2.60109 0.481395L3 0Z" fill="#0186FF" />
                    </svg>
                    <span class="material-icons-round absolute !text-[0.4rem] md:!text-[0.65rem] text-white leading-none">
                        check
                    </span>
                </div>
                @if($resep->user && $resep->user->verify)
                @endif
            </div>
        </div>
    </div>
</a>