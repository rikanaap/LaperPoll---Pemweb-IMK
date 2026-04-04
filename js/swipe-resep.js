
// <button id="dislike">
// <span class="material-icons-round">close</span>
// </button>
// <button id="like">
// <span class="material-icons-round">favorite</span>
// </button>

//mengambil id "like" dan "dislike"

const likeBtn = document.getElementById("like");
const dislikeBtn = document.getElementById("dislike");

// hitung jumlah like
let likeCount = 0;

/* ambil card paling atas */
// membuat fungsi pake nama getTopCard
function getTopCard(){
    //ambil semua elemen dengan class .swipe-card yang ada di swipe-resep.html
    const cards = document.querySelectorAll(".swipe-card");
    // ambil elemen terakhir dari array 
    // jadi seperti di anggap paling atas
    return cards[cards.length - 1];
}

/* fungsi swipe */
// membuat fungsi swipe dengan parameter direction
// untuk membuat kanan dan kiri 
function swipe(direction){
    //ambil card paling atas 
    const card = getTopCard();
    //apabila kalau misal card nya itu tidak ada, fungsi nya bakalan berhenti 
    if(!card) return;

    //fungsi nya untuk animasi smooth
    card.style.transition = "0.3s ease";

    //ini fungsi untuk ke kanan
    if(direction === "right"){
        //geser card ke kanan 
        card.style.transform = "translateX(400px) rotate(20deg)";

         // tambah jumlah like
        likeCount++;

        // kalau sudah 3 kali like pindah halaman
        if(likeCount === 3){
            setTimeout(() => {
                window.location.href = "../html/filter-resep-swipe.html";
            }, 300);
        }


        //kalau bukan ke kanan berarti ke kiri 
    }else{
        //geser card ke kiri 
        card.style.transform = "translateX(-400px) rotate(-20deg)";
    }

    //menunggu 300ms (0.3 detik )
    //hapus card dari DOM
    setTimeout(() => card.remove(), 300);
}

/* tombol */
// kalau misalkan klick tombol love, akan swipe ke kanan
likeBtn.addEventListener("click", () => swipe("right"));
// kalau misalkan klick tombol love, akan swipe ke kiri
dislikeBtn.addEventListener("click", () => swipe("left"));

/* drag swipe */
//mengambil semua card dari .swipe-card
// jadi di looping satu persatu 
document.querySelectorAll(".swipe-card").forEach(card => {
    //menyimpan posisi awal pas di klick 
    let startX = 0;
    //menyimpan posisi untuk yang sekarang 
    let currentX = 0;
    //berfungsi untuk mengecek status lagi posisi drag atau tidak 
    let isDragging = false;

    //berfungsi untuk ketika klick / sentuh 
    const start = (e) => {
        //ditandai bahwa card sedang di drag 
        isDragging = true;
        //mengambil posisi awal 
        startX = e.touches ? e.touches[0].clientX : e.clientX;
        //mematikan animasi supaya realtime 
        card.style.transition = "none";
    };

    // berfungsi saat mouse bergerak 
    const move = (e) => {
        //kalau misalkan tidak di drag berarti posisi stop 
        if(!isDragging) return;

        //mengambil posisi yang sekarang 
        currentX = e.touches ? e.touches[0].clientX : e.clientX;
        //menghitung jarak geres 
        const diff = currentX - startX;

        //geser card sesuai dengan jarak nya 
        card.style.transform =
            `translateX(${diff}px) rotate(${diff/12}deg)`;
    };

    //berfungsi saat mouse di lepas
    const end = () => {
        //kalau misalkan tidak di drag stop 
        if(!isDragging) return;
        //matikan mode drag
        isDragging = false;

        //menghitung total geser 
        const diff = currentX - startX;

        //kalau misalkan di geser ke kanan 120px swipe ke kanan
        if(diff > 120) swipe("right");
        //kalau misalkan di geser ke kiri 120px swipe ke kiri
        else if(diff < -120) swipe("left");
        else{
            //mengaktifkan animasi 
            card.style.transition = "0.3s ease";
            //mengembalikan posisi awal nya 
            card.style.transform = "";
        }
    };

    /* mouse */
    //saat mouse di klick 
    card.addEventListener("mousedown", start);
    //saat moudse di geser 
    card.addEventListener("mousemove", move);
    //saat mouse di lepas 
    card.addEventListener("mouseup", end);
    //kalau keluar area stop untuk drag 
    card.addEventListener("mouseleave", end);

    /* touch */
    //ketika sentuh layar start
    card.addEventListener("touchstart", start);
    //ketika di geser jadi move 
    card.addEventListener("touchmove", move);
    //ketika di lepas jadi end
    card.addEventListener("touchend", end);
});