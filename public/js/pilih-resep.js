// pilih-resep.js - Final Fix (Horizontal Card + Grid)

const labelWaktu = { sarapan: "Sarapan", siang: "Makan Siang", malam: "Makan Malam" };
const bulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"];

// Ambil slot dari query string
const params = new URLSearchParams(window.location.search);
const slot = params.get("slot");

let slotDate = null;
let slotWaktu = null;

if (slot) {
    const firstDash = slot.indexOf("-");
    const secondDash = slot.indexOf("-", firstDash + 1);
    const thirdDash = slot.indexOf("-", secondDash + 1);

    if (thirdDash !== -1) {
        slotDate = slot.substring(0, thirdDash);        // "2026-05-06"
        slotWaktu = slot.substring(thirdDash + 1);       // "sarapan"
    }
}

// Tampilkan info slot di header
const slotLabel = document.getElementById("slotLabel");
if (slotLabel) {
    if (slotDate && slotWaktu) {
        const d = new Date(slotDate + "T00:00:00");
        const hariNama = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"][d.getDay()];
        const tgl = `${d.getDate()} ${bulan[d.getMonth()]} ${d.getFullYear()}`;
        slotLabel.textContent = `${hariNama}, ${tgl} · ${labelWaktu[slotWaktu] || slotWaktu}`;
    } else {
        slotLabel.textContent = "Pilih slot di meal planner terlebih dahulu.";
        const warningEl = document.getElementById("slotWarning");
        if (warningEl) warningEl.style.display = "flex";
    }
}

// ======================= DATA RESEP =======================
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

// ======================= RENDER FUNCTION =======================
const resepList = document.getElementById("resepList");

function renderResep(data) {
    resepList.innerHTML = "";

    if (!data.length) {
        resepList.innerHTML = `<p class="empty-state font-jakarta text-body">🍽️ Resep tidak ditemukan.</p>`;
        return;
    }

    data.forEach(resep => {
        const card = document.createElement("div");
        card.className = "resep" + (!slotDate || !slotWaktu ? " no-slot" : "");
        card.innerHTML = `
            <div class="resep-content">
                <div class="resep-logo">
                    <span class="material-icons-round">${resep.icon}</span>
                </div>
                <div class="resep-detail">
                    <h1 class="font-jakarta font-semibold">${escapeHtml(resep.nama)}</h1>
                    <div class="resep-content-detail">
                        <div>
                            <span class="material-icons-round">watch_later</span>
                            <span>${resep.waktu}</span>
                        </div>
                        <div>
                            <span class="material-icons-round">local_fire_department</span>
                            <span>${resep.kalori}</span>
                        </div>
                    </div>
                </div>
            </div>
            <span class="material-icons-round arrow-icon">arrow_forward_ios</span>
        `;

        if (slotDate && slotWaktu) {
            card.addEventListener("click", () => {
                const storageKey = `meal_${slotDate}_${slotWaktu}`;
                localStorage.setItem(storageKey, JSON.stringify({
                    nama: resep.nama,
                    waktu: resep.waktu,
                    kalori: resep.kalori,
                    icon: resep.icon
                }));
                window.location.href = window.mealPlannerUrl || "/meal-planner";
            });
        }

        resepList.appendChild(card);
    });
}

// Helper sederhana untuk menghindari XSS
function escapeHtml(str) {
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// Render awal
renderResep(dataResep);

// Search
const searchInput = document.getElementById("searchResep");
if (searchInput) {
    searchInput.addEventListener("input", (e) => {
        const q = e.target.value.toLowerCase().trim();
        const filtered = dataResep.filter(r => r.nama.toLowerCase().includes(q));
        renderResep(filtered);
    });
}