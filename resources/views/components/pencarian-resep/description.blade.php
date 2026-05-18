{{--
    Component: x-pencarian-resep.description
    Preview deskripsi resep saat tidak ada filter bahan.

    Props:
      $text → string
--}}

@props(['text'])

<div class="resep-preview-info">
    <p class="preview-text">{{ $text }}</p>
</div>