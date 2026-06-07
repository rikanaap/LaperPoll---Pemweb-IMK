// auth.js — LaperPoll (signup + otp + toggle password)
// Includes: realtime validation, submit guard

// ─── OTP INPUT NAVIGATION ────────────────────────────────────────────────────
const inputsOTP = document.querySelectorAll('.otp-input');
inputsOTP.forEach((input, index) => {
    input.addEventListener('input', (e) => {
        // Hanya izinkan 1 karakter angka
        e.target.value = e.target.value.replace(/\D/g, '').slice(0, 1);
        if (e.target.value.length === 1 && index < inputsOTP.length - 1) {
            inputsOTP[index + 1].focus();
        }
    });
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace') {
            if (input.value === '' && index > 0) {
                inputsOTP[index - 1].focus();
            }
        }
        // Paste support
        if (e.key === 'v' && (e.ctrlKey || e.metaKey)) return;
    });
    // Handle paste ke OTP
    input.addEventListener('paste', (e) => {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
        [...pasted].slice(0, 6).forEach((char, i) => {
            if (inputsOTP[index + i]) inputsOTP[index + i].value = char;
        });
        const nextEmpty = [...inputsOTP].findIndex(el => el.value === '');
        if (nextEmpty !== -1) inputsOTP[nextEmpty].focus();
        else inputsOTP[inputsOTP.length - 1].focus();
    });
});

// ─── TOGGLE PASSWORD VISIBILITY ──────────────────────────────────────────────
function togglePassword(icon) {
    const input = icon.previousElementSibling;
    if (!input || input.tagName !== 'INPUT') return;
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
    } else {
        input.type = 'password';
        icon.textContent = 'remove_red_eye';
    }
}

// ─── REALTIME VALIDATION (signup only) ───────────────────────────────────────
const form        = document.getElementById('signupFormEl');
const inputName   = document.getElementById('inputName');
const inputEmail  = document.getElementById('inputEmail');
const inputPass   = document.getElementById('inputPassword');
const inputConf   = document.getElementById('inputConfirm');
const btnSubmit   = document.getElementById('btnSubmit');

if (form) {
    // Helper: set state field
    function setFieldState(wrapId, hintId, isError, message) {
        const wrap = document.getElementById(wrapId);
        const hint = document.getElementById(hintId);
        if (!wrap || !hint) return;
        if (isError && message) {
            wrap.classList.add('input-error');
            wrap.classList.remove('input-success');
            hint.textContent = message;
            hint.className = 'field-hint hint-error';
        } else if (!isError && message) {
            wrap.classList.remove('input-error');
            wrap.classList.add('input-success');
            hint.textContent = message;
            hint.className = 'field-hint hint-success';
        } else {
            wrap.classList.remove('input-error', 'input-success');
            hint.textContent = '';
            hint.className = 'field-hint';
        }
    }

    // Validasi nama
    function validateName() {
        if (!inputName) return true;
        const val = inputName.value.trim();
        if (!val) {
            setFieldState('wrapName', 'hintName', true, 'Nama lengkap wajib diisi.');
            return false;
        }
        if (val.length < 2) {
            setFieldState('wrapName', 'hintName', true, 'Nama minimal 2 karakter.');
            return false;
        }
        setFieldState('wrapName', 'hintName', false, '✓ Nama valid');
        return true;
    }

    // Validasi email
    function validateEmail() {
        if (!inputEmail) return true;
        const val = inputEmail.value.trim();
        const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!val) {
            setFieldState('wrapEmail', 'hintEmail', true, 'Email wajib diisi.');
            return false;
        }
        if (!emailRe.test(val)) {
            setFieldState('wrapEmail', 'hintEmail', true, 'Format email tidak valid.');
            return false;
        }
        setFieldState('wrapEmail', 'hintEmail', false, '✓ Format email valid');
        return true;
    }

    // Validasi password
    function validatePassword() {
        if (!inputPass) return true;
        const val = inputPass.value;
        if (!val) {
            setFieldState('wrapPassword', 'hintPassword', true, 'Password wajib diisi.');
            return false;
        }
        if (val.length < 6) {
            setFieldState('wrapPassword', 'hintPassword', true, `Password minimal 6 karakter (sekarang ${val.length}).`);
            return false;
        }
        setFieldState('wrapPassword', 'hintPassword', false, '✓ Password kuat');
        // Re-validate konfirmasi jika sudah diisi
        if (inputConf && inputConf.value) validateConfirm();
        return true;
    }

    // Validasi konfirmasi
    function validateConfirm() {
        if (!inputConf || !inputPass) return true;
        const val  = inputConf.value;
        const pass = inputPass.value;
        if (!val) {
            setFieldState('wrapConfirm', 'hintConfirm', true, 'Konfirmasi password wajib diisi.');
            return false;
        }
        if (val !== pass) {
            setFieldState('wrapConfirm', 'hintConfirm', true, 'Password tidak cocok.');
            return false;
        }
        setFieldState('wrapConfirm', 'hintConfirm', false, '✓ Password cocok');
        return true;
    }

    // Event listeners realtime (trigger setelah user mulai mengetik / blur)
    let nameTouched = false, emailTouched = false, passTouched = false, confTouched = false;

    inputName?.addEventListener('blur', () => { nameTouched = true; validateName(); });
    inputName?.addEventListener('input', () => { if (nameTouched) validateName(); });

    inputEmail?.addEventListener('blur', () => { emailTouched = true; validateEmail(); });
    inputEmail?.addEventListener('input', () => { if (emailTouched) validateEmail(); });

    inputPass?.addEventListener('blur', () => { passTouched = true; validatePassword(); });
    inputPass?.addEventListener('input', () => { if (passTouched) validatePassword(); });

    inputConf?.addEventListener('blur', () => { confTouched = true; validateConfirm(); });
    inputConf?.addEventListener('input', () => { if (confTouched) validateConfirm(); });

    // Submit guard
    form.addEventListener('submit', (e) => {
        nameTouched = emailTouched = passTouched = confTouched = true;
        const ok = validateName() & validateEmail() & validatePassword() & validateConfirm();
        if (!ok) {
            e.preventDefault();
            // Scroll ke error pertama
            const firstError = form.querySelector('.input-error');
            firstError?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        // Loading state
        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.querySelector('h1').textContent = 'Mendaftar...';
        }
    });
}

window.togglePassword = togglePassword;