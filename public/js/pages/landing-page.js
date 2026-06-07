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
// ── Dengan ID (LEBIH CLEAN) ──
gsap.registerPlugin(ScrollTrigger);

// Features Section
gsap.from('#features-section', {
    opacity: 0,
    y: 30,
    duration: 0.6,
    scrollTrigger: { trigger: '#features-section', start: "top 80%", toggleActions: "play none none reverse" }
});

// Resep Favorit Section
gsap.timeline({
    scrollTrigger: { trigger: '#favorit-section', start: "top 80%", toggleActions: "play none none reverse" }
})
.from('#favorit-section h1', { opacity: 0, y: 20, duration: 0.5 })
.from('#favorit-section > div:last-child > div', { opacity: 0, x: -50, duration: 0.6 }, "-=0.2")
.from('#favorit-section x-card-resep-landing', { opacity: 0, y: 30, duration: 0.5, stagger: 0.15 }, "-=0.3")
.from('#features-section .absolute:nth-child(1)', { opacity: 0, x: -20, duration: 0.6 }, "-=0.2");

// Resep Hari Ini Section
gsap.timeline({
    scrollTrigger: { trigger: '#hari-ini-section', start: "top 80%", toggleActions: "play none none reverse" }
})
.from('#hari-ini-section h1', { opacity: 0, y: 20, duration: 0.5 })
.from('#hari-ini-section > div:last-child > div', { opacity: 0, x: -50, duration: 0.6 }, "-=0.2")
.from('#hari-ini-section x-card-resep-landing', { opacity: 0, y: 30, duration: 0.5, stagger: 0.15 }, "-=0.3");

// Rekomendasi Section
gsap.timeline({
    scrollTrigger: { trigger: '#rekomendasi-section', start: "top 80%", toggleActions: "play none none reverse" }
})
.from('#rekomendasi-section h1', { opacity: 0, y: 20, duration: 0.5 })
.from('#rekomendasi-section p', { opacity: 0, y: 15, duration: 0.4 }, "-=0.2")
.from('#rekomendasi-section x-card-bahan-landing', { opacity: 0, scale: 0.9, duration: 0.5, stagger: 0.1 }, "-=0.3")
.from('#rekomendasi-section a', { opacity: 0, y: 10, duration: 0.4 }, "-=0.2")
.from('#rekomendasi-section .absolute', { opacity: 0, duration: 0.5, stagger: 0.15 }, "-=0.1");

// Pendapat Section
gsap.timeline({
    scrollTrigger: { trigger: '#pendapat-section', start: "top 80%", toggleActions: "play none none reverse" }
})
.from('#pendapat-section h1', { opacity: 0, y: 20, duration: 0.5 })
.from('#pendapat-section [class*="w-\\[15rem\\]"]', { opacity: 0, y: 40, duration: 0.6, stagger: 0.2 }, "-=0.2")
.from('#pendapat-section .absolute', { opacity: 0, duration: 0.5, stagger: 0.15 }, "-=0.3");

// FAQ Section
gsap.timeline({
    scrollTrigger: { trigger: '#faq-section', start: "top 80%", toggleActions: "play none none reverse" }
})
.from('#faq-section h1', { opacity: 0, y: 20, duration: 0.5 })
.from('#faq-section > .flex.flex-col p', { opacity: 0, y: 15, duration: 0.4 }, "-=0.2")
.from('#faq-section #faq-card', { opacity: 0, x: -30, duration: 0.5, stagger: 0.15 }, "-=0.3")
.from('#faq-section .absolute', { opacity: 0, duration: 0.5, stagger: 0.15 }, "-=0.2");

// Footer
gsap.from('#footer-section', { opacity: 0, y: 30, duration: 0.6, scrollTrigger: { trigger: '#footer-section', start: "top 100%", toggleActions: "play none none reverse" } });
const observerOptions = {
    threshold: 0.1,  // Trigger saat 10% elemen terlihat
    rootMargin: '0px 0px -50px 0px'  // Trigger 50px sebelum elemen muncul
};