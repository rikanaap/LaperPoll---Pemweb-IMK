const MODES = ["signup", "signin", "forgot"]

const signupSection = document.getElementById("signupForm")
const signinSection = document.getElementById("signinForm")
const forgotSection = document.getElementById("forgotForm")
const inputsOTP = document.querySelectorAll('.otp-input');

let mode = "signup"

inputsOTP.forEach((input, index) => {
    input.addEventListener('input', (e) => {
        const value = e.target.value;
        if (value.length === 1 && index < inputsOTP.length - 1) {
            inputsOTP[index + 1].focus();
        }
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === "Backspace") {
            if (input.value === "" && index > 0) {
                inputsOTP[index - 1].focus();
            }
        }
    });
});

function formatMode(modeName) {
    modeName = modeName.toLowerCase()
    mode = (MODES.includes(modeName)) ? modeName : null
    if (!mode) throw Error("Mode tidak ditemukan")
}

function changeMode(modeName) {
    modeName = formatMode(modeName)
    checkMode();
}

function togglePassword(icon) {
    const input = icon.previousElementSibling;
    if (input.type === "password") {
        input.type = "text";
        icon.innerText = "visibility_off";
    } else {
        input.type = "password";
        icon.innerText = "remove_red_eye";
    }
}


window.changeMode = changeMode;
window.togglePassword = togglePassword;
