const searchInput          = document.getElementById('searchInput');
const bahanItems           = document.querySelectorAll('.bahan-item');
const checkboxes           = document.querySelectorAll('.bahan-item input[type="checkbox"]');
const btnApply             = document.getElementById('terapkanBtn');
const btnHapus             = document.getElementById('hapusSemuaBtn');
const selectedInfo         = document.getElementById('selectedInfo');

/* =========================
   SEARCH
========================= */
searchInput.addEventListener('input', function () {
    const keyword = this.value.toLowerCase();
    bahanItems.forEach(item => {
        const nama = item.querySelector('.bahan-nama').textContent.toLowerCase();
        item.style.display = nama.includes(keyword) ? 'flex' : 'none';
    });
});

/* =========================
   CHECKBOX CHANGE
========================= */
checkboxes.forEach(box => {
    box.addEventListener('change', updateSelection);
});

/* =========================
   UPDATE UI
========================= */
function updateSelection() {
    const checked = document.querySelectorAll('.bahan-item input:checked');
    const total   = checked.length;

    // Tampilkan pesan info hanya jika ada bahan yang dipilih
    if (total > 0) {
        selectedInfo.style.display = 'block';
        selectedInfo.textContent = `${total} bahan telah terpilih`;

        // Aktifkan tombol
        btnApply.disabled = false;
        btnHapus.disabled = false;
        btnApply.classList.remove('disabled');
        btnHapus.classList.remove('disabled');
    } else {
        selectedInfo.style.display = 'none';

        // Nonaktifkan tombol (disabled)
        btnApply.disabled = true;
        btnHapus.disabled = true;
        btnApply.classList.add('disabled');
        btnHapus.classList.add('disabled');
    }

    // Active Card
    bahanItems.forEach(item => {
        const checkbox = item.querySelector('input');
        if (checkbox.checked) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
}

/* =========================
   BUTTON HAPUS SEMUA
========================= */
btnHapus.addEventListener('click', function() {
    if (this.disabled) return;
    checkboxes.forEach(box => {
        box.checked = false;
    });
    updateSelection();
});

/* =========================
   AMBIL BAHAN
========================= */
function getSelectedBahan() {
    const data = [];
    document.querySelectorAll('.bahan-item input:checked').forEach(box => {
        const nama = box.closest('.bahan-item').querySelector('.bahan-nama').textContent.trim();
        data.push(nama);
    });
    return data;
}

/* =========================
   BUTTON APPLY
========================= */
btnApply.addEventListener('click', function() {
    if (this.disabled) return;
    const query = getSelectedBahan().join(',');
    if (window.innerWidth < 768) {
        window.location.href = `/filter-resep?bahan=${query}`;
    } else {
        console.log('Desktop result panel:', query);
    }
});

/* =========================
   ENTER SEARCH
========================= */
searchInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        const keyword = this.value.trim();
        if (keyword) {
            window.location.href = `/filter-resep?search=${keyword}`;
        }
    }
});

/* init */
updateSelection();