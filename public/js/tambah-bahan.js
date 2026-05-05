document.addEventListener('DOMContentLoaded', () => {

    // ── AMBIL DATA BAHAN DARI SERVER ─────────────────────────
    const bahanData = JSON.parse(
        document.getElementById('bahanData')?.textContent || '[]'
    );

    // ── ELEMENTS ─────────────────────────────────────────────
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

    // ── SEARCH ────────────────────────────────────────────────
    searchInput?.addEventListener('input', () => {
        const q = searchInput.value.trim().toLowerCase();
        clearBtn.style.display = q ? 'block' : 'none';
        if (!q) { hideDropdown(); return; }
        const results = bahanData.filter(b =>
            b.nama.toLowerCase().includes(q)
        );
        renderDropdown(results);
    });

    clearBtn?.addEventListener('click', () => {
        searchInput.value  = '';
        bahanIdInput.value = '';
        clearBtn.style.display = 'none';
        hideDropdown();
        resetExpired();
        selectedBahan = null;
    });

    function renderDropdown(results) {
        dropdown.innerHTML = '';
        if (!results.length) {
            dropdown.innerHTML =
                '<li class="dropdown-empty font-jakarta text-caption text-primary-darker">Bahan tidak ditemukan</li>';
        } else {
            results.forEach(b => {
                const li = document.createElement('li');
                li.className = 'dropdown-item font-jakarta text-body';
                li.textContent = b.nama;
                li.addEventListener('click', () => selectBahan(b));
                dropdown.appendChild(li);
            });
        }
        dropdown.style.display = 'block';
    }

    function hideDropdown() { dropdown.style.display = 'none'; }

    function selectBahan(bahan) {
        selectedBahan       = bahan;
        searchInput.value   = bahan.nama;
        bahanIdInput.value  = bahan.id;
        clearBtn.style.display = 'block';
        hideDropdown();
        toggleExpiredSection(bahan);
    }

    // Tutup dropdown klik luar
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.search-wrapper')) hideDropdown();
    });

    // ── JUMLAH +/- ───────────────────────────────────────────
    document.getElementById('btnPlus')?.addEventListener('click', () => {
        jumlahAngka.value = parseInt(jumlahAngka.value || 0) + 1;
        syncJumlah();
    });
    document.getElementById('btnMinus')?.addEventListener('click', () => {
        const val = parseInt(jumlahAngka.value || 1);
        if (val > 1) { jumlahAngka.value = val - 1; syncJumlah(); }
    });

    // Sync angka ke field jumlah (nama input form)
    jumlahAngka?.addEventListener('input', syncJumlah);

    function syncJumlah() {
        // Ambil satuan dari nama field kalau sudah diisi
        const currentVal = satuanInput.value.trim();
        // Coba update angka di depan kalau formatnya "angka satuan"
        const match = currentVal.match(/^(\d+)\s+(.+)$/);
        if (match) {
            satuanInput.value = `${jumlahAngka.value} ${match[2]}`;
        }
    }

    // ── EXPIRED SECTION ──────────────────────────────────────
    function toggleExpiredSection(bahan) {
        if (bahan.has_expiry && bahan.expired_expectancy_day) {
            expiredSection.style.removeProperty('display');
            buildExpiredChips(bahan.expired_expectancy_day);
            // Auto-set default expired berdasarkan expectancy
            setExpiredFromDays(bahan.expired_expectancy_day);
        } else {
            expiredSection.style.display = 'none !important';
            expiredDate.value = '';
            expiredChips.innerHTML = '';
        }
    }

    function buildExpiredChips(defaultDays) {
        expiredChips.innerHTML = '';
        // Buat chip-chip: default, setengahnya, 2x, dan manual
        const options = [
            { label: `${defaultDays} hari (default)`, days: defaultDays },
            { label: '7 hari', days: 7 },
            { label: '14 hari', days: 14 },
            { label: '30 hari', days: 30 },
        ].filter((v, i, arr) =>
            arr.findIndex(x => x.days === v.days) === i
        );

        options.forEach(opt => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'expired-chip font-jakarta font-medium text-caption';
            btn.textContent = opt.label;
            btn.dataset.days = opt.days;
            btn.addEventListener('click', () => {
                document.querySelectorAll('.expired-chip')
                    .forEach(c => c.classList.remove('active'));
                btn.classList.add('active');
                setExpiredFromDays(opt.days);
            });
            expiredChips.appendChild(btn);
        });

        // Aktifkan chip default
        expiredChips.querySelector('.expired-chip')?.classList.add('active');
    }

    function setExpiredFromDays(days) {
        const d = new Date();
        d.setDate(d.getDate() + days);
        expiredDate.value = d.toISOString().split('T')[0];
    }

    function resetExpired() {
        expiredSection.style.display = 'none';
        expiredDate.value = '';
        expiredChips.innerHTML = '';
    }

    expiredDate?.addEventListener('input', () => {
        // Kalau user ganti manual, unset active chip
        document.querySelectorAll('.expired-chip')
            .forEach(c => c.classList.remove('active'));
    });

    // Set min date untuk expired = tanggal beli
    boughtDate?.addEventListener('change', () => {
        if (expiredDate) expiredDate.min = boughtDate.value;
    });
    if (expiredDate && boughtDate) {
        expiredDate.min = boughtDate.value;
    }

    // ── VALIDASI SUBMIT ───────────────────────────────────────
    document.getElementById('formTambahBahan')?.addEventListener('submit', (e) => {
        if (!bahanIdInput.value) {
            e.preventDefault();
            alert('Pilih nama bahan dari daftar terlebih dahulu!');
            searchInput.focus();
            return;
        }
        if (!satuanInput.value.trim()) {
            e.preventDefault();
            alert('Isi jumlah dan satuan bahan!');
            satuanInput.focus();
            return;
        }
    });
});