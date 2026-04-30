const stars = document.querySelectorAll('.star-icon');
        const btnSubmit = document.getElementById("btn-submit-review"); 
        let currentRating = 0;

        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                currentRating = this.getAttribute('data-value');

              
                stars.forEach((s, index) => {
                    if (index < currentRating) {
                        s.innerText = 'star'; 
                        s.classList.add('selected');
                    } else {
                        s.innerText = 'star_outline'; 
                        s.classList.remove('selected');
                    }
                });
                console.log("Rating terpilih:", currentRating);
            });
        });

        
        if (btnSubmit) {
            btnSubmit.addEventListener('click', function(e) {
                e.preventDefault(); 

                if (currentRating == 0) {
                    alert("Mohon pilih rating bintang terlebih dahulu!");
                } else {
                    alert("Terima kasih! Ulasan Anda berhasil dikirim.");
                   
                    window.location.href = 'detail-resep.html';
                }
            });
        } else {
            console.error("Tombol dengan ID 'btn-submit-review' tidak ditemukan!");
        }