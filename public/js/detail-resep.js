 const dropdownSelect = document.querySelector('.unit-select');
          const amountSpans = document.querySelectorAll('.amt');

          amountSpans.forEach(span => {
              const baseValue = parseFloat(span.innerText.replace('g', '').trim());
              span.dataset.baseGram = baseValue; 
          });
        
          dropdownSelect.addEventListener('change', function() {
              const satuanPilihan = this.value;
              amountSpans.forEach(span => {
                  
                  const baseGram = parseFloat(span.dataset.baseGram);
                  let hasilHitung = 0;
                  let simbolSatuan = '';
      
                  if (satuanPilihan === 'gram') {
                    hasilHitung = baseGram;
                    simbolSatuan = 'g';
                  } else if (satuanPilihan === 'miligram') {
                    hasilHitung = baseGram; 
                    simbolSatuan = 'mg';
                  } else if (satuanPilihan === 'sendok_makan') {
                     hasilHitung = baseGram / 15; 
                     simbolSatuan = ' sdm';
                  } else if (satuanPilihan === 'kilogram') {
                    hasilHitung = baseGram / 1000;
                    simbolSatuan = 'kg';
                }

                  span.innerText = `${hasilHitung}${simbolSatuan}`;
              });
          });

         

const STORAGE_KEY = "laberpoll_favorites";

// Ambil data resep dari data attributes di <main>
const mainEl = document.querySelector('main[data-recipe-id]');
const recipeId = mainEl?.dataset.recipeId ?? window.location.pathname.split('/').pop();

// Simpan info lengkap resep (supaya halaman favorit bisa nampilin kartunya)
const recipeData = {
  id:       recipeId,
  title:    mainEl?.dataset.recipeTitle    ?? document.querySelector('.recipe-title')?.innerText ?? 'Resep',
  time:     mainEl?.dataset.recipeTime     ?? '15 mins',
  category: mainEl?.dataset.recipeCategory ?? 'Dessert',
  image:    mainEl?.dataset.recipeImage    ?? '',
};

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
  return loadFavorites().some(f => f.id === id);
}

function toggleFavorite(data) {
  let favorites = loadFavorites();
  const exists = favorites.some(f => f.id === data.id);
  if (exists) {
    favorites = favorites.filter(f => f.id !== data.id);
  } else {
    favorites.push(data); // simpan objek lengkap
  }
  saveFavorites(favorites);
  return !exists;
}

// --- Update tampilan ikon ---

function updateFavoriteIcon(active) {
  if (!favoriteIcon) return;
  favoriteIcon.textContent = active ? "favorite" : "favorite_border";
  favoriteIcon.classList.toggle("favorite-active", active);
}

// --- Init ---

updateFavoriteIcon(isFavorite(recipeId));

favoriteIcon?.addEventListener('click', () => {
  const nowActive = toggleFavorite(recipeData);
  updateFavoriteIcon(nowActive);

  favoriteIcon.classList.add("favorite-pop");
  favoriteIcon.addEventListener('animationend', () => {
    favoriteIcon.classList.remove("favorite-pop");
  }, { once: true });
});