document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.getElementById('modalOverlay');
    const modalClose = document.getElementById('modalClose');

    if (modalClose) modalClose.addEventListener('click', closeModal);
    if (overlay) overlay.addEventListener('click', (e) => { if (e.target === overlay) closeModal(); });

    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });
});

function openModal(title, bodyHtml, footerHtml = '') {
    document.getElementById('modalTitle').textContent  = title;
    document.getElementById('modalBody').innerHTML     = bodyHtml;
    document.getElementById('modalFooter').innerHTML   = footerHtml;
    document.getElementById('modalOverlay').classList.add('is-open');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('modalOverlay').classList.remove('is-open');
    document.body.style.overflow = '';
}