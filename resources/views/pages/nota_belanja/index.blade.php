@extends('layouts.app')

@section('title', 'Nota Belanja - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/nota-belanja.css') }}">
@endpush

@section('content')
<main class="main-content flex flex-col">

    {{-- NAVBAR --}}
    <x-navbar :back="true"></x-navbar>

    {{-- KONTEN UTAMA --}}
    <div class="nota-konten flex flex-col gap-3">

        {{-- HEADER CARD --}}
        <div class="header-card flex flex-row">
            <h1 class="font-jakarta font-bold text-h5 kulkas-title">Nota Belanja</h1>
            <div class="nota-date flex flex-row gap-1" id="notaDateDisplay">
                <span class="material-icons-round nota-date-icon">calendar_today</span>
                <p class="font-jakarta font-medium text-caption nota-date-text" id="notaDateText">—</p>
            </div>
        </div>

        {{-- PROGRESS CARD --}}
        <div class="progress-card flex flex-col gap-2">
            <div class="progress-header flex flex-row gap-2">
                <span class="material-icons-round progress-cart-icon">shopping_cart</span>
                <p class="font-jakarta font-bold text-title2 text-secondary-normal" style="flex:1;">Daftar Belanja</p>
                <p class="font-jakarta font-bold text-title2 kulkas-title" id="progressLabel">0/0 ✓</p>
            </div>
            <div class="progress-bar-track">
                <div class="progress-bar-fill" id="progressFill" style="width:0%;"></div>
            </div>
            <p class="font-jakarta font-regular text-caption text-primary-darker" style="text-align:right;" id="progressPct">0% selesai</p>
        </div>

        {{-- BAHAN LIST (digenerate dari localStorage) --}}
        <div id="bahanList" class="flex flex-col gap-3"></div>

        {{-- EMPTY STATE --}}
        <div class="nota-empty hidden flex flex-col gap-2" id="notaEmpty">
            <span class="material-icons-round empty-icon">receipt_long</span>
            <p class="font-jakarta font-medium text-body text-secondary-normal">Belum ada resep di meal planner.</p>
            <a href="{{ route('meal-planner.index') }}" class="font-jakarta font-semibold text-body" style="color:#8C2A1A;">← Kembali ke Meal Planner</a>
        </div>

        {{-- BELUM DIBELI --}}
        <div class="belum-dibeli flex flex-row" id="belumDibeli" style="display:none!important;">
            <p class="font-jakarta font-medium text-body text-primary-darker">Item belum dibeli</p>
            <p class="font-jakarta font-bold text-body kulkas-title" id="belumDibeliCount">0 item</p>
        </div>

    </div>

</main>
@endsection

@push('scripts')
<script>
// ─── Ambil semua bahan dari meal planner localStorage ─────────
const waktuList = ["sarapan","siang","malam"];

