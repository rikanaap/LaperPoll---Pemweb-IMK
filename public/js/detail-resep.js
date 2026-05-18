const dropdownSelect = document.querySelector('.unit-select');
const amountSpans = document.querySelectorAll('.amt');
 
// Simpan nilai gram awal ke dataset supaya konversi selalu akurat
amountSpans.forEach(span => {
    const baseValue = parseFloat(span.innerText.replace('g', '').trim());
    span.dataset.baseGram = baseValue;
});
 
dropdownSelect.addEventListener('change', function () {
    const satuanPilihan = this.value;
 
    amountSpans.forEach(span => {
        const baseGram = parseFloat(span.dataset.baseGram);
        let hasilHitung = 0;
        let simbolSatuan = '';
 
        if (satuanPilihan === 'gram') {
            hasilHitung = baseGram;
            simbolSatuan = 'g';
        } else if (satuanPilihan === 'miligram') {
            
            hasilHitung = baseGram * 10;
            simbolSatuan = 'mg';
        } else if (satuanPilihan === 'kilogram') {
            hasilHitung = baseGram / 1000;
            simbolSatuan = 'kg';
        } else if (satuanPilihan === 'sendok_makan') {
            hasilHitung = (baseGram / 15).toFixed(1);
            simbolSatuan = ' sdm';
        }
 
        span.innerText = `${hasilHitung}${simbolSatuan}`;
    });
});
 
 
// ============================================================
// FAVORITE TOGGLE
// ============================================================
 
const STORAGE_KEY = "laperpoll_favorites";
 
// [UBAH] data-recipe-id sekarang diisi $resep->id dari blade, bukan hardcoded
const recipeId = document.querySelector('[data-recipe-id]')?.dataset.recipeId
               ?? window.location.pathname.split('/').pop();
 
const favoriteIcon = document.querySelector('.favorite-icon');
 
// --- Fungsi storage ---
 
function loadFavorites() {
    try {
        return JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]");
    } catch {
        return [];
    }
}
 
function saveFavorites(favorites) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(favorites));
}
 
function isFavorite(id) {
    return loadFavorites().includes(id);
}
 
function toggleFavorite(id) {
    let favorites = loadFavorites();
    if (favorites.includes(id)) {
        favorites = favorites.filter(f => f !== id);
    } else {
        favorites.push(id);
    }
    saveFavorites(favorites);
    return favorites.includes(id);
}
 
// --- Update tampilan ikon ---
 
function updateFavoriteIcon(active) {
    if (!favoriteIcon) return;
 
    if (active) {
        favoriteIcon.textContent = "favorite";
        favoriteIcon.classList.add("favorite-active");
    } else {
        favoriteIcon.textContent = "favorite_border";
        favoriteIcon.classList.remove("favorite-active");
    }
}
 
// --- Init: set tampilan awal sesuai status tersimpan ---
updateFavoriteIcon(isFavorite(recipeId));
 
// --- Klik tombol favorit ---
favoriteIcon?.addEventListener('click', () => {
    const nowActive = toggleFavorite(recipeId);
    updateFavoriteIcon(nowActive);
 
    // Animasi pop saat diklik
    favoriteIcon.classList.add("favorite-pop");
    favoriteIcon.addEventListener('animationend', () => {
        favoriteIcon.classList.remove("favorite-pop");
    }, { once: true });
});