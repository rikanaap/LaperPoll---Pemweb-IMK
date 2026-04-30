const inputSubmit = document.querySelector('.input-submit')
const listDropdown = document.querySelectorAll(".dropdown-data")
const allInputDropdown = document.querySelectorAll('.input-dropdown');
const expandIcons = document.querySelectorAll('.input-dropdown .material-icons-round:last-child');
const indicators = document.querySelectorAll('.indicator')

let formCounter = 1;
let inMainPage = true;
let tahapForm4 = 1
let pilihanBahan = [];

listDropdown.forEach((kategori) => {
  kategori.addEventListener("click", () => {
    kategori.classList.toggle("choosen")
  })
})
inputSubmit.addEventListener("click", () => {
  if (checkForm()) {
    formCounter++
    changeIndicator()
    showForm()
  }
})

allInputDropdown.forEach(dropdown => {
  const inputField = dropdown.querySelector('.input-data');
  const dropdownItems = dropdown.querySelectorAll('.dropdown-data');

  inputField.addEventListener('input', function () {
    const filterText = inputField.value.toLowerCase();
    dropdownItems.forEach(item => {
      const text = item.textContent.toLowerCase();
      if (text.includes(filterText)) {
        item.style.display = "flex";
        hasResults = true;
      } else {
        if (!item.classList.contains("choosen")) item.style.display = "none"
      }
    });
  });
});

expandIcons.forEach(icon => {
  icon.addEventListener('click', function () {
    const parent = this.closest('.input-dropdown');
    const dropdown = parent.querySelector('.dropdown-datas');

    if (dropdown.style.display === "none") {
      dropdown.style.display = "flex";
      this.style.transform = "rotate(180deg)";
    } else {
      dropdown.style.display = "none";
      this.style.transform = "rotate(0deg)";
    }
  });
});

// Ambil semua container input scale
const allScaleInputs = document.querySelectorAll('.input-scale-input');

allScaleInputs.forEach(container => {
  // Cari tombol dan field input di dalam container ini saja
  const btnAdd = container.querySelector('.material-icons-round:nth-child(1)'); // add_circle
  const inputField = container.querySelector('.input-number');
  const btnRemove = container.querySelector('.material-icons-round:nth-child(3)'); // remove_circle

  // Fungsi Tambah
  btnAdd.addEventListener('click', () => {
    let currentValue = parseInt(inputField.value) || 0;
    inputField.value = currentValue + 1;
  });

  // Fungsi Kurang
  btnRemove.addEventListener('click', () => {
    let currentValue = parseInt(inputField.value) || 0;
    // Opsional: cegah angka minus, misal porsi minimal 1
    if (currentValue > 0) {
      inputField.value = currentValue - 1;
    }
  });
});

function changeIndicator() {
  switch (formCounter) {
    case 1:
      indicators[0].classList.remove("i-enable")
      break
    case 2:
      indicators[0].classList.add("i-enable")
      indicators[1].classList.remove("i-enable")
      break
    case 4:
      indicators[1].classList.remove("i-enable")
      indicators[2].classList.add("i-enable")
      break
  }
  document.querySelector(".form-indicator > p").innerText = formCounter + "/5"
}

function showForm() {
  document.querySelectorAll('.form, .results').forEach(el => {
    el.style.display = 'none';
  });

  switch (formCounter) {
    case 1:
      document.getElementById('form-1').style.display = 'flex';
      break;
    case 2:
      inputSubmit.style.display = "none"
      document.getElementById('form-2').style.display = 'flex';
      break;
    case 3:
      if (!inMainPage) {
        formCounter = 2
        document.getElementById('form-2').style.display = 'flex';
        saveBahan()
        inMainPage = true
        hideEditBerat()
      } else {
        inputSubmit.style.display = "none"
        document.getElementById('form-3').style.display = 'flex';
      }
      break;
    case 4:
      document.getElementById('form-4-1').style.display = 'flex';
      inputSubmit.querySelector("h1").innerText = "Pilih Bahan"
      break;
    case 5:
      document.getElementById('form-5').style.display = 'flex';
      break;
    default:
      window.location.href = "profile.html"
  }
}

function checkForm() {
  switch (formCounter) {
    case 1:
      const namaResep = document.querySelector('#form-1 input[placeholder="Nama resep"]').value;
      if (namaResep.trim() === "") {
        alert("Mohon isi nama resep terlebih dahulu");
        return false;
      }
      return true;

    case 2:
      return true;

    case 3:
      return true;

    case 4:
      const langkah = document.getElementById('input-langkah-pembuatan').value;
      if (langkah.trim() === "") {
        alert("Mohon isi langkah pembuatan");
        return false;
      }
      return true;

    case 5:
      return true;
  }
}