// Bahan per resep (static map — bisa diganti API)
const bahanResep = {
    "Nasi Goreng Spesial":  [{ nama:"Nasi",          qty:"200 gram",  kat:"KARBOHIDRAT"}, {nama:"Telur",   qty:"2 butir", kat:"PROTEIN"}, {nama:"Bawang Merah",qty:"30 gram", kat:"BUMBU"}, {nama:"Bawang Putih",qty:"20 gram",kat:"BUMBU"}, {nama:"Kecap Manis",qty:"2 sdm",kat:"BUMBU"}],
    "Ayam Bakar Kecap":    [{ nama:"Ayam",           qty:"500 gram",  kat:"PROTEIN"},    {nama:"Kecap Manis",qty:"3 sdm",kat:"BUMBU"}, {nama:"Bawang Putih",qty:"30 gram",kat:"BUMBU"}],
    "Bolu Ketan":          [{ nama:"Tepung Ketan",   qty:"150 gram",  kat:"KARBOHIDRAT"},{nama:"Telur",   qty:"2 butir", kat:"PROTEIN"}, {nama:"Gula Pasir",  qty:"100 gram",kat:"BUMBU"}],
    "Roti Bakar Keju":     [{ nama:"Roti Tawar",     qty:"4 lembar",  kat:"KARBOHIDRAT"},{nama:"Keju",    qty:"50 gram", kat:"PROTEIN"}, {nama:"Mentega",     qty:"20 gram", kat:"BUMBU"}],
    "Soto Ayam":           [{ nama:"Ayam",           qty:"300 gram",  kat:"PROTEIN"},    {nama:"Beras",   qty:"200 gram",kat:"KARBOHIDRAT"},{nama:"Daun Bawang",qty:"2 batang",kat:"SAYURAN"}],
    "Mie Ayam Bakso":      [{ nama:"Mie Telur",      qty:"200 gram",  kat:"KARBOHIDRAT"},{nama:"Ayam",   qty:"200 gram",kat:"PROTEIN"},  {nama:"Bakso",       qty:"6 buah",  kat:"PROTEIN"}],
    "Rendang Daging":      [{ nama:"Daging Sapi",    qty:"500 gram",  kat:"PROTEIN"},    {nama:"Santan",  qty:"200 ml",  kat:"BUMBU"},    {nama:"Cabai Merah", qty:"10 buah", kat:"BUMBU"}],
    "Gado-Gado":           [{ nama:"Tahu",           qty:"2 buah",    kat:"PROTEIN"},    {nama:"Tempe",   qty:"1 papan", kat:"PROTEIN"},  {nama:"Kacang Tanah",qty:"100 gram",kat:"BUMBU"}],
    "Pancake Pisang":      [{ nama:"Tepung Terigu",  qty:"150 gram",  kat:"KARBOHIDRAT"},{nama:"Pisang",  qty:"2 buah",  kat:"SAYURAN"},  {nama:"Telur",       qty:"1 butir", kat:"PROTEIN"}],
    "Bubur Ayam":          [{ nama:"Beras",          qty:"100 gram",  kat:"KARBOHIDRAT"},{nama:"Ayam",   qty:"200 gram",kat:"PROTEIN"},  {nama:"Daun Bawang", qty:"2 batang",kat:"SAYURAN"}],
    "Sup Sayur Tahu":      [{ nama:"Tahu",           qty:"2 buah",    kat:"PROTEIN"},    {nama:"Wortel",  qty:"1 buah",  kat:"SAYURAN"},  {nama:"Bayam",       qty:"1 ikat",  kat:"SAYURAN"}],
    "Tempe Orek":          [{ nama:"Tempe",          qty:"1 papan",   kat:"PROTEIN"},    {nama:"Bawang Merah",qty:"30 gram",kat:"BUMBU"},{nama:"Cabai Merah", qty:"3 buah",  kat:"BUMBU"}],
    "Capcay Kuah":         [{ nama:"Wortel",         qty:"1 buah",    kat:"SAYURAN"},    {nama:"Kol",     qty:"100 gram",kat:"SAYURAN"},  {nama:"Bawang Putih",qty:"20 gram", kat:"BUMBU"}],
    "Oatmeal Buah":        [{ nama:"Oatmeal",        qty:"80 gram",   kat:"KARBOHIDRAT"},{nama:"Pisang",  qty:"1 buah",  kat:"SAYURAN"},  {nama:"Susu",        qty:"200 ml",  kat:"BUMBU"}],
    "Nasi Uduk":           [{ nama:"Beras",          qty:"200 gram",  kat:"KARBOHIDRAT"},{nama:"Santan",  qty:"100 ml",  kat:"BUMBU"},    {nama:"Daun Salam",  qty:"2 lembar",kat:"BUMBU"}],
    "Ikan Bakar Bumbu":    [{ nama:"Ikan",           qty:"500 gram",  kat:"PROTEIN"},    {nama:"Bawang Putih",qty:"30 gram",kat:"BUMBU"},{nama:"Cabai Merah", qty:"5 buah",  kat:"BUMBU"}],
    "Tumis Kangkung":      [{ nama:"Kangkung",       qty:"200 gram",  kat:"SAYURAN"},    {nama:"Bawang Putih",qty:"20 gram",kat:"BUMBU"},{nama:"Cabai Rawit", qty:"3 buah",  kat:"BUMBU"}],
    "Lontong Sayur":       [{ nama:"Lontong",        qty:"3 buah",    kat:"KARBOHIDRAT"},{nama:"Tahu",   qty:"2 buah",  kat:"PROTEIN"},  {nama:"Santan",      qty:"150 ml",  kat:"BUMBU"}],
    "Rawon Daging":        [{ nama:"Daging Sapi",    qty:"500 gram",  kat:"PROTEIN"},    {nama:"Kluwek", qty:"5 buah",  kat:"BUMBU"},    {nama:"Bawang Merah",qty:"50 gram", kat:"BUMBU"}],
    "Roti Telur Dadar":    [{ nama:"Roti Tawar",     qty:"2 lembar",  kat:"KARBOHIDRAT"},{nama:"Telur",  qty:"2 butir", kat:"PROTEIN"},  {nama:"Mentega",     qty:"10 gram", kat:"BUMBU"}],
    "Perkedel Jagung":     [{ nama:"Jagung",         qty:"200 gram",  kat:"SAYURAN"},    {nama:"Tepung Terigu",qty:"50 gram",kat:"KARBOHIDRAT"},{nama:"Telur",qty:"1 butir",kat:"PROTEIN"}],
};

