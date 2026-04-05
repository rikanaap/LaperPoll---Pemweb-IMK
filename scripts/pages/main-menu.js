import { changePage } from "../global.js";

let filterMode = 1;
let choosedFilter = [];

const filter2 = [["Berkuah", "Kering", "Basah", "Nyemek", "Kenyal", "Enyoy", "Chunky", "Keras"], []];
const filter3 = [["Asin Gurih", "Pahit", "Asam Segar", "Pedas", "Umami", "Asin Laut", "Manis Buah", "Manis"], ];

const searchIcon = document.getElementById("searchButton");

if (searchIcon) {
    searchIcon.addEventListener("click", () => {
        changePage('search');
    });
}

function changeFilterData(mode){
    const filters = document.querySelectorAll(".bottom-filter")
    filters.forEach((data, index) => {
        data.querySelector(p).innerText = (mode == 2) ? filter2[index] : filter3[index];
    })
}

function changeResult(mode, indexRemove=[2,3,5]){
    const reseps = document.querySelector('.resep')
    reseps.forEach((resep, index) => {
        if(indexRemove.includes(index)) resep.style.display = "none";
    })
}

function changeChoosenFilter(){
    
}

function chooseFilter(data){
    changeResult();
    filterMode++
    choosedFilter.push(data.querySelector(p).innerText)
    changeFilterData(filterMode)
}

