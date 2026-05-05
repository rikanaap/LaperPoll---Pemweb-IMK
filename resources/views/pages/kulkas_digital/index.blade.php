@extends('layouts.app')

@section('title', 'Kulkas Digital - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/kulkas-digital.css') }}">
@endpush

@section('content')
    <main class="main-content flex flex-col">

        {{-- NAVBAR --}}
        <x-navbar :back="true"></x-navbar>

        {{-- HEADER --}}
        <section class="kulkas-header flex flex-row">
            <h1 class="font-jakarta font-bold text-h5 kulkas-title">Kulkas Digital</h1>
            <a href="/" class="kulkas-add-btn" aria-label="Tambah bahan">
                <span class="material-icons-round">add</span>
            </a>
        </section>

        {{-- SEARCH BAR --}}
        <div class="kulkas-search">
            <div class="input">
                <span class="material-icons-round">search</span>
                <input type="text" id="searchKulkas" class="input-data font-jakarta text-body"
                    placeholder="Cari bahan di kulkas..." autocomplete="off">
            </div>
        </div>

        {{-- FILTER TABS --}}
        <div class="filter-tabs flex flex-row" role="tablist">
            <button class="filter-tab active font-jakarta font-semibold text-body" data-filter="semua" role="tab"
                aria-selected="true">Semua</button>
            <button class="filter-tab font-jakarta font-medium text-body" data-filter="tersedia" role="tab"
                aria-selected="false">Tersedia</button>
            <button class="filter-tab font-jakarta font-medium text-body" data-filter="hampir-habis" role="tab"
                aria-selected="false">Hampir Habis</button>
        </div>

        {{-- SUCCESS FLASH --}}
        @if(session('success'))
            <div class="flash-success font-jakarta text-body">
                <span class="material-icons-round">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        {{-- BAHAN GRID --}}
        <section class="bahan-grid" id="bahanGrid">
            @php
                $grouped = [
                    [
                        'nama' => 'Telur',
                        'status' => 'tersedia',
                        'has_expiry' => true,
                        'pembelian' => [
                            [
                                'id' => 1,
                                'jumlah' => '10 butir',
                                'bought_date' => '2026-05-01',
                                'expired_date' => '2026-05-10',
                                'sisa_hari' => 5,
                            ],
                            [
                                'id' => 2,
                                'jumlah' => '6 butir',
                                'bought_date' => '2026-05-03',
                                'expired_date' => '2026-05-08',
                                'sisa_hari' => 3,
                            ],
                        ],
                    ],
                    [
                        'nama' => 'Susu',
                        'status' => 'hampir-habis',
                        'has_expiry' => true,
                        'pembelian' => [
                            [
                                'id' => 3,
                                'jumlah' => '1 liter',
                                'bought_date' => '2026-05-02',
                                'expired_date' => '2026-05-06',
                                'sisa_hari' => 1,
                            ],
                        ],
                    ],
                    [
                        'nama' => 'Garam',
                        'status' => 'tersedia',
                        'has_expiry' => false,
                        'pembelian' => [
                            [
                                'id' => 4,
                                'jumlah' => '500 gram',
                                'bought_date' => '2026-04-20',
                                'expired_date' => null,
                                'sisa_hari' => null,
                                ],
                                ],
                                ],
                    [
                        'nama' => 'Tempe',
                        'status' => 'tersedia',
                        'has_expiry' => true,
                        'pembelian' => [
                            [
                                'id' => 5,
                                'jumlah' => '100 gram',
                                'bought_date' => '2026-04-20',
                                'expired_date' => null,
                                'sisa_hari' => null,
                            ],
                        ],
                    ],
                ];
            @endphp

            @forelse($grouped as $item)
                @php
                    $badgeClass = match ($item['status']) {
                        'tersedia' => 'badge-tersedia',
                        'hampir-habis' => 'badge-hampir',
                        default => 'badge-hampir',
                    };

                    $badgeLabel = match ($item['status']) {
                        'tersedia' => 'Tersedia',
                        'hampir-habis' => 'Hampir Habis',
                        default => 'Hampir Habis',
                    };

                    // $totalJumlah = $item['pembelian']->pluck('jumlah')->implode(' + ');
                    $totalJumlah = "0";
                @endphp

                <div class="bahan-card {{ count($item['pembelian']) > 1 ? 'has-multi' : '' }}"
                    data-status="{{ $item['status'] }}" data-nama="{{ strtolower($item['nama']) }}">

                    {{-- COLLAPSED --}}
                    <div class="card-collapsed">
                        <h2 class="font-jakarta font-semibold text-title2 text-secondary-normal">
                            {{ $item['nama'] }}
                        </h2>

                        <p class="font-jakarta font-regular text-caption text-primary-darker">
                            {{ $totalJumlah }}
                            @if($item['pembelian'][0]['sisa_hari'] !== null)
                                        · {{ $item['pembelian'][0]['sisa_hari'] > 0
                                ? $item['pembelian'][0]['sisa_hari'] . ' hari'
                                : 'Segera habiskan' }}
                            @endif
                        </p>

                        <div class="card-footer flex flex-row">
                            <span class="status-badge {{ $badgeClass }} font-jakarta font-medium text-caption">
                                {{ $badgeLabel }}
                            </span>

                            @if(count($item['pembelian']) > 1)
                                <span class="expand-indicator font-jakarta text-caption text-primary-darker">
                                    {{ count($item['pembelian']) }}x beli
                                    <span class="material-icons-round expand-arrow">expand_more</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- EXPANDED --}}
                    <div class="card-expanded" style="display:none;">
                        <div class="expanded-header flex flex-row">
                            <h2 class="font-jakarta font-semibold text-title2 text-secondary-normal">
                                {{ $item['nama'] }}
                            </h2>
                            <span class="material-icons-round expand-arrow">expand_less</span>
                        </div>

                        @foreach($item['pembelian'] as $i => $beli)
                            <div class="pembelian-item flex flex-col">
                                <div class="pembelian-row flex flex-row">
                                    <span class="font-jakarta font-semibold text-caption pembelian-label">
                                        Pembelian {{ $i + 1 }}
                                    </span>

                                    <form action="/" method="POST" class="hapus-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="hapus-btn" onclick="return confirm('Hapus pembelian ini?')"
                                            aria-label="Hapus">
                                            <span class="material-icons-round">delete_outline</span>
                                        </button>
                                    </form>
                                </div>

                                <p class="font-jakarta font-regular text-caption text-secondary-normal">
                                    <span class="material-icons-round icon-inline">scale</span>
                                    {{ $beli['jumlah'] }}
                                </p>

                                <p class="font-jakarta font-regular text-caption text-primary-darker">
                                    <span class="material-icons-round icon-inline">calendar_today</span>
                                    Dibeli: {{ $beli['bought_date'] ?? '-' }}
                                </p>

                                @if($item['has_expiry'] && $beli['expired_date'])
                                        <p class="font-jakarta font-regular text-caption
                                                        {{ $beli['sisa_hari'] !== null && $beli['sisa_hari'] <= 3
                                    ? 'text-accent-normal' : 'text-primary-darker' }}">
                                            <span class="material-icons-round icon-inline">event_busy</span>
                                            Expired: {{ $beli['expired_date'] }}
                                            @if($beli['sisa_hari'] !== null)
                                                    ({{ $beli['sisa_hari'] > 0
                                                ? $beli['sisa_hari'] . ' hari lagi'
                                                : 'Segera habiskan' }})
                                            @endif
                                        </p>
                                @endif
                            </div>

                            @if(!$loop->last)
                                <hr class="pembelian-divider">
                            @endif
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="empty-state flex flex-col gap-2">
                    <span class="material-icons-round empty-icon">kitchen</span>
                    <p class="font-jakarta font-medium text-body text-secondary-normal">
                        Kulkas masih kosong
                    </p>
                    <p class="font-jakarta font-regular text-caption text-primary-darker">
                        Tambahkan bahan dengan tombol + di atas
                    </p>
                </div>
            @endforelse
        </section>
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('js/kulkas-digital.js') }}"></script>
@endpush