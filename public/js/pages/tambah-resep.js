const inputSubmit = document.querySelector('.input-submit')
const listDropdown = document.querySelectorAll(".dropdown-data")
const allInputDropdown = document.querySelectorAll('.input-dropdown');
const expandIcons = document.querySelectorAll('.input-dropdown .material-icons-round:last-child');
const indicators = document.querySelectorAll('.indicator')

let formCounter = 1;
let inMainPage = true;
let tahapForm4 = 1
let pilihanBahan = [];
let formData = {
    "title": "",
    "description": "",
    "cook_duration": 0,
    "calorie": 0,
    "thumbnail": "",
    "main_filter_id": null,
    "attachments": [],
    "steps": {},
    "bahans": [],
    "filters": []
}

listDropdown.forEach((kategori) => {
  kategori.addEventListener("click", () => {
    kategori.classList.toggle("choosen")
  })
})

inputSubmit.addEventListener("click", () => {
console.log('dbasjdabsd')
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
    checkAndShowAddButton()

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
      indicators[1].classList.add("i-enable")
      indicators[2].classList.add("i-enable")
      break
      case 2:
          indicators[0].classList.add("i-enable")
          indicators[2].classList.add("i-enable")
          indicators[1].classList.remove("i-enable")
          break
      case 5:
        indicators[2].classList.add("i-enable")
        indicators[1].classList.add("i-enable")
        indicators[0].classList.remove("i-enable")
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
        inputSubmit.style.display = (formData.bahans.length > 1) ? "flex" : "none" 
      showResultForm2()
      document.getElementById('form-2').style.display = 'flex';
      break;
      case 3:
          inputSubmit.style.display = (formData.filters.length > 2) ? "flex" : "none" 
          showResultForm3()
          checkAndShowAddButton()
        document.getElementById('form-3').style.display = 'flex';
        break;
    case 4:
        inputSubmit.style.display = (Object.keys(formData.steps).length > 1) ? "flex" : "none" 
        document.getElementById('form-4-1').style.display = 'flex';
        showResultStep4()
        if(checkLeftoverBahan()) {
            buttonTambahStep.style.display = "flex"
            inputSubmit.style.display = "none"
        }else{
            buttonTambahStep.style.display = "none"
            inputSubmit.style.display = "flex"
        }
    break;
    case 5:
        document.getElementById('form-5').style.display = 'flex';
        inputSubmit.style.display = "none"
        break;
    default:
        submitResep()
        document.getElementById('resep-submit-loading').style.display = 'flex'
        inputSubmit.style.display = "none"
  }
}

function checkForm() {
  switch (formCounter) {
    case 1:
      const namaResep = document.querySelector('#form-1 input[placeholder="Nama resep"]').value;
      const kalorie = +document.querySelector('#form-1 input[placeholder="Kalori (kcal)"]').value;
      const choosenKategori = document.querySelector('#form-1 #listKategori .dropdown-data.choosen');

      if (namaResep.trim() === "") {
        showAlert("Validasi", "Mohon isi nama resep terlebih dahulu");
        return false;
      }
      if (!kalorie || kalorie < 0) {
        showAlert("Validasi", "Mohon isi kalori");
        return false;
      }
      if (!choosenKategori) {
        showAlert("Validasi", "Mohon pilih kategori resep");
        return false;
      }

      formData.title = namaResep;
      formData.calorie = kalorie;
      formData.main_filter_id = +choosenKategori.getAttribute('data-kategori-id');

      return true;

    case 2:
      if (formData.bahans.length < 1) {
        showAlert("Validasi", "Mohon tambahkan minimal 1 bahan");
        return false;
      }
      return true;

    case 3:
    if (formData.filters.length < 3) {
        showAlert("Validasi", "Mohon tambahkan minimal 3 bahan");
        return false;
      }

      return true;

    case 4:
      if (Object.keys(formData.steps).length < 1) {
        showAlert("Validasi", "Mohon isi langkah pembuatan");
        return false;
      }
      return true;

    case 5:
      if(formData.attachments.length < 1){
        showAlert("Validasi", "Mohon kirimkan attachment")
        return false
      }
      console.log(formData)
      if(formData.thumbnail === '') {
            showAlert("Validasi", "Mohon pilih satu image sebagai thumbnail")
            return false
        }
      return true;
  }
}

