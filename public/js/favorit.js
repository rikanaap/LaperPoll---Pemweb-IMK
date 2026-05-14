function toggleFavorite(button) {
    // Animasi klik
    button.style.transform = "scale(1.3)";
    
    setTimeout(() => {
        button.style.transform = "scale(1)";
        button.classList.toggle('active');
        
        // Logika sederhana: Jika tidak aktif, beri efek pudar (seolah akan dihapus dari daftar favorit)
        const card = button.closest('.recipe-card');
        if (!button.classList.contains('active')) {
            card.style.opacity = "0.5";
            console.log("Resep dihapus dari favorit");
        } else {
            card.style.opacity = "1";
            console.log("Resep ditambahkan ke favorit");
        }
    }, 200);
}