// timer-resep.js — LaperPoll (data dari server, no dummy, no alert/confirm)

(function () {
    'use strict';

    // ─── DATA DARI BLADE ─────────────────────────────────────────────────────
    const STEPS   = window.TR_STEPS  || [];
    const BAHANS  = window.TR_BAHANS || [];
    const CIRC    = 427.256; // 2π × 68, harus sama dengan stroke-dasharray di CSS

    // ─── STATE ───────────────────────────────────────────────────────────────
    let currentIndex  = 0;
    let timeLeft      = 0;
    let totalTime     = 0;
    let timerInterval = null;
    let isPlaying     = false;

    // ─── DOM REFS ─────────────────────────────────────────────────────────────
    const el = {
        stepBadge    : document.getElementById('trStepBadge'),
        stepLabel    : document.getElementById('trStepLabel'),
        stepDuration : document.getElementById('trStepDuration'),
        durationLabel: document.getElementById('trDurationLabel'),
        stepDesc     : document.getElementById('trStepDesc'),
        bahanChips   : document.getElementById('trBahanChips'),
        bahanSection : document.getElementById('trBahanSection'),
        timerDisplay : document.getElementById('trTimerDisplay'),
        timerStatus  : document.getElementById('trTimerStatus'),
        ringFill     : document.getElementById('trRingFill'),
        ringWrap     : document.getElementById('trTimerRingWrap'),
        noTimer      : document.getElementById('trNoTimer'),
        timerActions : document.getElementById('trTimerActions'),
        btnStart     : document.getElementById('trBtnStart'),
        btnReset     : document.getElementById('trBtnReset'),
        btnPrev      : document.getElementById('trBtnPrev'),
        btnNext      : document.getElementById('trBtnNext'),
        nextLabel    : document.getElementById('trNextLabel'),
        stepCounter  : document.getElementById('trStepCounter'),
        stepper      : document.getElementById('trStepper'),
        doneToast    : document.getElementById('trDoneToast'),
        doneClose    : document.getElementById('trDoneClose'),
        finishModal  : document.getElementById('trFinishModal'),
        finishOverlay: document.getElementById('trFinishOverlay'),
    };

    // ─── INIT ────────────────────────────────────────────────────────────────
    if (!STEPS.length) {
        if (el.stepDesc) el.stepDesc.textContent = 'Langkah memasak belum tersedia untuk resep ini.';
        return;
    }

    renderStep(0);
    bindEvents();

    // ─── RENDER LANGKAH ──────────────────────────────────────────────────────
    function renderStep(index) {
        stopTimer();
        currentIndex = index;

        const step = STEPS[index];
        if (!step) return;

        // Badge & label
        el.stepLabel.textContent = step.label;
        el.stepCounter.textContent = `Langkah ${index + 1} dari ${STEPS.length}`;

        // Deskripsi
        el.stepDesc.textContent = step.description || 'Tidak ada deskripsi.';

        // Durasi
        const secs = parseDuration(step.step_duration);
        if (secs > 0) {
            totalTime = secs;
            timeLeft  = secs;
            el.durationLabel.textContent = formatTime(secs);
            el.stepDuration.style.display = 'inline-flex';
            el.ringWrap.style.display     = 'flex';
            el.noTimer.style.display      = 'none';
            el.timerActions.style.display = 'flex';
            setRing(1);
            updateTimerDisplay(secs, 'Belum mulai');
        } else {
            totalTime = 0;
            timeLeft  = 0;
            el.stepDuration.style.display = 'none';
            el.ringWrap.style.display     = 'none';
            el.noTimer.style.display      = 'flex';
            el.timerActions.style.display = 'none';
        }

        // Bahan — tampilkan semua bahan resep (langkah tidak punya relasi bahan spesifik)
        renderBahans();

        // Stepper
        updateStepper(index);

        // Nav buttons
        el.btnPrev.disabled = index === 0;
        el.nextLabel.textContent = index === STEPS.length - 1 ? 'Selesai' : 'Lanjut';

        // Reset warna
        el.timerDisplay.classList.remove('done-color');
        el.ringFill.classList.remove('done-color');
        resetStartBtn();

        // Sembunyikan toast
        hideDoneToast();
    }

    // ─── BAHAN CHIPS ─────────────────────────────────────────────────────────
    function renderBahans() {
        if (!BAHANS.length) {
            el.bahanSection.style.display = 'none';
            return;
        }
        el.bahanSection.style.display = 'flex';
        el.bahanChips.innerHTML = BAHANS.map(b => `
            <div class="tr-bahan-chip">
                <span class="tr-bahan-chip-amt">${b.gram}g</span>
                ${escHtml(b.nama)}
            </div>
        `).join('');
    }

    // ─── STEPPER ─────────────────────────────────────────────────────────────
    function updateStepper(active) {
        const nodes = el.stepper.querySelectorAll('.tr-step-node');
        nodes.forEach((node, i) => {
            node.classList.remove('active', 'done');
            if (i < active)  node.classList.add('done');
            if (i === active) node.classList.add('active');
        });

        // Update connector warna
        const connectors = el.stepper.querySelectorAll('.tr-step-connector');
        connectors.forEach((c, i) => {
            c.classList.toggle('done', i < active);
        });

        // Scroll stepper ke posisi aktif
        const activeNode = el.stepper.querySelector('.tr-step-node.active');
        if (activeNode) {
            activeNode.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
    }

    // ─── TIMER ───────────────────────────────────────────────────────────────
    function startPauseTimer() {
        if (totalTime === 0) return;

        if (isPlaying) {
            // JEDA
            clearInterval(timerInterval);
            isPlaying = false;
            el.btnStart.innerHTML = '<span class="material-icons-round">play_arrow</span> Lanjut';
            el.btnStart.classList.remove('paused');
            el.timerStatus.textContent = 'Dijeda';
            return;
        }

        // MULAI
        isPlaying = true;
        el.btnStart.innerHTML = '<span class="material-icons-round">pause</span> Jeda';
        el.btnStart.classList.add('paused');
        el.timerStatus.textContent = 'Berjalan';

        timerInterval = setInterval(() => {
            if (timeLeft > 0) {
                timeLeft--;
                updateTimerDisplay(timeLeft, 'Berjalan');
                setRing(timeLeft / totalTime);
            } else {
                onTimerDone();
            }
        }, 1000);
    }

    function onTimerDone() {
        clearInterval(timerInterval);
        isPlaying = false;

        el.timerDisplay.textContent = '00:00';
        el.timerDisplay.classList.add('done-color');
        el.ringFill.classList.add('done-color');
        el.timerStatus.textContent = 'Selesai!';

        el.btnStart.innerHTML = '<span class="material-icons-round">check</span> Selesai';
        el.btnStart.classList.remove('paused');
        el.btnStart.classList.add('done');
        el.btnStart.disabled = true;

        setRing(0);
        showDoneToast();
    }

    function resetTimer() {
        stopTimer();
        timeLeft = totalTime;
        updateTimerDisplay(timeLeft, 'Belum mulai');
        setRing(1);
        el.timerDisplay.classList.remove('done-color');
        el.ringFill.classList.remove('done-color');
        resetStartBtn();
        hideDoneToast();
    }

    function stopTimer() {
        clearInterval(timerInterval);
        isPlaying = false;
    }

    function resetStartBtn() {
        el.btnStart.innerHTML = '<span class="material-icons-round">play_arrow</span> Mulai';
        el.btnStart.classList.remove('paused', 'done');
        el.btnStart.disabled = false;
    }

    // ─── RING HELPER ─────────────────────────────────────────────────────────
    function setRing(fraction) {
        const offset = CIRC * (1 - Math.max(0, Math.min(1, fraction)));
        el.ringFill.style.strokeDashoffset = offset;
    }

    function updateTimerDisplay(secs, status) {
        el.timerDisplay.textContent = formatTime(secs);
        if (status) el.timerStatus.textContent = status;
    }

    // ─── TOAST selesai timer ──────────────────────────────────────────────────
    function showDoneToast() {
        el.doneToast.style.display = 'block';
    }
    function hideDoneToast() {
        el.doneToast.style.display = 'none';
    }

    // ─── MODAL selesai masak ──────────────────────────────────────────────────
    function showFinishModal() {
        el.finishModal.style.display  = 'flex';
        el.finishOverlay.style.display = 'block';
        document.body.style.overflow  = 'hidden';
    }

    // ─── EVENTS ──────────────────────────────────────────────────────────────
    function bindEvents() {
        el.btnStart.addEventListener('click', startPauseTimer);
        el.btnReset.addEventListener('click', resetTimer);

        el.btnPrev.addEventListener('click', () => {
            if (currentIndex > 0) renderStep(currentIndex - 1);
        });

        el.btnNext.addEventListener('click', () => {
            if (currentIndex < STEPS.length - 1) {
                renderStep(currentIndex + 1);
            } else {
                showFinishModal();
            }
        });

        el.doneClose?.addEventListener('click', hideDoneToast);

        el.finishOverlay?.addEventListener('click', () => {
            el.finishModal.style.display   = 'none';
            el.finishOverlay.style.display = 'none';
            document.body.style.overflow   = '';
        });
    }

    // ─── FORMAT HELPERS ──────────────────────────────────────────────────────
    function parseDuration(val) {
        if (!val || val === '00:00:00') return 0;
        // Format HH:MM:SS
        const parts = String(val).split(':').map(Number);
        if (parts.length === 3) return parts[0] * 3600 + parts[1] * 60 + parts[2];
        if (parts.length === 2) return parts[0] * 60 + parts[1];
        return parseInt(val) || 0;
    }

    function formatTime(s) {
        const m   = Math.floor(s / 60);
        const sec = s % 60;
        return `${String(m).padStart(2,'0')}:${String(sec).padStart(2,'0')}`;
    }

    function escHtml(str) {
        return String(str || '')
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

})();