function getTotalCookDuration() {
    let totalSeconds = 0

    Object.values(formData.steps).forEach(step => {
        const [h, m, s] = step.step_duration.split(':').map(Number)
        totalSeconds += h * 3600 + m * 60 + s
    })

    const h = Math.floor(totalSeconds / 3600)
    const m = Math.floor((totalSeconds % 3600) / 60)
    const s = totalSeconds % 60

    // Format HH:MM:SS
    return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`
}

async function submitResep() {
    const token = document.querySelector('meta[name="csrf-token"]')?.content
    if (!token) { showAlert("Validasi", 'CSRF token tidak ditemukan'); return }

    const fd = new FormData()

    console.log(formData)

    // ── Data dasar ──────────────────────────────────────────
    fd.append('title',          formData.title)
    fd.append('calorie',        formData.calorie)
    fd.append('main_filter_id', formData.main_filter_id)
    fd.append('cook_duration', getTotalCookDuration())
    fd.append('thumbnail_index', formData.thumbnail)

    // ── Bahans → [{ bahan_id, gram_total }] ─────────────────
    // ResepBahan fillable: resep_id, bahan_id, gram_total
    formData.bahans.forEach((b, i) => {
        fd.append(`bahans[${i}][bahan_id]`,   b.id)
        fd.append(`bahans[${i}][gram_total]`,  b.gram_total)
    })

    formData.filters.forEach((f, i) => {
        fd.append(`filters[${i}][filters_id]`, f.id)
    })

    Object.values(formData.steps).forEach((step, i) => {
        fd.append(`steps[${i}][step_order]`,    i + 1)
        fd.append(`steps[${i}][step_duration]`, step.step_duration)
        fd.append(`steps[${i}][description]`,   step.description)

        step.bahans.forEach((b, j) => {
            fd.append(`steps[${i}][bahans][${j}][bahan_id]`,  b.bahan_id)
            fd.append(`steps[${i}][bahans][${j}][gram_total]`, b.gram_total)
        })
    })

    formData.attachments.forEach((att, i) => {
        fd.append(`attachments[${i}]`, att.file, att.file.name)
    })
     // Tandai sedang loading
    sessionStorage.setItem('resep_submit_status', 'loading')
    sessionStorage.setItem('resep_submit_title', formData.title)

    try {
        const res = await fetch(
            document.querySelector('meta[name="form-submit-url"]')?.content || '/resep/store',
            { method: 'POST', headers: { 'X-CSRF-TOKEN': token }, body: fd }
        )

        if (res.ok) {
            sessionStorage.setItem('resep_submit_status', 'success')
            window.location.href = '/profile'
        } else {
            sessionStorage.setItem('resep_submit_status', 'error')
            sessionStorage.setItem('resep_submit_message', data.message || 'Terjadi kesalahan')
        }
    } catch (e) {
        console.error(e)
        sessionStorage.setItem('resep_submit_status', 'error')
        sessionStorage.setItem('resep_submit_message', 'Gagal terhubung ke server')
    }
}

// Form 1
const listKategoriResep = document.querySelectorAll("#form-1 .dropdown-data")
listKategoriResep.forEach((kategori) => {
    kategori.addEventListener("click", () => {
        listKategoriResep.forEach(el => el.classList.remove("choosen"))
        kategori.classList.add("choosen")
    })
})

// Form 2
const listBahan = document.querySelectorAll("#form-2 .dropdown-data")
const buttonTambahBahan = document.querySelector("#form-2 .btn-add-bahan")
const divFormPilihBahan2 =  document.querySelector('#form-2 .input-dropdown')
const inputBeratForm2 = document.getElementById("InputBerat")
const inputJudulForm2 = document.getElementById("JudulBahan")
let currentBahanId = null;
let bahanSudahAda = false

listBahan.forEach((bahan) => {
  bahan.addEventListener("click", () => {
    currentBahanId = +bahan.getAttribute('data-bahan-id')
    bahanSudahAda = formData.bahans.find(b => b.id === currentBahanId)
    if(bahanSudahAda) bahan.classList.toggle("choosen")
    editBerat(bahan.querySelector('p').innerText)
  })
})

buttonTambahBahan.addEventListener("click", () => {
    const checkSave = saveBahan();
    if(!checkSave) return
    hideEditBerat();
    buttonTambahBahan.style.display = "none"
})

function saveBahan() {
    const gram_total = +inputBeratForm2.querySelector('input').value
    if (gram_total == null || gram_total <= 0) {
        showAlert("Validasi", "Mohon isi gram terlebih dahulu, perhatikan bahwa gram tidak boleh 0 dan dibawah 0")
        return false
    }
    
    if(bahanSudahAda) {
        const indexBahan = formData.bahans.findIndex(b => b.id === currentBahanId)
        if(formData.bahans[indexBahan].temp_used_gram > gram_total) { 
            showAlert("Validasi", "Nilai gram harus lebih tinggi dari: " + formData.bahans[indexBahan].temp_used_gram + " gram")
            return
         }
        if(indexBahan !== -1) formData.bahans[indexBahan].gram_total = gram_total
    } else {
        formData.bahans.push({
            id: currentBahanId,
            judul: inputJudulForm2.querySelector('input').value,
            temp_used_gram: 0,
            gram_total
        })
    }

    showResultForm2()
    return true
}

function showResultForm2() {
    if(formData.bahans.length < 1) return
  const resultSection = document.getElementById('result-2');
  const wrapperResult = resultSection.querySelector('.wrapper-result');
  wrapperResult.innerHTML = '';

  formData.bahans.forEach((item) => {
    const resultDataHTML = `
                <div class="result-data flex flex-row">
                    <p class="font-jakarta font-regular text-body">${item.gram_total} g</p>
                    <div class="vertical-line"></div>
                    <p class="font-jakarta font-regular text-body">${item.judul}</p>
                </div>
            `;

    wrapperResult.insertAdjacentHTML('beforeend', resultDataHTML);
  });
  resultSection.style.display = "flex";
}

function editBerat(nama) {
    divFormPilihBahan2.style.display = "none"
    
  inputBeratForm2.style.display = "flex";
  inputBeratForm2.querySelector('input').value = null;
  inputJudulForm2.style.display = "flex";
  inputJudulForm2.querySelector('input').value = nama;

    if(bahanSudahAda) {
        const bahanExist = formData.bahans.find(b => b.id === currentBahanId)
        
        if(bahanExist) {
            inputBeratForm2.querySelector('input').value = bahanExist.gram_total
            buttonTambahBahan.innerText = "Update Bahan"
        }
    } else {
        inputBeratForm2.querySelector('input').value = null;        
        buttonTambahBahan.innerText = "Tambah Bahan"
    }
  
  buttonTambahBahan.style.display = "flex"
  inputSubmit.style.display = "none";
}

function hideEditBerat() {
  divFormPilihBahan2.style.display = "flex"
  inputJudulForm2.style.display = "none";
  inputBeratForm2.style.display = "none";
  if(formData.bahans.length > 1) inputSubmit.style.display = "flex"

}

// Form 3
const listFilterisasi = document.querySelectorAll("#form-3 .dropdown-data")

listFilterisasi.forEach((filterisasi) => {
  filterisasi.addEventListener("click", () => {
    filterisasi.classList.contains("choosen") ?
      tambahFilterisasi(filterisasi.querySelector('p').innerText, filterisasi.getAttribute('data-filter-id')) 
      : hapusFilterisasi(filterisasi.querySelector('p').innerText)
  })
})

function tambahFilterisasi(nama, id) {
  formData.filters.push({ id, nama })
  if (formData.filters.length > 2) {
    inputSubmit.style.display = "flex"
  } else { inputSubmit.style.display = "none" }
  showResultForm3()
}
function hapusFilterisasi(nama) {
  formData.filters = formData.filters.filter(item => item.nama !== nama)
   listFilterisasi.forEach(el => {
    if (el.querySelector('p').innerText === nama) {
      el.classList.remove("choosen")
    }
  })
  if (formData.filters.length > 2) {
    inputSubmit.style.display = "flex"
  } else { inputSubmit.style.display = "none" }
  showResultForm3()
}

function showResultForm3() {
if(formData.filters.length < 1) return
  const resultSection = document.getElementById('result-3');
  resultSection.style.display = (formData.filters.length > 0) ? "flex" : "none"
  const wrapper = resultSection.querySelector('.wrapper-result');

  wrapper.innerHTML = "";

  formData.filters.forEach((filter) => {
    const itemHTML = `
            <div class="result-data flex flex-row">
                <p class="font-jakarta font-regular text-body">${filter.nama}</p>
                <span class="material-icons-round text-title2" 
                      style="cursor: pointer;" 
                      onclick="hapusFilterisasi('${filter.nama}')">
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
const buttonTambahStep = document.querySelector("#form-4-1 .btn-add-step")
const buttonBahanStep = document.querySelector("#form-4-2 .btn-add-bahan-step")
const wrapperResultStep = document.querySelector('#result-4-1 .wrapper-result.fix')
const wrapperResultBahan = document.querySelector('#result-4-2 .wrapper-result')
const dropdownDatasForm4 = document.querySelector('#form-4-2 .dropdown-datas')
const wrapperResultTempStep = document.querySelector('#result-4-1 .wrapper-result.temp')
const inputBeratForm4 = document.getElementById("InputBerat4")
const wrapperResultStepExtra = document.querySelector('#result-4-1 .wrapper-result.extra-step')
let currentTotalStep = 0
let addingFirstStep = true
    
buttonTambahStep.addEventListener('click', () => {
    showDropdownDataBahan()
    if(dropdownDatasForm4.innerHTML == ''){
        showAlert("Validasi", "Tidak ada bahan yang bisa dipakai")
        return
    }
    const lanjut = saveFormStep()
    if(!lanjut) return 
    showResultBahan4()
    showFormBahanStep4()
    inputSubmit.style.display = "none"
})

buttonBahanStep.addEventListener('click', () => {
    addingFirstStep = false
    showFormStep4()
    showResultStep4()
    resetAllInputBahan();
    inputSubmit.style.display = (Object.keys(formData.steps).length > 0) ? "flex" : "none" 
})

document.querySelectorAll('.dur-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target)
        const max   = parseInt(input.max)
        let val     = parseInt(input.value) || 0

        if (btn.classList.contains('dur-up')) {
            val = val >= max ? 0 : val + 1
        } else {
            val = val <= 0 ? max : val - 1
        }

        input.value = val
        syncTimeInput()
    })
})

