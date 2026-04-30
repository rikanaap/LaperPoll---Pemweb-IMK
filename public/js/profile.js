const fabBtn     = document.getElementById("fabBtn");
const fabIcon    = document.getElementById("fabIcon");
const fabMenu    = document.getElementById("fabMenu");
const fabOverlay = document.getElementById("fabOverlay");

let isOpen = false;

function openFAB() {
    isOpen = true;
    fabMenu.classList.add("active");
    fabOverlay.classList.add("active");
    fabIcon.textContent = "close";
    fabIcon.classList.add("rotated");
}

function closeFAB() {
    isOpen = false;
    fabMenu.classList.remove("active");
    fabOverlay.classList.remove("active");
    fabIcon.textContent = "add";
    fabIcon.classList.remove("rotated");
}

fabBtn.addEventListener("click", () => {
    isOpen ? closeFAB() : openFAB();
});

fabOverlay.addEventListener("click", closeFAB);