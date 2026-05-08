@extends('layouts.app')

@section('title', 'Tambah Bahan - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/tambah-bahan.css') }}">
@endpush

@section('content')
<main class="tb-main">

    <x-navbar :backUrl="route('kulkas.index')"></x-navbar>

    {{-- FORM CARD --}}
    <div class="tb-card">
        <div class="tb-card-header">
            <h1 class="tb-title font-jakarta font-bold">Tambah Bahan</h1>
            <p class="tb-subtitle font-jakarta font-regular">Tambahkan bahan ke kulkas digitalmu</p>
        </div>

        {{-- VALIDATION ERRORS --}}
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
            {{-- Data bahan untuk JS autocomplete --}}
            <script id="bahanData" type="application/json">
                <?php
                    $bahanJson = $bahans->map(function($b) {
                        return [
                            'id'                     => $b->id,
                            'nama'                   => $b->nama,
                            'has_expiry'             => $b->expired_expectancy_day !== null,
                            'expired_expectancy_day' => $b->expired_expectancy_day,
                        ];
                    });
                    echo json_encode($bahanJson);
                ?>
            </script>

            <input type="hidden" name="bahan_id" id="bahanId">

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
                               autocomplete="off">
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
                        <input type="text" id="satuanBahan" name="jumlah"
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
                           value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            {{-- TANGGAL EXPIRED (muncul kalau bahan punya expiry) --}}
            <div class="tb-group" id="expiredSection" style="display:none;">
                <label class="tb-label font-jakarta font-semibold">
                    Tanggal Expired
                </label>
                <div id="expiredChips" class="tb-chips"></div>
                <div class="input" style="margin-top: 0.5rem;">
                    <span class="material-icons-round">event_busy</span>
                    <input type="date" id="expiredDate" name="expired_date"
                           class="input-data font-jakarta text-body">
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
    <script src="{{ asset('js/tambah-bahan.js') }}"></script>
    <script>
    // Sync jumlahAngka + satuanBahan → input name="jumlah"
    document.addEventListener('DOMContentLoaded', () => {
        const angka  = document.getElementById('jumlahAngka');
        const satuan = document.getElementById('satuanBahan');

        // Hapus name dari satuan, pakai hidden field sebagai pengganti
        satuan.removeAttribute('name');
        const hid = document.createElement('input');
        hid.type = 'hidden';
        hid.name = 'jumlah';
        hid.id   = 'jumlahHidden';
        hid.value = '1';
        document.getElementById('formTambahBahan').appendChild(hid);

        function syncJumlah() {
            const a = angka.value || '1';
            const s = satuan.value.trim();
            // Kirim gabungan ke server via hidden field
            document.getElementById('jumlahHidden').value = a + (s ? ' ' + s : '');
        }
        angka.addEventListener('input', syncJumlah);
        satuan.addEventListener('input', syncJumlah);

        syncJumlah();
    });
    </script>
@endpush