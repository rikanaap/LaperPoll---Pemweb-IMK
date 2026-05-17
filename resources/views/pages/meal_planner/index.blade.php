@extends('layouts.app')

@section('title', 'Meal Planner - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/meal-planner.css') }}">
@endpush

@section('content')
<div class="mp-page">

    {{-- ── STICKY TOP BAR ── --}}
    <div class="mp-topbar">
        <x-navbar :back="true"></x-navbar>

        <div class="mp-topbar-inner">
            <div class="mp-heading">
                <h1 class="mp-title font-jakarta font-bold">Meal Planner</h1>
                <button class="mp-range-btn" id="dateRangeBtn">
                    <span class="material-icons-round">calendar_month</span>
                    <span class="font-jakarta font-semibold" id="dateRangeLabel">Pilih tanggal</span>
                    <span class="material-icons-round mp-chevron" id="dateRangeChevron">expand_more</span>
                </button>
            </div>

            {{-- Kalori bar (muncul setelah ada target) --}}
            <div class="mp-kalori-bar-wrap" id="mpKaloriWrap" style="display:none;">
                <div class="mp-kalori-info">
                    <div class="mp-kalori-text-group">
                        <span class="mp-kalori-current font-jakarta font-bold" id="mpKaloriCurrent">0</span>
                        <span class="mp-kalori-sep font-jakarta font-regular">/</span>
                        <span class="mp-kalori-target font-jakarta font-regular" id="mpKaloriTarget">0 kal</span>
                    </div>
                    <button class="mp-kalori-edit" id="mpKaloriEdit" title="Atur target kalori">
                        <span class="material-icons-round">edit</span>
                    </button>
                </div>
                <div class="mp-bar-track">
                    <div class="mp-bar-fill" id="mpBarFill"></div>
                    <div class="mp-bar-label font-jakarta font-bold" id="mpBarLabel"></div>
                </div>
                <p class="mp-kalori-over font-jakarta font-semibold" id="mpKaloriOver" style="display:none;">
                    <span class="material-icons-round">warning_amber</span>
                    Kalori melebihi target!
                </p>
            </div>

            {{-- Set target kalori (muncul kalau belum ada target) --}}
            <button class="mp-set-kalori-btn font-jakarta font-semibold" id="mpSetKaloriBtn" style="display:none;">
                <span class="material-icons-round">local_fire_department</span>
                Set target kalori hari ini
            </button>

            {{-- Tab hari --}}
            <div class="mp-tabs-scroll" id="mpTabsWrap" style="display:none;">
                <div class="mp-tabs" id="mpTabs"></div>
            </div>
        </div>
    </div>

    {{-- ── BODY ── --}}
    <div class="mp-body" id="mpBody">

        {{-- Empty state --}}
        <div class="mp-empty" id="mpEmpty">
            <div class="mp-empty-icon-wrap">
                <span class="material-icons-round">restaurant_menu</span>
            </div>
            <p class="font-jakarta font-bold mp-empty-title">Rencanakan Makananmu</p>
            <p class="font-jakarta font-regular mp-empty-sub">
                Pilih tanggal untuk mulai mengatur jadwal makan harianmu
            </p>
            <button class="mp-empty-cta font-jakarta font-semibold" id="mpEmptyCta">
                <span class="material-icons-round">calendar_month</span>
                Pilih Tanggal
            </button>
        </div>

        {{-- Content slot (diisi JS) --}}
        <div class="mp-content" id="mpContent" style="display:none;"></div>

        {{-- Loading --}}
        <div class="mp-loading" id="mpLoading" style="display:none;">
            <div class="mp-spinner"></div>
            <span class="font-jakarta font-medium">Memuat jadwal...</span>
        </div>

    </div>

    {{-- ── GENERATE NOTA (fixed bottom) ── --}}
    <button class="mp-generate-btn" id="mpGenerateBtn" style="opacity:0.45;pointer-events:none;">
        <span class="material-icons-round">receipt_long</span>
        <span class="font-jakarta font-bold">Generate Nota Belanja</span>
    </button>

</div>

