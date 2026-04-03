const likeBtn = document.getElementById("like");
const dislikeBtn = document.getElementById("dislike");

function getTopCard(){
    const cards = document.querySelectorAll(".swipe-card");
    return cards[cards.length - 1];
}

function swipeCard(direction){
    const card = getTopCard();
    if(!card) return;

    card.style.transition = "0.3s";

    if(direction === "right"){
        card.style.transform = "translateX(400px) rotate(20deg)";
    }else{
        card.style.transform = "translateX(-400px) rotate(-20deg)";
    }

    setTimeout(()=> card.remove(),300);
}

// tombol klik
likeBtn.addEventListener("click", ()=> swipeCard("right"));
dislikeBtn.addEventListener("click", ()=> swipeCard("left"));


// DRAG SWIPE
document.querySelectorAll(".swipe-card").forEach(card => {

    let startX = 0;
    let currentX = 0;
    let isDragging = false;

    const start = (e)=>{
        isDragging = true;
        startX = e.type.includes("mouse") ? e.clientX : e.touches[0].clientX;
        card.style.transition = "none";
    }

    const move = (e)=>{
        if(!isDragging) return;

        currentX = e.type.includes("mouse") ? e.clientX : e.touches[0].clientX;
        const diffX = currentX - startX;

        card.style.transform = `translateX(${diffX}px) rotate(${diffX/10}deg)`;
    }

    const end = ()=>{
        if(!isDragging) return;
        isDragging = false;

        const diffX = currentX - startX;

        if(diffX > 120){
            swipeCard("right");
        }else if(diffX < -120){
            swipeCard("left");
        }else{
            card.style.transition = "0.3s";
            card.style.transform = "translateX(0) rotate(0)";
        }
    }

    card.addEventListener("mousedown", start);
    card.addEventListener("mousemove", move);
    card.addEventListener("mouseup", end);
    card.addEventListener("mouseleave", end);

    card.addEventListener("touchstart", start);
    card.addEventListener("touchmove", move);
    card.addEventListener("touchend", end);

});