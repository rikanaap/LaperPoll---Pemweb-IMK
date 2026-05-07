@extends('layouts.app')

@section('title', 'Meal Planner - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/meal-planner.css') }}">
@endpush

@section('content')
<main class="main-content flex flex-col">

    {{-- NAVBAR --}}
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
                <button class="preset-btn font-jakarta text-body" data-preset="today">Hari ini</button>
                <button class="preset-btn font-jakarta text-body" data-preset="yesterday">Kemarin</button>
                <button class="preset-btn font-jakarta text-body" data-preset="thisweek">Minggu ini</button>
                <button class="preset-btn font-jakarta text-body" data-preset="lastweek">Minggu lalu</button>
                <button class="preset-btn font-jakarta text-body" data-preset="thismonth">Bulan ini</button>
            </div>
            <div class="date-range-calendar" id="dateRangeCalendar"></div>
            <div class="date-range-footer flex flex-row gap-2">
                <button class="date-range-reset font-jakarta font-medium text-caption" id="dateRangeReset">Reset</button>
            </div>
        </div>
    </div>

    {{-- KALORI TRACKER --}}
    <div class="kalori-tracker flex flex-col gap-2" id="kaloriTracker">
        <div class="kalori-row flex flex-row gap-2">
            <span class="kalori-nilai font-jakarta font-bold text-h5" id="kaloriNilai">0/0</span>
            <button class="kalori-edit-btn" id="kaloriEditBtn" title="Atur target kalori">
                <span class="material-icons-round">edit</span>
            </button>
        </div>
        <div class="kalori-bar-track">
            <div class="kalori-bar-fill" id="kaloriBarFill" style="width:0%"></div>
        </div>
        <div class="kalori-alert hidden flex flex-row gap-1" id="kaloriAlert">
            <span class="material-icons-round kalori-alert-icon">warning_amber</span>
            <span class="font-jakarta font-semibold text-caption">Melebihi Batas!</span>
        </div>
    </div>

    {{-- MODAL ATUR KALORI --}}
    <div class="modal-overlay hidden" id="kaloriModal">
        <div class="modal-card flex flex-col gap-3">
            <h2 class="font-jakarta font-bold text-title2" style="color:#8C2A1A;">Target/Batas Kalori Anda</h2>
            <div class="modal-input-row flex flex-row gap-2">
                <input type="number" class="modal-input font-jakarta text-h5 font-bold" id="kaloriInput" placeholder="1700" min="100" max="9999">
                <span class="font-jakarta font-semibold text-body" style="color:#555;align-self:center;">kal</span>
            </div>
            <button class="modal-submit font-jakarta font-bold text-title2" id="kaloriSubmit">SUBMIT</button>
        </div>
    </div>

    {{-- TAB HARI --}}
    <div class="hari-tabs">
        <input type="radio" name="hari" id="tab-sen" checked>
        <input type="radio" name="hari" id="tab-sel">
        <input type="radio" name="hari" id="tab-rab">
        <input type="radio" name="hari" id="tab-kam">
        <input type="radio" name="hari" id="tab-jum">
        <input type="radio" name="hari" id="tab-sab">
        <input type="radio" name="hari" id="tab-min">

        <div class="hari-labels flex flex-row">
            <label for="tab-sen" class="hari-label flex flex-col gap-0" data-hari="sen">
                <span class="hari-label-day font-jakarta font-semibold text-caption">Sen</span>
                <span class="hari-label-date font-jakarta font-bold text-body" id="date-sen"></span>
            </label>
            <label for="tab-sel" class="hari-label flex flex-col gap-0" data-hari="sel">
                <span class="hari-label-day font-jakarta font-semibold text-caption">Sel</span>
                <span class="hari-label-date font-jakarta font-bold text-body" id="date-sel"></span>
            </label>
            <label for="tab-rab" class="hari-label flex flex-col gap-0" data-hari="rab">
                <span class="hari-label-day font-jakarta font-semibold text-caption">Rab</span>
                <span class="hari-label-date font-jakarta font-bold text-body" id="date-rab"></span>
            </label>
            <label for="tab-kam" class="hari-label flex flex-col gap-0" data-hari="kam">
                <span class="hari-label-day font-jakarta font-semibold text-caption">Kam</span>
                <span class="hari-label-date font-jakarta font-bold text-body" id="date-kam"></span>
            </label>
            <label for="tab-jum" class="hari-label flex flex-col gap-0" data-hari="jum">
                <span class="hari-label-day font-jakarta font-semibold text-caption">Jum</span>
                <span class="hari-label-date font-jakarta font-bold text-body" id="date-jum"></span>
            </label>
            <label for="tab-sab" class="hari-label flex flex-col gap-0" data-hari="sab">
                <span class="hari-label-day font-jakarta font-semibold text-caption">Sab</span>
                <span class="hari-label-date font-jakarta font-bold text-body" id="date-sab"></span>
            </label>
            <label for="tab-min" class="hari-label flex flex-col gap-0" data-hari="min">
                <span class="hari-label-day font-jakarta font-semibold text-caption">Min</span>
                <span class="hari-label-date font-jakarta font-bold text-body" id="date-min"></span>
            </label>
        </div>

        {{-- Konten diisi oleh meal-planner.js --}}
        <div class="hari-content" id="content-sen"></div>
        <div class="hari-content" id="content-sel"></div>
        <div class="hari-content" id="content-rab"></div>
        <div class="hari-content" id="content-kam"></div>
        <div class="hari-content" id="content-jum"></div>
        <div class="hari-content" id="content-sab"></div>
        <div class="hari-content" id="content-min"></div>
    </div>

    {{-- GENERATE NOTA --}}
    <a href="{{ route('nota.index') }}" class="generate-btn flex flex-row gap-2">
        <span class="material-icons-round">bolt</span>
        <span class="font-jakarta font-semibold text-title2">Generate Nota Belanja Otomatis</span>
    </a>

</main>
@endsection

@push('scripts')
    <script>
        window.pilihResepUrl = "{{ route('pilih-resep.index') }}";
    </script>
    <script src="{{ asset('js/meal-planner.js') }}"></script>
@endpush