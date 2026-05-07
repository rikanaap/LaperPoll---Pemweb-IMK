/**
 * LaperPoll - Swipe Resep Engine
 * Optimized for Performance & Memory Management
 */

const swipeApp = {
    // ======================================
    // ELEMENTS
    // ======================================
    ui: {
        cardsContainer: document.getElementById("swipeCards"),
        likeBtn: document.getElementById("like"),
        dislikeBtn: document.getElementById("dislike"),
        counterText: document.getElementById("counterText"),
        progressBar: document.getElementById("progressBar"),
        mobileBar: document.getElementById("mobileProgressBar"),
        emptyState: document.getElementById("emptyState"),
    },

    // ======================================
    // STATE
    // ======================================
    state: {
        likeCount: 0,
        maxLike: 3,
        isAnimating: false,
        threshold: 120, // Jarak minimal untuk trigger swipe
    },

    // ======================================
    // CORE LOGIC
    // ======================================
    init() {
        this.updateUI();
        this.bindEvents();
        this.enableDragAll();
    },

    bindEvents() {
        this.ui.likeBtn?.addEventListener("click", () => this.swipe("right"));
        this.ui.dislikeBtn?.addEventListener("click", () => this.swipe("left"));
    },

    updateUI() {
        const percentage = (this.state.likeCount / this.state.maxLike) * 100;

        if (this.ui.counterText) {
            this.ui.counterText.textContent = `${this.state.likeCount} / ${this.state.maxLike}`;
        }

        [this.ui.progressBar, this.ui.mobileBar].forEach(bar => {
            if (bar) bar.style.width = `${percentage}%`;
        });
    },

    getTopCard() {
        const cards = this.ui.cardsContainer.querySelectorAll(".swipe-card");
        return cards.length > 0 ? cards[cards.length - 1] : null;
    },

    swipe(direction) {
        if (this.state.isAnimating) return;

        const card = this.getTopCard();
        if (!card) return;

        this.state.isAnimating = true;
        const moveX = direction === "right" ? 450 : -450;
        const rotate = direction === "right" ? 25 : -25;

        // Visual Label (LIKE/SKIP)
        this.addLabel(card, direction === "right" ? "like" : "nope");

        // Animate Out
        card.style.transition = "transform 0.45s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s";
        card.style.transform = `translateX(${moveX}px) rotate(${rotate}deg)`;
        card.style.opacity = "0";

        if (direction === "right") {
            this.state.likeCount++;
            this.updateUI();
        }

        // Cleanup after animation
        card.addEventListener("transitionend", () => {
            card.remove();
            this.state.isAnimating = false;
            this.checkEmpty();
            this.checkLimit();
        }, { once: true });
    },

    addLabel(card, type) {
        // Hapus label preview jika ada
        const existingLabel = card.querySelector(".swipe-label");
        if (existingLabel) existingLabel.remove();

        const label = document.createElement("div");
        label.className = `swipe-label ${type}`;
        label.innerText = type === "like" ? "LIKE" : "SKIP";
        card.appendChild(label);
    },

    checkEmpty() {
        const remaining = this.ui.cardsContainer.querySelectorAll(".swipe-card");
        if (remaining.length === 0 && this.ui.emptyState) {
            this.ui.emptyState.style.display = "block";
        }
    },

    checkLimit() {
        if (this.state.likeCount >= this.state.maxLike) {
            // Disable tombol agar tidak diklik lagi
            if (this.ui.likeBtn) this.ui.likeBtn.disabled = true;
            if (this.ui.dislikeBtn) this.ui.dislikeBtn.disabled = true;

            setTimeout(() => {
                window.location.href = "/filter-resep-swipe";
            }, 600);
        }
    },

    // ======================================
    // DRAG FEATURE
    // ======================================
    enableDragAll() {
        const cards = this.ui.cardsContainer.querySelectorAll(".swipe-card");
        cards.forEach(card => this.setupDrag(card));
    },

    setupDrag(card) {
        let startX = 0;
        let currentX = 0;
        let isDragging = false;

        const onStart = (e) => {
            if (this.state.isAnimating) return;
            isDragging = true;
            startX = e.touches ? e.touches[0].clientX : e.clientX;
            card.style.transition = "none";
        };

        const onMove = (e) => {
            if (!isDragging) return;

            currentX = e.touches ? e.touches[0].clientX : e.clientX;
            const diff = currentX - startX;
            const rotate = diff / 15;

            card.style.transform = `translateX(${diff}px) rotate(${rotate}deg)`;
            
            // Preview Label Logic
            if (diff > 50) {
                card.classList.add("preview-like");
                card.classList.remove("preview-nope");
            } else if (diff < -50) {
                card.classList.add("preview-nope");
                card.classList.remove("preview-like");
            } else {
                card.classList.remove("preview-like", "preview-nope");
            }
        };

        const onEnd = () => {
            if (!isDragging) return;
            isDragging = false;
            const diff = currentX - startX;

            card.classList.remove("preview-like", "preview-nope");

            if (diff > this.state.threshold) {
                this.swipe("right");
            } else if (diff < -this.state.threshold) {
                this.swipe("left");
            } else {
                // Reset posisi kalau swipe tidak cukup jauh
                card.style.transition = "transform 0.3s cubic-bezier(0.16, 1, 0.3, 1)";
                card.style.transform = "";
            }
        };

        // Desktop Events
        card.addEventListener("mousedown", onStart);
        window.addEventListener("mousemove", onMove);
        window.addEventListener("mouseup", onEnd);

        // Mobile Events
        card.addEventListener("touchstart", onStart, { passive: true });
        card.addEventListener("touchmove", onMove, { passive: true });
        card.addEventListener("touchend", onEnd);
    }
};

// Start the App
document.addEventListener("DOMContentLoaded", () => {
    swipeApp.init();
});