function checkLeftoverBahan() {
    return formData.bahans.filter(b => (b.gram_total - (b.temp_used_gram || 0)) > 0).length
}



function resetAllInputBahan() {
    const allInputNumbers = document.querySelectorAll('#form-4-2 .input-number');
    allInputNumbers.forEach(input => {
        input.value = 0;
    });
}


function syncTimeInput() {
    const h = String(parseInt(document.getElementById('dur-jam').value)   || 0).padStart(2, '0')
    const m = String(parseInt(document.getElementById('dur-menit').value) || 0).padStart(2, '0')
    const s = String(parseInt(document.getElementById('dur-detik').value) || 0).padStart(2, '0')
    
    document.getElementById('timeInput').value = `${h}:${m}:${s}`
}

// Sync awal
syncTimeInput()

function saveFormStep(){
    if(inputForm4Text.value.trim() === "") {
        showAlert("Validasi", "Mohon isi deskripsi langkah terlebih dahulu");
        return false;
    }
    
    const timeValue = inputForm4Time.value;
    if(!timeValue || timeValue === "00:00:00") {
        showAlert("Validasi", "Mohon isi durasi waktu langkah");
        return false;
    }

    currentTotalStep = currentTotalStep + 1
    if(inputForm4Text.value != "" || inputBeratText)
    formData.steps[currentTotalStep] = {
        step_order: currentTotalStep,
        step_duration: inputForm4Time.value,
        description: inputForm4Text.value,
        bahans: []
    }
    inputForm4Text.value = "";
    return true
}

