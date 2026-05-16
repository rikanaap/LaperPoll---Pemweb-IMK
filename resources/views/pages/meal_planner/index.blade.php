@extends('layouts.app')

@section('title', 'Meal Planner - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/meal-planner.css') }}">
@endpush

@section('content')
<main class="main-content flex flex-col">

    <x-navbar :back="true"></x-navbar>

    {{-- PAGE HEADER --}}
    <div class="page-header flex flex-col gap-1">
        <h1 class="font-jakarta font-bold text-h5 kulkas-title">Meal Planner</h1>
    </div>

    {{-- DATE RANGE PICKER --}}
    <div class="date-range-wrapper flex flex-row gap-2">
        <button class="date-range-btn flex flex-row gap-1" id="dateRangeBtn">
            <span class="material-icons-round date-range-icon">date_range</span>
            <span class="font-jakarta font-semibold text-caption" id="dateRangeLabel">Pilih Rentang Tanggal</span>
            <span class="material-icons-round date-range-chevron">expand_more</span>
        </button>
        <div class="date-range-dropdown" id="dateRangeDropdown">
            <div class="date-range-presets flex flex-col gap-1">
                {{-- REVISI 3: "Kemarin" dan "Minggu lalu" dihapus, diganti preset masa depan --}}
                <button class="preset-btn font-jakarta text-body" data-preset="today">Hari ini</button>
                <button class="preset-btn font-jakarta text-body" data-preset="tomorrow">Besok</button>
                <button class="preset-btn font-jakarta text-body" data-preset="thisweek">Minggu ini</button>
                <button class="preset-btn font-jakarta text-body" data-preset="next7">7 Hari ke depan</button>
                <button class="preset-btn font-jakarta text-body" data-preset="thismonth">Bulan ini</button>
            </div>
            <div class="date-range-calendar" id="dateRangeCalendar"></div>
            <div class="date-range-footer flex flex-row gap-2">
                <button class="date-range-reset font-jakarta font-medium text-caption" id="dateRangeReset">Reset</button>
            </div>
        </div>
    </div>

    {{-- KALORI TRACKER --}}
    {{-- REVISI 1+2: Kalori per hari, warna berbeda saat melebihi --}}
    <div class="kalori-tracker flex flex-col gap-2" id="kaloriTracker">
        {{-- Diisi oleh JS berdasarkan tab aktif --}}
        <button class="kalori-atur-btn flex flex-row gap-1" id="kaloriAturBtnInit">
            <span class="material-icons-round">emoji_food_beverage</span>
            <span class="font-jakarta font-semibold text-caption">🔥 Atur Target Kalori Hari Ini</span>
        </button>
    </div>

    {{-- MODAL ATUR KALORI --}}
    <div class="modal-overlay hidden" id="kaloriModal">
        <div class="modal-card flex flex-col gap-3">
            <h2 class="font-jakarta font-bold text-title2 mp-modal-title">Target Kalori Harian</h2>
            <p class="font-jakarta font-regular text-caption mp-modal-date" id="kaloriModalTanggal"></p>
            <div class="modal-input-row flex flex-row gap-2">
                <input type="number" class="modal-input font-jakarta text-h5 font-bold"
                       id="kaloriInput" placeholder="2000" min="100" max="9999">
                <span class="font-jakarta font-semibold text-body mp-modal-unit">kal / hari</span>
            </div>
            <button class="modal-submit font-jakarta font-bold text-title2" id="kaloriSubmit">Simpan</button>
        </div>
    </div>

    {{-- TAB HARI — diisi penuh oleh meal-planner.js --}}
    {{-- REVISI 4: tab dinamis sesuai tanggal range yang dipilih --}}
    <div class="hari-tabs" id="hariTabs">
        {{-- Radio, label, dan content diinjeksi oleh JS --}}
        <div class="hari-labels flex flex-row" id="hariLabels"></div>
    </div>

    {{-- LOADING STATE --}}
    <div class="mp-loading hidden" id="mpLoading">
        <span class="material-icons-round mp-loading-icon">hourglass_empty</span>
        <p class="font-jakarta font-medium text-caption">Memuat jadwal...</p>
    </div>

    {{-- GENERATE NOTA --}}
    <button class="generate-btn flex flex-row gap-2" id="generateNotaBtn" style="opacity:0.5;pointer-events:none;">
        <span class="material-icons-round">bolt</span>
        <span class="font-jakarta font-semibold text-title2">Generate Nota Belanja Otomatis</span>
    </button>

</main>
@endsection

@push('scripts')
    <script>
        window.pilihResepUrl = "{{ route('pilih-resep.index') }}";
        window.mpApiBase     = "{{ url('/api/meal-planner') }}";
        window.notaUrl       = "{{ route('nota.index') }}";
        window.csrfToken     = "{{ csrf_token() }}";
    </script>
    <script src="{{ asset('js/meal-planner.js') }}"></script>
@endpush