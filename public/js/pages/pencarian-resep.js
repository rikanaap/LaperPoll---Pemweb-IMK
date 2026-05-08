/**
 * LaperPoll - Pencarian Resep Logic
 * UAS Project - Ikbal Miftahudin
 */

// 1. State Management (Sumber kebenaran data)
let state = {
    selectedBahan: [], // Isinya {id, nama}
    isMobile: window.innerWidth < 768
};

// 2. DOM Elements
const elements = {
    searchInput: document.getElementById('searchInput'),
    bahanList: document.querySelector('.bahan-list'),
    btnApply: document.getElementById('terapkanBtn'),
    btnHapus: document.getElementById('hapusSemuaBtn'),
    selectedInfo: document.getElementById('selectedInfo'),
    chipsWrapper: document.getElementById('selectedChips'),
    resultInfoText: document.getElementById('resultInfoText'),
    resepContainer: document.getElementById('resepContainer'),
    resultPlaceholder: document.getElementById('resultPlaceholder'),
    loadingState: document.getElementById('loadingState'),
};

// 3. UI Functions (Hanya urusan visual)
const ui = {
    updateItemActiveState: () => {
        document.querySelectorAll('.bahan-item').forEach(item => {
            const checkbox = item.querySelector('input');
            item.classList.toggle('active', checkbox.checked);
        });
    },

    renderSelectedUI: () => {
        const total = state.selectedBahan.length;
        
        // Update Info Text & Button
        if (elements.selectedInfo) {
            elements.selectedInfo.style.display = total > 0 ? 'block' : 'none';
            elements.selectedInfo.textContent = `${total} bahan telah terpilih`;
        }

        [elements.btnApply, elements.btnHapus].forEach(btn => {
            if (btn) {
                btn.disabled = total === 0;
                btn.classList.toggle('disabled', total === 0);
            }
        });

        // Render Chips & Results (Desktop Only)
        if (!state.isMobile) {
            ui.renderChips();
            ui.updateResultHeaderText();
            ui.fetchResepSimulation();
        }
    },

    renderChips: () => {
        if (!elements.chipsWrapper) return;
        elements.chipsWrapper.innerHTML = state.selectedBahan.map(bahan => `
            <div class="chip">
                <span>${bahan.nama}</span>
                <span class="material-icons-round chip-close" data-nama="${bahan.nama}">close</span>
            </div>
        `).join('');
    },

    updateResultHeaderText: () => {
        if (!elements.resultInfoText) return;
        elements.resultInfoText.textContent = state.selectedBahan.length > 0 
            ? `Menampilkan resep dengan bahan: ${state.selectedBahan.map(b => b.nama).join(', ')}`
            : 'Pilih bahan untuk melihat resep';
    },

    fetchResepSimulation: () => {
        if (state.selectedBahan.length === 0) {
            elements.resepContainer.classList.add('hidden');
            elements.resultPlaceholder.style.display = 'block';
            return;
        }

        elements.loadingState.classList.remove('hidden');
        elements.resepContainer.classList.add('hidden');
        elements.resultPlaceholder.style.display = 'none';

        // Simulasi loading API
        setTimeout(() => {
            elements.loadingState.classList.add('hidden');
            elements.resepContainer.classList.remove('hidden');
        }, 800);
    }
};

// 4. Logic Functions
const logic = {
    syncStateFromDOM: () => {
        state.selectedBahan = [];
        document.querySelectorAll('.bahan-item input:checked').forEach(cb => {
            const nama = cb.closest('.bahan-item').querySelector('.bahan-nama').textContent.trim();
            state.selectedBahan.push({ id: cb.value, nama: nama });
        });
        ui.updateItemActiveState();
        ui.renderSelectedUI();
    },

    handleSearch: (e) => {
        const keyword = e.target.value.toLowerCase();
        document.querySelectorAll('.bahan-item').forEach(item => {
            const nama = item.querySelector('.bahan-nama').textContent.toLowerCase();
            item.style.display = nama.includes(keyword) ? 'flex' : 'none';
        });
    }
};

// 5. Event Listeners (Event Delegation Pattern)
const initEvents = () => {
    // Search input
    elements.searchInput?.addEventListener('input', logic.handleSearch);

    // Event Delegation: Klik di area bahan-list
    elements.bahanList?.addEventListener('click', (e) => {
        const item = e.target.closest('.bahan-item');
        if (!item) return;

        const checkbox = item.querySelector('input');
        // Jika klik bukan di checkbox, kita toggle manual
        if (e.target.tagName !== 'INPUT') {
            checkbox.checked = !checkbox.checked;
        }
        logic.syncStateFromDOM();
    });

    // Chips close button delegation
    elements.chipsWrapper?.addEventListener('click', (e) => {
        if (e.target.classList.contains('chip-close')) {
            const nama = e.target.dataset.nama;
            document.querySelectorAll('.bahan-item').forEach(item => {
                if (item.querySelector('.bahan-nama').textContent.trim() === nama) {
                    item.querySelector('input').checked = false;
                }
            });
            logic.syncStateFromDOM();
        }
    });

    // Hapus Semua
    elements.btnHapus?.addEventListener('click', () => {
        document.querySelectorAll('.bahan-item input').forEach(cb => cb.checked = false);
        logic.syncStateFromDOM();
    });

    // Terapkan (Mobile Redirect)
    elements.btnApply?.addEventListener('click', () => {
        if (state.isMobile) {
            const query = state.selectedBahan.map(b => b.nama).join(',');
            window.location.href = `/filter-resep?bahan=${encodeURIComponent(query)}`;
        }
    });

    // Window Resize
    window.addEventListener('resize', () => {
        state.isMobile = window.innerWidth < 768;
    });
};

// 6. Init
document.addEventListener('DOMContentLoaded', () => {
    initEvents();
    logic.syncStateFromDOM(); // Sinkronkan jika ada checkbox yang tercentang dari awal (misal: back button browser)
});