function saveBahanStep(data) {
    console.log(data)
    const bahans = formData.steps[currentTotalStep].bahans
    const index = bahans.findIndex(b => b.bahan_id === data.bahan_id)
    index !== -1 ? bahans[index].gram_total = data.gram_total : bahans.push(data)
}


function hapusBahan(bahanId) {
    const bahans = formData.steps[currentTotalStep].bahans
    const index = bahans.findIndex(b => b.bahan_id === bahanId)
    if (index !== -1) bahans.splice(index, 1)
}


function showFormStep4() {
  document.querySelector("#form-4-1").style.display = "flex"
  document.querySelector("#form-4-2").style.display = "none"
  
  document.querySelector("#result-4-1").style.display = "flex"
  document.querySelector("#result-4-2").style.display = "none"
  
  showDropdownDataBahan()
  if(dropdownDatasForm4.innerHTML == ''){
    document.querySelector("#form-4-1").style.display = "none"
    buttonTambahStep.style.display = 'none'
    }
}

function showFormBahanStep4(){
    document.querySelector("#result-4-1").style.display = "flex"
    document.querySelector("#result-4-2").style.display = "none"
    wrapperResultStep.style.display = "none"
    wrapperResultTempStep.style.display = "flex";
    
    const p1 = wrapperResultTempStep.querySelector("p:first-child")
    const p2 = wrapperResultTempStep.querySelector("p:last-child")
    p1.innerText = formData.steps[currentTotalStep].description
    p2.innerText = formatTime(formData.steps[currentTotalStep].step_duration)

    document.querySelector("#form-4-1").style.display = "none"
    document.querySelector("#form-4-2").style.display = "flex"
}

function showResultStep4(){
    if(Object.keys(formData.steps).length < 1) return
    document.querySelector("#result-4-1").style.display = "flex"
    wrapperResultTempStep.style.display = "none"
    wrapperResultStep.innerHTML = ''
    wrapperResultStepExtra.innerHTML = ''
    wrapperResultStepExtra.classList.remove('open')

    const steps = Object.entries(formData.steps)

    if(steps.length < 1) return

    // Badge jumlah step
    const badge = document.createElement('div')
    badge.className = 'step-count-badge'
    badge.innerHTML = `
        <p class="font-jakarta font-semibold text-body">${steps.length} langkah pembuatan</p>
        <span class="material-icons-round">expand_circle_down</span>
    `

    // Isi semua step ke extra
    steps.forEach(([key, item]) => {
        const html = `
            <div class="result-bahan flex flex-row" style="opacity:0; transform:translateY(4px); transition: opacity 0.25s ease, transform 0.25s ease">
                <p class="font-jakarta font-regular text-body step-desc">${item.description}</p>
                <div class="vertical-line"></div>
                <p class="font-jakarta font-regular text-body step-time">${formatTime(item.step_duration)}</p>
            </div>
        `
        wrapperResultStepExtra.insertAdjacentHTML('beforeend', html)
    })

    badge.addEventListener('click', () => {
        const isOpen = wrapperResultStepExtra.classList.contains('open')

        if(isOpen){
            wrapperResultStepExtra.classList.remove('open')
            badge.classList.remove('open')
             if(dropdownDatasForm4.innerHTML == ''){
    document.querySelector("#form-4-1").style.display = "none"
    buttonTambahStep.style.display = 'none'
}
            
            // Reset animasi item
            wrapperResultStepExtra.querySelectorAll('.result-bahan').forEach(el => {
                el.style.opacity = '0'
                el.style.transform = 'translateY(4px)'
            })
            wrapperResultStepExtra.style.display = "none"
        } else {
            wrapperResultStepExtra.classList.add('open')
            wrapperResultStepExtra.style.display = "flex"
            badge.classList.add('open')
            document.querySelector("#form-4-1").style.display = "none"

            // Animasi staggered
            wrapperResultStepExtra.querySelectorAll('.result-bahan').forEach((el, i) => {
                setTimeout(() => {
                    el.style.opacity = '1'
                    el.style.transform = 'translateY(0)'
                }, i * 80)
            })
        }
    })

    wrapperResultStep.appendChild(badge)
    wrapperResultStep.style.display = "flex"
}