// Form 2
const listBahan = document.querySelectorAll("#form-2 .dropdown-data")

listBahan.forEach((bahan) => {
  bahan.addEventListener("click", () => {
    editBerat(bahan.querySelector('p').innerText)
  })
})

function saveBahan() {
  pilihanBahan.push({
    judul: document.querySelector("#JudulBahan > input").value,
    berat: document.querySelector("#InputBerat input").value
  })

  showResultForm2()
}

function showResultForm2() {
  const resultSection = document.getElementById('result-2');
  const wrapperResult = resultSection.querySelector('.wrapper-result');
  wrapperResult.innerHTML = '';

  pilihanBahan.forEach((item) => {
    const resultDataHTML = `
                <div class="result-data flex flex-row">
                    <p class="font-jakarta font-regular text-body">${item.berat} g</p>
                    <div class="vertical-line"></div>
                    <p class="font-jakarta font-regular text-body">${item.judul}</p>
                </div>
            `;

    wrapperResult.insertAdjacentHTML('beforeend', resultDataHTML);
  });
  resultSection.style.display = "flex";
}

function editBerat(nama) {
  inMainPage = false
  document.querySelector('#form-2 .input-dropdown').style.display = "none"
  const judulBahan = document.getElementById("JudulBahan")
  judulBahan.style.display = "flex";
  judulBahan.querySelector('input').value = nama;

  document.getElementById("InputBerat").style.display = "flex";
  inputSubmit.style.display = "flex"
}

function hideEditBerat() {
  document.querySelector('#form-2 .input-dropdown').style.display = "flex"
  document.getElementById("JudulBahan").style.display = "none";
  document.getElementById("InputBerat").style.display = "none";

}

// Form 3
let listFilters = [];
const listFilterisasi = document.querySelectorAll("#form-3 .dropdown-data")

listFilterisasi.forEach((filterisasi) => {
  filterisasi.addEventListener("click", () => {
    filterisasi.classList.contains("choosen") ?
      tambahFilterisasi(filterisasi.querySelector('p').innerText) : hapusFilterisasi(filterisasi.querySelector('p').innerText)
  })
})

function tambahFilterisasi(nama) {
  listFilters.push(nama)
  if (listFilters.length > 0) {
    inputSubmit.style.display = "flex"
  } else { inputSubmit.style.display = "none" }
  showResultForm3()
}
function hapusFilterisasi(nama) {
  listFilters.pop(listFilters.indexOf(nama))
  if (listFilters.length > 0) {
    inputSubmit.style.display = "flex"
  } else { inputSubmit.style.display = "none" }
  showResultForm3()
}

function showResultForm3() {
  const resultSection = document.getElementById('result-3');
  if (listFilters.length > 0) {
    resultSection.style.display = "flex"
  } else { resultSection.style.display = "none" }
  const wrapper = resultSection.querySelector('.wrapper-result');

  wrapper.innerHTML = "";

  listFilters.forEach((nama, index) => {
    const itemHTML = `
            <div class="result-data flex flex-row" data-index="${index}">
                <p class="font-jakarta font-regular text-body">${nama}</p>
                <span class="material-icons-round text-title2" 
                      style="cursor: pointer;" 
                      onclick="hapusFilter(${index})">
                      remove_circle_outline
                </span>
            </div>
        `;
    wrapper.insertAdjacentHTML('beforeend', itemHTML);
  });
}

// Form 4
const inputForm4Text = document.getElementById("input-langkah-pembuatan")
const inputForm4Time = document.getElementById("timeInput")

function submitForm4() {
  const p1 = document.querySelector("#result-4-1 .result-bahan p:first-child")
  const p2 = document.querySelector("#result-4-1 .result-bahan p:last-child")
  p1.innerText = inputForm4Text
  p2.innerText = formatTime(inputForm4Time)

  document.querySelector("#result-4-1").style.display = "flex"
  document.querySelector("#form-4-1").style.display = "none"
  document.querySelector("#form-4-2").style.display = "flex"
}

function formatTime(time) {
  const [hours, minutes, seconds] = time.split(":").map(Number)

  const totalSeconds = hours * 3600 + minutes * 60 + seconds

  const m = Math.floor(totalSeconds / 60)
  const d = totalSeconds % 60

  return `${m}m${d}d`
}