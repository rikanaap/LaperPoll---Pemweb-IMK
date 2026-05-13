<div class="bahan-item">
    <div class="bahan-left">
        <div class="bahan-icon">
            <span class="material-icons-round">restaurant</span>
        </div>
        <span class="bahan-nama">{{ $bahan->nama }}</span>
    </div>

    <input 
        type="checkbox" 
        value="{{ $bahan->id }}" 
        data-id="{{ $bahan->id }}" 
        data-nama="{{ $bahan->nama }}"
    >
</div>