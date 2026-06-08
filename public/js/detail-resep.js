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
            lpToast(`Maksimal ${MAX_PHOTOS} foto.`, 'warn');
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

// ─── TOAST HELPER — dihandle oleh window.lpToast dari toast.js ──────────────

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
    drFollowBtn.addEventListener('click', () => {
        if (!IS_AUTH) { window.location.href = SIGN_IN_URL; return; }
        const isFollowing = drFollowBtn.classList.contains('following');
        if (isFollowing) {
            openDrUnfollowConfirm();
        } else {
            doDrFollow();
        }
    });
}

async function doDrFollow() {
    if (!drFollowBtn) return;
    const userId = drFollowBtn.dataset.userId;
    const icon   = drFollowBtn.querySelector('.material-icons-round');
    const label  = drFollowBtn.querySelector('.dr-follow-label');
    const origIcon  = icon?.textContent;
    const origLabel = label?.textContent;

    drFollowBtn.disabled = true;
    if (icon) { icon.textContent = 'autorenew'; icon.classList.add('lp-spin'); }

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
            icon?.classList.remove('lp-spin');
            if (data.is_following) {
                drFollowBtn.classList.add('following');
                if (icon)  icon.textContent  = 'person_remove';
                if (label) label.textContent = 'Mengikuti';
                lpToast('Berhasil mengikuti!', 'success');
            } else {
                drFollowBtn.classList.remove('following');
                if (icon)  icon.textContent  = 'person_add';
                if (label) label.textContent = 'Ikuti';
                lpToast('Berhenti mengikuti.', 'info');
            }
            drFollowBtn.style.transform = 'scale(1.1)';
            setTimeout(() => drFollowBtn.style.transform = '', 200);
        }
    } catch (err) {
        console.error('Gagal toggle follow:', err);
        icon?.classList.remove('lp-spin');
        if (icon)  icon.textContent  = origIcon;
        if (label) label.textContent = origLabel;
        lpToast('Gagal, coba lagi.', 'error');
    } finally {
        drFollowBtn.disabled = false;
    }
}

function openDrUnfollowConfirm() {
    document.getElementById('drUnfollowModal')?.classList.add('open');
    document.getElementById('drUnfollowOverlay')?.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeDrUnfollowConfirm() {
    document.getElementById('drUnfollowModal')?.classList.remove('open');
    document.getElementById('drUnfollowOverlay')?.classList.remove('open');
    document.body.style.overflow = '';
}

function confirmDrUnfollow() {
    closeDrUnfollowConfirm();
    doDrFollow();
}

// ─── AUTO HIDE FLASH MESSAGE ──────────────────────────────────────────────────
document.querySelectorAll('.dr-flash').forEach(flash => {
    setTimeout(() => {
        flash.style.transition = 'opacity 0.4s, transform 0.4s';
        flash.style.opacity    = '0';
        flash.style.transform  = 'translateY(-6px)';
        setTimeout(() => flash.remove(), 400);
    }, 4000);
});

// ─── ULASAN FORM SUBMIT GUARD ────────────────────────────────────────────────
const drUlasanForm  = document.getElementById('drUlasanForm');
const btnKirimUlasan = document.getElementById('btnKirimUlasan');

if (drUlasanForm) {
    drUlasanForm.addEventListener('submit', (e) => {
        const rating = parseInt(document.getElementById('ratingInput')?.value || 0);
        if (rating === 0) {
            e.preventDefault();
            lpToast('Pilih rating bintang terlebih dahulu.', 'warn');
            document.getElementById('starInput')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        if (btnKirimUlasan) {
            btnKirimUlasan.disabled = true;
            btnKirimUlasan.innerHTML = '<span class="material-icons-round">hourglass_top</span> Mengirim...';
        }
    });
}

// ─── TOGGLE REPLY FORM ───────────────────────────────────────────────────────
window.toggleReplyForm = function(feedbackId) {
    const formContainer = document.getElementById(`reply-form-${feedbackId}`);
    const triggerContainer = document.getElementById(`reply-trigger-${feedbackId}`);
    if (formContainer) {
        if (formContainer.style.display === 'none' || formContainer.style.display === '') {
            formContainer.style.display = 'block';
            if (triggerContainer) triggerContainer.style.display = 'none';
        } else {
            formContainer.style.display = 'none';
            if (triggerContainer) triggerContainer.style.display = '';
        }
    }
};