import { changePage } from "../global";

const MODES = ["signup", "signin", "forgot"]


const signupSection = document.getElementById("signupForm")
const signinSection = document.getElementById("signinForm")
const forgotSection = document.getElementById("forgotForm")
const inputsOTP = document.querySelectorAll('.otp-input');


let mode = "signup"

inputsOTP.forEach((input, index) => {
    input.addEventListener('input', (e) => {
        const value = e.target.value;
        if (value.length === 1 && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === "Backspace") {
            if (input.value === "" && index > 0) {
                inputs[index - 1].focus();
            }
        }
    });
});

function checkMode() {
    switch (mode) {
        case "signin":
            signupSection.style.display = "none";
            signinSection.style.display = "flex";
            forgotSection.style.display = "none";
            break;
        case "forgot":
            signupSection.style.display = "none";
            signinSection.style.display = "none";
            forgotSection.style.display = "flex";
            break;
        default:
            signupSection.style.display = "flex";
            signinSection.style.display = "none";
            forgotSection.style.display = "none";
    }
}

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

function kirimData(modeName) {
    modeName = formatMode(modeName);
    switch (modeName) {
        case "signup":
            changePage("main-menu")
            break
        case "forgot":
            break;
        default:
            changePage("main-menu")

    }
}