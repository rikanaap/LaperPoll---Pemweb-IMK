{{--
    Component: x-pencarian-resep.match-info
    Menampilkan info kecocokan bahan saat pencarian by bahan aktif.

    Props:
      $matchPercent  → int (0-100)
      $matchedBahan  → int
      $totalBahan    → int
      $matchStatus   → array ['class' => string, 'label' => string]
      $missingBahans → array [['id' => int, 'nama' => string], ...]
--}}

@props([
    'matchPercent',
    'matchedBahan',
    'totalBahan',
    'matchStatus',
    'missingBahans' => [],
])

<div class="match-wrapper">
    <div class="match-header">
        <div class="match-percent">
            <div class="match-percent-circle">{{ $matchPercent }}%</div>
            <div class="match-percent-info">
                <h4>Kecocokan</h4>
                <p>{{ $matchedBahan }} / {{ $totalBahan }} bahan terpenuhi untuk resep ini</p>
            </div>
        </div>
        <div class="match-status {{ $matchStatus['class'] }}">{{ $matchStatus['label'] }}</div>
    </div>

    @if (count($missingBahans) > 0)
        <div class="missing-section">
            <div class="missing-label">
                <span class="material-icons-round">kitchen</span>
                <span>Bahan belum tersedia</span>
            </div>
            <div class="missing-chips">
                @foreach ($missingBahans as $item)
                    <div class="missing-chip">{{ $item['nama'] }}</div>
                @endforeach
            </div>
        </div>
    @else
        <div class="perfect-match">
            <span class="material-icons-round">verified</span>
            <span>Bahan lengkap! 🎉</span>
        </div>
    @endif
</div>