function showResultBahan4(){
    if(formData.steps[currentTotalStep].bahans.length < 1) {
        document.querySelector("#result-4-2").style.display = "none"
        return
    }
    buttonBahanStep.style.display = "flex"
    document.querySelector("#result-4-2").style.display = "flex"
    wrapperResultBahan.innerHTML = ''
    formData.steps[currentTotalStep].bahans.forEach((item) => {
        const html = `
             <div class="result-data flex flex-row">
                <p class="font-jakarta font-regular text-body">${item.judul} (${item.gram_total})</p>
                <span class="material-icons-round text-title2">remove_circle_outline</span>
            </div>
        `
        wrapperResultBahan.insertAdjacentHTML('beforeend', html)
    })
    wrapperResultBahan.style.display = "flex"
}

// Recalculate temp_used_gram dari semua step
function updateTempTotal(bahanId) {
    const bahan = formData.bahans.find(b => b.id === bahanId)
    if (!bahan) return

    let total = 0
    Object.values(formData.steps).forEach(step => {
        const found = step.bahans.find(b => b.bahan_id === bahanId)
        if (found) total += found.gram_total
    })

    bahan.temp_used_gram = total
    console.log(formData.bahans)
}


function showDropdownDataBahan(){
    dropdownDatasForm4.innerHTML = ''

    formData.bahans.forEach((item) => {
        const sisa = item.gram_total - (item.temp_used_gram || 0)
        if(sisa == 0) return
        const html = `
            <div class="dropdown-data" data-bahan-id="${item.id}">
                <p class="font-jakarta font-semibold text-body text-primary-dark-active">
                    ${item.judul} (${sisa}g tersisa)
                </p>
                <div class="input-scale-input flex flex-row gap-4">
                    <span class="material-icons-round">add_circle_outline</span>
                    <input class="input-number text-body font-jakarta font-semibold" type="number" min="1" max="${sisa}" size="4" placeholder="0">
                    <span class="material-icons-round">remove_circle_outline</span>
                </div>
            </div>
        `
        dropdownDatasForm4.insertAdjacentHTML('beforeend', html)

        const el      = dropdownDatasForm4.lastElementChild
        const bahanId = item.id
        const maxGram = item.gram_total - (item.temp_used_gram || 0)

        const btnAdd    = el.querySelector('.input-scale-input .material-icons-round:nth-child(1)')
        const inputNum  = el.querySelector('.input-number')
        const btnRemove = el.querySelector('.input-scale-input .material-icons-round:nth-child(3)')

        // Tambah gram
        btnAdd.addEventListener('click', (e) => {
            e.stopPropagation()
            let val = parseInt(inputNum.value) || 0
            if (val < maxGram) {
                inputNum.value = val + 1
                saveBahanStep({ bahan_id: bahanId, gram_total: val + 1, judul: item.judul })
                updateTempTotal(bahanId)
            }
            showResultBahan4()
        })

        // Kurang gram
        btnRemove.addEventListener('click', (e) => {
            e.stopPropagation()
            let val = parseInt(inputNum.value) || 0
            if (val > 1) {
                inputNum.value = val - 1
                saveBahanStep({ bahan_id: bahanId, gram_total: val - 1, judul: item.judul })
                updateTempTotal(bahanId)
            } else if (val === 1) {
                inputNum.value = 0
                updateTempTotal(bahanId)      
                hapusBahan(bahanId)
            }
            showResultBahan4()
        })

        // Ketik manual
        inputNum.addEventListener('input', (e) => {
            e.stopPropagation()
            if (!inputNum.value) return
            let val = parseInt(inputNum.value) || 0
            if (val > maxGram) { inputNum.value = maxGram; val = maxGram }
            if (val > 0) {
                saveBahanStep({ bahan_id: bahanId, gram_total: val, judul: item.judul })
                updateTempTotal(bahanId)    
            } else {
                hapusBahan(bahanId)
                updateTempTotal(bahanId)  
            }
            showResultBahan4()
        })
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
}
function formatTime(time) {
  const [hours, minutes, seconds] = time.split(":").map(Number)

  const totalSeconds = hours * 3600 + minutes * 60 + seconds

  const m = Math.floor(totalSeconds / 60)
  const d = totalSeconds % 60

  return `${m}m${d}d`
}


//Form 5
const fileInput = document.getElementById('file-upload');
const uploadDefault = document.querySelector('.upload-default');

fileInput.addEventListener('change', (e) => {
    handleFileUpload(e.target.files);
    e.target.value = ''; // reset agar file sama bisa dipilih lagi
});

uploadDefault.addEventListener('click', () => fileInput.click());

function handleFileUpload(files) {
    Array.from(files).forEach((file) => {
        if (!file.type.startsWith('image/') && !file.type.startsWith('video/')) {
            showAlert("Validasi", `File ${file.name} tidak didukung`);
            return;
        }

        formData.attachments.push({
            mimetype: file.type,
            path: URL.createObjectURL(file), // preview sementara
            file: file                        // file asli untuk dikirim nanti
        });

        if(formData.attachments.length > 1) inputSubmit.style.display = "flex"

        displayUploadPreview();
        console.log('✓ File added:', file.name);
    });
}

function openPreview(att, el) {
    const uploadBox = document.getElementById('upload-box-label');
    const previewArea = document.getElementById('upload-preview-area');
    const previewImg = document.getElementById('preview-img');
    const previewVid = document.getElementById('preview-vid');

    // Tandai thumbnail aktif
    document.querySelectorAll('.upload-data').forEach(d => d.classList.remove('active'));
    el.classList.add('active');

    // Sembunyikan upload box, tampilkan preview
    uploadBox.style.display = 'none';
    previewArea.style.display = 'block';

    if (att.mimetype.startsWith('image/')) {
        previewImg.src = att.path;
        previewImg.style.display = 'block';
        previewVid.style.display = 'none';
        previewVid.pause();
        previewVid.src = '';
    } else {
        previewVid.src = att.path;
        previewVid.style.display = 'block';
        previewImg.style.display = 'none';
    }

    // Tombol close
    document.getElementById('preview-close').onclick = () => {
        previewArea.style.display = 'none';
        uploadBox.style.display = 'flex';
        previewVid.pause();
        previewVid.src = '';
        previewImg.src = '';
        el.classList.remove('active');
    };
}

function displayUploadPreview() {
    const uploadWrapper = document.querySelector('.upload-wrapper');
    if (!uploadWrapper) return;

    const uploadDefault = uploadWrapper.querySelector('.upload-default');
    uploadWrapper.querySelectorAll('.upload-data').forEach(el => el.remove());

    formData.attachments.forEach((att, idx) => {
        const div = document.createElement('div');
        div.className = 'upload-data';
        div.style.cursor = 'pointer';

        const media = document.createElement(att.mimetype.startsWith('image/') ? 'img' : 'video');
        media.src = att.path;

        // ← TAMBAHKAN TOGGLE UNTUK IMAGE SAJA
       if (att.mimetype.startsWith('image/')) {
            const badgeWrapper = document.createElement('div');
            badgeWrapper.className = 'thumbnail-badge';
            badgeWrapper.style.cssText = 'position:absolute; top:0rem; left:0rem;';
            
            const radioInput = document.createElement('input');
            radioInput.type = 'radio';
            radioInput.name = 'thumbnail-select';
            radioInput.value = idx;
            radioInput.id = `thumbnail-${idx}`;
            
            // Check jika sudah dipilih sebelumnya
            if (formData.thumbnail == idx) {
                radioInput.checked = true;
            }
            
            radioInput.addEventListener('change', (e) => {
                if (e.target.checked) {
                    formData.thumbnail = idx;
                    console.log('✓ Thumbnail:', att.file.name);
                }
            });
            
            badgeWrapper.appendChild(radioInput);
            div.appendChild(badgeWrapper);
        }

        // ← IKON PLAY TETAP UNTUK VIDEO
        if (att.mimetype.startsWith('video/')) {
            const playIcon = document.createElement('span');
            playIcon.className = 'material-icons-round';
            playIcon.textContent = 'play_circle';
            playIcon.style.cssText = 'position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); color:white; font-size:1.5rem; pointer-events:none;';
            div.appendChild(playIcon);
        }

        // ← TOMBOL REMOVE TETAP
        const removeBtn = document.createElement('span');
        removeBtn.className = 'material-icons-round remove-upload';
        removeBtn.textContent = 'cancel';
        removeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            URL.revokeObjectURL(att.path);
            formData.attachments.splice(idx, 1);
            
            // Reset thumbnail jika yang dihapus adalah thumbnail terpilih
            if (formData.thumbnail == idx) {
                formData.thumbnail = ''; // ← RESET THUMBNAIL
            }

            if (div.classList.contains('active')) {
                document.getElementById('upload-preview-area').style.display = 'none';
                document.getElementById('upload-box-label').style.display = 'flex';
                document.getElementById('preview-vid').pause();
            }

            displayUploadPreview(); // ← Re-render untuk update index
        });

        // div.appendChild(media);
        // div.appendChild(removeBtn);
        // uploadWrapper.insertBefore(div, uploadDefault);
        // Klik thumbnail → buka preview
        div.addEventListener('click', () => openPreview(att, div));

        div.appendChild(media);
        div.appendChild(removeBtn);
        uploadWrapper.insertBefore(div, uploadDefault);
    });

    // ← TAMBAHKAN INI: Set default thumbnail ke image pertama jika kosong
    if (formData.thumbnail === '') {
        // Cari index pertama dari image
        const firstImageIdx = formData.attachments.findIndex(att => att.mimetype.startsWith('image/'));
        
        if (firstImageIdx !== -1) {
            formData.thumbnail = firstImageIdx;
            // Auto-check radio button pertama
            const firstRadio = document.querySelector(`#thumbnail-${firstImageIdx}`);
            if (firstRadio) {
                firstRadio.checked = true;
            }
            console.log('✓ Default thumbnail set to:', formData.attachments[firstImageIdx].file.name);
        }
    }
}

