// toast.js — LaperPoll unified toast (slide dari atas, gaya konsisten login)

(function () {
    'use strict';

    // Inject CSS sekali saja
    if (!document.getElementById('lp-toast-style')) {
        const style = document.createElement('style');
        style.id = 'lp-toast-style';
        style.textContent = `
            #lp-toast-container {
                position: fixed;
                top: 0;
                left: 50%;
                transform: translateX(-50%) translateY(-130%);
                z-index: 9999;
                width: calc(100% - 2rem);
                max-width: 400px;
                transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
                pointer-events: none;
            }
            #lp-toast-container.show {
                transform: translateX(-50%) translateY(0);
            }
            .lp-toast-inner {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 0.875rem 1rem;
                border-radius: 1rem;
                background: var(--orange-normal-hover, #CF4900);
                box-shadow: 0 8px 24px rgba(0,0,0,0.15);
                margin-top: 1rem;
                font-family: var(--font-jakarta, 'Plus Jakarta Sans', sans-serif);
            }
            .lp-toast-logo {
                width: 4rem;
                object-fit: contain;
                flex-shrink: 0;
                filter: brightness(10);
            }
            .lp-toast-divider {
                width: 1px;
                height: 1.5rem;
                background: rgba(255,255,255,0.25);
                flex-shrink: 0;
            }
            .lp-toast-icon {
                font-size: 1.2rem;
                flex-shrink: 0;
                color: white;
            }
            .lp-toast-msg {
                font-size: 0.875rem;
                font-weight: 600;
                color: white;
                flex: 1;
                margin: 0;
                font-family: var(--font-jakarta, 'Plus Jakarta Sans', sans-serif);
            }

            /* Warna per tipe */
            .lp-toast-inner.success { background: #16A34A; }
            .lp-toast-inner.error   { background: #DC2626; }
            .lp-toast-inner.warn    { background: #D97706; }
            .lp-toast-inner.info    { background: var(--orange-normal-hover, #CF4900); }
        `;
        document.head.appendChild(style);
    }

    const ICONS = {
        success: 'check_circle',
        error:   'cancel',
        warn:    'warning',
        info:    'info',
    };

    function getContainer() {
        let c = document.getElementById('lp-toast-container');
        if (!c) {
            c = document.createElement('div');
            c.id = 'lp-toast-container';
            c.innerHTML = `
                <div class="lp-toast-inner" id="lp-toast-inner">
                    <img src="/assets/images/Logo_Laperpoll.png"
                         alt="LaperPoll" class="lp-toast-logo">
                    <div class="lp-toast-divider"></div>
                    <span class="material-icons-round lp-toast-icon" id="lp-toast-icon">info</span>
                    <p class="lp-toast-msg" id="lp-toast-msg"></p>
                </div>
            `;
            document.body.appendChild(c);
        }
        return c;
    }

    /**
     * Tampilkan toast slide dari atas
     * @param {string} msg
     * @param {'success'|'error'|'warn'|'info'} type
     * @param {number} duration ms (default 3000)
     */
    window.lpToast = function (msg, type = 'info', duration = 3000) {
        const container = getContainer();
        const inner     = document.getElementById('lp-toast-inner');
        const iconEl    = document.getElementById('lp-toast-icon');
        const msgEl     = document.getElementById('lp-toast-msg');

        if (!inner || !iconEl || !msgEl) return;

        // Set konten
        msgEl.textContent  = msg;
        iconEl.textContent = ICONS[type] || ICONS.info;

        // Reset class warna
        inner.className = 'lp-toast-inner ' + type;

        // Slide masuk
        container.classList.add('show');

        // Reset timer
        clearTimeout(window._lpToastTimer);
        window._lpToastTimer = setTimeout(() => {
            container.classList.remove('show');
        }, duration);
    };

    // Alias showToast → lpToast (supaya kode lama yang pakai showToast tetap jalan)
    window.showToast = function (msg, type = 'success', duration = 3000) {
        window.lpToast(msg, type, duration);
    };

})();