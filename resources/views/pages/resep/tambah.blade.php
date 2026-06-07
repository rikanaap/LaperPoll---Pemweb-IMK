@extends('layouts.app')

@section('title', 'LaperPoll')

@push('styles')

@endpush

@push('links')
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('css/pages/tambah-resep.css') }}">
<meta name="form-submit-url" content="{{ route('resep.store') }}">
@endpush

@section('content')
<main class="main-content flex flex-col">
    <x-navbar />
    <section class="resep-forms flex flex-col">
        <div class="forms flex flex-col gap-2">
            <div class="form-indicator flex flex-row gap-3">
                <button type="button" class="btn-back" onclick="previousForm()" style="cursor: pointer; width: 100%; display: flex; gap: 0.5rem; align-items: center; padding: 0.5rem 0; font-weight: 600; background: none; border: none;">
                    <span class="material-icons-round text-accent-normal">arrow_back</span>
                    <p class="font-jakarta text-accent-normal" style="margin: 0;">Kembali</p>
                </button>

                <div class="indicator-wrapper flex flex-row">
                    <div class="indicator"></div>
                    <div class="indicator i-enable "></div>
                    <div class="indicator i-enable"></div>
                </div>
                <p class="font-poppins text-title2 text-accent-normal font-semibold">1/5</p>
            </div>

            <!-- Form 1 Start -->
            <div class="form" id="form-1">
                <div class="input-wrapper flex flex-col">
                    <h5 class="font-jakarta text-title2 font-regular text-secondary-normal">Sebutkan nama resep</h5>
                    <div class="input">
                        <input class="input-data text-body font-jakarta font-semibold" type="text"
                            placeholder="Nama resep">
                    </div>
                </div>

                <div class="input-wrapper flex flex-col">
                    <h5 class="font-jakarta text-title2 font-regular text-secondary-normal">Berapa kalori resep ini?</h5>
                    <div class="input">
                        <input class="input-data text-body font-jakarta font-semibold" type="number"
                            placeholder="Kalori (kcal)" min="0">
                    </div>
                </div>

                <div class="input-wrapper flex flex-col">
                    <h5 class="font-jakarta text-title2 font-regular text-secondary-normal">Pilih kategori resep
                    </h5>
                    <div class="input-dropdown">
                        <div class="input">
                            <span class="material-icons-round">search</span>
                            <input id="searchKategori" class="input-data text-body font-jakarta font-semibold"
                                type="text" placeholder="Cari Kategori">
                            <span class="material-icons-round">expand_circle_down</span>
                        </div>
                        <div id="listKategori" class="dropdown-datas">
                            @foreach ($kategories as $kategori)
                            <div class="dropdown-data" data-kategori-id="{{ $kategori->id }}">
                                <p class="font-jakarta font-semibold text-body text-primary-dark-active">{{ $kategori->title }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <!-- Form 1 End -->

            <!-- Form 2 Start -->
            <div class="results" id="result-2" style="display: none;">
                <div class="input-wrapper flex flex-col">
                    <h5 class="font-jakarta text-title2 font-regular text-secondary-normal">Bahan yang digunakan
                    </h5>
                    <div class="wrapper-result flex flex-row">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>
            <div class="form" id="form-2" style="display: none;">
                <div class="input-wrapper flex flex-col">
                    <h5 class="font-jakarta text-title2 font-regular text-secondary-normal">Pilih bahan
                    </h5>
                    <div class="input-dropdown">
                        <div class="input">
                            <span class="material-icons-round">search</span>
                            <input class="input-data text-body font-jakarta font-semibold" type="text"
                                placeholder="Cari bahan">
                            <span class="material-icons-round">expand_circle_down</span>
                        </div>
                        <div class="dropdown-datas">
                            @foreach ($bahans as $bahan)
                            <div class="dropdown-data" data-bahan-id="{{ $bahan->id }}">
                                <p class="font-jakarta font-semibold text-body text-primary-dark-active">{{ $bahan->nama }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div id="JudulBahan" style="display: none;" class="input">
                        <span class="material-icons-round">menu_book</span>
                        <input class="input-data text-body font-jakarta font-semibold" type="text"
                            placeholder="Cari Bahan" readonly>
                    </div>
                    <div id="InputBerat" style="display: none;" class="input input-scale">
                        <div class="input-scale-text flex flex-row gap-4">
                            <span class="material-icons-round text-secondary-normal">scale</span>
                            <div class="vertical-line bg-secondary-normal"></div>
                            <p class="font-jakarta text-body text-secondary-normal">Berat Gram</p>
                        </div>
                        <div class="input-scale-input flex flex-row gap-4">
                            <span class="material-icons-round">add_circle_outline</span>
                            <input class="input-number text-body font-jakarta font-semibold" type="number" size="4"
                                placeholder="20">
                            <span class="material-icons-round">remove_circle_outline</span>
                        </div>
                    </div>
                    <button type="button" class="btn-add-bahan mt-4 px-4 py-2 bg-orange-normal text-white rounded" style="display: none;">
                        Tambah Bahan
                    </button>
                </div>
            </div>
            <!-- Form 2 End -->

            <!-- Form 3 Start -->
            <div class="results" id="result-3" style="display: none;">
                <div class="input-wrapper flex flex-col">
                    <h5 class="font-jakarta text-title2 font-regular text-secondary-normal">Filter yang dipilih
                    </h5>
                    <div class="wrapper-result flex flex-row">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>
            <div class="form" id="form-3" style="display: none;">
                <div class="input-wrapper flex flex-col">
                    <h5 class="font-jakarta text-title2 font-regular text-secondary-normal">Pilih filterisasi resep
                    </h5>
                    <div class="input-dropdown">
                        <div class="input">
                            <span class="material-icons-round">search</span>
                            <input class="input-data text-body font-jakarta font-semibold" type="text"
                                placeholder="Cari filter">
                            <span class="material-icons-round">expand_circle_down</span>
                        </div>
                        <div class="dropdown-datas">
                            @foreach ($filters as $filter)
                            <div class="dropdown-data" data-filter-id="{{ $filter->id }}">
                                <p class="font-jakarta font-semibold text-body text-primary-dark-active">{{ $filter->title }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <!-- Form 3 End -->

            <!-- Form 4 Start -->
            <div class="results" id="result-4-1" style="display: none;">
                <div class="wrapper-result fix flex flex-col">
                </div>
                <div class="wrapper-result temp flex flex-col" style="display: none;">
                    <div class="result-bahan temp flex flex-row">
                        <p class="font-jakarta font-regular text-body"></p>
                        <div class="vertical-line"></div>
                        <p class="font-jakarta font-regular text-body"></p>
                    </div>
                </div>
                <div class="wrapper-result extra-step flex flex-col" style="display: none;">
                </div>
            </div>
            <div class="results" id="result-4-2" style="display: none;">
                <div class="input-wrapper flex flex-col">
                    <h5 class="font-jakarta text-title2 font-regular text-secondary-normal">Bahan yang digunakan di langkah ini
                    </h5>
                    <div class="wrapper-result flex flex-row">
                    </div>
                </div>
            </div>
            <div class="form" id="form-4-1" style="display: none;">
                <div class="input-wrapper flex flex-col">
                    <h5 class="font-jakarta text-title2 font-regular text-secondary-normal">Tulis langkah pembuatan
                    </h5>
                    <textarea placeholder="Jelaskan langkah" name="langkah-pembuatan" id="input-langkah-pembuatan"
                        class="long-input text-body font-jakarta font-semibold"></textarea>
                    <div class="horizontal-line "></div>
                </div>
                <div class="input-wrapper flex flex-col" style="gap: 0.5rem;">
                    <h5 class="font-jakarta text-title2 font-regular text-secondary-normal">
                        Berapa perkiraan langkah ini memakan waktu?
                    </h5>
                    <div class="duration-picker">
                        <div class="duration-unit">
                            <button type="button" class="dur-btn dur-up" data-target="dur-jam">
                                <span class="material-icons-round">expand_less</span>
                            </button>
                            <input class="dur-input font-jakarta font-semibold" id="dur-jam" type="number" min="0" max="23" value="0" readonly>
                            <button type="button" class="dur-btn dur-down" data-target="dur-jam">
                                <span class="material-icons-round">expand_more</span>
                            </button>
                            <span class="dur-label font-jakarta font-regular">jam</span>
                        </div>

                        <div class="dur-divider">:</div>

                        <div class="duration-unit">
                            <button type="button" class="dur-btn dur-up" data-target="dur-menit">
                                <span class="material-icons-round">expand_less</span>
                            </button>
                            <input class="dur-input font-jakarta font-semibold" id="dur-menit" type="number" min="0" max="59" value="10" readonly>
                            <button type="button" class="dur-btn dur-down" data-target="dur-menit">
                                <span class="material-icons-round">expand_more</span>
                            </button>
                            <span class="dur-label font-jakarta font-regular">menit</span>
                        </div>

                        <div class="dur-divider">:</div>

                        <div class="duration-unit">
                            <button type="button" class="dur-btn dur-up" data-target="dur-detik">
                                <span class="material-icons-round">expand_less</span>
                            </button>
                            <input class="dur-input font-jakarta font-semibold" id="dur-detik" type="number" min="0" max="59" value="0" readonly>
                            <button type="button" class="dur-btn dur-down" data-target="dur-detik">
                                <span class="material-icons-round">expand_more</span>
                            </button>
                            <span class="dur-label font-jakarta font-regular">detik</span>
                        </div>
                    </div>

                    {{-- Hidden input untuk kompatibilitas dengan JS yang sudah ada --}}
                    <input type="hidden" id="timeInput" value="00:10:00">
                    <span id="duration-label" class="font-jakarta font-regular text-body" style="color: var(--secondary-normal);"></span>
                </div>
                <button type="button" class="btn-add-step self-stretch mb-4 px-4 py-2 bg-orange-normal text-white rounded">
                    Tambah Langkah
                </button>
            </div>
            <div class="form" id="form-4-2" style="display: none;">
                <div class="input-wrapper flex flex-col">
                    <h5 class="font-jakarta text-title2 font-regular text-secondary-normal">Bahan yang digunakan di langkah ini
                    </h5>
                    <div class="input-dropdown">
                        <div class="input">
                            <span class="material-icons-round">search</span>
                            <input class="input-data text-body font-jakarta font-semibold" type="text"
                                placeholder="Cari bahan">
                            <span class="material-icons-round">expand_circle_down</span>
                        </div>
                        <div class="dropdown-datas">

                        </div>
                    </div>
                </div>
                <button type="button" class="btn-add-bahan-step self-stretch mt-4 px-4 py-2 bg-orange-normal text-white rounded" style="display: none;">
                    Simpan Langkah
                </button>
            </div>
            <!-- Form 4 End -->

            <!-- Form 5 Start -->
            <div class="form" id="form-5" style="display: none;">
                <div class="input-wrapper flex flex-col">
                    <h5 class="font-jakarta text-title2 font-regular text-secondary-normal">Tambahkan Foto/Video</h5>
                    <div class="flex flex-col gap-1">
                        <div class="upload-container">
                            <input type="file" id="file-upload" hidden accept="image/*,video/*" multiple>

                            {{-- Default upload box --}}
                            <label for="file-upload" class="upload-box" id="upload-box-label">
                                <div class="upload-content">
                                    <span class="material-icons-round add-icon">add_circle_outline</span>
                                    <p class="font-jakarta text-body">Tambahkan Foto/Video</p>
                                </div>
                            </label>

                            {{-- Preview area (hidden by default) --}}
                            <div id="upload-preview-area" style="display:none; position:relative; width:100%; border-radius:0.4rem; overflow:hidden; aspect-ratio:16/9;">
                                <img id="preview-img" src="" style="width:100%; height:100%; object-fit:cover; display:none;">
                                <video id="preview-vid" src="" controls style="width:100%; height:100%; object-fit:cover; display:none;"></video>
                                <button id="preview-close" type="button" style="position:absolute; top:0.5rem; right:0.5rem; background:rgba(0,0,0,0.5); border:none; border-radius:50%; width:2rem; height:2rem; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                                    <span class="material-icons-round" style="color:white; font-size:1.1rem;">close</span>
                                </button>
                            </div>
                            <p class="font-jakarta text-[0.8rem] text-orange-normal">Hasil Preview sesuai dengan apa yang akan diterima oleh pengguna</p>
                        </div>

                        <div class="upload-wrapper flex flex-row">
                            <div class="upload-default">
                                <span class="material-icons-round add-icon">add_circle_outline</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Form 5 End -->

            <div id="resep-submit-loading" style="display:none; justify-content: center;">
                <div class="resep-loading-wrap">
                    <div class="resep-loading-card">

                        {{-- Icon orbit --}}
                        <div class="resep-loading-orbit">
                            <div class="resep-loading-icon-center">
                                <span class="material-icons-round">outdoor_grill</span>
                            </div>
                            <div class="resep-orbit-dot"></div>
                            <div class="resep-orbit-dot-2"></div>
                        </div>

                        {{-- Teks --}}
                        <div class="resep-loading-text">
                            <p class="font-jakarta font-semibold resep-loading-title">Resep sedang dikirimkan</p>
                            <p class="font-jakarta font-regular resep-loading-sub">
                                Mohon tunggu sebentar
                                <span class="resep-dot"></span>
                                <span class="resep-dot"></span>
                                <span class="resep-dot"></span>
                            </p>
                        </div>

                        {{-- Progress bar --}}
                        <div class="resep-loading-progress">
                            <div class="resep-progress-label">
                                <span class="font-jakarta">Mengupload foto &amp; video</span>
                                <span class="material-icons-round resep-spin" style="font-size: 1rem;">autorenew</span>
                            </div>
                            <div class="resep-progress-track">
                                <div class="resep-progress-fill"></div>
                            </div>
                        </div>

                        {{-- Tombol kembali --}}
                        <div class="resep-loading-footer">
                            <a href="{{ route('profile.index') }}" class="resep-loading-back font-jakarta">
                                <span class="material-icons-round" style="font-size: 1rem;">arrow_back</span>
                                Kembali ke profil
                            </a>
                        </div>

                    </div>
                    <p class="resep-loading-note font-jakarta">Jangan tutup halaman ini sebelum proses selesai</p>
                </div>
            </div>
        </div>
    </section>
    <div class="input-submit">
        <h1 class="font-jakarta">Lanjut</h1>
    </div>
</main>

@endsection
@push('scripts')
<script src="{{ asset('js/pages/tambah-resep.js') }}"></script>
@endpush