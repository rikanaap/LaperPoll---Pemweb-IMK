@extends('layouts.app')

@section('title', 'Timer Masak - {{ $resep->title }}')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/timer-resep.css') }}">
@endpush

@section('content')
<main class="tr-main font-jakarta">

    {{-- ── NAVBAR ── --}}
    <x-navbar :backUrl="route('detail.resep', $resep->id)"></x-navbar>

    {{-- ── HEADER RESEP ── --}}
    <div class="tr-resep-header">
        <div class="tr-resep-info">
            <h1 class="tr-resep-title font-bold">{{ $resep->title }}</h1>
            <p class="tr-resep-sub">
                <span class="material-icons-round">restaurant</span>
                Oleh {{ $resep->user->name ?? 'Anonim' }}
                &nbsp;·&nbsp;
                <span class="material-icons-round">format_list_numbered</span>
                {{ $langkahs->count() }} langkah
            </p>
        </div>
        <img src="{{ $resep->thumbnail ? asset($resep->thumbnail) : asset('assets/images/Image_DummyProfile.png') }}"
             alt="{{ $resep->title }}" class="tr-resep-thumb"
             onerror="this.src='{{ asset('assets/images/Image_DummyProfile.png') }}'">
    </div>

    {{-- ── STEPPER ── --}}
    <div class="tr-stepper-wrap">
        <div class="tr-stepper" id="trStepper">
            @foreach($langkahs as $i => $langkah)
                <div class="tr-step-node {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}">
                    <div class="tr-step-dot">{{ $i + 1 }}</div>
                    @if(!$loop->last)
                        <div class="tr-step-connector"></div>
                    @endif
                </div>
            @endforeach
        </div>
        <p class="tr-step-counter" id="trStepCounter">Langkah 1 dari {{ $langkahs->count() }}</p>
    </div>

    {{-- ── MAIN CONTENT ── --}}
    <div class="tr-content-grid">

        {{-- KIRI: instruksi langkah --}}
        <div class="tr-instruction-card" id="trInstructionCard">

            {{-- Badge langkah --}}
            <div class="tr-step-badge" id="trStepBadge">
                <span class="material-icons-round">receipt_long</span>
                <span id="trStepLabel">Langkah 1</span>
            </div>

            {{-- Durasi langkah --}}
            <div class="tr-step-duration" id="trStepDuration" style="display:none">
                <span class="material-icons-round">timer</span>
                <span id="trDurationLabel"></span>
            </div>

            {{-- Deskripsi --}}
            <p class="tr-step-desc" id="trStepDesc">Memuat langkah...</p>

            {{-- Bahan langkah ini --}}
            <div class="tr-bahan-section" id="trBahanSection">
                <p class="tr-bahan-title font-semibold">
                    <span class="material-icons-round">kitchen</span>
                    Bahan yang dipakai
                </p>
                <div class="tr-bahan-chips" id="trBahanChips"></div>
            </div>

        </div>

        {{-- KANAN: timer --}}
        <div class="tr-timer-card">

            {{-- Circular timer --}}
            <div class="tr-timer-ring-wrap" id="trTimerRingWrap">
                <svg class="tr-timer-svg" id="trTimerSvg" viewBox="0 0 160 160">
                    <circle class="tr-ring-track" cx="80" cy="80" r="68"/>
                    <circle class="tr-ring-fill"  cx="80" cy="80" r="68" id="trRingFill"/>
                </svg>
                <div class="tr-timer-inner">
                    <span class="tr-timer-display font-bold" id="trTimerDisplay">--:--</span>
                    <span class="tr-timer-status" id="trTimerStatus">Belum mulai</span>
                </div>
            </div>

            {{-- No timer state --}}
            <div class="tr-no-timer" id="trNoTimer" style="display:none">
                <span class="material-icons-round">timer_off</span>
                <p>Langkah ini tidak ada timer</p>
            </div>

            {{-- Timer actions --}}
            <div class="tr-timer-actions" id="trTimerActions">
                <button class="tr-btn-start font-semibold" id="trBtnStart">
                    <span class="material-icons-round">play_arrow</span>
                    Mulai
                </button>
                <button class="tr-btn-reset font-semibold" id="trBtnReset">
                    <span class="material-icons-round">restart_alt</span>
                    Reset
                </button>
            </div>

            {{-- Nav prev/next --}}
            <div class="tr-nav-row">
                <button class="tr-nav-btn" id="trBtnPrev" disabled>
                    <span class="material-icons-round">arrow_back</span>
                    <span class="tr-nav-label">Sebelumnya</span>
                </button>
                <button class="tr-nav-btn tr-nav-next" id="trBtnNext">
                    <span class="tr-nav-label" id="trNextLabel">Lanjut</span>
                    <span class="material-icons-round">arrow_forward</span>
                </button>
            </div>

        </div>
    </div>

    {{-- ── TOAST selesai ── --}}
    <div class="tr-done-toast" id="trDoneToast" style="display:none">
        <div class="tr-done-toast-inner">
            <span class="material-icons-round tr-done-icon">check_circle</span>
            <div>
                <p class="tr-done-title font-bold">Timer selesai! ⏰</p>
                <p class="tr-done-sub">Lanjut ke langkah berikutnya?</p>
            </div>
            <button class="tr-done-close" id="trDoneClose">
                <span class="material-icons-round">close</span>
            </button>
        </div>
    </div>

    {{-- ── MODAL selesai masak ── --}}
    <div class="tr-finish-modal" id="trFinishModal" style="display:none">
        <div class="tr-finish-modal-box">
            <div class="tr-finish-icon">🎉</div>
            <h2 class="font-bold tr-finish-title">Masakan Selesai!</h2>
            <p class="tr-finish-sub">Bagus! Yuk bagikan hasil masakanmu dan ceritakan pengalamannya.</p>
            <div class="tr-finish-actions">
                <a href="{{ route('detail.resep', $resep->id) }}" class="tr-finish-btn-secondary font-semibold">
                    Kembali ke Resep
                </a>
                <a href="{{ route('ulasan.show', $resep->id) }}?from=timer" class="tr-finish-btn-primary font-semibold">
                    <span class="material-icons-round">star</span>
                    Bagikan Hasil Masakan
                </a>
            </div>
        </div>
    </div>
    <div class="tr-finish-overlay" id="trFinishOverlay" style="display:none"></div>

</main>

@push('scripts')
<script>
    // Data langkah dari server — tidak ada dummy
    window.TR_STEPS  = @json($stepsData);
    window.TR_BAHANS = @json($bahansData);

    window.TR_RESEP_ID  = {{ $resep->id }};
    window.TR_ULASAN_URL = "{{ route('ulasan.show', $resep->id) }}";
</script>
<script src="{{ asset('js/timer-resep.js') }}"></script>
@endpush

@endsection