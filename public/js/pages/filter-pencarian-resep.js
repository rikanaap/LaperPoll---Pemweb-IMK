const urlParams = new URLSearchParams(window.location.search);
const bahanParam = urlParams.get('bahan') || '';

const elements = {
    chipsContainer: document.getElementById('chipsContainer'),
    resepList: document.getElementById('resepList'),
    resultInfo: document.getElementById('resultInfo'),
    emptyState: document.getElementById('emptyState'),
    loadingState: document.getElementById('loadingState'),
};

function init() {
    // 1. Validasi awal sebelum loading
    if (!bahanParam.trim()) {
        showEmpty('Pilih bahan terlebih dahulu');
        hideLoading();
        return;
    }

    showLoading();

    // 2. Simulasi delay loading
    setTimeout(() => {
        renderPage();
    }, 800); // Kurangi sedikit biar gak kelamaan
}

function renderPage() {
    hideLoading();
    
    const bahanList = bahanParam.split(',')
        .map(s => s.trim())
        .filter(s => s !== '');

    if (bahanList.length === 0) {
        showEmpty('Tidak ada bahan dipilih');
        return;
    }

    renderChips(bahanList);
    updateResultView(bahanList);
}

function renderChips(list) {
    elements.chipsContainer.innerHTML = '';

    list.forEach(nama => {
        const chip = document.createElement('div');
        chip.className = 'chip';

        // Gunakan textContent untuk keamanan XSS
        const spanText = document.createElement('span');
        spanText.textContent = nama;

        const closeBtn = document.createElement('span');
        closeBtn.className = 'material-icons-round chip-close';
        closeBtn.textContent = 'close';
        
        closeBtn.onclick = () => {
            const updated = list.filter(item => item !== nama);
            const query = updated.join(',');
            window.location.href = updated.length > 0 
                ? `/filter-resep?bahan=${encodeURIComponent(query)}`
                : '/pencarian-resep'; // Balik ke awal kalau kosong
        };

        chip.appendChild(spanText);
        chip.appendChild(closeBtn);
        elements.chipsContainer.appendChild(chip);
    });
}

function updateResultView(list) {
    const totalResep = elements.resepList.querySelectorAll('.resep').length;
    
    if (totalResep === 0) {
        showEmpty('Resep tidak ditemukan dengan bahan tersebut');
    } else {
        elements.resepList.classList.remove('hidden');
        elements.emptyState.classList.add('hidden');
        elements.resultInfo.textContent = `Menampilkan ${totalResep} resep dari: ${list.join(', ')}`;
    }
}

function showEmpty(message) {
    elements.resepList.classList.add('hidden');
    elements.emptyState.classList.remove('hidden');
    elements.resultInfo.textContent = message;
}

function showLoading() {
    elements.loadingState.classList.remove('hidden');
    elements.resepList.classList.add('hidden');
    elements.emptyState.classList.add('hidden');
}

function hideLoading() {
    elements.loadingState.classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', init);