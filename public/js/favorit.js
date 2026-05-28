// favorit.js — LaperPoll

// Hapus favorit langsung dari halaman favorit
document.querySelectorAll('.fav-remove-btn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
        e.preventDefault();
        e.stopPropagation();

        const resepId = btn.dataset.resepId;
        const card    = btn.closest('.fav-card-link');

        // Visual feedback
        btn.classList.add('removing');
        btn.disabled = true;

        try {
            const res = await fetch(`${TOGGLE_BASE_URL}/${resepId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            });

            const data = await res.json();

            if (!data.isFavorite) {
                // Animasi keluar lalu hapus card
                card.style.transition = 'opacity 0.3s, transform 0.3s';
                card.style.opacity    = '0';
                card.style.transform  = 'scale(0.9)';
                setTimeout(() => {
                    card.remove();
                    updateCount();
                }, 300);
            } else {
                btn.classList.remove('removing');
                btn.disabled = false;
            }

        } catch (err) {
            console.error('Gagal hapus favorit:', err);
            btn.classList.remove('removing');
            btn.disabled = false;
        }
    });
});

function updateCount() {
    const remaining = document.querySelectorAll('.fav-card-link').length;
    const subEl     = document.querySelector('.fav-sub');
    if (subEl) subEl.textContent = `${remaining} resep tersimpan`;

    // Kalau sudah kosong, tampilkan empty state
    if (remaining === 0) {
        const grid = document.querySelector('.fav-grid');
        if (grid) {
            grid.outerHTML = `
                <div class="fav-empty">
                    <span class="material-icons-round fav-empty-icon">favorite_border</span>
                    <p class="fav-empty-title font-semibold">Belum ada resep favorit</p>
                    <p class="fav-empty-sub">Ketuk ikon hati di detail resep untuk menyimpannya di sini.</p>
                    <a href="/" class="fav-empty-btn font-semibold">
                        <span class="material-icons-round">explore</span>
                        Jelajahi Resep
                    </a>
                </div>`;
        }
    }
}