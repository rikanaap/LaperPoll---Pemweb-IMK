import { changePage } from "../global.js";

let filterMode = 1;
let choosedFilter = [];

const filter2 = ["Berkuah", "Kering", "Basah", "Nyemek", "Kenyal", "Enyoy", "Chunky", "Keras"];
const filter3 = ["Asin Gurih", "Pahit", "Asam Segar", "Pedas", "Umami", "Asin Laut", "Manis Buah", "Manis"];

const searchIcon = document.getElementById("searchButton");
const filters = document.querySelectorAll(".bottom-filter");
const searchInput = document.getElementById("search-filter")

searchIcon.addEventListener("click", () => changePage('search'));
searchInput.addEventListener("input", (data) => { searchFilter(searchInput.value) })
filters.forEach((filter) => { filter.addEventListener("click", () => chooseFilter(filter)) })

function changeFilterData(mode) {
    filters.forEach((data, index) => {
        data.querySelector('p').innerText = (mode == 2) ? filter2[index] : filter3[index];
    })
}

function changeResult() {
    let indexRemove;
    switch (filterMode) {
        case 1: indexRemove = [2, 3, 5]; break;
        case 2: indexRemove = [0, 1, 4, 9, 10]; break;
        case 3: indexRemove = [8]; break;
    }
    const reseps = document.querySelectorAll('.resep')
    reseps.forEach((resep, index) => {
        if (indexRemove.includes(index)) resep.style.display = "none";
    })
}

function changeChoosenFilter() {
    const container = document.querySelector(".bottom-filters-used");

    const title = container.querySelector('p:first-child').outerHTML;
    const filterHTML = choosedFilter.map((filterName, index) => {
        const isLast = index === choosedFilter.length - 1;

        return `
        <div class="bottom-filter-used">
            <p class="font-jakarta text-body font-semibold text-secondary-light">${filterName}</p>
            ${isLast ? `
                <span class="material-icons-round text-title2 text-white">cancel</span>
            ` : ""}
        </div>
    `;
    }).join("");

    container.innerHTML = title + filterHTML;
    container.style.display = "flex"

}

function chooseFilter(data) {
    document.querySelector('.bottom-text').style.display = "none";
    if (filterMode > 3) return;
    changeResult(); //Ganti result dengan cara ngehapus index

    filterMode++
    choosedFilter.push(data.querySelector('p').innerText)
    changeFilterData(filterMode)
    changeChoosenFilter()
    if (filterMode == 4) document.querySelector('.bottom-filters').style.display = 'none'
}

function searchFilter(data) {
    console.log("Makanan", data)
    filters.forEach(filter => {
        const text = filter.querySelector('p').innerText.toLowerCase()
        console.log(text)
        if (text.includes(data)) {
            filter.style.display = "flex"; 
        } else {
            filter.style.display = "none";
        }
    })
}