function previousForm() {
    if(formCounter > 1) {
        formCounter--;
        changeIndicator();
        showForm();
        inputSubmit.style.display = "flex"
    } else {
        if(confirm('Apakah Anda yakin ingin membatalkan?\nData yang belum disimpan akan hilang.')) {
            window.location.href = '/profile';
        }
    }
}


function showAlert(title, message, type = 'error') {
    const overlay = document.getElementById('lp-alert-overlay');
    const box = document.getElementById('lp-alert-box');
    const icon = document.getElementById('lp-alert-icon');
    const titleEl = document.getElementById('lp-alert-title');
    const msgEl = document.getElementById('lp-alert-message');
    
    /* ── Set icon berdasarkan type ── */
    icon.className = 'lp-alert-icon material-icons-round';
    switch(type) {
        case 'success':
            icon.textContent = 'check_circle';
            icon.classList.add('success');
            break;
        case 'warning':
            icon.textContent = 'warning';
            icon.classList.add('warning');
            break;
        case 'error':
            icon.textContent = 'error';
            icon.classList.add('error');
            break;
        default:
            icon.textContent = 'info';
            icon.classList.add('info');
    }
    
    /* ── Set title & message ── */
    titleEl.textContent = title;
    msgEl.textContent = message;
    
    /* ── Show overlay & box ── */
    overlay.classList.add('open');
    box.classList.add('open');
}

