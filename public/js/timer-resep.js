// timer-resep.js — LaperPoll
// Features: timer countdown, ding bell (Web Audio), vibration, Web Notification

(function () {
    'use strict';

    // ─── DATA DARI BLADE ─────────────────────────────────────────────────────
    const STEPS       = window.TR_STEPS       || [];
    const BAHANS      = window.TR_BAHANS      || [];
    const RESEP_TITLE = window.TR_RESEP_TITLE || 'Timer Masak';
    const CIRC        = 427.256; // 2π × 68

    // ─── STATE ───────────────────────────────────────────────────────────────
    let currentIndex  = 0;
    let timeLeft      = 0;
    let totalTime     = 0;
    let timerInterval = null;
    let isPlaying     = false;

    // ─── DOM REFS ────────────────────────────────────────────────────────────
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
        notifBanner  : document.getElementById('trNotifBanner'),
        notifAllow   : document.getElementById('trNotifAllow'),
        notifDismiss : document.getElementById('trNotifDismiss'),
    };

    // ─── INIT ────────────────────────────────────────────────────────────────
    if (!STEPS.length) {
        if (el.stepDesc) el.stepDesc.textContent = 'Langkah memasak belum tersedia untuk resep ini.';
        return;
    }

    initNotification();
    renderStep(0);
    bindEvents();

    // ─── WEB NOTIFICATION ────────────────────────────────────────────────────
    function initNotification() {
        if (!('Notification' in window)) return;
        // Tampilkan banner minta izin hanya jika belum ditentukan
        if (Notification.permission === 'default') {
            el.notifBanner.style.display = 'flex';
        }
        el.notifAllow?.addEventListener('click', () => {
            Notification.requestPermission().then(() => {
                el.notifBanner.style.display = 'none';
            });
        });
        el.notifDismiss?.addEventListener('click', () => {
            el.notifBanner.style.display = 'none';
        });
    }

    function sendNotification(stepLabel) {
        if (!('Notification' in window) || Notification.permission !== 'granted') return;
        try {
            new Notification('⏰ Timer Selesai!', {
                body: `${RESEP_TITLE} — ${stepLabel} sudah selesai. Lanjut ke langkah berikutnya!`,
                icon: '/assets/images/Logo_Laperpoll.png',
                badge: '/assets/images/Logo_Laperpoll.png',
                tag: 'laperpoll-timer',
                renotify: true,
            });
        } catch (e) {
            // Silent fail - notifikasi tidak kritis
        }
    }

    // ─── DING BELL (Web Audio API synthesized) ───────────────────────────────
    function playDingBell() {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();

            // Ding 1: nada dasar lonceng
            function ding(startTime, freq, duration, gain) {
                const osc  = ctx.createOscillator();
                const env  = ctx.createGain();
                const comp = ctx.createDynamicsCompressor();

                osc.connect(env);
                env.connect(comp);
                comp.connect(ctx.destination);

                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, startTime);
                // Slight pitch drop — karakter lonceng
                osc.frequency.exponentialRampToValueAtTime(freq * 0.85, startTime + duration * 0.8);

                env.gain.setValueAtTime(0, startTime);
                env.gain.linearRampToValueAtTime(gain, startTime + 0.005); // attack cepat
                env.gain.exponentialRampToValueAtTime(0.001, startTime + duration); // decay panjang

                osc.start(startTime);
                osc.stop(startTime + duration);
            }

            // Tambah harmonik (2nd + 3rd partial) biar terdengar seperti lonceng sungguhan
            const t = ctx.currentTime;
            ding(t,        880,  2.5, 0.5);   // fundamental A5
            ding(t,        1760, 2.0, 0.25);  // 2nd harmonic
            ding(t,        2640, 1.5, 0.12);  // 3rd harmonic
            ding(t + 0.35, 1047, 2.0, 0.4);   // C6 — ding kedua
            ding(t + 0.35, 2093, 1.5, 0.15);

            // Tutup context setelah selesai
            setTimeout(() => ctx.close().catch(() => {}), 3500);
        } catch (e) {
            // Silent fail
        }
    }

    // ─── VIBRATION ───────────────────────────────────────────────────────────
    function vibrate() {
        if ('vibrate' in navigator) {
            // Pola: getar-jeda-getar-jeda-getar (ms)
            navigator.vibrate([200, 100, 200, 100, 400]);
        }
    }

    // ─── RENDER LANGKAH ──────────────────────────────────────────────────────
    function renderStep(index) {
        stopTimer();
        currentIndex = index;

        const step = STEPS[index];
        if (!step) return;

        // Badge & label
        el.stepLabel.textContent   = step.label;
        el.stepCounter.textContent = `Langkah ${index + 1} dari ${STEPS.length}`;

        // Deskripsi — animasi fade
        el.stepDesc.style.opacity = '0';
        el.stepDesc.style.transform = 'translateY(4px)';
        el.stepDesc.textContent = step.description || 'Tidak ada deskripsi.';
        requestAnimationFrame(() => {
            el.stepDesc.style.transition = 'opacity 0.25s, transform 0.25s';
            el.stepDesc.style.opacity    = '1';
            el.stepDesc.style.transform  = 'translateY(0)';
        });

        // Durasi
        const secs = parseDuration(step.step_duration);
        if (secs > 0) {
            totalTime = secs;
            timeLeft  = secs;
            el.durationLabel.textContent  = formatTime(secs);
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

        // Bahan
        renderBahans();

        // Stepper
        updateStepper(index);

        // Nav buttons
        el.btnPrev.disabled           = index === 0;
        el.nextLabel.textContent      = index === STEPS.length - 1 ? 'Selesai' : 'Lanjut';
        el.btnNext.disabled           = false;

        // Reset warna
        el.timerDisplay.classList.remove('done-color');
        el.ringFill.classList.remove('done-color');
        resetStartBtn();
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
            if (i < active)   node.classList.add('done');
            if (i === active) node.classList.add('active');
        });

        const connectors = el.stepper.querySelectorAll('.tr-step-connector');
        connectors.forEach((c, i) => {
            c.classList.toggle('done', i < active);
        });

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
            el.btnStart.innerHTML   = '<span class="material-icons-round">play_arrow</span> Lanjut';
            el.btnStart.classList.remove('paused');
            el.timerStatus.textContent = 'Dijeda';
            return;
        }

        // MULAI / RESUME
        isPlaying = true;
        el.btnStart.innerHTML   = '<span class="material-icons-round">pause</span> Jeda';
        el.btnStart.classList.add('paused');
        el.timerStatus.textContent = 'Berjalan';

        timerInterval = setInterval(() => {
            if (timeLeft > 0) {
                timeLeft--;
                updateTimerDisplay(timeLeft, 'Berjalan');
                setRing(timeLeft / totalTime);

                // Warning merah saat 10 detik terakhir
                if (timeLeft <= 10) {
                    el.ringFill.style.stroke  = '#DC2626';
                    el.timerDisplay.style.color = '#DC2626';
                } else {
                    el.ringFill.style.stroke  = '';
                    el.timerDisplay.style.color = '';
                }
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
        el.timerDisplay.style.color  = ''; // reset inline dari warning
        el.ringFill.style.stroke     = ''; // reset inline
        el.ringFill.classList.add('done-color');
        el.timerStatus.textContent   = 'Selesai!';

        el.btnStart.innerHTML = '<span class="material-icons-round">check</span> Selesai';
        el.btnStart.classList.remove('paused');
        el.btnStart.classList.add('done');
        el.btnStart.disabled = true;

        setRing(0);

        // ── NOTIFIKASI SELESAI ──
        playDingBell();
        vibrate();
        sendNotification(STEPS[currentIndex]?.label || 'Langkah ini');
        showDoneToast();

        // Auto-scroll ke tombol Lanjut supaya user tahu ada aksi
        setTimeout(() => {
            el.btnNext?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 600);
    }

    function resetTimer() {
        stopTimer();
        timeLeft = totalTime;
        updateTimerDisplay(timeLeft, 'Belum mulai');
        setRing(1);
        el.timerDisplay.classList.remove('done-color');
        el.timerDisplay.style.color  = '';
        el.ringFill.classList.remove('done-color');
        el.ringFill.style.stroke     = '';
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
        el.finishModal.style.display   = 'flex';
        el.finishOverlay.style.display = 'block';
        document.body.style.overflow   = 'hidden';
        playDingBell();
        vibrate();
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

        // Keyboard shortcut: Space = mulai/jeda, ArrowRight = lanjut, ArrowLeft = sebelumnya
        document.addEventListener('keydown', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
            if (e.code === 'Space')       { e.preventDefault(); startPauseTimer(); }
            if (e.code === 'ArrowRight')  { el.btnNext.click(); }
            if (e.code === 'ArrowLeft')   { el.btnPrev.click(); }
            if (e.code === 'KeyR')        { resetTimer(); }
        });
    }

    // ─── FORMAT HELPERS ──────────────────────────────────────────────────────
    function parseDuration(val) {
        if (!val || val === '00:00:00') return 0;
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