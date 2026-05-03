<div class="bahan-item">
    <div class="bahan-left">
        <div class="bahan-icon">
            <span class="material-icons-round">restaurant</span>
        </div>
        <span class="bahan-nama">{{ $nama }}</span>
    </div>
    
    {{-- Input checkbox dinamis --}}
    <input type="checkbox" name="bahan[]" value="{{ $id ?? strtolower($nama) }}">
</div>