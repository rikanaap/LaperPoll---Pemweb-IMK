{{--
    Komponen: swipe-resep/progress
    Props:
        $mobileId - string : ID elemen progress bar mobile (default: mobileProgressBar)
        $desktopId - string : ID elemen progress bar desktop (default: progressBar)
        $counterId - string : ID counter text (default: counterText)
        $max       - int   : Batas pilihan (default: 3)
--}}
@props([
    'mobileId'  => 'mobileProgressBar',
    'desktopId' => 'progressBar',
    'counterId' => 'counterText',
    'max'       => 3,
])

<div class="progress-box" data-max="{{ $max }}">
    <div class="progress-box__label">
        <span>Batas Pilihan</span>
        <span id="{{ $counterId }}">0 / {{ $max }}</span>
    </div>
    <div class="progress-box__track">
        <div id="{{ $desktopId }}" class="progress-box__fill"></div>
    </div>
</div>