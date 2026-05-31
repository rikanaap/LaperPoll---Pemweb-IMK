// toast.js — LaperPoll global toast utility

(function () {
    'use strict';

    // Inject CSS sekali saja
    if (!document.getElementById('lp-toast-style')) {
        const style = document.createElement('style');
        style.id = 'lp-toast-style';
        style.textContent = `
            #lp-toast-container {
                position: fixed;
                bottom: 5rem;
                left: 50%;
                transform: translateX(-50%);
                z-index: 9999;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.5rem;
                pointer-events: none;
            }
            .lp-toast {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.65rem 1.25rem;
                border-radius: 2rem;
                font-size: 0.813rem;
                font-family: var(--font-jakarta, sans-serif);
                font-weight: 500;
                color: white;
                white-space: nowrap;
                box-shadow: 0 4px 16px rgba(0,0,0,0.2);
                animation: lpToastIn 0.25s ease;
                pointer-events: none;
                max-width: 90vw;
                white-space: normal;
                text-align: center;
            }
            .lp-toast .lp-toast-icon {
                font-size: 1rem;
                flex-shrink: 0;
            }
            .lp-toast.success { background: #16A34A; }
            .lp-toast.error   { background: #DC2626; }
            .lp-toast.warn    { background: #D97706; }
            .lp-toast.info    { background: #172D23; }
            .lp-toast.fade-out {
                animation: lpToastOut 0.3s ease forwards;
            }
            @keyframes lpToastIn {
                from { opacity: 0; transform: translateY(0.75rem) scale(0.95); }
                to   { opacity: 1; transform: translateY(0) scale(1); }
            }
            @keyframes lpToastOut {
                from { opacity: 1; transform: translateY(0) scale(1); }
                to   { opacity: 0; transform: translateY(0.5rem) scale(0.95); }
            }
        `;
        document.head.appendChild(style);
    }

    // Container
    function getContainer() {
        let c = document.getElementById('lp-toast-container');
        if (!c) {
            c = document.createElement('div');
            c.id = 'lp-toast-container';
            document.body.appendChild(c);
        }
        return c;
    }

    const ICONS = {
        success: 'check_circle',
        error:   'error',
        warn:    'warning',
        info:    'info',
    };

    /**
     * Tampilkan toast
     * @param {string} msg   - Pesan
     * @param {'success'|'error'|'warn'|'info'} type
     * @param {number} duration - ms (default 3000)
     */
    window.lpToast = function (msg, type = 'info', duration = 3000) {
        const container = getContainer();
        const toast     = document.createElement('div');
        toast.className = `lp-toast ${type}`;
        toast.innerHTML = `
            <span class="material-icons-round lp-toast-icon">${ICONS[type] || ICONS.info}</span>
            <span>${msg}</span>
        `;
        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('fade-out');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    };

})();