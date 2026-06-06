// edit-profile.js — LaperPoll

// ── Avatar preview ──
const avatarWrapper   = document.getElementById('avatarWrapper');
const profileInput    = document.getElementById('profilePhotoInput');
const avatarPreview   = document.getElementById('avatarPreview');

if (avatarWrapper && profileInput && avatarPreview) {
    avatarWrapper.addEventListener('click', () => profileInput.click());

    profileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        // Validasi sisi client
        if (file.size > 2 * 1024 * 1024) {
            showToast('Ukuran foto maksimal 2 MB.', 'error');
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            avatarPreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
}

// ── Toggle password visibility ──
function setupToggle(btnId, iconId, inputId) {
    const btn  = document.getElementById(btnId);
    const icon = document.getElementById(iconId);
    const inp  = document.getElementById(inputId);
    if (!btn) return;

    btn.addEventListener('click', () => {
        const isPass = inp.type === 'password';
        inp.type  = isPass ? 'text' : 'password';
        icon.textContent = isPass ? 'visibility' : 'visibility_off';
    });
}

setupToggle('togglePassword', 'togglePassIcon', 'password');
setupToggle('toggleConfirm', 'toggleConfirmIcon', 'password_confirmation');

// ── Simple toast helper ──
function showToast(msg, type = 'info') {
    const existing = document.querySelector('.ep-toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = 'ep-toast';
    toast.style.cssText = `
        position: fixed; bottom: 5rem; left: 50%; transform: translateX(-50%);
        background: ${type === 'error' ? '#B91C1C' : '#172D23'};
        color: white; padding: 0.6rem 1.25rem; border-radius: 2rem;
        font-size: 0.8rem; font-family: var(--font-jakarta); z-index: 999;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2); white-space: nowrap;
    `;
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// ── Toggle show/hide current password (dipanggil dari onclick) ──
function epTogglePass(inputId, iconId) {
    const inp  = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!inp || !icon) return;
    const isPass = inp.type === 'password';
    inp.type         = isPass ? 'text' : 'password';
    icon.textContent = isPass ? 'visibility' : 'visibility_off';
}
window.epTogglePass = epTogglePass;

// ── Tampilkan field current_password saat user mulai isi password baru ──
const passInput        = document.getElementById('password');
const currentPassGroup = document.getElementById('currentPassGroup');
const passConfirmInput = document.getElementById('password_confirmation');
const passConfirmHint  = document.createElement('span');

if (passInput && currentPassGroup) {
    passInput.addEventListener('input', () => {
        // Tampilkan field password lama jika password baru mulai diisi
        currentPassGroup.style.display = passInput.value.length > 0 ? 'flex' : 'none';
    });
}

// ── Realtime konfirmasi password ──
if (passConfirmInput && passInput) {
    passConfirmInput.addEventListener('input', () => {
        const match = passInput.value === passConfirmInput.value;
        passConfirmInput.style.borderColor = passConfirmInput.value
            ? (match ? '#86EFAC' : '#FCA5A5')
            : '';
    });
}

// ── Loading state saat submit ──
const epForm    = document.querySelector('.ep-form');
const epBtnSave = document.getElementById('epBtnSave');

if (epForm && epBtnSave) {
    epForm.addEventListener('submit', () => {
        epBtnSave.disabled = true;
        epBtnSave.innerHTML = '<span class="material-icons-round">hourglass_top</span> Menyimpan...';
    });
}

// ── Tampilkan error current_password dari server jika ada ──
const currPassErr = document.querySelector('[data-field="current_password"]');
if (currPassErr && currentPassGroup) {
    currentPassGroup.style.display = 'flex';
}