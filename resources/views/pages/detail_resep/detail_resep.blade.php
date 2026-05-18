@extends('layouts.app')

@section('title', 'Detail Resep - LaperPoll')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/detail-resep.css') }}">
@endpush

{{-- [UBAH] data-recipe-id: dari hardcoded "roti-bakar-keju" → $resep->id --}}
<main class="main-content flex flex-col font-jakarta" data-recipe-id="{{ $resep->id }}">
  <x-navbar :back="true"></x-navbar>

  <section>
    <div class="recipe-header-container">
      <div class="header-left">
        <h1 class="recipe-title">{{ $resep->title }}</h1>
        <div class="recipe-meta">
          <span class="meta-item">
            <span class="material-icons-round">timer</span>{{ $resep->cook_duration }}
          </span>
          
          <span class="meta-item">
            <span class="material-icons-round">icecream</span>
            {{ $resep->resep_filters}}
          </span>
        </div>
      </div>

      <div class="header-right">
        <div class="author-section">
          <div class="author-text">
            <span class="created-by font-jakarta">Dibuat oleh</span>
            {{-- [UBAH] Username: dari hardcoded "@RotiRoni" → relasi user --}}
            <span class="author-username font-jakarta font-semibold">
              {{ $resep->user->name ?? 'Anonim' }}
            </span>
          </div>
          
          <img
            src="{{ $resep->user->avatar
              ? Storage::url($resep->user->avatar)
              : asset('assets/images/Image_DummyProfile.png') }}"
            alt="Author"
            class="author-avatar">
        </div>
        

        <div class="rating-section">
          
          <div class="stars">
            @php
              $star  = $resep->current_star;
              $full  = floor($star);
              $half  = ($star - $full) >= 0.3 ? 1 : 0;
              $empty = 5 - $full - $half;
            @endphp

            @for ($i = 0; $i < $full; $i++)
              <span class="material-icons-round">star</span>
            @endfor

            @if ($half)
              <span class="material-icons-round">star_half</span>
            @endif

            @for ($i = 0; $i < $empty; $i++)
              <span class="material-icons-round">star_border</span>
            @endfor
          </div>
          <span class="rating-score">{{ $resep->current_star }}</span>
        </div>
      </div>
    </div>
  </section>

  <section class="recipe-hero">
    <img
      src="{{ asset($resep->thumbnail) }}"
      alt="{{ $resep->title }}"
      class="hero-image">
    <div class="hero-overlay">
      <span class="material-icons-round favorite-icon">favorite_border</span>
      <span class="material-icons-round play-icon">play_circle_outline</span>
    </div>
    <div class="carousel-dots">
      <span class="dot active"></span>
      <span class="dot"></span>
      <span class="dot"></span>
    </div>
  </section>

  <section class="sidebar-card flex-flex-col">
    <div class="sidebar-card-header">
      <span class="sidebar-section-title font-jakarta font-semibold">Bahan-bahan</span>
      <div class="unit-toggle wrapper font-jakarta">
        <span class="unit-icon">&#9878</span>
        <select class="unit-select">
          <option value="gram">Gram</option>
          <option value="miligram">Miligram</option>
          <option value="kilogram">Kilogram</option>
          <option value="sendok_makan">Sdm</option>
        </select>
      </div>
    </div>

    {{--
      [UBAH] Chips bahan: dari hardcoded → loop dari relasi bahans (belongsToMany)
      Akses gram pakai ->pivot->gram_total karena relasinya many-to-many dengan withPivot
    --}}
    <div class="chips-grid">
      @forelse ($resep->bahans as $bahan)
        <div class="chip font-jakarta">
          <span class="amt">{{ $bahan->pivot->gram_total }}g</span>
          {{ $bahan->nama }}
        </div>
      @empty
        <p class="font-jakarta text-sm">Bahan belum tersedia.</p>
      @endforelse
    </div>
  </section>

  {{--
    [UBAH] Langkah memasak: dari hardcoded → loop dari relasi langkahs
    Diurutkan berdasarkan step_order, kolom isi adalah description
    step_duration ditampilkan jika ada isinya
  --}}
  <section>
    <div class="steps-card">
      <h2 class="section-title font-jakarta font-semibold">Cara Membuat</h2>

      @forelse ($resep->langkahs->sortBy('step_order') as $langkah)
        <div class="step-item">
          <div class="step-num font-jakarta font-bold">{{ $langkah->step_order }}</div>
          <div class="step-body">
         
              <div class="step-title">
              </div>
            <p class="step-text font-jakarta font-semibold">{{ $langkah->description }}</p>
          </div>
        </div>
      @empty
        <p class="font-jakarta text-sm">Langkah memasak belum tersedia.</p>
      @endforelse

    </div>
  </section>

  <section>
    <a href="{{ url('/timer-resep') }}">
      <button class="button font-jakarta font-semibold">
        Buat sekarang
        <span class="arrow-forward material-icons-round">arrow_forward</span>
      </button>
    </a>
  </section>

</main>

<script src="{{ asset('js/detail-resep.js') }}"></script>

</html>