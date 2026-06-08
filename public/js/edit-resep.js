// edit-resep.js — LaperPoll

// ── Thumbnail Preview ─────────────────────────────────────────────────────────
const thumbInput   = document.getElementById('erThumbInput');
const thumbPreview = document.getElementById('erThumbPreview');
const thumbEmpty   = document.getElementById('erThumbEmpty');
const thumbOverlay = document.getElementById('erThumbOverlay');

thumbInput?.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (ev) => {
        if (thumbPreview) {
            thumbPreview.src = ev.target.result;
            thumbPreview.style.display = 'block';
        }
        if (thumbEmpty)   thumbEmpty.style.display   = 'none';
        if (thumbOverlay) thumbOverlay.style.display = 'flex';
    };
    reader.readAsDataURL(file);
});

// ── Char Counter ──────────────────────────────────────────────────────────────
const descTextarea = document.getElementById('erDesc');
const descCount    = document.getElementById('erDescCount');
descTextarea?.addEventListener('input', () => {
    if (descCount) descCount.textContent = descTextarea.value.length;
});

// ── Dynamic List: Bahan ───────────────────────────────────────────────────────
let bahanCount = document.querySelectorAll('.er-bahan-item').length;

document.getElementById('btnTambahBahan')?.addEventListener('click', () => {
    const list = document.getElementById('bahanList');
    const idx  = bahanCount++;
    const item = document.createElement('div');
    item.className = 'er-list-item er-bahan-item';
    item.dataset.index = idx;
    item.innerHTML = `
        <div class="er-list-item-num">${idx + 1}</div>
        <div class="er-bahan-inputs">
            <input type="text" name="ingredients[${idx}][amount]"
                   class="er-input er-input-sm" placeholder="Jumlah (mis. 200g)">
            <input type="text" name="ingredients[${idx}][name]"
                   class="er-input er-input-sm er-bahan-name" placeholder="Nama bahan">
        </div>
        <button type="button" class="er-remove-btn" onclick="removeListItem(this)">
            <span class="material-icons-round">close</span>
        </button>`;
    list.appendChild(item);
    item.querySelector('input').focus();
    renumberList(list, '.er-list-item-num');
});

// ── Dynamic List: Langkah ─────────────────────────────────────────────────────
let langkahCount = document.querySelectorAll('.er-langkah-item').length;

document.getElementById('btnTambahLangkah')?.addEventListener('click', () => {
    const list = document.getElementById('langkahList');
    const idx  = langkahCount++;
    const item = document.createElement('div');
    item.className = 'er-list-item er-langkah-item';
    item.dataset.index = idx;
    item.innerHTML = `
        <div class="er-list-item-num">${idx + 1}</div>
        <textarea name="steps[${idx}]"
                  class="er-textarea er-textarea-sm"
                  placeholder="Jelaskan langkah ini..."
                  rows="2"></textarea>
        <button type="button" class="er-remove-btn" onclick="removeListItem(this)">
            <span class="material-icons-round">close</span>
        </button>`;
    list.appendChild(item);
    item.querySelector('textarea').focus();
    renumberList(list, '.er-list-item-num');
});

// ── Remove Item ───────────────────────────────────────────────────────────────
function removeListItem(btn) {
    const item = btn.closest('.er-list-item');
    const list = item.parentElement;

    // Jangan hapus kalau cuma satu item tersisa
    if (list.querySelectorAll('.er-list-item').length <= 1) {
        item.style.animation = 'none';
        item.style.transform = 'translateX(6px)';
        setTimeout(() => { item.style.transform = ''; }, 200);
        return;
    }

    item.style.opacity = '0';
    item.style.transform = 'translateX(12px)';
    item.style.transition = 'all 0.18s ease';
    setTimeout(() => {
        item.remove();
        renumberList(list, '.er-list-item-num');
    }, 180);
}
window.removeListItem = removeListItem;

// ── Renumber list items ───────────────────────────────────────────────────────
function renumberList(list, numSelector) {
    list.querySelectorAll('.er-list-item').forEach((item, i) => {
        const num = item.querySelector(numSelector);
        if (num) num.textContent = i + 1;
        // Update name indices
        item.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/\[\d+\]/, `[${i}]`);
        });
    });
}

// ── Form Submit: loading state ────────────────────────────────────────────────
document.getElementById('editResepForm')?.addEventListener('submit', () => {
    const btn = document.getElementById('erBtnSave');
    if (btn) {
        btn.classList.add('loading');
        btn.innerHTML = `<span class="material-icons-round lp-spin">autorenew</span> <span>Menyimpan...</span>`;
    }
});