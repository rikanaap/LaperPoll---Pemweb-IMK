function hitungStatus(expiredDate) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const exp = new Date(expiredDate);
    exp.setHours(0, 0, 0, 0);
    const diffDays = Math.ceil((exp - today) / (1000 * 60 * 60 * 24));

    if (diffDays <= 0) return { status: "expired", label: "Habis", diffDays };
    if (diffDays <= 2) return { status: "hampir-habis", label: "Segera Habis", diffDays };
    return { status: "tersedia", label: "Tersedia", diffDays };
}

function renderBahan() {
    const grid = document.getElementById("bahanGrid");
    const bahan = JSON.parse(localStorage.getItem("kulkas_bahan") || "[]");
    const dummyBahan = [
        { nama: "Bawang Putih", jumlah: 6, satuan: "siung", expired: offsetDate(3) },
        { nama: "Telur", jumlah: 6, satuan: "butir", expired: offsetDate(3) },
        { nama: "Bawang Merah", jumlah: 5, satuan: "siung", expired: offsetDate(7) },
        { nama: "Ketumbar Bubuk", jumlah: 10, satuan: "gram", expired: offsetDate(7) },
        { nama: "Kunyit Bubuk", jumlah: 25, satuan: "gram", expired: offsetDate(7) },
        { nama: "Susu", jumlah: 200, satuan: "ml", expired: offsetDate(1) },
        { nama: "Toge", jumlah: 100, satuan: "gram", expired: offsetDate(1) },
        { nama: "Tomat", jumlah: 5, satuan: "buah", expired: offsetDate(1) },
        { nama: "Cabai Merah", jumlah: 0, satuan: "buah", expired: offsetDate(0) },
        { nama: "Beras", jumlah: 0, satuan: "liter", expired: offsetDate(0) },
    ];
    const semuaBahan = [...bahan, ...dummyBahan];

    grid.innerHTML = "";

    semuaBahan.forEach((item, index) => {
        const { status, label, diffDays } = hitungStatus(item.expired);

        const badgeClass = status === "tersedia" ? "badge-tersedia"
            : status === "hampir-habis" ? "badge-hampir"
            : "badge-expired";

        const infoText = item.jumlah > 0
            ? `${item.jumlah} ${item.satuan} · ${diffDays > 0 ? diffDays + " hari" : "Habis"}`
            : `0 ${item.satuan}`;

        const card = document.createElement("div");
        card.className = "bahan-card";
        card.dataset.status = status;
        card.innerHTML = `
            <h2 class="font-jakarta font-semibold text-title2 text-secondary-normal">${item.nama}</h2>
            <p class="font-jakarta font-regular text-caption text-primary-darker">${infoText}</p>
            <span class="status-badge ${badgeClass} font-jakarta font-medium text-caption">${label}</span>
        `;

        grid.appendChild(card);
    });

    pasangFilter();
}

function offsetDate(days) {
    const d = new Date();
    d.setDate(d.getDate() + days);
    return d.toISOString().split("T")[0];
}

function pasangFilter() {
    const tabs = document.querySelectorAll(".filter-tab");
    const cards = document.querySelectorAll(".bahan-card");

    tabs.forEach(tab => {
        tab.addEventListener("click", () => {
            tabs.forEach(t => {
                t.classList.remove("active");
                t.setAttribute("aria-selected", "false");
            });
            tab.classList.add("active");
            tab.setAttribute("aria-selected", "true");

            const filter = tab.dataset.filter;
            cards.forEach(card => {
                if (filter === "semua" || card.dataset.status === filter) {
                    card.style.display = "flex";
                } else {
                    card.style.display = "none";
                }
            });
        });
    });
}

document.getElementById("btnTambah").addEventListener("click", () => {
    window.location.href = "tambah-bahan.html";
});

renderBahan();