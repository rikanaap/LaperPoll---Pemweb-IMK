@if (session('toast') || isset($message))
@php
$toastMessage = session('toast') ?? $message;
$toastType = session('toast_type') ?? $type ?? 'success';
@endphp
<div id="popup-toast"
    data-message="{{ $toastMessage }}"
    data-type="{{ $toastType }}">
</div>
@endif

{{-- Container toast (selalu ada, diisi JS) --}}
<div id="toast-container"
    style="
        position: fixed;
        top: 0;
        left: 50%;
        transform: translateX(-50%) translateY(-120%);
        z-index: 9999;
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        width: calc(100% - 2rem);
        max-width: 400px;
     ">
    <div id="toast-inner"
        style="
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 16px;
            background-color: var(--orange-normal-hover);
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            margin-top: 1rem;
         ">

        {{-- Logo --}}
        <img src="{{ asset('assets/images/Logo_Laperpoll.png') }}"
            alt="LaperPoll"
            style="width: 5rem; object-fit: contain; flex-shrink: 0; filter: brightness(10);">

        {{-- Divider --}}
        <div style="width: 1px; height: 24px; background: rgba(255,255,255,0.2); flex-shrink:0;"></div>

        {{-- Icon type --}}
        <span id="toast-icon"
            class="material-icons-round"
            style="font-size: 1.2rem; flex-shrink:0; color: white;">
            check_circle
        </span>

        {{-- Message --}}
        <p id="toast-message"
            style="
              font-family: var(--font-jakarta);
              font-size: var(--text-body);
              font-weight: 600;
              color: white;
              flex: 1;
              margin: 0;
           ">
        </p>

    </div>
</div>

<script>
    const _toastIcons = {
        success: {
            icon: 'check_circle',
            color: '#4ade80'
        },
        error: {
            icon: 'cancel',
            color: '#f87171'
        },
        info: {
            icon: 'info',
            color: '#60a5fa'
        },
    };

    /**
     * Tampilkan toast popup
     * @param {string} message  - Pesan yang ditampilkan
     * @param {string} type     - 'success' | 'error' | 'info' (default: 'success')
     * @param {number} duration - Durasi tampil dalam ms (default: 3000)
     */
    function showToast(message, type = 'success', duration = 3000) {
        const container = document.getElementById('toast-container');
        const msgEl = document.getElementById('toast-message');
        const iconEl = document.getElementById('toast-icon');
        const config = _toastIcons[type] ?? _toastIcons.success;

        // Set isi
        msgEl.textContent = message;
        iconEl.textContent = config.icon;
        iconEl.style.color = config.color;

        // Masuk dari atas
        container.style.transform = 'translateX(-50%) translateY(0)';

        // Hilang ke atas setelah `duration` ms
        clearTimeout(window._toastTimer);
        window._toastTimer = setTimeout(() => {
            container.style.transform = 'translateX(-50%) translateY(-120%)';
        }, duration);
    }

    // Auto-trigger dari session flash (Blade)
    document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('popup-toast');
        if (el) {
            showToast(el.dataset.message, el.dataset.type);
        }
    });
</script>