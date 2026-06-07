document.addEventListener('DOMContentLoaded', () => {

    // ── Modal ─────────────────────────────────────────────────
    const overlay    = document.getElementById('modalOverlay');
    const modalClose = document.getElementById('modalClose');

    modalClose?.addEventListener('click', closeModal);
    overlay?.addEventListener('click', (e) => { if (e.target === overlay) closeModal(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });

    // ── Sidebar hamburger (mobile) ────────────────────────────
    const sidebar        = document.getElementById('sidebar');
    const sidebarToggle  = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    function openSidebar() {
        sidebar?.classList.add('is-open');
        sidebarOverlay?.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar?.classList.remove('is-open');
        sidebarOverlay?.classList.remove('is-active');
        document.body.style.overflow = '';
    }

    sidebarToggle?.addEventListener('click', openSidebar);
    sidebarOverlay?.addEventListener('click', closeSidebar);

    // Tutup sidebar saat klik link di mobile
    sidebar?.querySelectorAll('.sidebar__link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) closeSidebar();
        });
    });

    // Tutup saat resize ke desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) closeSidebar();
    });

});

// ── Modal helpers — global ────────────────────────────────────
function openModal(title, bodyHtml, footerHtml = '') {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalBody').innerHTML    = bodyHtml;
    document.getElementById('modalFooter').innerHTML  = footerHtml;
    document.getElementById('modalOverlay').classList.add('is-open');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('modalOverlay').classList.remove('is-open');
    document.body.style.overflow = '';
}


// ── Custom Select ─────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.custom-select').forEach(sel => {
        const trigger  = sel.querySelector('.custom-select__trigger');
        const dropdown = sel.querySelector('.custom-select__dropdown');
        const options  = sel.querySelectorAll('.custom-select__option');
        const input    = sel.querySelector('input[type="hidden"]');
        const label    = trigger.querySelector('.custom-select__label');

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            document.querySelectorAll('.custom-select.is-open').forEach(s => {
                if (s !== sel) s.classList.remove('is-open');
            });
            sel.classList.toggle('is-open');
        });

        options.forEach(opt => {
            opt.addEventListener('click', () => {
                options.forEach(o => o.classList.remove('is-selected'));
                opt.classList.add('is-selected');
                label.textContent = opt.textContent.trim();
                input.value = opt.dataset.value;
                sel.classList.remove('is-open');
                // auto submit form
                input.closest('form')?.submit();
            });
        });

        document.addEventListener('click', () => sel.classList.remove('is-open'));
    });
});