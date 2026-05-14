// tambah-bahan.js

(function () {
    'use strict';

    const bahanDataJson = document.getElementById('bahanData')?.textContent || '[]';
    let bahanList = [];
    try { bahanList = JSON.parse(bahanDataJson); } catch (e) { console.warn(e); }

    const searchInput    = document.getElementById('searchBahan');
    const dropdown       = document.getElementById('bahanDropdown');
    const clearBtn       = document.getElementById('clearSearch');
    const bahanIdInput   = document.getElementById('bahanId');
    const jumlahAngka    = document.getElementById('jumlahAngka');
    const satuanInput    = document.getElementById('satuanBahan');
    const boughtDate     = document.getElementById('boughtDate');
    const expiredDate    = document.getElementById('expiredDate');
    const expiredSection = document.getElementById('expiredSection');
    const expiredChips   = document.getElementById('expiredChips');
    const form           = document.getElementById('formTambahBahan');

    let selectedBahan     = null;
    let defaultExpiryDays = null;

    // ── HIDDEN FIELD jumlah ───────────────────────────────────────────────
    let hiddenJumlah = document.getElementById('jumlahHidden');
    if (!hiddenJumlah) {
        hiddenJumlah       = document.createElement('input');
        hiddenJumlah.type  = 'hidden';
        hiddenJumlah.name  = 'jumlah';
        hiddenJumlah.id    = 'jumlahHidden';
        hiddenJumlah.value = '1';
        form?.appendChild(hiddenJumlah);
    }
    if (satuanInput) satuanInput.removeAttribute('name');

    function syncJumlah() {
        const angka  = jumlahAngka?.value || '1';
        const satuan = satuanInput?.value.trim() || '';
        hiddenJumlah.value = angka + (satuan ? ' ' + satuan : '');
    }

    // ── DROPDOWN ──────────────────────────────────────────────────────────
    function closeDropdown() {
        if (dropdown) dropdown.style.display = 'none';
    }

    function renderDropdown(results, query) {
        if (!dropdown) return;
        dropdown.innerHTML = '';

        if (results.length > 0) {
            results.forEach(b => {
                const li = document.createElement('li');
                li.textContent = b.nama;
                li.addEventListener('click', (e) => {
                    e.stopPropagation();
                    selectBahan(b);
                });
                dropdown.appendChild(li);
            });
        }

        // Kalau tidak ada hasil, tampilkan opsi tambah bahan baru
        if (results.length === 0 && query) {
            const li = document.createElement('li');
            li.className = 'tb-add-new';
            li.innerHTML = `<span class="material-icons-round">add_circle_outline</span> Tambah "<strong>${query}</strong>" sebagai bahan baru`;
            li.addEventListener('click', (e) => {
                e.stopPropagation();
                closeDropdown();
                showKonfirmasiModal(query);
            });
            dropdown.appendChild(li);
        }

        dropdown.style.display = 'block';
    }

    function selectBahan(bahan) {
        selectedBahan      = bahan;
        searchInput.value  = bahan.nama;
        bahanIdInput.value = bahan.id;
        if (clearBtn) clearBtn.style.display = 'block';
        closeDropdown();

        if (bahan.has_expiry && bahan.expired_expectancy_day) {
            defaultExpiryDays = bahan.expired_expectancy_day;
            showExpiredSection(bahan.expired_expectancy_day);
        } else {
            hideExpiredSection();
        }
    }

    // ── MODAL KONFIRMASI BAHAN BARU ───────────────────────────────────────
    function showKonfirmasiModal(nama) {
        // Buat modal kalau belum ada
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
        document.getElementById('modalDesc').textContent =
            'Bahan ini belum ada di database Laperpoll. Kamu tetap bisa menyimpannya ke kulkas, dan tim kami akan menambahkan informasi lengkapnya nanti.';

        const confirmBtn = document.getElementById('modalConfirm');
        // Hapus listener lama biar tidak double
        const newConfirm = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirm, confirmBtn);
        newConfirm.addEventListener('click', () => simpanBahanBaru(nama));

        modal.style.display = 'flex';
    }

    function hideModal() {
        const modal = document.getElementById('modalBahanBaru');
        if (modal) modal.style.display = 'none';
    }

    async function simpanBahanBaru(nama) {
        const confirmBtn = document.getElementById('modalConfirm');
        confirmBtn.disabled     = true;
        confirmBtn.textContent  = 'Menyimpan...';

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const res = await fetch('/api/bahans/baru', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ nama }),
            });

            if (!res.ok) throw new Error('Gagal menyimpan');

            const data = await res.json();
            hideModal();
            selectBahan(data);

        } catch (err) {
            console.error(err);
            alert('Gagal menyimpan bahan baru. Coba lagi.');
        } finally {
            if (confirmBtn) {
                confirmBtn.disabled    = false;
                confirmBtn.textContent = 'Ya, Tambahkan';
            }
        }
    }

    // ── EXPIRED SECTION ───────────────────────────────────────────────────
    function showExpiredSection(defaultDays) {
        if (!expiredSection) return;
        expiredSection.style.display = 'flex';
        buildExpiredChips(defaultDays);
        updateExpiredDateByDays(defaultDays);
    }

    function hideExpiredSection() {
        if (expiredSection) expiredSection.style.display = 'none';
        if (expiredDate)    expiredDate.value = '';
        if (expiredChips)   expiredChips.innerHTML = '';
    }

    function buildExpiredChips(defaultDays) {
        if (!expiredChips) return;
        expiredChips.innerHTML = '';
        const daysArray = Array.from(new Set([defaultDays, 7, 14, 30])).sort((a, b) => a - b);

        daysArray.forEach(day => {
            const chip = document.createElement('button');
            chip.type      = 'button';
            chip.className = 'tb-chip-btn' + (day === defaultDays ? ' active' : '');
            chip.textContent = day === defaultDays ? `${day} hari (rekomendasi)` : `${day} hari`;
            chip.addEventListener('click', () => {
                document.querySelectorAll('#expiredChips .tb-chip-btn').forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
                updateExpiredDateByDays(day);
            });
            expiredChips.appendChild(chip);
        });
    }

    function updateExpiredDateByDays(days) {
        if (!expiredDate || !boughtDate) return;
        let base = boughtDate.value ? new Date(boughtDate.value + 'T00:00:00') : new Date();
        if (isNaN(base)) base = new Date();
        const newDate = new Date(base);
        newDate.setDate(base.getDate() + days);
        expiredDate.value = newDate.toISOString().split('T')[0];
    }

    // ── EVENTS ────────────────────────────────────────────────────────────
    searchInput?.addEventListener('input', (e) => {
        const q = e.target.value.trim();
        if (clearBtn) clearBtn.style.display = q ? 'block' : 'none';
        if (!q) { closeDropdown(); return; }
        const filtered = bahanList.filter(b => b.nama.toLowerCase().includes(q.toLowerCase()));
        renderDropdown(filtered, q);
    });

    clearBtn?.addEventListener('click', () => {
        searchInput.value  = '';
        bahanIdInput.value = '';
        clearBtn.style.display = 'none';
        closeDropdown();
        hideExpiredSection();
        selectedBahan     = null;
        defaultExpiryDays = null;
        searchInput.focus();
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.tb-search-wrap')) closeDropdown();
    });

    document.getElementById('btnPlus')?.addEventListener('click', () => {
        jumlahAngka.value = (parseInt(jumlahAngka.value) || 1) + 1;
        syncJumlah();
    });

    document.getElementById('btnMinus')?.addEventListener('click', () => {
        const v = parseInt(jumlahAngka.value) || 1;
        if (v > 1) jumlahAngka.value = v - 1;
        syncJumlah();
    });

    jumlahAngka?.addEventListener('input', () => {
        if (!jumlahAngka.value || parseInt(jumlahAngka.value) < 1) jumlahAngka.value = 1;
        syncJumlah();
    });

    satuanInput?.addEventListener('input', syncJumlah);

    boughtDate?.addEventListener('change', () => {
        if (!selectedBahan?.has_expiry) return;
        const activeChip = document.querySelector('#expiredChips .tb-chip-btn.active');
        const days = activeChip ? parseInt(activeChip.textContent) : defaultExpiryDays;
        if (days) updateExpiredDateByDays(days);
    });

    form?.addEventListener('submit', (e) => {
        if (!bahanIdInput.value) {
            e.preventDefault();
            alert('Pilih nama bahan dari daftar, atau tambahkan sebagai bahan baru.');
            searchInput?.focus();
            return;
        }
        if (!satuanInput?.value.trim()) {
            e.preventDefault();
            alert('Isi satuan bahan (contoh: gram, butir, liter).');
            satuanInput?.focus();
            return;
        }
        syncJumlah();
    });

    // Inisialisasi
    if (boughtDate && !boughtDate.value) {
        boughtDate.value = new Date().toISOString().split('T')[0];
    }
    if (bahanIdInput?.value && searchInput?.value) {
        const found = bahanList.find(b => b.id == bahanIdInput.value);
        if (found) {
            selectedBahan = found;
            if (found.has_expiry && found.expired_expectancy_day) {
                defaultExpiryDays = found.expired_expectancy_day;
                showExpiredSection(found.expired_expectancy_day);
            }
        }
    }
    syncJumlah();

})();