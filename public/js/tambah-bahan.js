// tambah-bahan.js

(function () {
    'use strict';

    // ── Parse data bahan dari blade ──────────────────────────────────────
    const bahanDataJson = document.getElementById('bahanData')?.textContent || '[]';
    let bahanList = [];
    try { bahanList = JSON.parse(bahanDataJson); } catch (e) { console.warn(e); }

    // ── Element refs ─────────────────────────────────────────────────────
    const searchInput      = document.getElementById('searchBahan');
    const dropdown         = document.getElementById('bahanDropdown');
    const clearBtn         = document.getElementById('clearSearch');
    const bahanIdInput     = document.getElementById('bahanId');
    const jumlahAngka      = document.getElementById('jumlahAngka');
    const boughtDateInput  = document.getElementById('boughtDate');
    const expiredDateInput = document.getElementById('expiredDate');
    const expiredChips     = document.getElementById('expiredChips');
    const expiredHint      = document.getElementById('expiredHint');
    const form             = document.getElementById('formTambahBahan');

    // Toggle tanggal
    const dateTypeToggleBtns = document.querySelectorAll('.tb-date-toggle-btn');
    const sectionBought  = document.getElementById('sectionBoughtDate');
    const sectionExpired = document.getElementById('sectionExpiredDate');

    let selectedBahan     = null;
    let defaultExpiryDays = null;
    let activeDateType    = 'bought'; // 'bought' | 'expired'

    // ── COUNTER (step 50 gram) ────────────────────────────────────────────
    document.getElementById('btnPlus')?.addEventListener('click', () => {
        jumlahAngka.value = (parseInt(jumlahAngka.value) || 0) + 50;
        enforceBounds();
    });

    document.getElementById('btnMinus')?.addEventListener('click', () => {
        jumlahAngka.value = Math.max(1, (parseInt(jumlahAngka.value) || 50) - 50);
    });

    jumlahAngka?.addEventListener('input', enforceBounds);

    function enforceBounds() {
        const v = parseInt(jumlahAngka?.value);
        if (!v || v < 1) jumlahAngka.value = 1;
        if (v > 99999)   jumlahAngka.value = 99999;
    }

    // ── TOGGLE TIPE TANGGAL ───────────────────────────────────────────────
    const dateModeInput = document.getElementById('dateMode');

    dateTypeToggleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            dateTypeToggleBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            activeDateType = btn.dataset.type;

            if (dateModeInput) dateModeInput.value = activeDateType === 'bought' ? 'beli' : 'expired';

            if (activeDateType === 'bought') {
                sectionBought.style.display  = '';
                sectionExpired.style.display = 'none';
                if (expiredDateInput) expiredDateInput.value = '';
                if (boughtDateInput && !boughtDateInput.value) {
                    boughtDateInput.value = new Date().toISOString().split('T')[0];
                }
                setWrapHeight(false);
            } else {
                sectionBought.style.display  = 'none';
                sectionExpired.style.display = '';
                if (boughtDateInput) boughtDateInput.value = '';
                if (selectedBahan?.has_expiry && defaultExpiryDays) {
                    showExpiredChips(defaultExpiryDays);
                    autoFillExpired(defaultExpiryDays);
                }
            }
        });
    });

    // ── DROPDOWN SEARCH ───────────────────────────────────────────────────
    function closeDropdown() {
        if (dropdown) dropdown.style.display = 'none';
    }

   function renderDropdown(results, query) {
    if (!dropdown) return;
    dropdown.innerHTML = '';

    results.forEach(b => {
        const li = document.createElement('li');
        li.textContent = b.nama;
        li.addEventListener('click', (e) => { e.stopPropagation(); selectBahan(b); });
        dropdown.appendChild(li);
    });

    // ── DESAIN BARU: ACTION ROW UNTUK BAHAN BARU ──
    if (results.length === 0 && query) {
        const li = document.createElement('li');
        li.className = 'tb-add-new';
        
        // Menggunakan struktur HTML baru yang lebih kokoh dan mudah di-styling via CSS
        li.innerHTML = `
            <span class="material-icons-round tb-add-icon">add_circle</span>
            <div class="tb-add-text-wrapper">
                <span class="tb-add-action-text">Tambah</span>
                <span class="tb-add-target-badge">"${query}"</span>
                <span class="tb-add-action-text">sebagai bahan baru</span>
            </div>
        `;
        
        li.addEventListener('click', (e) => { 
            e.stopPropagation(); 
            closeDropdown(); 
            showKonfirmasiModal(query); 
        });
        dropdown.appendChild(li);
    }

    dropdown.style.display = results.length > 0 || query ? 'block' : 'none';
}

    function selectBahan(bahan) {
        selectedBahan      = bahan;
        searchInput.value  = bahan.nama;
        bahanIdInput.value = bahan.id;
        if (clearBtn) clearBtn.style.display = 'block';
        closeDropdown();

        if (bahan.has_expiry && bahan.expired_expectancy_day) {
            defaultExpiryDays = bahan.expired_expectancy_day;
            if (expiredHint) expiredHint.textContent = `Rekomendasi: ${bahan.expired_expectancy_day} hari dari tanggal beli`;
            if (activeDateType === 'expired') {
                showExpiredChips(bahan.expired_expectancy_day);
                autoFillExpired(bahan.expired_expectancy_day);
            }
        } else {
            defaultExpiryDays = null;
            if (expiredChips) { expiredChips.innerHTML = ''; expiredChips.style.display = 'none'; }
            if (expiredHint) expiredHint.textContent = 'Isi tanggal kedaluwarsa bahan ini';
            setWrapHeight(false);
        }
    }

    // ── EXPIRED CHIPS ─────────────────────────────────────────────────────
    const dateSectionWrap = document.querySelector('.tb-date-section-wrap');

    function setWrapHeight(hasChips) {
        if (!dateSectionWrap) return;
        dateSectionWrap.style.minHeight = hasChips ? '12rem' : '9rem';
    }

    function showExpiredChips(defaultDays) {
        if (!expiredChips) return;
        expiredChips.style.display = 'flex';
        expiredChips.innerHTML = '';
        setWrapHeight(true);

        const daysArr = Array.from(new Set([defaultDays, 7, 14, 30])).sort((a, b) => a - b);
        daysArr.forEach(day => {
            const chip = document.createElement('button');
            chip.type      = 'button';
            chip.className = 'tb-chip-btn' + (day === defaultDays ? ' active' : '');
            chip.textContent = day === defaultDays ? `${day} hari (rekomendasi)` : `${day} hari`;
            chip.addEventListener('click', () => {
                expiredChips.querySelectorAll('.tb-chip-btn').forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
                autoFillExpired(day);
            });
            expiredChips.appendChild(chip);
        });
    }

    function autoFillExpired(days) {
        if (!expiredDateInput) return;
        const base = new Date();
        base.setHours(0, 0, 0, 0);
        base.setDate(base.getDate() + days);
        expiredDateInput.value = base.toISOString().split('T')[0];
    }

    // ── MODAL KONFIRMASI BAHAN BARU ───────────────────────────────────────
    function showKonfirmasiModal(nama) {
        let modal = document.getElementById('modalBahanBaru');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'modalBahanBaru';
            modal.innerHTML = `
                <div class="modal-overlay" id="modalOverlay"></div>
                <div class="modal-box">
                    <div class="modal-icon">
                        <span class="material-icons-round">help_outline</span>
                    </div>
                    <h3 class="modal-title font-jakarta font-bold" id="modalTitle"></h3>
                    <p class="modal-desc font-jakarta font-regular" id="modalDesc"></p>
                    <div class="modal-actions">
                        <button class="modal-btn-cancel font-jakarta font-semibold" id="modalCancel">Batal</button>
                        <button class="modal-btn-confirm font-jakarta font-bold" id="modalConfirm">Ya, Tambahkan</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            document.getElementById('modalCancel').addEventListener('click', hideModal);
            document.getElementById('modalOverlay').addEventListener('click', hideModal);
        }

        document.getElementById('modalTitle').textContent = `Tambah "${nama}" sebagai bahan baru?`;
        document.getElementById('modalDesc').textContent  = 'Bahan ini belum ada di database LaperPoll. Kamu tetap bisa menyimpannya ke kulkas.';

        const confirmBtn = document.getElementById('modalConfirm');
        const newConfirm = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirm, confirmBtn);
        newConfirm.addEventListener('click', () => simpanBahanBaru(nama));

        modal.style.display = 'flex';
    }

    function hideModal() {
        const modal = document.getElementById('modalBahanBaru');
        if (modal) modal.style.display = 'none';
    }

    function showToast(msg, isError = false) {
        const existing = document.querySelector('.tb-toast');
        if (existing) existing.remove();
        const toast = document.createElement('div');
        toast.className = 'tb-toast' + (isError ? ' toast-error' : '');
        toast.innerHTML = `<span class="material-icons-round">${isError ? 'error_outline' : 'check_circle'}</span> ${msg}`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3500);
    }

    async function simpanBahanBaru(nama) {
        const confirmBtn = document.getElementById('modalConfirm');
        if (confirmBtn) { confirmBtn.disabled = true; confirmBtn.textContent = 'Menyimpan...'; }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const res = await fetch('/api/bahans/baru', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ nama }),
            });

            if (!res.ok) throw new Error(`Server error ${res.status}`);

            const data = await res.json();
            hideModal();
            selectBahan(data);
            showToast(`"${data.nama}" berhasil ${data.status === 'created' ? 'ditambahkan ke database' : 'ditemukan'}`);
        } catch (err) {
            console.error(err);
            showToast('Gagal menyimpan bahan baru: ' + err.message, true);
        } finally {
            if (confirmBtn) { confirmBtn.disabled = false; confirmBtn.textContent = 'Ya, Tambahkan'; }
        }
    }

    // ── SEARCH INPUT EVENTS ───────────────────────────────────────────────
    searchInput?.addEventListener('input', (e) => {
        const q = e.target.value.trim();
        if (clearBtn) clearBtn.style.display = q ? 'block' : 'none';
        if (!q) { closeDropdown(); bahanIdInput.value = ''; selectedBahan = null; return; }
        const filtered = bahanList.filter(b => b.nama.toLowerCase().includes(q.toLowerCase())).slice(0, 8);
        renderDropdown(filtered, q);
    });

    clearBtn?.addEventListener('click', () => {
        searchInput.value  = '';
        bahanIdInput.value = '';
        clearBtn.style.display = 'none';
        closeDropdown();
        selectedBahan     = null;
        defaultExpiryDays = null;
        if (expiredChips)  { expiredChips.innerHTML = ''; expiredChips.style.display = 'none'; }
        if (expiredHint)   expiredHint.textContent = 'Isi tanggal kedaluwarsa bahan ini';
        searchInput.focus();
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.tb-search-wrap')) closeDropdown();
    });

    // ── VALIDASI SUBMIT ───────────────────────────────────────────────────
    form?.addEventListener('submit', (e) => {
        if (!bahanIdInput.value) {
            e.preventDefault();
            searchInput?.focus();
            searchInput?.classList.add('tb-input-error');
            alert('Pilih nama bahan dari daftar terlebih dahulu.');
            return;
        }
        if (activeDateType === 'bought' && !boughtDateInput?.value) {
            e.preventDefault();
            boughtDateInput?.focus();
            alert('Isi tanggal beli.');
            return;
        }
        if (activeDateType === 'expired' && !expiredDateInput?.value) {
            e.preventDefault();
            expiredDateInput?.focus();
            alert('Isi tanggal expired.');
            return;
        }
    });

    // ── INIT ──────────────────────────────────────────────────────────────
    if (boughtDateInput && !boughtDateInput.value) {
        boughtDateInput.value = new Date().toISOString().split('T')[0];
    }

    if (bahanIdInput?.value && searchInput?.value) {
        const found = bahanList.find(b => b.id == bahanIdInput.value);
        if (found) { selectedBahan = found; defaultExpiryDays = found.expired_expectancy_day; }
    }

})();