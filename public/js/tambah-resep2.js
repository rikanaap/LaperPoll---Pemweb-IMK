// ──────────────────────────────────────────────────────────
// TAMBAH RESEP - Enhanced Form Multi-Step Management
// ──────────────────────────────────────────────────────────

console.log('dasdkandlkanskdnasklndlkansdklnaslkdndasndlkasndlk')

class TambahResepForm {
    constructor() {
        this.currentForm = 1;
        this.maxForm = 5;
        this.selectedBahanId = null;
        
        // Data storage
        this.formData = {
            nama: '',
            calorie: 0,
            kategori_id: null,
            bahans: [],
            filters: [],
            steps: [],
            attachments: []
        };

        this.init();
    }

    init() {
        try {
            this.loadFromCookie();
            this.attachEventListeners();
            this.populateDropdowns();
            this.showForm(1);
            console.log('✓ TambahResepForm initialized');
        } catch (error) {
            console.error('✗ Error initializing TambahResepForm:', error);
        }
    }

    // ──────────────────────────────────────────────────────────
    // Cookie Management
    // ──────────────────────────────────────────────────────────

    loadFromCookie() {
        try {
            const bahansCookie = this.getCookie('form_bahans');
            this.formData.bahans = bahansCookie ? JSON.parse(bahansCookie) : [];

            const filtersCookie = this.getCookie('form_filters');
            this.formData.filters = filtersCookie ? JSON.parse(filtersCookie) : [];

            const stepsCookie = this.getCookie('form_steps');
            this.formData.steps = stepsCookie ? JSON.parse(stepsCookie) : [];

            const attachmentsCookie = this.getCookie('form_attachments');
            this.formData.attachments = attachmentsCookie ? JSON.parse(attachmentsCookie) : [];

            console.log('✓ Loaded from cookie:', this.formData);
        } catch (error) {
            console.error('✗ Error loading from cookie:', error);
        }
    }

    saveToCookie() {
        try {
            this.setCookie('form_bahans', JSON.stringify(this.formData.bahans), 1);
            this.setCookie('form_filters', JSON.stringify(this.formData.filters), 1);
            this.setCookie('form_steps', JSON.stringify(this.formData.steps), 1);
            this.setCookie('form_attachments', JSON.stringify(this.formData.attachments), 1);
            console.log('✓ Saved to cookie');
        } catch (error) {
            console.error('✗ Error saving to cookie:', error);
        }
    }

    getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i].trim();
            if (c.indexOf(nameEQ) === 0) {
                return decodeURIComponent(c.substring(nameEQ.length));
            }
        }
        return null;
    }

    setCookie(name, value, days = 1) {
        const d = new Date();
        d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = "expires=" + d.toUTCString();
        document.cookie = `${name}=${encodeURIComponent(value)};${expires};path=/`;
    }

    // ──────────────────────────────────────────────────────────
    // Dropdown Population
    // ──────────────────────────────────────────────────────────

    populateDropdowns() {
        this.setupKategoriDropdown();
        this.setupBahanDropdown();
        this.setupFilterDropdown();
    }

    setupKategoriDropdown() {
        const form1 = document.getElementById('form-1');
        if (!form1) return;

        const dropdown = form1.querySelector('.input-dropdown');
        if (!dropdown) return;

        const searchInput = dropdown.querySelector('input[type="text"]');
        const dropdownDatas = dropdown.querySelector('.dropdown-datas');
        const items = dropdownDatas.querySelectorAll('.dropdown-data');

        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                items.forEach(item => {
                    const text = item.querySelector('p').textContent.toLowerCase();
                    item.style.display = text.includes(term) ? 'block' : 'none';
                });
            });
        }

        items.forEach(item => {
            item.addEventListener('click', () => {
                const selected = dropdown.querySelector('.selected');
                if (selected) selected.classList.remove('selected');
                item.classList.add('selected');
                searchInput.value = item.querySelector('p').textContent;
                this.formData.kategori_id = parseInt(item.dataset.kategoriId);
                console.log('✓ Kategori selected:', this.formData.kategori_id);
            });
        });
    }

    setupBahanDropdown() {
        const form2 = document.getElementById('form-2');
        if (!form2) return;

        const dropdown = form2.querySelector('.input-dropdown');
        if (!dropdown) return;

        const searchInput = dropdown.querySelector('input[type="text"]');
        const dropdownDatas = dropdown.querySelector('.dropdown-datas');
        const items = dropdownDatas.querySelectorAll('.dropdown-data');

        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                items.forEach(item => {
                    const text = item.querySelector('p').textContent.toLowerCase();
                    item.style.display = text.includes(term) ? 'block' : 'none';
                });
            });
        }

        items.forEach(item => {
            item.addEventListener('click', () => {
                const bahanId = parseInt(item.dataset.bahanId);
                const bahanName = item.querySelector('p').textContent;

                this.selectedBahanId = bahanId;

                // Tampilkan input berat
                const judulBahan = document.getElementById('JudulBahan');
                const inputBerat = document.getElementById('InputBerat');
                const addBtn = dropdown.parentElement.querySelector('.btn-add-bahan');

                if (judulBahan && inputBerat) {
                    judulBahan.style.display = 'flex';
                    judulBahan.querySelector('input').value = bahanName;
                    inputBerat.style.display = 'flex';
                    addBtn.style.display = 'block';
                }

                searchInput.value = bahanName;
                console.log('✓ Bahan selected:', bahanId, bahanName);
            });
        });

        // Attach button handler
        const addBtn = form2.querySelector('.btn-add-bahan');
        if (addBtn) {
            addBtn.addEventListener('click', () => {
                this.confirmAddBahan();
            });
        }
    }

    confirmAddBahan() {
        if (!this.selectedBahanId) {
            alert('Pilih bahan terlebih dahulu');
            return;
        }

        const gramInput = document.querySelector('#InputBerat .input-number');
        const gram = parseInt(gramInput.value) || 0;

        if (gram <= 0) {
            alert('Berat harus lebih dari 0');
            return;
        }

        const bahanName = document.getElementById('JudulBahan').querySelector('input').value;
        this.addBahan(this.selectedBahanId, bahanName, gram);
        this.clearBahanForm();
    }

    setupFilterDropdown() {
        const form3 = document.getElementById('form-3');
        if (!form3) return;

        const dropdown = form3.querySelector('.input-dropdown');
        if (!dropdown) return;

        const searchInput = dropdown.querySelector('input[type="text"]');
        const dropdownDatas = dropdown.querySelector('.dropdown-datas');
        const items = dropdownDatas.querySelectorAll('.dropdown-data');

        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                items.forEach(item => {
                    const text = item.querySelector('p').textContent.toLowerCase();
                    item.style.display = text.includes(term) ? 'block' : 'none';
                });
            });
        }

        items.forEach(item => {
            item.addEventListener('click', () => {
                const filterId = parseInt(item.dataset.filterId);
                const filterName = item.querySelector('p').textContent;

                if (!this.formData.filters.find(f => f.id === filterId)) {
                    this.formData.filters.push({ id: filterId, name: filterName });
                    this.saveToCookie();
                    this.updateFilterResult();
                    console.log('✓ Filter added:', filterId, filterName);
                } else {
                    alert('Filter ini sudah dipilih');
                }
            });
        });
    }

    // ──────────────────────────────────────────────────────────
    // Form Navigation
    // ──────────────────────────────────────────────────────────

    showForm(formNumber) {
        this.currentForm = formNumber;
        console.log(currentForm)

        // Hide all forms
        for (let i = 1; i <= 5; i++) {
            const form = document.getElementById(`form-${i}`);
            const result = document.getElementById(`result-${i}`);
            if (form) form.style.display = 'none';
            if (result) result.style.display = 'none';
            
            if (i <= 4) {
                const result1 = document.getElementById(`result-${i}-1`);
                const result2 = document.getElementById(`result-${i}-2`);
                if (result1) result1.style.display = 'none';
                if (result2) result2.style.display = 'none';
            }
        }

        // Show current form and result
        const currentFormEl = document.getElementById(`form-${formNumber}`);
        if (currentFormEl) currentFormEl.style.display = 'flex';

        if (formNumber === 2) {
            const result2 = document.getElementById('result-2');
            if (result2) result2.style.display = 'flex';
            this.updateBahanResult();
        }

        if (formNumber === 3) {
            const result3 = document.getElementById('result-3');
            if (result3) result3.style.display = 'flex';
            this.updateBahanResult();
        }

        if (formNumber === 4) {
            console.log("dasjkdnandkajnkdnalkdnal")
            const result41 = document.getElementById('result-4-1');
            const result42 = document.getElementById('result-4-2');
            if (result41) result41.style.display = 'flex';
            if (result42) result42.style.display = 'flex';
            this.updateStepsResult();
            this.populateForm42();
        }

        this.updateIndicator();
        console.log('✓ Form shown:', formNumber);
    }

    updateIndicator() {
        const indicators = document.querySelectorAll('.indicator');
        indicators.forEach((ind, idx) => {
            if (idx < this.currentForm) {
                ind.classList.add('i-enable');
            } else {
                ind.classList.remove('i-enable');
            }
        });

        const label = document.querySelector('.form-indicator p');
        if (label) {
            label.textContent = `${this.currentForm}/${this.maxForm}`;
        }
    }

    nextForm() {
        if (!this.validateForm(this.currentForm)) {
            return false;
        }

        console.log(this.currentForm)
        
        if (this.currentForm < this.maxForm) {
            this.currentForm++;
            this.showForm(this.currentForm);
            return true;
        }
        return false;
    }

    prevForm() {
        if (this.currentForm > 1) {
            this.currentForm--;
            this.showForm(this.currentForm);
            return true;
        }
        return false;
    }

    // ──────────────────────────────────────────────────────────
    // Event Listeners
    // ──────────────────────────────────────────────────────────

    attachEventListeners() {
        console.log('what the fuck')
        this.attachForm1Listeners();
        this.attachForm4StepListener();
        this.attachForm5Listeners();
        this.attachSubmitListener();
        this.attachNavigationListeners();
    }

    attachForm1Listeners() {
        const form1 = document.getElementById('form-1');
        if (!form1) return;

        const namaInput = form1.querySelector('input[type="text"]');
        if (namaInput) {
            namaInput.addEventListener('input', (e) => {
                this.formData.nama = e.target.value;
            });
        }

        const calorieInput = form1.querySelector('input[type="number"]');
        if (calorieInput) {
            calorieInput.addEventListener('input', (e) => {
                this.formData.calorie = parseInt(e.target.value) || 0;
            });
        }
    }

    attachForm4StepListener() {
        const form4_1 = document.getElementById('form-4-1');
        if (!form4_1) return;

        const addStepBtn = form4_1.querySelector('.btn-add-step');
        if (addStepBtn) {
            addStepBtn.addEventListener('click', () => {
                const textarea = form4_1.querySelector('textarea');
                const deskripsi = textarea.value.trim();

                if (!deskripsi) {
                    alert('Tulis deskripsi langkah terlebih dahulu');
                    return;
                }

                console.log(deskripsi)

                this.addStep(deskripsi);
                textarea.value = '';
            });
        }
    }

    attachForm5Listeners() {
        const fileInput = document.getElementById('file-upload');
        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                this.handleFileUpload(e.target.files);
            });
        }
    }

    attachSubmitListener() {
        const submitBtn = document.querySelector('.input-submit');
        if (submitBtn) {
            console.log('FUCK IS THIS')
            submitBtn.addEventListener('click', () => {
                if (this.currentForm === this.maxForm) {
                    this.submitForm();
                } else {
                    this.nextForm();
                }
            });
        }
    }

    attachNavigationListeners() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('.nav-prev')) {
                e.preventDefault();
                this.prevForm();
            }
            if (e.target.closest('.nav-next')) {
                e.preventDefault();
                this.nextForm();
            }
        });
    }

    // ──────────────────────────────────────────────────────────
    // Form Actions
    // ──────────────────────────────────────────────────────────

    addBahan(bahanId, bahanName, gram) {
        if (this.formData.bahans.find(b => b.bahan_id === bahanId)) {
            alert('Bahan ini sudah ditambahkan');
            return;
        }

        this.formData.bahans.push({
            bahan_id: parseInt(bahanId),
            nama: bahanName,
            gram_total: parseInt(gram)
        });

        this.saveToCookie();
        this.updateBahanResult();
        console.log('✓ Bahan added:', bahanId);
    }

    removeBahan(bahanId) {
        const index = this.formData.bahans.findIndex(b => b.bahan_id === bahanId);
        if (index > -1) {
            this.formData.bahans.splice(index, 1);
            this.saveToCookie();
            this.updateBahanResult();
            console.log('✓ Bahan removed:', bahanId);
        }
    }

    updateBahanResult() {
        const result = document.querySelector('#result-2 .wrapper-result, #result-3 .wrapper-result');
        if (!result) return;

        result.innerHTML = this.formData.bahans.map(bahan => `
            <div class="result-data flex flex-row" data-bahan-id="${bahan.bahan_id}" style="position: relative;">
                <p class="font-jakarta font-regular text-body">${bahan.gram_total} g</p>
                <div class="vertical-line"></div>
                <p class="font-jakarta font-regular text-body">${bahan.nama}</p>
                <span class="material-icons-round text-title2 cursor-pointer remove-bahan" style="margin-left: auto;">remove_circle_outline</span>
            </div>
        `).join('');

        document.querySelectorAll('.remove-bahan').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const bahanId = parseInt(e.target.closest('.result-data').dataset.bahanId);
                this.removeBahan(bahanId);
            });
        });
    }

    clearBahanForm() {
        const judulBahan = document.getElementById('JudulBahan');
        const inputBerat = document.getElementById('InputBerat');
        const addBtn = document.querySelector('#form-2 .btn-add-bahan');

        if (judulBahan) judulBahan.style.display = 'none';
        if (inputBerat) inputBerat.style.display = 'none';
        if (addBtn) addBtn.style.display = 'none';

        const searchInput = document.querySelector('#form-2 .input-dropdown input[type="text"]');
        if (searchInput) searchInput.value = '';

        const gramInput = document.querySelector('#InputBerat .input-number');
        if (gramInput) gramInput.value = '20';

        this.selectedBahanId = null;
    }

    addStep(deskripsi) {
        const timeInput = document.getElementById('timeInput');
        const durasi = timeInput ? timeInput.value : '00:10:00';

        this.formData.steps.push({
            deskripsi: deskripsi,
            durasi: durasi,
            bahans: []
        });

        this.saveToCookie();
        this.updateStepsResult();
        console.log('✓ Step added');
    }

    removeStep(index) {
        if (index >= 0 && index < this.formData.steps.length) {
            this.formData.steps.splice(index, 1);
            this.saveToCookie();
            this.updateStepsResult();
            console.log('✓ Step removed:', index);
        }
    }

    updateStepsResult() {
        const result = document.getElementById('result-4-1');
        if (!result) return;

        const wrapper = result.querySelector('.wrapper-result');
        wrapper.innerHTML = this.formData.steps.map((step, idx) => `
            <div class="result-bahan flex flex-row" data-step-index="${idx}" style="position: relative;">
                <p class="font-jakarta font-regular text-body">${step.deskripsi}</p>
                <div class="vertical-line"></div>
                <p class="font-jakarta font-regular text-body">${step.durasi}</p>
                <span class="material-icons-round text-title2 cursor-pointer remove-step" style="margin-left: auto;">remove_circle_outline</span>
            </div>
        `).join('');

        document.querySelectorAll('.remove-step').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const index = parseInt(e.target.closest('.result-bahan').dataset.stepIndex);
                this.removeStep(index);
            });
        });
    }

    updateFilterResult() {
        const result = document.querySelector('#result-3 .wrapper-result');
        if (!result) return;

        result.innerHTML = this.formData.filters.map(filter => `
            <div class="result-data flex flex-row" data-filter-id="${filter.id}" style="position: relative;">
                <p class="font-jakarta font-regular text-body">${filter.name}</p>
                <span class="material-icons-round text-title2 cursor-pointer remove-filter" style="margin-left: auto;">remove_circle_outline</span>
            </div>
        `).join('');

        document.querySelectorAll('.remove-filter').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const filterId = parseInt(e.target.closest('.result-data').dataset.filterId);
                const index = this.formData.filters.findIndex(f => f.id === filterId);
                if (index > -1) {
                    this.formData.filters.splice(index, 1);
                    this.saveToCookie();
                    this.updateFilterResult();
                }
            });
        });
    }

    populateForm42() {
        const form4_2 = document.getElementById('form-4-2');
        if (!form4_2) return;

        const dropdownDatas = form4_2.querySelector('.dropdown-datas');
        if (!dropdownDatas) return;

        // Clear existing
        dropdownDatas.innerHTML = '';

        // Populate dari bahans
        this.formData.bahans.forEach((bahan, idx) => {
            const html = `
                <div class="input input-scale">
                    <div class="input-scale-text flex flex-row gap-4">
                        <input class="input-checkbox" type="checkbox" name="checkbox-bahan" data-bahan-idx="${idx}">
                        <p class="font-jakarta text-body text-secondary-normal">${bahan.nama} (${bahan.gram_total}g)</p>
                    </div>
                    <div class="input-scale-input flex flex-row gap-4">
                        <span class="material-icons-round">add_circle_outline</span>
                        <input class="input-number text-body font-jakarta font-semibold" type="number" size="4" placeholder="1" value="1" data-bahan-idx="${idx}">
                        <span class="material-icons-round">remove_circle_outline</span>
                    </div>
                </div>
            `;
            dropdownDatas.insertAdjacentHTML('beforeend', html);
        });
    }

    handleFileUpload(files) {
        Array.from(files).forEach((file, idx) => {
            if (!file.type.startsWith('image/') && !file.type.startsWith('video/')) {
                alert(`File ${file.name} tidak didukung. Hanya image dan video.`);
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                this.formData.attachments.push({
                    name: file.name,
                    type: file.type,
                    data: e.target.result
                });
                this.saveToCookie();
                this.displayUploadPreview();
            };
            reader.onerror = () => {
                alert(`Error reading file: ${file.name}`);
            };
            reader.readAsDataURL(file);
        });
    }

    displayUploadPreview() {
        const uploadData = document.querySelector('.upload-data');
        if (!uploadData) return;

        uploadData.innerHTML = this.formData.attachments.map((att, idx) => {
            const preview = att.type.startsWith('image/')
                ? `<img src="${att.data}" alt="${att.name}" style="max-width: 100px; max-height: 100px; object-fit: cover;">`
                : `<video src="${att.data}" style="max-width: 100px; max-height: 100px;"></video>`;

            return `
                <div style="position: relative; display: inline-block; margin: 5px;">
                    ${preview}
                    <button class="remove-upload" data-index="${idx}" style="position: absolute; top: 0; right: 0; background: red; color: white; border: none; cursor: pointer; border-radius: 50%; width: 20px; height: 20px;">×</button>
                </div>
            `;
        }).join('');

        document.querySelectorAll('.remove-upload').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const idx = parseInt(e.target.dataset.index);
                this.formData.attachments.splice(idx, 1);
                this.saveToCookie();
                this.displayUploadPreview();
            });
        });
    }

    // ──────────────────────────────────────────────────────────
    // Validasi
    // ──────────────────────────────────────────────────────────

    validateForm(formNumber) {
        switch (formNumber) {
            case 1:
                if (!this.formData.nama.trim()) {
                    alert('⚠ Nama resep wajib diisi');
                    return false;
                }
                if (!this.formData.calorie || this.formData.calorie <= 0) {
                    alert('⚠ Kalori wajib diisi dan lebih dari 0');
                    return false;
                }
                if (!this.formData.kategori_id) {
                    alert('⚠ Kategori wajib dipilih');
                    return false;
                }
                return true;

            case 2:
                if (this.formData.bahans.length === 0) {
                    alert('⚠ Tambahkan minimal satu bahan');
                    return false;
                }
                return true;

            case 3:
                if (this.formData.filters.length === 0) {
                    alert('⚠ Tambahkan minimal satu filter');
                    return false;
                }
                return true;

            case 4:
                if (this.formData.steps.length === 0) {
                    alert('⚠ Tambahkan minimal satu langkah pembuatan');
                    return false;
                }
                return true;

            default:
                return true;
        }
    }

    // ──────────────────────────────────────────────────────────
    // Submit
    // ──────────────────────────────────────────────────────────

    submitForm() {
        if (!this.validateForm(5)) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = document.querySelector('meta[name="form-submit-url"]')?.content || '/resep/store';

        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!token) {
            alert('⚠ CSRF token tidak ditemukan');
            return;
        }

        const formHTML = `
            <input type="hidden" name="_token" value="${token}">
            <input type="hidden" name="title" value="${this.escapeHtml(this.formData.nama)}">
            <input type="hidden" name="calorie" value="${this.formData.calorie}">
            <input type="hidden" name="main_filter_id" value="${this.formData.kategori_id}">
            <input type="hidden" name="form_bahans" value='${JSON.stringify(this.formData.bahans)}'>
            <input type="hidden" name="form_filters" value='${JSON.stringify(this.formData.filters)}'>
            <input type="hidden" name="form_steps" value='${JSON.stringify(this.formData.steps)}'>
        `;

        form.innerHTML = formHTML;
        document.body.appendChild(form);

        console.log('✓ Submitting form with data:', this.formData);
        form.submit();
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// ──────────────────────────────────────────────────────────
// Initialize
// ──────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', () => {
    window.resepForm = new TambahResepForm();
    console.log('✓ Window.resepForm ready');
});