// // ── FUNCTION: Close Alert ──
function closeAlert() {
    const overlay = document.getElementById('lp-alert-overlay');
    const box = document.getElementById('lp-alert-box');
    
    overlay.classList.remove('open');
    box.classList.remove('open');
}

// ── CLOSE ALERT SAAT OVERLAY DIKLIK ── 
document.getElementById('lp-alert-overlay').addEventListener('click', closeAlert);

// ── CLOSE ALERT DENGAN ESCAPE KEY ──
document.addEventListener('keydown', (e) => {
    if(e.key === 'Escape') closeShowAlert("Validasi", );
});

// Modal functions
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Check if dropdown empty dan show "Tambah Baru" button
function checkAndShowAddButton() {
    const form2Dropdown = document.querySelector('#form-2 .dropdown-datas');
    const form3Dropdown = document.querySelector('#form-3 .dropdown-datas');

    // Remove button yang sudah ada
    form2Dropdown?.querySelector('.btn-tambah-baru')?.remove();
    form3Dropdown?.querySelector('.btn-tambah-baru')?.remove();

    if (form2Dropdown) {
    const visibleItems = Array.from(form2Dropdown.children).filter(
        child => child.style.display !== 'none' && !child.classList.contains('choosen')
    );
    if (visibleItems.length === 0) {
        showAddButtonInDropdown(form2Dropdown, 'Tambah Bahan Baru', 'modal-bahan');
        document.getElementById('form-tambah-bahan-input').value =  document.querySelector('#form-2 .input-data').value;
    }
}

if (form3Dropdown) {
    const visibleItems = Array.from(form3Dropdown.children).filter(
        child => child.style.display !== 'none' && !child.classList.contains('choosen')
    );
    if (visibleItems.length === 0) {
        showAddButtonInDropdown(form3Dropdown, 'Tambah Filter Baru', 'modal-filter');
        document.getElementById('form-tambah-filter-input').value =  document.querySelector('#form-3 .input-data').value;
    }
}
}
function showAddButtonInDropdown(dropdownEl, labelText, modalId) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'btn-tambah-baru';
    btn.innerHTML = `<span class="material-icons-round">add_circle_outline</span> ${labelText}`;

    btn.onclick = (e) => {
        e.preventDefault();
        openModal(modalId);
    };
    dropdownEl.appendChild(btn);
}