function toISO(d) {
    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
}
function hariList7(anchor) {
    const d = new Date(anchor);
    d.setHours(0,0,0,0);
    const dow = d.getDay();
    const diff = dow === 0 ? -6 : 1 - dow;
    const mon = new Date(d); mon.setDate(d.getDate()+diff);
    return Array.from({length:7}, (_,i)=>{ const x=new Date(mon); x.setDate(mon.getDate()+i); return toISO(x); });
}

// Kumpulkan bahan dari semua meal di minggu ini
const dates = hariList7(new Date());
const bulanNames = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Ags","Sep","Okt","Nov","Des"];
const firstDate = new Date(dates[0]+"T00:00:00");
const lastDate  = new Date(dates[6]+"T00:00:00");
document.getElementById("notaDateText").textContent =
    `${firstDate.getDate()} ${bulanNames[firstDate.getMonth()]} – ${lastDate.getDate()} ${bulanNames[lastDate.getMonth()]} ${lastDate.getFullYear()}`;

// Aggregate bahan
const aggregated = {}; // {kat: {nama: {qty_list, checked}}}
let hasAnyMeal = false;

dates.forEach(dateStr => {
    waktuList.forEach(waktu => {
        const data = localStorage.getItem(`meal_${dateStr}_${waktu}`);
        if (!data) return;
        hasAnyMeal = true;
        const resep = JSON.parse(data);
        const bahans = bahanResep[resep.nama] || [{nama: resep.nama, qty:"1 porsi", kat:"LAINNYA"}];
        bahans.forEach(b => {
            if (!aggregated[b.kat]) aggregated[b.kat] = {};
            if (!aggregated[b.kat][b.nama]) aggregated[b.kat][b.nama] = { qty: b.qty, checked: false };
        });
    });
});

const bahanList = document.getElementById("bahanList");
const notaEmpty = document.getElementById("notaEmpty");

if (!hasAnyMeal) {
    notaEmpty.classList.remove("hidden");
    document.getElementById("belumDibeli").style.removeProperty("display");
    document.getElementById("belumDibeli").style.display = "none";
} else {
    // Render kategori
    const katOrder = ["KARBOHIDRAT","PROTEIN","SAYURAN","BUMBU","LAINNYA"];
    const allKats = [...new Set([...katOrder, ...Object.keys(aggregated)])];

    allKats.forEach(kat => {
        if (!aggregated[kat]) return;
        const items = Object.entries(aggregated[kat]);
        const section = document.createElement("div");
        section.className = "belanja-kategori flex flex-col gap-2";
        section.innerHTML = `<p class="font-jakarta font-bold text-caption kategori-label">${kat}</p>`;
        items.forEach(([nama, info]) => {
            const label = document.createElement("label");
            label.className = "belanja-item flex flex-row gap-3";
            label.innerHTML = `
                <input type="checkbox" class="belanja-check">
                <span class="checkmark"></span>
                <span class="font-jakarta font-medium text-body text-secondary-normal item-nama">${nama}</span>
                <span class="font-jakarta font-regular text-body text-primary-darker item-qty">${info.qty}</span>
            `;
            label.querySelector(".belanja-check").addEventListener("change", updateProgress);
            section.appendChild(label);
        });
        bahanList.appendChild(section);
    });

    updateProgress();
    document.getElementById("belumDibeli").style.removeProperty("display");
}

function updateProgress() {
    const all   = document.querySelectorAll(".belanja-check");
    const done  = document.querySelectorAll(".belanja-check:checked");
    const total = all.length, checked = done.length;
    const pct   = total > 0 ? Math.round((checked/total)*100) : 0;

    document.getElementById("progressLabel").textContent = `${checked}/${total} ✓`;
    document.getElementById("progressFill").style.width  = pct + "%";
    document.getElementById("progressPct").textContent   = pct + "% selesai";

    const belum = total - checked;
    const belumEl = document.getElementById("belumDibeli");
    document.getElementById("belumDibeliCount").textContent = belum + " item";
    belumEl.style.display = belum > 0 ? "flex" : "none";
}
</script>
@endpush