{{--
    Component: x-pencarian-resep.empty-state
    Placeholder kosong reusable.

    Props:
      $id      → string (default: 'emptyState')
      $icon    → string material icon name (default: 'restaurant_menu')
      $title   → string (default: 'Tidak ada hasil')
      $message → string (default: '')
      $hidden  → bool   (default: false)
--}}

@props([
    'id'      => 'emptyState',
    'icon'    => 'restaurant_menu',
    'title'   => 'Tidak ada hasil',
    'message' => '',
    'hidden'  => false,
])

<div
    id="{{ $id }}"
    class="result-placeholder {{ $hidden ? 'hidden' : '' }}"
>
    <span class="material-icons-round">{{ $icon }}</span>
    <h3>{{ $title }}</h3>
    @if ($message)
        <p>{{ $message }}</p>
    @endif
</div>