// Handle Form Tambah Bahan
document.getElementById('form-tambah-bahan')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch(ROUTE_BAHAN_STORE, {
            method: 'POST',
            body: formData
        });
        
        if (response.ok) {
            const bahan = await response.json();
            addBahanToDropdown(bahan);
            closeModal('modal-bahan');
            e.target.reset();
            showAlert('Sukses', 'Bahan berhasil ditambahkan', 'success');
        } else {
            showAlert('Error', 'Gagal menambahkan bahan', 'error');
        }
    } catch (error) {
        showAlert('Error', 'Terjadi kesalahan: ' + error.message, 'error');
    }
});


// Handle Form Tambah Filter
document.getElementById('form-tambah-filter')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch(ROUTE_FILTER_STORE, {
            method: 'POST',
            body: formData
        });
        
        if (response.ok) {
            const filter = await response.json();
            addFilterToDropdown(filter);
            closeModal('modal-filter');
            e.target.reset();
            showAlert('Sukses', 'Filter berhasil ditambahkan', 'success');
        } else {
            showAlert('Error', 'Gagal menambahkan filter', 'error');
        }
    } catch (error) {
        showAlert('Error', 'Terjadi kesalahan: ' + error.message, 'error');
    }
});

// Tambah bahan ke dropdown setelah submit
function addBahanToDropdown(bahan) {
    const dropdown = document.querySelector('#form-2 .dropdown-datas');
    const div = document.createElement('div');
    div.attri
    div.className = 'dropdown-data';
    div.setAttribute('data-bahan-id', bahan.id);
    div.innerHTML = `<p class="font-jakarta font-semibold text-body text-primary-dark-active">${bahan.nama}</p>`;
    
    const addBtn = dropdown.querySelector('.btn-tambah-baru');
    if (addBtn) {
        dropdown.insertBefore(div, addBtn);
    } else {
        dropdown.appendChild(div);
    }

    div.addEventListener("click", () => {
        currentBahanId = +div.getAttribute('data-bahan-id')
        bahanSudahAda = formData.bahans.find(b => b.id === currentBahanId)
        if(bahanSudahAda) div.classList.toggle("choosen")
        editBerat(div.querySelector('p').innerText)
    })

    const dropdownFilter = document.querySelector('#form-2 .input-data');
    const filterText = dropdownFilter.value.toLowerCase();
    document.querySelector('#form-2 .dropdown-datas').querySelectorAll('.dropdown-data').forEach(item => {
        const text = item.textContent.toLowerCase();
        if (text.includes(filterText)) {
            item.style.display = "flex";
        } else {
            if (!item.classList.contains("choosen")) item.style.display = "none";
        }
    });

    dropdownFilter.addEventListener('input', function () {
        const filter2 = document.querySelector('#form-2 .input-data').value.toLowerCase();
        document.querySelectorAll('#form-2 .dropdown-data').forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(filter2)) {
                item.style.display = "flex";
                hasResults = true;
            } else {
                if (!item.classList.contains("choosen")) item.style.display = "none"
            }
        });
        checkAndShowAddButton()
    });
}

// Tambah filter ke dropdown setelah submit
function addFilterToDropdown(filter) {
    const dropdown = document.querySelector('#form-3 .dropdown-datas');
    const div = document.createElement('div');
    div.className = 'dropdown-data';
    div.setAttribute('data-filter-id', filter.id);
    div.innerHTML = `<p class="font-jakarta font-semibold text-body text-primary-dark-active">${filter.title}</p>`;
    
    const addBtn = dropdown.querySelector('.btn-tambah-baru');
    if (addBtn) {
        dropdown.insertBefore(div, addBtn);
    } else {
        dropdown.appendChild(div);
    }

    div.addEventListener("click", () => {
        div.classList.toggle("choosen")
        div.classList.contains("choosen") ? tambahFilterisasi(div.querySelector('p').innerText, div.getAttribute('data-filter-id')) : hapusFilterisasi(div.querySelector('p').innerText)
    })

    console.log('dasndasjdbjkhaskjhsd')
    const dropdownFilter = document.querySelector('#form-3 .input-data');
    const filterText = dropdownFilter.value.toLowerCase();
    document.querySelector('#form-3 .dropdown-datas').querySelectorAll('.dropdown-data').forEach(item => {
        const text = item.textContent.toLowerCase();
        if (text.includes(filterText)) {
            item.style.display = "flex";
        } else {
            if (!item.classList.contains("choosen")) item.style.display = "none";
        }
    });

    dropdownFilter.addEventListener('input', function () {
        const filter2 = document.querySelector('#form-3 .input-data').value.toLowerCase();
        document.querySelectorAll('#form-3 .dropdown-data').forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(filter2)) {
                item.style.display = "flex";
                hasResults = true;
            } else {
                if (!item.classList.contains("choosen")) item.style.display = "none"
            }
        });
        checkAndShowAddButton()
    });
}
