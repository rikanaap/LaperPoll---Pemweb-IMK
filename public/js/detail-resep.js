// detail-resep.js — LaperPoll

// ─── UNIT KONVERSI BAHAN (custom dropdown) ───────────────────────────────────
const unitToggle   = document.getElementById('unitToggle');
const unitLabel    = document.getElementById('unitLabel');
const unitDropdown = document.getElementById('unitDropdown');
const unitOptions  = document.querySelectorAll('.dr-unit-option');
const chips        = document.querySelectorAll('.dr-chip');

let currentUnit = 'gram';

// Toggle buka/tutup dropdown
unitToggle?.addEventListener('click', (e) => {
    e.stopPropagation();
    unitToggle.classList.toggle('open');
});

// Tutup kalau klik di luar
document.addEventListener('click', () => {
    unitToggle?.classList.remove('open');
});

// Pilih opsi
unitOptions.forEach(opt => {
    opt.addEventListener('click', (e) => {
        e.stopPropagation();
        currentUnit = opt.dataset.value;
        unitLabel.textContent = opt.textContent;

        // Update active state
        unitOptions.forEach(o => o.classList.remove('active'));
        opt.classList.add('active');

        // Tutup dropdown
        unitToggle.classList.remove('open');

        // Konversi semua chip
        convertChips(currentUnit);
    });
});

function convertChips(satuan) {
    chips.forEach(chip => {
        const baseGram = parseFloat(chip.dataset.gram || 0);
        const amtEl    = chip.querySelector('.dr-chip-amt');
        if (!amtEl) return;

        let hasil = 0, simbol = '';
        if      (satuan === 'gram')      { hasil = baseGram;                      simbol = 'g';    }
        else if (satuan === 'miligram')  { hasil = baseGram * 1000;               simbol = 'mg';   }
        else if (satuan === 'kilogram')  { hasil = (baseGram / 1000).toFixed(3);  simbol = 'kg';   }
        else if (satuan === 'sdm')       { hasil = (baseGram / 15).toFixed(1);    simbol = ' sdm'; }

        amtEl.textContent = `${hasil}${simbol}`;
    });
}

// ─── FAVORIT TOGGLE ──────────────────────────────────────────────────────────
const favBtn = document.getElementById('drFavBtn');

if (favBtn) {
    favBtn.addEventListener('click', async () => {
        if (!IS_AUTH) {
            window.location.href = SIGN_IN_URL;
            return;
        }

        try {
            const res  = await fetch(FAVORIT_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            });
            const data = await res.json();

            const icon = favBtn.querySelector('.material-icons-round');
            // FavoriteController return isFavorite
            const isFav = data.isFavorite ?? data.favorited ?? false;
            if (isFav) {
                icon.textContent = 'favorite';
                favBtn.classList.add('active');
            } else {
                icon.textContent = 'favorite_border';
                favBtn.classList.remove('active');
            }

            // Animasi pop
            favBtn.style.transform = 'scale(1.3)';
            setTimeout(() => favBtn.style.transform = '', 200);

        } catch (err) {
            console.error('Gagal toggle favorit:', err);
        }
    });
}

// ─── STAR INPUT (form ulasan) ────────────────────────────────────────────────
const starPicks   = document.querySelectorAll('.dr-star-pick');
const ratingInput = document.getElementById('ratingInput');

starPicks.forEach((star, idx) => {
    star.addEventListener('mouseenter', () => highlightStars(idx + 1));
    star.addEventListener('mouseleave', () => highlightStars(parseInt(ratingInput?.value || 0)));
    star.addEventListener('click', () => {
        if (ratingInput) ratingInput.value = idx + 1;
        highlightStars(idx + 1);
    });
});

function highlightStars(count) {
    starPicks.forEach((s, i) => {
        s.textContent = i < count ? 'star' : 'star_border';
        s.classList.toggle('active', i < count);
    });
}

