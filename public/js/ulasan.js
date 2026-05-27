// ulasan.js — LaperPoll

// ─── STAR RATING ─────────────────────────────────────────────────────────────
const starPicks    = document.querySelectorAll('.ul-star-pick');
const ratingInput  = document.getElementById('ulRatingInput');
const ratingHint   = document.getElementById('ulRatingHint');

const HINTS = ['', 'Sangat Buruk 😞', 'Kurang Baik 😕', 'Cukup 😐', 'Baik 😊', 'Luar Biasa 🤩'];

starPicks.forEach((star, idx) => {
    star.addEventListener('mouseenter', () => highlightStars(idx + 1, false));
    star.addEventListener('mouseleave', () => highlightStars(parseInt(ratingInput?.value || 0), false));
    star.addEventListener('click', () => {
        if (ratingInput) ratingInput.value = idx + 1;
        highlightStars(idx + 1, true);
    });
});

function highlightStars(count, save) {
    starPicks.forEach((s, i) => {
        s.textContent = i < count ? 'star' : 'star_border';
        s.classList.toggle('active', i < count);
    });
    if (ratingHint) {
        ratingHint.textContent = HINTS[count] || 'Pilih bintang';
        ratingHint.classList.toggle('selected', count > 0 && save);
    }
}

// ─── PHOTO UPLOAD PREVIEW ────────────────────────────────────────────────────
const photoUpload = document.getElementById('ulPhotoUpload');
const photoInput  = document.getElementById('ulPhotoInput');
const photoGrid   = document.getElementById('ulPhotoGrid');
const MAX_PHOTOS  = 3;

if (photoUpload && photoInput) {
    photoUpload.addEventListener('click', () => photoInput.click());

    photoInput.addEventListener('change', function () {
        const existing = photoGrid?.querySelectorAll('.ul-preview-wrap').length || 0;
        const slots    = MAX_PHOTOS - existing;

        if (slots <= 0) {
            showToast(`Maksimal ${MAX_PHOTOS} foto.`, 'warn');
            this.value = '';
            return;
        }

        Array.from(this.files).slice(0, slots).forEach(file => {
            if (!file.type.startsWith('image/')) return;
            if (file.size > 2 * 1024 * 1024) {
                showToast('Ukuran foto maksimal 2 MB.', 'warn');
                return;
            }
            const reader = new FileReader();
            reader.onload = (e) => {
                const wrap = document.createElement('div');
                wrap.className = 'ul-preview-wrap';
                wrap.innerHTML = `
                    <img src="${e.target.result}" class="ul-preview-img" alt="preview">
                    <button type="button" class="ul-preview-remove" aria-label="Hapus foto">
                        <span class="material-icons-round">close</span>
                    </button>`;
                wrap.querySelector('.ul-preview-remove').addEventListener('click', () => wrap.remove());
                photoGrid.appendChild(wrap);
            };
            reader.readAsDataURL(file);
        });

        this.value = '';
    });
}

// ─── VALIDASI SUBMIT ─────────────────────────────────────────────────────────
const ulasanForm = document.getElementById('ulasanForm');

if (ulasanForm) {
    ulasanForm.addEventListener('submit', function (e) {
        const rating = parseInt(ratingInput?.value || 0);
        if (rating < 1 || rating > 5) {
            e.preventDefault();
            showToast('Pilih rating bintang terlebih dahulu!', 'warn');
            // Scroll ke bintang
            document.getElementById('ulStars')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
    });
}

// ─── PHOTO MODAL ─────────────────────────────────────────────────────────────
const ulModal    = document.getElementById('ulModal');
const ulModalImg = document.getElementById('ulModalImg');

function openModal(src) {
    if (!ulModal || !ulModalImg) return;
    ulModalImg.src = src;
    ulModal.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    if (!ulModal) return;
    ulModal.classList.remove('open');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeModal();
});

// ─── TOAST ───────────────────────────────────────────────────────────────────
function showToast(msg, type = 'info') {
    const existing = document.querySelector('.ul-toast');
    if (existing) existing.remove();

    const colors = { info: '#172D23', warn: '#B45309', error: '#B91C1C' };
    const toast  = document.createElement('div');
    toast.className = 'ul-toast';
    toast.style.cssText = `
        position:fixed; bottom:5rem; left:50%; transform:translateX(-50%);
        background:${colors[type] || colors.info}; color:white;
        padding:0.6rem 1.25rem; border-radius:2rem;
        font-size:0.8rem; font-family:var(--font-jakarta);
        z-index:999; box-shadow:0 4px 12px rgba(0,0,0,0.2);
        white-space:nowrap; pointer-events:none;
    `;
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}