<a href="{{ route('detail.resep', $resep->id) }}" class="no-underline text-inherit block">
    <div class="resep">
        <div class="resep-content">
            <div class="resep-logo">
                <span class="material-icons-round !text-100 text-accent-dark">
                    @switch($resep->main_filter_id)
                    @case(1) restaurant @break
                    @case(2) lunch_dining @break
                    @case(3) breakfast_dining @break
                    @case(4) ramen_dining @break
                    @case(5) cake @break
                    @default food_bank
                    @endswitch
                </span>
            </div>
            <div class="resep-detail">
                <!-- Judul Resep -->
                <h1 class="font-jakarta text-title2 text-black font-regular">{{ $resep->title }}</h1>

                <!-- Detail Resep -->
                <div class="resep-content-detail">
                    <!-- Duration Resep -->
                    <div>
                        <span class="material-icons-round text-title2">watch_later</span>
                        <p class="text-body font-jakarta font-medium text-black">{{ $resep->cook_duration_formatted }}</p>
                    </div>

                    <!-- Resep Tersedia atau Bahan Kurang -->
                    @if ($resep)
                    <div>
                        <span class="material-icons-round text-title2">menu_book</span>
                        <p class="text-body font-jakarta font-medium text-black">Bahan Tersedia</p>
                    </div>
                    @else
                    <div>
                        <span class="material-icons-round text-title2">menu_book</span>
                        <p class="text-body font-jakarta font-medium text-accent-normal-active">Bahan Kurang
                        </p>
                    </div>
                    @endif
                </div>

                <!-- Account Info -->
                <div class="resep-verified flex flex-row gap-2">
                    <p class="font-jakarta font-medium text-body">{{ $resep->user->name }}</p>
                    @if($resep->user)
                    <div class="verified_logo">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                            fill="none">
                            <path
                                d="M8 0L9.06375 1.28372L10.4721 0.391548L11.0871 1.94116L12.7023 1.52786L12.8083 3.19167L14.4721 3.29772L14.0588 4.91286L15.6085 5.52786L14.7163 6.93625L16 8L14.7163 9.06375L15.6085 10.4721L14.0588 11.0871L14.4721 12.7023L12.8083 12.8083L12.7023 14.4721L11.0871 14.0588L10.4721 15.6085L9.06375 14.7163L8 16L6.93625 14.7163L5.52786 15.6085L4.91286 14.0588L3.29772 14.4721L3.19167 12.8083L1.52786 12.7023L1.94116 11.0871L0.391548 10.4721L1.28372 9.06375L0 8L1.28372 6.93625L0.391548 5.52786L1.94116 4.91286L1.52786 3.29772L3.19167 3.19167L3.29772 1.52786L4.91286 1.94116L5.52786 0.391548L6.93625 1.28372L8 0Z"
                                fill="#0186FF" />
                        </svg>
                        <span class="material-icons-round text-body text-white">check</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <span class="material-icons-round text-h4 text-secondary-normal">arrow_forward_ios</span>
    </div>
</a>