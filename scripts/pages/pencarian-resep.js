// ambil elemen
const searchInput = document.getElementById('searchInput');
const searchWrapper = document.getElementById('searchWrapper');
const bahanItems = document.querySelectorAll('.bahan-item');
const checkboxes = document.querySelectorAll('.bahan-item input[type="checkbox"]');
const terapkanBtn = document.getElementById('terapkanBtn');

searchInput.addEventListener('input', () => {
    const keyword = searchInput.value.toLowerCase();

    bahanItems.forEach(item => {
        const nama = item.querySelector('.bahan-nama').textContent.toLowerCase();

        if (nama.includes(keyword)) {
            item.style.display = "flex";
        } else {
            item.style.display = "none";
        }
    });
});

checkboxes.forEach(cb => {
    cb.addEventListener('change', () => {

        const checked = document.querySelectorAll('.bahan-item input[type="checkbox"]:checked');

        terapkanBtn.style.display = checked.length > 0 ? "block" : "none";
    });
});

function getSelectedBahan() {
    const selected = [];

    checkboxes.forEach(cb => {
        if (cb.checked) {
            const nama = cb.closest('.bahan-item')
                           .querySelector('.bahan-nama')
                           .textContent;

            selected.push(nama);
        }
    });

    return selected;
}

terapkanBtn.addEventListener("click", () => {
    const bahan = getSelectedBahan();
    const query = bahan.join(",");

    window.location.href = `filter-pencarian-resep.html?bahan=${query}`;
});

searchInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        const keyword = searchInput.value.trim();
        window.location.href = `filter-pencarian-resep.html?search=${keyword}`;
    }
});
