@extends('layouts.app')

@section('title', 'Tambah Bahan - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/tambah-bahan.css') }}">
@endpush

@section('content')
<main class="tb-main">

    <x-navbar :backUrl="route('kulkas.index')"></x-navbar>

    <div class="tb-card">
        <div class="tb-card-header">
            <h1 class="tb-title font-jakarta font-bold">Tambah Bahan</h1>
            <p class="tb-subtitle font-jakarta font-regular">Tambahkan bahan ke kulkas digitalmu</p>
        </div>

        @if($errors->any())
            <div class="tb-errors">
                <span class="material-icons-round">error_outline</span>
                <ul>
                    @foreach($errors->all() as $err)
                        <li class="font-jakarta text-body">{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('kulkas.store') }}" method="POST" id="formTambahBahan">
            @csrf

            {{-- Data bahan untuk JS autocomplete --}}
            <script id="bahanData" type="application/json">
                {!! $bahans->map(fn($b) => [
                    'id'                     => $b->id,
                    'nama'                   => $b->nama,
                    'has_expiry'             => $b->expired_expectancy_day !== null,
                    'expired_expectancy_day' => $b->expired_expectancy_day,
                ])->toJson() !!}
            </script>

            {{-- Hidden fields --}}
            <input type="hidden" name="bahan_id"  id="bahanId"  value="{{ old('bahan_id') }}">
            <input type="hidden" name="date_mode" id="dateMode" value="{{ old('date_mode', 'beli') }}">

            {{-- NAMA BAHAN --}}
            <div class="tb-group">
                <label class="tb-label font-jakarta font-semibold" for="searchBahan">
                    <span class="material-icons-round tb-label-icon">kitchen</span>
                    Nama Bahan
                </label>
                <div class="tb-search-wrap">
                    <div class="input">
                        <span class="material-icons-round">search</span>
                        <input type="text" id="searchBahan"
                               class="input-data font-jakarta text-body"
                               placeholder="Cari nama bahan..."
                               autocomplete="off"
                               value="{{ old('bahan_id') ? \App\Models\Bahan::find(old('bahan_id'))?->nama : '' }}">
                        <span class="material-icons-round tb-clear" id="clearSearch"
                              style="display:none; cursor:pointer;">close</span>
                    </div>
                    <ul class="tb-dropdown" id="bahanDropdown"></ul>
                </div>
            </div>

            {{-- JUMLAH --}}
            <div class="tb-group">
                <label class="tb-label font-jakarta font-semibold">
                    <span class="material-icons-round tb-label-icon">scale</span>
                    Jumlah (gram)
                </label>
                <div class="tb-jumlah-row">
                    <div class="input tb-jumlah-input">
                        <button type="button" class="tb-counter-btn" id="btnMinus">
                            <span class="material-icons-round">remove</span>
                        </button>
                        <input type="number" id="jumlahAngka" name="jumlah"
                               value="{{ old('jumlah', 100) }}" min="1" max="99999"
                               class="input-data font-jakarta text-body tb-counter-val">
                        <button type="button" class="tb-counter-btn" id="btnPlus">
                            <span class="material-icons-round">add</span>
                        </button>
                    </div>
                    <span class="tb-satuan-label font-jakarta font-semibold">gram</span>
                </div>
            </div>

            {{-- TOGGLE TIPE TANGGAL --}}
            <div class="tb-group">
                <label class="tb-label font-jakarta font-semibold">
                    <span class="material-icons-round tb-label-icon">event</span>
                    Jenis Tanggal
                </label>
                <div class="tb-date-toggle" id="dateTypeToggle">
                    <button type="button" class="tb-date-toggle-btn active" data-type="bought">
                        <span class="material-icons-round">shopping_bag</span>
                        Tanggal Beli
                    </button>
                    <button type="button" class="tb-date-toggle-btn" data-type="expired">
                        <span class="material-icons-round">event_busy</span>
                        Tanggal Expired
                    </button>
                </div>
            </div>

            {{-- TANGGAL BELI + EXPIRED dalam wrapper fixed-height agar card tidak gerak saat toggle --}}
            <div class="tb-date-section-wrap">

                {{-- TANGGAL BELI --}}
                <div class="tb-group tb-date-section" id="sectionBoughtDate">
                    <label class="tb-label font-jakarta font-semibold" for="boughtDate">
                        <span class="material-icons-round tb-label-icon">shopping_bag</span>
                        Tanggal Beli
                    </label>
                    <div class="input">
                        <span class="material-icons-round">calendar_today</span>
                        <input type="date" id="boughtDate" name="bought_date"
                               class="input-data font-jakarta text-body"
                               value="{{ old('bought_date', date('Y-m-d')) }}">
                    </div>
                    <p class="tb-hint font-jakarta">Tanggal kamu membeli bahan ini</p>
                </div>

                {{-- TANGGAL EXPIRED --}}
                <div class="tb-group tb-date-section" id="sectionExpiredDate" style="display:none;">
                    <label class="tb-label font-jakarta font-semibold">
                        <span class="material-icons-round tb-label-icon">event_busy</span>
                        Tanggal Expired
                    </label>
                    <div id="expiredChips" class="tb-chips" style="display:none;"></div>
                    <div class="input">
                        <span class="material-icons-round">event_busy</span>
                        <input type="date" id="expiredDate" name="expired_date"
                               class="input-data font-jakarta text-body"
                               value="{{ old('expired_date') }}">
                    </div>
                    <p class="tb-hint font-jakarta" id="expiredHint">Isi tanggal kedaluwarsa bahan ini</p>
                </div>

            </div>

            {{-- SUBMIT --}}
            <button type="submit" class="tb-submit font-jakarta font-bold" id="submitBtn">
                <span class="material-icons-round">add_circle</span>
                Tambahkan ke Kulkas
            </button>

        </form>
    </div>

</main>
@endsection

@push('scripts')
    <script src="{{ asset('js/tambah-bahan.js') }}"></script>
@endpush