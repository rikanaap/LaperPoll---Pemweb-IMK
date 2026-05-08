/**
 * LaperPoll - Swipe Resep Engine (Pro Version)
 * Author: Gemini x Ikbal Miftahudin
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
        drawer: document.getElementById("historyDrawer"),
        drawerTrigger: document.getElementById("drawerTrigger"),
        likedList: document.getElementById("likedHistoryList"),
        dislikedList: document.getElementById("dislikedHistoryList"),
    },

    // ======================================
    // STATE
    // ======================================
    state: {
        likeCount: 0,
        maxLike: 3,
        isAnimating: false,
        threshold: 120, // Jarak minimal swipe
        likedHistory: [],
        dislikedHistory: [],
        redirectUrl: "/filter-resep-swipe" // Ganti sesuai route filter lo
    },

    // ======================================
    // INITIALIZATION
    // ======================================
    init() {
        this.updateUI();
        this.bindEvents();
        this.enableDragAll();
        this.initDrawer();
        console.log("Swipe Engine Ready, Mas Bro!");
    },

    bindEvents() {
        // Klik Tombol
        this.ui.likeBtn?.addEventListener("click", () => this.swipe("right"));
        this.ui.dislikeBtn?.addEventListener("click", () => this.swipe("left"));
    },

    initDrawer() {
        if (!this.ui.drawerTrigger || !this.ui.drawer) return;

        this.ui.drawerTrigger.addEventListener("click", () => {
            this.ui.drawer.classList.toggle("is-open");
            const arrow = this.ui.drawerTrigger.querySelector(".arrow-icon");
            if (arrow) {
                arrow.innerText = this.ui.drawer.classList.contains("is-open") 
                    ? "expand_more" : "expand_less";
            }
        });
    },

    // ======================================
    // UI UPDATER
    // ======================================
    updateUI() {
        const percentage = (this.state.likeCount / this.state.maxLike) * 100;

        if (this.ui.counterText) {
            this.ui.counterText.textContent = `${this.state.likeCount} / ${this.state.maxLike}`;
        }

        [this.ui.progressBar, this.ui.mobileBar].forEach(bar => {
            if (bar) bar.style.width = `${percentage}%`;
        });
    },

    renderHistory() {
        // Render List Like (Bisa diklik untuk redirect)
        if (this.ui.likedList) {
            if (this.state.likedHistory.length === 0) {
                this.ui.likedList.innerHTML = `<p class="empty-history">Belum ada rasa disukai</p>`;
            } else {
                this.ui.likedList.innerHTML = this.state.likedHistory.map(item => `
                    <div class="history-chip liked clickable-history" onclick="window.location.href='${this.state.redirectUrl}'">
                        <span class="material-icons-round" style="font-size: 16px">${item.icon}</span>
                        <span>${item.title}</span>
                    </div>
                `).join('');
            }
        }

        // Render List Skip (Hanya tampilan)
        if (this.ui.dislikedList) {
            if (this.state.dislikedHistory.length === 0) {
                this.ui.dislikedList.innerHTML = `<p class="empty-history">Belum ada rasa dilewati</p>`;
            } else {
                this.ui.dislikedList.innerHTML = this.state.dislikedHistory.map(item => `
                    <div class="history-chip disliked">
                        <span class="material-icons-round" style="font-size: 16px">${item.icon}</span>
                        <span>${item.title}</span>
                    </div>
                `).join('');
            }
        }
    },

    // ======================================
    // SWIPE CORE LOGIC
    // ======================================
    getTopCard() {
        const cards = this.ui.cardsContainer.querySelectorAll(".swipe-card");
        return cards.length > 0 ? cards[cards.length - 1] : null;
    },

    swipe(direction) {
        if (this.state.isAnimating) return;

        const card = this.getTopCard();
        if (!card) return;

        this.state.isAnimating = true;

        // Data rasa dari elemen kartu
        const cardData = {
            title: card.querySelector('.swipe-title')?.innerText || 'Rasa',
            icon: card.querySelector('.material-icons-round')?.innerText || 'restaurant'
        };

        const moveX = direction === "right" ? 500 : -500;
        const rotate = direction === "right" ? 30 : -30;

        // Munculkan label LIKE/SKIP
        this.addLabel(card, direction === "right" ? "like" : "nope");

        // Animasi keluar
        card.style.transition = "transform 0.5s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.4s";
        card.style.transform = `translateX(${moveX}px) rotate(${rotate}deg)`;
        card.style.opacity = "0";

        // Logic Simpan
        if (direction === "right") {
            this.state.likeCount++;
            this.state.likedHistory.push(cardData);
            this.updateUI();
        } else {
            // RASA YANG DI-SKIP: Tidak akan pernah muncul lagi karena DOM element dihapus
            this.state.dislikedHistory.push(cardData);
        }

        this.renderHistory();

        // Bersihkan DOM setelah animasi selesai
        card.addEventListener("transitionend", () => {
            card.remove();
            this.state.isAnimating = false;
            this.checkEmpty();
            this.checkLimit();
        }, { once: true });
    },

    addLabel(card, type) {
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
            // Kunci tombol
            if (this.ui.likeBtn) this.ui.likeBtn.disabled = true;
            if (this.ui.dislikeBtn) this.ui.dislikeBtn.disabled = true;

            // Feedback: Buka drawer sebentar lalu redirect
            setTimeout(() => {
                this.ui.drawer?.classList.add("is-open");
                setTimeout(() => {
                    window.location.href = this.state.redirectUrl;
                }, 1000);
            }, 400);
        }
    },

    // ======================================
    // GESTURE (DRAG) HANDLING
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
            const rotate = diff / 18;

            card.style.transform = `translateX(${diff}px) rotate(${rotate}deg)`;
            
            // Visual feedback saat drag
            if (diff > 60) {
                card.classList.add("preview-like");
                card.classList.remove("preview-nope");
            } else if (diff < -60) {
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
                // Kembalikan ke posisi awal jika tidak cukup jauh
                card.style.transition = "transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)";
                card.style.transform = "";
            }
        };

        // Mouse Events
        card.addEventListener("mousedown", onStart);
        window.addEventListener("mousemove", onMove);
        window.addEventListener("mouseup", onEnd);

        // Touch Events
        card.addEventListener("touchstart", onStart, { passive: true });
        card.addEventListener("touchmove", onMove, { passive: true });
        card.addEventListener("touchend", onEnd);
    }
};

// RUN ENGINE
document.addEventListener("DOMContentLoaded", () => {
    swipeApp.init();
});