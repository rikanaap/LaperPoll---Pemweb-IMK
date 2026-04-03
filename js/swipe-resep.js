const likeBtn = document.getElementById("like");
const dislikeBtn = document.getElementById("dislike");

/* ambil card paling atas */
function getTopCard(){
    const cards = document.querySelectorAll(".swipe-card");
    return cards[cards.length - 1];
}

/* fungsi swipe */
function swipe(direction){
    const card = getTopCard();
    if(!card) return;

    card.style.transition = "0.3s ease";

    if(direction === "right"){
        card.style.transform = "translateX(400px) rotate(20deg)";
    }else{
        card.style.transform = "translateX(-400px) rotate(-20deg)";
    }

    setTimeout(() => card.remove(), 300);
}

/* tombol */
likeBtn.addEventListener("click", () => swipe("right"));
dislikeBtn.addEventListener("click", () => swipe("left"));

/* drag swipe */
document.querySelectorAll(".swipe-card").forEach(card => {

    let startX = 0;
    let currentX = 0;
    let isDragging = false;

    const start = (e) => {
        isDragging = true;
        startX = e.touches ? e.touches[0].clientX : e.clientX;
        card.style.transition = "none";
    };

    const move = (e) => {
        if(!isDragging) return;

        currentX = e.touches ? e.touches[0].clientX : e.clientX;
        const diff = currentX - startX;

        card.style.transform =
            `translateX(${diff}px) rotate(${diff/12}deg)`;
    };

    const end = () => {
        if(!isDragging) return;
        isDragging = false;

        const diff = currentX - startX;

        if(diff > 120) swipe("right");
        else if(diff < -120) swipe("left");
        else{
            card.style.transition = "0.3s ease";
            card.style.transform = "";
        }
    };

    /* mouse */
    card.addEventListener("mousedown", start);
    card.addEventListener("mousemove", move);
    card.addEventListener("mouseup", end);
    card.addEventListener("mouseleave", end);

    /* touch */
    card.addEventListener("touchstart", start);
    card.addEventListener("touchmove", move);
    card.addEventListener("touchend", end);
});