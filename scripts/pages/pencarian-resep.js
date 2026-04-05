const checkboxes = document.querySelectorAll('.bahan-item input[type="checkbox"]');
const terapkanBtn = document.getElementById('terapkanBtn');

checkboxes.forEach(cb => {
    cb.addEventListener('change', () => {

        const adaYangDipilih =
        document.querySelectorAll('.bahan-item input[type="checkbox"]:checked').length > 0;

        if(adaYangDipilih){
            terapkanBtn.style.display = "block";
        }else{
            terapkanBtn.style.display = "none";
        }
    });
});

terapkanBtn.addEventListener("click", () => {
    window.location.href = "../html/filter-pencarian-resep.html";
});