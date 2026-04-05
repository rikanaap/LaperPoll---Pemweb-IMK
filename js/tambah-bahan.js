const dataBahan = [
    { nama: "Telur", satuan: ["butir", "gram"] },
    { nama: "Susu", satuan: ["ml", "liter"] },
    { nama: "Bawang Merah", satuan: ["gram", "kg", "siung"] },
    { nama: "Bawang Putih", satuan: ["gram", "kg", "siung"] },
    { nama: "Cabai Merah", satuan: ["gram", "buah"] },
    { nama: "Cabai Rawit", satuan: ["gram", "buah"] },
    { nama: "Tomat", satuan: ["gram", "buah"] },
    { nama: "Kentang", satuan: ["gram", "kg", "buah"] },
    { nama: "Wortel", satuan: ["gram", "kg", "buah"] },
    { nama: "Bayam", satuan: ["gram", "ikat"] },
    { nama: "Kangkung", satuan: ["gram", "ikat"] },
    { nama: "Tahu", satuan: ["gram", "buah", "potong"] },
    { nama: "Tempe", satuan: ["gram", "papan"] },
    { nama: "Ayam", satuan: ["gram", "kg", "potong"] },
    { nama: "Daging Sapi", satuan: ["gram", "kg"] },
    { nama: "Ikan Lele", satuan: ["gram", "ekor"] },
    { nama: "Udang", satuan: ["gram", "ekor"] },
    { nama: "Tepung Terigu", satuan: ["gram", "kg"] },
    { nama: "Gula Pasir", satuan: ["gram", "kg", "sdm"] },
    { nama: "Garam", satuan: ["gram", "sdt", "sdm"] },
    { nama: "Minyak Goreng", satuan: ["ml", "liter", "sdm"] },
    { nama: "Kecap Manis", satuan: ["ml", "sdm"] },
    { nama: "Saus Tiram", satuan: ["ml", "sdm"] },
    { nama: "Mentega", satuan: ["gram", "sdm"] },
    { nama: "Keju", satuan: ["gram"] },
    { nama: "Nasi", satuan: ["gram", "porsi"] },
    { nama: "Mie", satuan: ["gram", "bungkus"] },
    { nama: "Jahe", satuan: ["gram", "ruas"] },
    { nama: "Kunyit", satuan: ["gram", "ruas"] },
    { nama: "Serai", satuan: ["gram", "batang"] },
    { nama: "Daun Salam", satuan: ["lembar"] },
    { nama: "Santan", satuan: ["ml", "liter"] },
];

const searchInput  = document.getElementById("searchBahan");
const dropdown     = document.getElementById("bahanDropdown");
const clearBtn     = document.getElementById("clearSearch");
const satuanSelect = document.getElementById("satuanBahan");
const satuanHint   = document.getElementById("satuanHint");
const jumlahInput  = document.getElementById("jumlahBahan");
const expiredInput = document.getElementById("expiredDate");

let selectedBahan = null;

searchInput.addEventListener("input", () => {
    const q = searchInput.value.trim().toLowerCase();
    clearBtn.style.display = q ? "block" : "none";
    if (!q) { dropdown.style.display = "none"; return; }
    const results = dataBahan.filter(b => b.nama.toLowerCase().includes(q));
    renderDropdown(results);
});

clearBtn.addEventListener("click", () => {
    searchInput.value = "";
    clearBtn.style.display = "none";
    dropdown.style.display = "none";
    resetSatuan();
    selectedBahan = null;
});

function renderDropdown(results) {
    dropdown.innerHTML = "";
    if (!results.length) {
        dropdown.innerHTML = '<li class="dropdown-empty font-jakarta text-caption text-primary-darker">Bahan tidak ditemukan</li>';
        dropdown.style.display = "block";
        return;
    }
    results.forEach(b => {
        const li = document.createElement("li");
        li.className = "dropdown-item font-jakarta text-body";
        li.textContent = b.nama;
        li.setAttribute("role", "option");
        li.addEventListener("click", () => selectBahan(b));
        dropdown.appendChild(li);
    });
    dropdown.style.display = "block";
}

function selectBahan(bahan) {
    selectedBahan = bahan;
    searchInput.value = bahan.nama;
    dropdown.style.display = "none";
    clearBtn.style.display = "block";
    populateSatuan(bahan.satuan);
}

function populateSatuan(satuanList) {
    satuanSelect.innerHTML = "";
    satuanList.forEach((s, i) => {
        const opt = document.createElement("option");
        opt.value = s;
        opt.textContent = s;
        if (i === 0) opt.selected = true;
        satuanSelect.appendChild(opt);
    });
    satuanHint.textContent = `Satuan tersedia: ${satuanList.join(", ")}`;
    satuanHint.style.display = "block";
}

function resetSatuan() {
    satuanSelect.innerHTML = '<option value="" disabled selected>Pilih satuan</option>';
    satuanHint.style.display = "none";
}

document.getElementById("btnPlus").addEventListener("click", () => {
    jumlahInput.value = parseInt(jumlahInput.value || 0) + 1;
});
document.getElementById("btnMinus").addEventListener("click", () => {
    const val = parseInt(jumlahInput.value || 1);
    if (val > 1) jumlahInput.value = val - 1;
});

document.querySelectorAll(".expired-chip").forEach(chip => {
    chip.addEventListener("click", () => {
        document.querySelectorAll(".expired-chip").forEach(c => c.classList.remove("active"));
        chip.classList.add("active");
        const days = parseInt(chip.dataset.days);
        const d = new Date();
        d.setDate(d.getDate() + days);
        expiredInput.value = d.toISOString().split("T")[0];
    });
});

expiredInput.min = new Date().toISOString().split("T")[0];

document.addEventListener("click", (e) => {
    if (!e.target.closest(".search-wrapper")) {
        dropdown.style.display = "none";
    }
});

document.getElementById("btnTambahBahan").addEventListener("click", () => {
    if (!selectedBahan) {
        alert("Pilih nama bahan terlebih dahulu!");
        return;
    }
    if (!satuanSelect.value) {
        alert("Pilih satuan terlebih dahulu!");
        return;
    }
    if (!jumlahInput.value || jumlahInput.value < 1) {
        alert("Masukkan jumlah yang valid!");
        return;
    }
    if (!expiredInput.value) {
        alert("Pilih tanggal expired terlebih dahulu!");
        return;
    }

    const existing = JSON.parse(localStorage.getItem("kulkas_bahan") || "[]");
    existing.unshift({
        nama: selectedBahan.nama,
        jumlah: parseInt(jumlahInput.value),
        satuan: satuanSelect.value,
        expired: expiredInput.value
    });
    localStorage.setItem("kulkas_bahan", JSON.stringify(existing));

    window.location.href = "kulkas-digital.html";
});