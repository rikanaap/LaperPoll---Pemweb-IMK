document.querySelectorAll('#faq-card').forEach(card => {
    card.addEventListener('click', () => {
        const answer = card.querySelector('.faq-answer');
        const icon = card.querySelector('.faq-icon');

        if (answer.classList.contains('hidden')) {
            answer.classList.remove('hidden');
            icon.textContent = 'keyboard_arrow_up';
        } else {
            answer.classList.add('hidden');
            icon.textContent = 'keyboard_arrow_down';
        }
    });
});
