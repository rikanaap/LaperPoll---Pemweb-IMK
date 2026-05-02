// ======================================
// Swipe Resep - LaperPoll
// ======================================

const likeButton = document.getElementById("like");
const dislikeButton = document.getElementById("dislike");

let likeCount = 0;
const maxLike = 3;


// ======================================
// Ambil Card Paling Atas
// ======================================
function getTopCard() {
    const cards = document.querySelectorAll(".swipe-card");
    return cards[cards.length - 1];
}


// ======================================
// Hapus Card Setelah Animasi
// ======================================
function removeCard(card) {
    setTimeout(() => {
        card.remove();
    }, 300);
}


// ======================================
// Redirect Jika Like Sudah 3x
// ======================================
function checkLikeLimit() {
    if (likeCount >= maxLike) {
        setTimeout(() => {
            window.location.href = "/filter-resep-swipe";
        }, 300);
    }
}


// ======================================
// Swipe Card
// direction = right / left
// ======================================
function swipeCard(direction) {
    const card = getTopCard();

    if (!card) return;

    card.style.transition = "0.3s ease";

    if (direction === "right") {
        card.style.transform = "translateX(400px) rotate(20deg)";
        likeCount++;
        checkLikeLimit();
    } else {
        card.style.transform = "translateX(-400px) rotate(-20deg)";
    }

    removeCard(card);
}


// ======================================
// Event Tombol
// ======================================
likeButton.addEventListener("click", () => swipeCard("right"));
dislikeButton.addEventListener("click", () => swipeCard("left"));


// ======================================
// Drag Swipe
// ======================================
function enableDrag(card) {
    let startX = 0;
    let currentX = 0;
    let isDragging = false;

    const startDrag = (event) => {
        isDragging = true;

        startX = event.touches
            ? event.touches[0].clientX
            : event.clientX;

        card.style.transition = "none";
    };

    const moveDrag = (event) => {
        if (!isDragging) return;

        currentX = event.touches
            ? event.touches[0].clientX
            : event.clientX;

        const diff = currentX - startX;

        card.style.transform =
            `translateX(${diff}px) rotate(${diff / 12}deg)`;
    };

    const endDrag = () => {
        if (!isDragging) return;

        isDragging = false;

        const diff = currentX - startX;

        if (diff > 120) {
            swipeCard("right");
        } else if (diff < -120) {
            swipeCard("left");
        } else {
            card.style.transition = "0.3s ease";
            card.style.transform = "";
        }
    };

    // Mouse
    card.addEventListener("mousedown", startDrag);
    card.addEventListener("mousemove", moveDrag);
    card.addEventListener("mouseup", endDrag);
    card.addEventListener("mouseleave", endDrag);

    // Touch
    card.addEventListener("touchstart", startDrag);
    card.addEventListener("touchmove", moveDrag);
    card.addEventListener("touchend", endDrag);
}


// ======================================
// Init Semua Card
// ======================================
document.querySelectorAll(".swipe-card").forEach(enableDrag);