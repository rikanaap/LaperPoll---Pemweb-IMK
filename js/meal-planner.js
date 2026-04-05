const labelHari = {
    sen: "Senin", sel: "Selasa", rab: "Rabu",
    kam: "Kamis", jum: "Jumat", sab: "Sabtu", min: "Minggu"
};

const waktuList = ["sarapan", "siang", "malam"];

const iconWaktu = {
    sarapan: "wb_sunny",
    siang: "restaurant",
    malam: "bedtime"
};

const labelWaktu = {
    sarapan: "SARAPAN",
    siang: "MAKAN SIANG",
    malam: "MAKAN MALAM"
};

function renderSemuaSlot() {
    const hariList = ["sen", "sel", "rab", "kam", "jum", "sab", "min"];

    hariList.forEach(hari => {
        const container = document.getElementById(`content-${hari}`);
        if (!container) return;

        container.innerHTML = "";

        waktuList.forEach(waktu => {
            const key = `meal_${hari}-${waktu}`;
            const data = localStorage.getItem(key);

            const section = document.createElement("div");
            section.className = "meal-section flex flex-col gap-2";

            if (data) {
                const resep = JSON.parse(data);
                section.innerHTML = `
                    <div class="meal-section-header flex flex-row">
                        <span class="material-icons-round meal-icon">${iconWaktu[waktu]}</span>
                        <p class="font-jakarta font-bold text-caption meal-label">${labelWaktu[waktu]}</p>
                        <button class="meal-action font-jakarta font-bold text-caption hapus-btn"
                            data-key="${key}">HAPUS</button>
                    </div>
                    <div class="meal-card flex flex-row gap-3">
                        <div class="meal-img-placeholder"></div>
                        <div class="meal-info flex flex-col gap-1">
                            <h2 class="font-jakarta font-semibold text-title2 text-secondary-normal">${resep.nama}</h2>
                            <p class="font-jakarta font-regular text-caption text-primary-darker">${resep.waktu} · ${resep.kalori}</p>
                        </div>
                    </div>
                `;
            } else {
                section.innerHTML = `
                    <div class="meal-section-header flex flex-row">
                        <span class="material-icons-round meal-icon">${iconWaktu[waktu]}</span>
                        <p class="font-jakarta font-bold text-caption meal-label">${labelWaktu[waktu]}</p>
                    </div>
                    <a href="pilih-resep.html?slot=${hari}-${waktu}" class="slot-kosong flex flex-row gap-3">
                        <span class="material-icons-round slot-kosong-icon">add_circle_outline</span>
                        <p class="font-jakarta font-medium text-body text-primary-darker">Tambah resep</p>
                    </a>
                `;
            }

            container.appendChild(section);
        });
    });

    document.querySelectorAll(".hapus-btn").forEach(btn => {
        btn.addEventListener("click", (e) => {
            const key = e.target.dataset.key;
            localStorage.removeItem(key);
            renderSemuaSlot(); // re-render
        });
    });
}

renderSemuaSlot();