// ─── PHOTO UPLOAD PREVIEW ────────────────────────────────────────────────────
const photoUploadArea = document.getElementById('photoUploadArea');
const photoInput      = document.getElementById('photoInput');
const photoPreviews   = document.getElementById('photoPreviews');
const MAX_PHOTOS      = 3;

if (photoUploadArea && photoInput) {
    photoUploadArea.addEventListener('click', () => photoInput.click());

    photoInput.addEventListener('change', function () {
        const files    = Array.from(this.files);
        const existing = photoPreviews?.querySelectorAll('.dr-preview-wrap').length || 0;
        const slots    = MAX_PHOTOS - existing;

        if (slots <= 0) {
            showToast(`Maksimal ${MAX_PHOTOS} foto.`, 'warn');
            return;
        }

        files.slice(0, slots).forEach(file => {
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                const wrap = document.createElement('div');
                wrap.className = 'dr-preview-wrap';
                wrap.innerHTML = `
                    <img src="${e.target.result}" class="dr-preview-img" alt="preview">
                    <button type="button" class="dr-preview-remove" aria-label="Hapus foto">
                        <span class="material-icons-round">close</span>
                    </button>`;
                wrap.querySelector('.dr-preview-remove').addEventListener('click', () => wrap.remove());
                photoPreviews.appendChild(wrap);
            };
            reader.readAsDataURL(file);
        });

        // Reset input supaya bisa pilih file yang sama lagi
        this.value = '';
    });
}

// ─── PHOTO MODAL ─────────────────────────────────────────────────────────────
const photoModal    = document.getElementById('photoModal');
const photoModalImg = document.getElementById('photoModalImg');

function openPhotoModal(src) {
    if (!photoModal || !photoModalImg) return;
    photoModalImg.src = src;
    photoModal.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closePhotoModal() {
    if (!photoModal) return;
    photoModal.classList.remove('open');
    document.body.style.overflow = '';
}

// Tutup modal kalau tekan ESC
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closePhotoModal();
});

// ─── TOAST HELPER ────────────────────────────────────────────────────────────
function showToast(msg, type = 'info') {
    const existing = document.querySelector('.dr-toast');
    if (existing) existing.remove();

    const colors = { info: '#172D23', warn: '#B45309', error: '#B91C1C' };
    const toast  = document.createElement('div');
    toast.className = 'dr-toast';
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

// ─── DELETE CONFIRM (ulasan di detail resep) ──────────────────────────────────
function drConfirmDelete() {
    document.getElementById('drConfirmModal')?.classList.add('open');
    document.getElementById('drConfirmOverlay')?.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeDrConfirm() {
    document.getElementById('drConfirmModal')?.classList.remove('open');
    document.getElementById('drConfirmOverlay')?.classList.remove('open');
    document.body.style.overflow = '';
}

// ─── FOLLOW BUTTON (author) ───────────────────────────────────────────────────
const drFollowBtn = document.getElementById('drFollowBtn');

if (drFollowBtn) {
    drFollowBtn.addEventListener('click', async () => {
        if (!IS_AUTH) { window.location.href = SIGN_IN_URL; return; }

        const userId = drFollowBtn.dataset.userId;
        drFollowBtn.disabled = true;

        try {
            const res  = await fetch(`/follow/${userId}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            });
            const data = await res.json();

            if (data.success) {
                const icon  = drFollowBtn.querySelector('.material-icons-round');
                const label = drFollowBtn.querySelector('.dr-follow-label');

                if (data.is_following) {
                    drFollowBtn.classList.add('following');
                    icon.textContent  = 'person_remove';
                    label.textContent = 'Mengikuti';
                } else {
                    drFollowBtn.classList.remove('following');
                    icon.textContent  = 'person_add';
                    label.textContent = 'Ikuti';
                }

                // Animasi pop
                drFollowBtn.style.transform = 'scale(1.1)';
                setTimeout(() => drFollowBtn.style.transform = '', 200);
            }
        } catch (err) {
            console.error('Gagal toggle follow:', err);
        } finally {
            drFollowBtn.disabled = false;
        }
    });
}