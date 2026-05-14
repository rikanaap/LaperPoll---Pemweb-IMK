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

            <input type="hidden" name="bahan_id" id="bahanId" value="{{ old('bahan_id') }}">

            {{-- NAMA BAHAN --}}
            <div class="tb-group">
                <label class="tb-label font-jakarta font-semibold" for="searchBahan">
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
                <label class="tb-label font-jakarta font-semibold">Jumlah</label>
                <div class="tb-jumlah-row">
                    <div class="input tb-jumlah-input">
                        <button type="button" class="tb-counter-btn" id="btnMinus">
                            <span class="material-icons-round">remove</span>
                        </button>
                        <input type="number" id="jumlahAngka" value="1" min="1"
                               class="input-data font-jakarta text-body tb-counter-val">
                        <button type="button" class="tb-counter-btn" id="btnPlus">
                            <span class="material-icons-round">add</span>
                        </button>
                    </div>
                    <div class="input tb-satuan-input">
                        {{-- Tidak pakai name="jumlah" di sini, dihandle oleh JS via hidden field --}}
                        <input type="text" id="satuanBahan"
                               class="input-data font-jakarta text-body"
                               placeholder="satuan (gram, buah, ...)"
                               autocomplete="off">
                    </div>
                </div>
                <p class="tb-hint font-jakarta" id="satuanHint"></p>
            </div>

            {{-- TANGGAL BELI --}}
            <div class="tb-group">
                <label class="tb-label font-jakarta font-semibold" for="boughtDate">
                    Tanggal Beli
                </label>
                <div class="input">
                    <span class="material-icons-round">shopping_bag</span>
                    <input type="date" id="boughtDate" name="bought_date"
                           class="input-data font-jakarta text-body"
                           value="{{ old('bought_date', date('Y-m-d')) }}" required>
                </div>
            </div>

            {{-- TANGGAL EXPIRED --}}
            <div class="tb-group" id="expiredSection" style="display:none;">
                <label class="tb-label font-jakarta font-semibold">
                    Tanggal Expired
                </label>
                <div id="expiredChips" class="tb-chips"></div>
                <div class="input" style="margin-top: 0.5rem;">
                    <span class="material-icons-round">event_busy</span>
                    <input type="date" id="expiredDate" name="expired_date"
                           class="input-data font-jakarta text-body"
                           value="{{ old('expired_date') }}">
                </div>
            </div>

            {{-- SUBMIT --}}
            <button type="submit" class="tb-submit font-jakarta font-bold">
                Tambahkan ke Kulkas
            </button>

        </form>
    </div>

</main>
@endsection

@push('scripts')
    {{-- Tidak ada inline script di sini — semua dihandle oleh tambah-bahan.js --}}
    <script src="{{ asset('js/tambah-bahan.js') }}"></script>
@endpush