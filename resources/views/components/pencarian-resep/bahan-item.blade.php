{{--
    Component: x-pencarian-resep.bahan-item
    Satu item bahan di daftar pencarian.

    Props:
      $bahan     → Bahan model instance
      $isChecked → bool (default: false)
--}}

@props([
    'bahan',
    'isChecked' => false,
])

<div class="bahan-item {{ $isChecked ? 'active' : '' }}">
    <div class="bahan-left">
        <span class="bahan-nama">{{ $bahan->nama }}</span>
    </div>
    <input
        type="checkbox"
        id="bahan-{{ $bahan->id }}"
        data-id="{{ $bahan->id }}"
        data-nama="{{ $bahan->nama }}"
        {{ $isChecked ? 'checked' : '' }}
    >
</div>