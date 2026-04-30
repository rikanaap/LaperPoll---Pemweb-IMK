const dataResep = [
    { id: 1, nama: "Nasi Goreng Spesial", waktu: "25 mnt", kalori: "450 kkal", icon: "rice_bowl" },
    { id: 2, nama: "Ayam Bakar Kecap", waktu: "40 mnt", kalori: "480 kkal", icon: "lunch_dining" },
    { id: 3, nama: "Mie Ayam Bakso", waktu: "20 mnt", kalori: "510 kkal", icon: "ramen_dining" },
    { id: 4, nama: "Soto Ayam", waktu: "35 mnt", kalori: "420 kkal", icon: "soup_kitchen" },
    { id: 5, nama: "Rendang Daging", waktu: "60 mnt", kalori: "520 kkal", icon: "lunch_dining" },
    { id: 6, nama: "Gado-Gado", waktu: "20 mnt", kalori: "390 kkal", icon: "eco" },
    { id: 7, nama: "Bolu Ketan", waktu: "30 mnt", kalori: "320 kkal", icon: "cake" },
    { id: 8, nama: "Pancake Pisang", waktu: "25 mnt", kalori: "340 kkal", icon: "breakfast_dining" },
    { id: 9, nama: "Bubur Ayam", waktu: "15 mnt", kalori: "280 kkal", icon: "soup_kitchen" },
    { id: 10, nama: "Roti Bakar Keju", waktu: "20 mnt", kalori: "550 kkal", icon: "breakfast_dining" },
    { id: 11, nama: "Sup Sayur Tahu", waktu: "30 mnt", kalori: "210 kkal", icon: "soup_kitchen" },
    { id: 12, nama: "Tempe Orek", waktu: "20 mnt", kalori: "290 kkal", icon: "lunch_dining" },
    { id: 13, nama: "Capcay Kuah", waktu: "25 mnt", kalori: "230 kkal", icon: "eco" },
    { id: 14, nama: "Oatmeal Buah", waktu: "10 mnt", kalori: "250 kkal", icon: "breakfast_dining" },
    { id: 15, nama: "Nasi Uduk", waktu: "30 mnt", kalori: "400 kkal", icon: "rice_bowl" },
    { id: 16, nama: "Ikan Bakar Bumbu", waktu: "45 mnt", kalori: "460 kkal", icon: "set_meal" },
    { id: 17, nama: "Tumis Kangkung", waktu: "15 mnt", kalori: "180 kkal", icon: "eco" },
    { id: 18, nama: "Lontong Sayur", waktu: "35 mnt", kalori: "350 kkal", icon: "rice_bowl" },
    { id: 19, nama: "Rawon Daging", waktu: "50 mnt", kalori: "530 kkal", icon: "soup_kitchen" },
    { id: 20, nama: "Roti Telur Dadar", waktu: "15 mnt", kalori: "310 kkal", icon: "breakfast_dining" },
    { id: 21, nama: "Perkedel Jagung", waktu: "25 mnt", kalori: "270 kkal", icon: "lunch_dining" },
];

const labelHari = {
    sen: "Senin", sel: "Selasa", rab: "Rabu",
    kam: "Kamis", jum: "Jumat", sab: "Sabtu", min: "Minggu"
};

const labelWaktu = {
    sarapan: "Sarapan",
    siang: "Makan Siang",
    malam: "Makan Malam"
};

const params = new URLSearchParams(window.location.search);
const slot = params.get("slot"); 

const slotLabel = document.getElementById("slotLabel");
if (slot) {
    const [hari, waktu] = slot.split("-");
    const namaHari = labelHari[hari] || hari;
    const namaWaktu = labelWaktu[waktu] || waktu;
    slotLabel.textContent = `${namaHari} · ${namaWaktu}`;
} else {
    slotLabel.textContent = "Pilih slot di meal planner terlebih dahulu.";
}

const resepList = document.getElementById("resepList");

function renderResep(data) {
    resepList.innerHTML = "";

    if (!data.length) {
        resepList.innerHTML = `<p class="empty-state font-jakarta text-body">Resep tidak ditemukan.</p>`;
        return;
    }

    data.forEach(resep => {
        const card = document.createElement("div");
        card.className = "resep";
        card.innerHTML = `
            <div class="resep-content">
                <div class="resep-logo">
                    <span class="material-icons-round text-h3 text-accent-dark">${resep.icon}</span>
                </div>
                <div class="resep-detail">
                    <h1 class="font-jakarta text-title2 text-black font-regular">${resep.nama}</h1>
                    <div class="resep-content-detail">
                        <div>
                            <span class="material-icons-round text-title2">watch_later</span>
                            <p class="text-body font-jakarta font-medium text-black">${resep.waktu}</p>
                        </div>
                        <div>
                            <span class="material-icons-round text-title2">local_fire_department</span>
                            <p class="text-body font-jakarta font-medium text-black">${resep.kalori}</p>
                        </div>
                    </div>
                </div>
            </div>
            <span class="material-icons-round text-h4 text-secondary-normal">arrow_forward_ios</span>
        `;

        card.addEventListener("click", () => {
            if (!slot) return;
            localStorage.setItem(
                `meal_${slot}`,
                JSON.stringify({ nama: resep.nama, waktu: resep.waktu, kalori: resep.kalori, icon: resep.icon })
            );
            window.location.href = "meal-planner.html";
        });

        resepList.appendChild(card);
    });
}

renderResep(dataResep);

document.getElementById("searchResep").addEventListener("input", (e) => {
    const q = e.target.value.toLowerCase().trim();
    const filtered = dataResep.filter(r => r.nama.toLowerCase().includes(q));
    renderResep(filtered);
});