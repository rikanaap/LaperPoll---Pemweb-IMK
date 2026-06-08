@extends('layouts.app')

@section('title', 'Edit Resep — ' . $resep->title . ' | LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages/edit-resep.css') }}">
@endpush

@section('content')
<div class="er-page font-jakarta">

    {{-- NAVBAR --}}
    <x-navbar backUrl="{{ route('profile.index') }}" :hamburger="false"></x-navbar>

    {{-- PAGE HEADER --}}
    <div class="er-page-header">
        <div class="er-page-header-inner">
            <div class="er-page-header-icon">
                <span class="material-icons-round">edit_note</span>
            </div>
            <div>
                <h1 class="er-page-title font-bold">Edit Resep</h1>
                <p class="er-page-sub">Perbarui informasi resepmu</p>
            </div>
        </div>
    </div>

    {{-- FORM --}}
    <form action="{{ route('resep.update', $resep->id) }}" method="POST" enctype="multipart/form-data"
          class="er-form" id="editResepForm">
        @csrf
        @method('PUT')

        {{-- ══════════════════════════════════
             DESKTOP: 2 kolom side-by-side
             TABLET/MOBILE: 1 kolom stack
        ══════════════════════════════════ --}}
        <div class="er-layout">

            {{-- ── KOLOM KIRI (thumbnail + info dasar) ── --}}
            <div class="er-col-left">

                {{-- Thumbnail Upload --}}
                <div class="er-card">
                    <div class="er-card-header">
                        <span class="material-icons-round er-card-icon">image</span>
                        <h2 class="er-card-title font-semibold">Foto Resep</h2>
                    </div>
                    <div class="er-thumb-upload" id="erThumbArea">
                        <input type="file" name="thumbnail" id="erThumbInput"
                               accept="image/*" class="er-thumb-input">
                        @if($resep->thumbnail)
                            <img src="{{ $resep->thumbnail_url }}" alt="Thumbnail"
                                 class="er-thumb-preview" id="erThumbPreview">
                            <div class="er-thumb-overlay" id="erThumbOverlay">
                                <span class="material-icons-round">photo_camera</span>
                                <span>Ganti Foto</span>
                            </div>
                        @else
                            <div class="er-thumb-empty" id="erThumbEmpty">
                                <span class="material-icons-round">add_photo_alternate</span>
                                <span class="er-thumb-empty-label">Tambah Foto</span>
                                <span class="er-thumb-empty-sub">JPG, PNG, WebP (maks. 2MB)</span>
                            </div>
                            <img src="" alt="Preview" class="er-thumb-preview" id="erThumbPreview" style="display:none">
                            <div class="er-thumb-overlay" id="erThumbOverlay" style="display:none">
                                <span class="material-icons-round">photo_camera</span>
                                <span>Ganti Foto</span>
                            </div>
                        @endif
                    </div>
                    @error('thumbnail')
                        <p class="er-field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Informasi Dasar --}}
                <div class="er-card">
                    <div class="er-card-header">
                        <span class="material-icons-round er-card-icon">info</span>
                        <h2 class="er-card-title font-semibold">Informasi Dasar</h2>
                    </div>

                    {{-- Nama Resep --}}
                    <div class="er-field">
                        <label class="er-label font-semibold" for="erTitle">
                            Nama Resep <span class="er-required">*</span>
                        </label>
                        <div class="er-input-wrap">
                            <span class="material-icons-round er-input-icon">restaurant_menu</span>
                            <input type="text" name="title" id="erTitle"
                                   class="er-input @error('title') er-input-error @enderror"
                                   placeholder="Contoh: Ayam Kecap Pedas"
                                   value="{{ old('title', $resep->title) }}"
                                   maxlength="100" required>
                        </div>
                        @error('title')
                            <p class="er-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kategori --}}
                    <div class="er-field">
                        <label class="er-label font-semibold" for="erKategori">
                            Kategori <span class="er-required">*</span>
                        </label>
                        <div class="er-input-wrap">
                            <span class="material-icons-round er-input-icon">category</span>
                            <select name="category_id" id="erKategori"
                                    class="er-select @error('category_id') er-input-error @enderror" required>
                                <option value="">— Pilih Kategori —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('category_id', $resep->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('category_id')
                            <p class="er-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Grid: Kalori + Waktu + Porsi --}}
                    <div class="er-field-row">
                        <div class="er-field">
                            <label class="er-label font-semibold" for="erKalori">
                                Kalori (kal)
                            </label>
                            <div class="er-input-wrap">
                                <span class="material-icons-round er-input-icon">local_fire_department</span>
                                <input type="number" name="calories" id="erKalori"
                                       class="er-input @error('calories') er-input-error @enderror"
                                       placeholder="620"
                                       value="{{ old('calories', $resep->calories) }}"
                                       min="0" max="9999">
                            </div>
                            @error('calories')
                                <p class="er-field-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="er-field">
                            <label class="er-label font-semibold" for="erWaktu">
                                Waktu (menit) <span class="er-required">*</span>
                            </label>
                            <div class="er-input-wrap">
                                <span class="material-icons-round er-input-icon">schedule</span>
                                <input type="number" name="cook_duration" id="erWaktu"
                                       class="er-input @error('cook_duration') er-input-error @enderror"
                                       placeholder="30"
                                       value="{{ old('cook_duration', $resep->cook_duration) }}"
                                       min="1" max="999" required>
                            </div>
                            @error('cook_duration')
                                <p class="er-field-error">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="er-field">
                            <label class="er-label font-semibold" for="erPorsi">
                                Porsi
                            </label>
                            <div class="er-input-wrap">
                                <span class="material-icons-round er-input-icon">people</span>
                                <input type="number" name="servings" id="erPorsi"
                                       class="er-input @error('servings') er-input-error @enderror"
                                       placeholder="2"
                                       value="{{ old('servings', $resep->servings) }}"
                                       min="1" max="99">
                            </div>
                            @error('servings')
                                <p class="er-field-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Deskripsi Singkat --}}
                    <div class="er-field">
                        <label class="er-label font-semibold" for="erDesc">Deskripsi Singkat</label>
                        <textarea name="description" id="erDesc"
                                  class="er-textarea @error('description') er-input-error @enderror"
                                  placeholder="Ceritakan sedikit tentang resepmu..."
                                  rows="3" maxlength="300">{{ old('description', $resep->description) }}</textarea>
                        <p class="er-char-count"><span id="erDescCount">{{ strlen(old('description', $resep->description ?? '')) }}</span>/300</p>
                        @error('description')
                            <p class="er-field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>{{-- /er-col-left --}}

            {{-- ── KOLOM KANAN (bahan + langkah) ── --}}
            <div class="er-col-right">

                {{-- Bahan-Bahan --}}
                <div class="er-card">
                    <div class="er-card-header">
                        <span class="material-icons-round er-card-icon">grocery</span>
                        <h2 class="er-card-title font-semibold">Bahan-Bahan</h2>
                        <button type="button" class="er-add-btn" id="btnTambahBahan">
                            <span class="material-icons-round">add</span>
                            Tambah
                        </button>
                    </div>

                    <div class="er-dynamic-list" id="bahanList">
                        @forelse(old('ingredients', $resep->ingredients ?? []) as $i => $bahan)
                            <div class="er-list-item er-bahan-item" data-index="{{ $i }}">
                                <div class="er-list-item-num">{{ $i + 1 }}</div>
                                <div class="er-bahan-inputs">
                                    <input type="text"
                                           name="ingredients[{{ $i }}][amount]"
                                           class="er-input er-input-sm"
                                           placeholder="Jumlah (mis. 200g)"
                                           value="{{ is_array($bahan) ? ($bahan['amount'] ?? '') : '' }}">
                                    <input type="text"
                                           name="ingredients[{{ $i }}][name]"
                                           class="er-input er-input-sm er-bahan-name"
                                           placeholder="Nama bahan"
                                           value="{{ is_array($bahan) ? ($bahan['name'] ?? $bahan) : $bahan }}">
                                </div>
                                <button type="button" class="er-remove-btn" onclick="removeListItem(this)">
                                    <span class="material-icons-round">close</span>
                                </button>
                            </div>
                        @empty
                            <div class="er-list-item er-bahan-item" data-index="0">
                                <div class="er-list-item-num">1</div>
                                <div class="er-bahan-inputs">
                                    <input type="text" name="ingredients[0][amount]"
                                           class="er-input er-input-sm" placeholder="Jumlah (mis. 200g)">
                                    <input type="text" name="ingredients[0][name]"
                                           class="er-input er-input-sm er-bahan-name" placeholder="Nama bahan">
                                </div>
                                <button type="button" class="er-remove-btn" onclick="removeListItem(this)">
                                    <span class="material-icons-round">close</span>
                                </button>
                            </div>
                        @endforelse
                    </div>
                    @error('ingredients')
                        <p class="er-field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Langkah Pembuatan --}}
                <div class="er-card">
                    <div class="er-card-header">
                        <span class="material-icons-round er-card-icon">format_list_numbered</span>
                        <h2 class="er-card-title font-semibold">Langkah Pembuatan</h2>
                        <button type="button" class="er-add-btn" id="btnTambahLangkah">
                            <span class="material-icons-round">add</span>
                            Tambah
                        </button>
                    </div>

                    <div class="er-dynamic-list" id="langkahList">
                        @forelse(old('steps', $resep->steps ?? []) as $i => $step)
                            <div class="er-list-item er-langkah-item" data-index="{{ $i }}">
                                <div class="er-list-item-num">{{ $i + 1 }}</div>
                                <textarea name="steps[{{ $i }}]"
                                          class="er-textarea er-textarea-sm"
                                          placeholder="Jelaskan langkah ini..."
                                          rows="2">{{ is_array($step) ? ($step['description'] ?? $step) : $step }}</textarea>
                                <button type="button" class="er-remove-btn" onclick="removeListItem(this)">
                                    <span class="material-icons-round">close</span>
                                </button>
                            </div>
                        @empty
                            <div class="er-list-item er-langkah-item" data-index="0">
                                <div class="er-list-item-num">1</div>
                                <textarea name="steps[0]"
                                          class="er-textarea er-textarea-sm"
                                          placeholder="Jelaskan langkah ini..."
                                          rows="2"></textarea>
                                <button type="button" class="er-remove-btn" onclick="removeListItem(this)">
                                    <span class="material-icons-round">close</span>
                                </button>
                            </div>
                        @endforelse
                    </div>
                    @error('steps')
                        <p class="er-field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tips Tambahan --}}
                <div class="er-card">
                    <div class="er-card-header">
                        <span class="material-icons-round er-card-icon">tips_and_updates</span>
                        <h2 class="er-card-title font-semibold">Tips & Catatan</h2>
                        <span class="er-optional-badge">Opsional</span>
                    </div>
                    <div class="er-field">
                        <textarea name="tips" id="erTips"
                                  class="er-textarea @error('tips') er-input-error @enderror"
                                  placeholder="Tips, variasi, atau catatan tambahan untuk pembuat..."
                                  rows="3" maxlength="500">{{ old('tips', $resep->tips) }}</textarea>
                        @error('tips')
                            <p class="er-field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>{{-- /er-col-right --}}

        </div>{{-- /er-layout --}}

        {{-- ── ACTION BAR (sticky bottom) ── --}}
        <div class="er-action-bar">
            <div class="er-action-inner">
                <a href="{{ route('profile.index') }}" class="er-btn-cancel">
                    <span class="material-icons-round">close</span>
                    <span>Batal</span>
                </a>
                <button type="submit" class="er-btn-save" id="erBtnSave">
                    <span class="material-icons-round">check</span>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/edit-resep.js') }}"></script>
@endpush