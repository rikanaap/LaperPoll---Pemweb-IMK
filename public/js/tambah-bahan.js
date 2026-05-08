// tambah-bahan.js

document.addEventListener('DOMContentLoaded', () => {

    const bahanData      = JSON.parse(document.getElementById('bahanData')?.textContent || '[]');
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

    let selectedBahan = null;

    // ── SEARCH AUTOCOMPLETE ──────────────────────────────────
    searchInput?.addEventListener('input', () => {
        const q = searchInput.value.trim().toLowerCase();
        clearBtn.style.display = q ? 'block' : 'none';
        if (!q) { closeDropdown(); return; }
        renderDropdown(bahanData.filter(b => b.nama.toLowerCase().includes(q)));
    });

    clearBtn?.addEventListener('click', () => {
        searchInput.value  = '';
        bahanIdInput.value = '';
        clearBtn.style.display = 'none';
        closeDropdown();
        hideExpired();
        selectedBahan = null;
    });

    function renderDropdown(results) {
        dropdown.innerHTML = '';
        if (!results.length) {
            dropdown.innerHTML = '<li class="dropdown-empty font-jakarta text-caption text-primary-darker">Bahan tidak ditemukan</li>';
        } else {
            results.forEach(b => {
                const li = document.createElement('li');
                li.className = 'dropdown-item font-jakarta text-body';
                li.textContent = b.nama;
                li.addEventListener('click', () => pick(b));
                dropdown.appendChild(li);
            });
        }
        dropdown.style.display = 'block';
    }

    function closeDropdown() { dropdown.style.display = 'none'; }

    function pick(bahan) {
        selectedBahan      = bahan;
        searchInput.value  = bahan.nama;
        bahanIdInput.value = bahan.id;
        clearBtn.style.display = 'block';
        closeDropdown();
        bahan.has_expiry ? showExpired(bahan) : hideExpired();
    }

    document.addEventListener('click', e => {
        if (!e.target.closest('.search-wrapper')) closeDropdown();
    });

    // ── JUMLAH +/- ───────────────────────────────────────────
    document.getElementById('btnPlus')?.addEventListener('click', () => {
        jumlahAngka.value = parseInt(jumlahAngka.value || 0) + 1;
    });
    document.getElementById('btnMinus')?.addEventListener('click', () => {
        const v = parseInt(jumlahAngka.value || 1);
        if (v > 1) jumlahAngka.value = v - 1;
    });

    // ── EXPIRED SECTION ──────────────────────────────────────
    function showExpired(bahan) {
        expiredSection.style.display = 'flex';
        buildChips(bahan.expired_expectancy_day);
        setExpiredFromDays(bahan.expired_expectancy_day);
    }

    function hideExpired() {
        expiredSection.style.display = 'none';
        expiredDate.value = '';
        expiredChips.innerHTML = '';
    }

    function buildChips(defaultDays) {
        expiredChips.innerHTML = '';
        const days = [...new Set([defaultDays, 7, 14, 30])].filter(Boolean).sort((a,b)=>a-b);
        days.forEach((d, i) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'expired-chip font-jakarta font-medium text-caption' + (d === defaultDays ? ' active' : '');
            btn.textContent = d === defaultDays ? `${d} hari (default)` : `${d} hari`;
            btn.addEventListener('click', () => {
                document.querySelectorAll('.expired-chip').forEach(c => c.classList.remove('active'));
                btn.classList.add('active');
                setExpiredFromDays(d);
            });
            expiredChips.appendChild(btn);
        });
    }

    function setExpiredFromDays(days) {
        const base = boughtDate?.value ? new Date(boughtDate.value + 'T00:00:00') : new Date();
        base.setDate(base.getDate() + days);
        expiredDate.value = base.toISOString().split('T')[0];
    }

    boughtDate?.addEventListener('change', () => {
        if (expiredDate) expiredDate.min = boughtDate.value;
        // Update expired date kalau bahan punya expiry
        if (selectedBahan?.has_expiry) {
            const active = document.querySelector('.expired-chip.active');
            if (active) setExpiredFromDays(parseInt(active.textContent));
        }
    });

    // ── VALIDASI + GABUNG ANGKA & SATUAN SEBELUM SUBMIT ──────
    document.getElementById('formTambahBahan')?.addEventListener('submit', e => {
        if (!bahanIdInput.value) {
            e.preventDefault();
            alert('Pilih nama bahan dari daftar terlebih dahulu!');
            searchInput.focus();
            return;
        }

        const angka  = parseInt(jumlahAngka.value || 1);
        const satuan = satuanInput.value.trim();

        if (!satuan) {
            e.preventDefault();
            alert('Isi satuan bahan! Contoh: gram, butir, buah, liter');
            satuanInput.focus();
            return;
        }

        // Gabungkan angka + satuan → set ke field name="jumlah"
        // Field satuanBahan sudah punya name="jumlah", kita set valuenya
        satuanInput.value = angka + ' ' + satuan;
    });
});