{{-- ── DATE PICKER DROPDOWN ── --}}
<div class="mp-backdrop" id="mpBackdrop" style="display:none;"></div>
<div class="mp-dropdown" id="mpDropdown" style="display:none;">
    <div class="mp-dropdown-presets">
        <p class="mp-section-label font-jakarta font-bold">Pilih Cepat</p>
        <div class="mp-presets-grid">
            <button class="mp-preset font-jakarta font-semibold" data-preset="today">
                <span class="material-icons-round">today</span>Hari ini
            </button>
            <button class="mp-preset font-jakarta font-semibold" data-preset="tomorrow">
                <span class="material-icons-round">event</span>Besok
            </button>
            <button class="mp-preset font-jakarta font-semibold" data-preset="next7">
                <span class="material-icons-round">date_range</span>7 Hari ke depan
            </button>
            <button class="mp-preset font-jakarta font-semibold" data-preset="thisweek">
                <span class="material-icons-round">view_week</span>Minggu ini
            </button>
            <button class="mp-preset font-jakarta font-semibold" data-preset="thismonth">
                <span class="material-icons-round">calendar_month</span>Bulan ini
            </button>
        </div>
    </div>
    <div class="mp-dropdown-divider"></div>
    <div class="mp-dropdown-cal">
        <p class="mp-section-label font-jakarta font-bold">Pilih Manual</p>
        <div id="mpCalendar"></div>
        <p class="mp-cal-hint font-jakarta font-regular" id="mpCalHint">Ketuk tanggal mulai</p>
    </div>
</div>

{{-- ── MODAL KALORI (redesign) ── --}}
<div class="mp-modal-overlay" id="mpModalOverlay" style="display:none;">
    <div class="mp-modal">

        {{-- Header --}}
        <div class="mp-modal-header">
            <div class="mp-modal-flame">🔥</div>
            <div class="mp-modal-header-text">
                <p class="mp-modal-title font-jakarta font-bold">Target Kalori Harian</p>
                <p class="mp-modal-date font-jakarta font-regular" id="mpModalDate"></p>
            </div>
            <button class="mp-modal-close" id="mpModalClose">
                <span class="material-icons-round">close</span>
            </button>
        </div>

        {{-- Body --}}
        <div class="mp-modal-body">
            <p class="mp-modal-desc font-jakarta font-regular">
                Berapa kalori yang ingin kamu capai hari ini?
            </p>

            {{-- Stepper input --}}
            <div class="mp-stepper">
                <button class="mp-step-btn" id="mpStepMinus">
                    <span class="material-icons-round">remove</span>
                </button>
                <div class="mp-input-wrap">
                    <input type="number" id="mpKaloriInput"
                           class="mp-kalori-input font-jakarta font-bold"
                           min="100" max="9999" placeholder="2000">
                    <span class="mp-input-unit font-jakarta font-regular">kal</span>
                </div>
                <button class="mp-step-btn" id="mpStepPlus">
                    <span class="material-icons-round">add</span>
                </button>
            </div>

            {{-- Quick chips --}}
            <div class="mp-chips">
                <button class="mp-chip font-jakarta font-semibold" data-val="1200">1.200</button>
                <button class="mp-chip font-jakarta font-semibold" data-val="1500">1.500</button>
                <button class="mp-chip font-jakarta font-semibold" data-val="2000">2.000</button>
                <button class="mp-chip font-jakarta font-semibold" data-val="2500">2.500</button>
                <button class="mp-chip font-jakarta font-semibold" data-val="3000">3.000</button>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mp-modal-footer">
            <button class="mp-btn-cancel font-jakarta font-semibold" id="mpModalCancel">Batal</button>
            <button class="mp-btn-save font-jakarta font-bold" id="mpModalSave">
                <span class="material-icons-round">check</span>
                Simpan Target
            </button>
        </div>
    </div>
</div>

{{-- ── TOAST ── --}}
<div class="mp-toast" id="mpToast" style="display:none;">
    <span class="material-icons-round" id="mpToastIcon">check_circle</span>
    <span class="font-jakarta font-semibold" id="mpToastMsg"></span>
</div>

@endsection

@push('scripts')
<script>
    window.MP = {
        pilihResepUrl : "{{ route('pilih-resep.index') }}",
        apiBase       : "{{ url('/api/meal-planner') }}",
        notaUrl       : "{{ route('nota.index') }}",
        csrf          : "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('js/meal-planner.js') }}"></script>
@endpush