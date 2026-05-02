const searchInput = document.getElementById('searchInput');
const bahanItems  = document.querySelectorAll('.bahan-item');
const checkboxes  = document.querySelectorAll('.bahan-item input');
const btnApply    = document.getElementById('terapkanBtn');

/* SEARCH */
searchInput.addEventListener('input', function () {
    const keyword = this.value.toLowerCase();

    bahanItems.forEach(item => {
        const nama = item.querySelector('.bahan-nama').textContent.toLowerCase();

        item.style.display = nama.includes(keyword)
            ? 'flex'
            : 'none';
    });
});

/* CHECKBOX */
checkboxes.forEach(box => {
    box.addEventListener('change', toggleButton);
});

function toggleButton(){
    const checked = document.querySelectorAll('.bahan-item input:checked');
    btnApply.style.display = checked.length ? 'block' : 'none';
}

/* AMBIL DATA */
function selectedBahan(){
    const data = [];

    document.querySelectorAll('.bahan-item input:checked').forEach(box => {
        const nama = box.closest('.bahan-item')
            .querySelector('.bahan-nama')
            .textContent;

        data.push(nama);
    });

    return data;
}

/* BUTTON */
btnApply.addEventListener('click', () => {
    const query = selectedBahan().join(',');
    window.location.href = `/filter-resep?bahan=${query}`;
});

/* ENTER SEARCH */
searchInput.addEventListener('keydown', e => {
    if(e.key === 'Enter'){
        const keyword = searchInput.value.trim();
        window.location.href = `/filter-resep?search=${keyword}`;
    }
});