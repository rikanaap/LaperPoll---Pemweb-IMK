{{--
    Component: x-pencarian-resep.load-more
    Tombol "Muat Lebih Banyak".

    Props:
      $id     → string (default: 'loadMoreWrapper')
      $label  → string (default: 'Muat Lebih Banyak')
      $hidden → bool   (default: true)
--}}

@props([
    'id'     => 'loadMoreWrapper',
    'label'  => 'Muat Lebih Banyak',
    'hidden' => true,
])

<div id="{{ $id }}" class="load-more-wrapper {{ $hidden ? 'hidden' : '' }}">
    <button id="loadMoreBtn" class="load-more-btn" type="button">
        {{ $label }}
    </button>
</div>