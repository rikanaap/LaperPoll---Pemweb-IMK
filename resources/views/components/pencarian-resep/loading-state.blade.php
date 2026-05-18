{{--
    Component: x-pencarian-resep.loading-state
    Spinner loading reusable.

    Props:
      $id      → string (default: 'loadingState')
      $message → string (default: 'Sedang mencari resep...')
      $hidden  → bool   (default: true)
--}}

@props([
    'id'      => 'loadingState',
    'message' => 'Sedang mencari resep...',
    'hidden'  => true,
])

<div
    id="{{ $id }}"
    class="loading-state {{ $hidden ? 'hidden' : '' }}"
    aria-live="polite"
>
    <div class="loading-spinner"></div>
    <p>{{ $message }}</p>
</div>