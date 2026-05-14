/**
 * LaperPoll - Swipe Resep Engine
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

        // MOBILE DRAWER
        drawer: document.getElementById("historyDrawer"),
        drawerTrigger: document.getElementById("drawerTrigger"),

        likedList: document.getElementById("likedHistoryList"),
        dislikedList: document.getElementById("dislikedHistoryList"),

        // DESKTOP HISTORY PANEL
        desktopLikedList: document.getElementById("desktopLikedHistory"),
        desktopDislikedList: document.getElementById("desktopDislikedHistory"),
    },

    // ======================================
    // STATE
    // ======================================
    state: {
        likeCount: 0,
        maxLike: 3,

        isAnimating: false,

        threshold: 120,

        likedHistory: [],
        dislikedHistory: [],

        redirectUrl: "/filter-resep-swipe"
    },

    // ======================================
    // INIT
    // ======================================
    init() {
        this.updateUI();
        this.bindEvents();
        this.enableDragAll();
        this.initDrawer();

        console.log("Swipe Engine Ready, Mas Bro!");
    },

    // ======================================
    // EVENTS
    // ======================================
    bindEvents() {

        this.ui.likeBtn?.addEventListener("click", () => {
            this.swipe("right");
        });

        this.ui.dislikeBtn?.addEventListener("click", () => {
            this.swipe("left");
        });

    },

    // ======================================
    // DRAWER
    // ======================================
    initDrawer() {

        if (!this.ui.drawerTrigger || !this.ui.drawer) return;

        this.ui.drawerTrigger.addEventListener("click", () => {

            this.ui.drawer.classList.toggle("is-open");

            const arrow =
                this.ui.drawerTrigger.querySelector(".arrow-icon");

            if (arrow) {

                arrow.innerText =
                    this.ui.drawer.classList.contains("is-open")
                        ? "expand_more"
                        : "expand_less";
            }

        });

    },

    // ======================================
    // UI UPDATE
    // ======================================
    updateUI() {

        const percentage =
            (this.state.likeCount / this.state.maxLike) * 100;

        if (this.ui.counterText) {

            this.ui.counterText.textContent =
                `${this.state.likeCount} / ${this.state.maxLike}`;

        }

        [
            this.ui.progressBar,
            this.ui.mobileBar
        ].forEach(bar => {

            if (bar) {
                bar.style.width = `${percentage}%`;
            }

        });

    },

    // ======================================
    // HISTORY RENDER
    // ======================================
    renderHistory() {

        const renderItems = (items, type) => {

            if (items.length === 0) {

                return `
                    <p class="empty-history">
                        Belum ada rasa
                        ${type === 'liked'
                            ? 'disukai'
                            : 'dilewati'}
                    </p>
                `;

            }

            return items.map(item => `

                <div
                    class="history-chip
                           ${type}
                           ${type === 'liked'
                                ? 'clickable-history'
                                : ''}"

                    ${type === 'liked'
                        ? `onclick="window.location.href='${this.state.redirectUrl}'"`
                        : ''}>

                    <span class="material-icons-round history-icon">
                        ${item.icon}
                    </span>

                    <span>
                        ${item.title}
                    </span>

                </div>

            `).join('');

        };

        const likedHTML =
            renderItems(this.state.likedHistory, 'liked');

        const dislikedHTML =
            renderItems(this.state.dislikedHistory, 'disliked');

        // MOBILE
        if (this.ui.likedList) {
            this.ui.likedList.innerHTML = likedHTML;
        }

        if (this.ui.dislikedList) {
            this.ui.dislikedList.innerHTML = dislikedHTML;
        }

        // DESKTOP
        if (this.ui.desktopLikedList) {
            this.ui.desktopLikedList.innerHTML = likedHTML;
        }

        if (this.ui.desktopDislikedList) {
            this.ui.desktopDislikedList.innerHTML = dislikedHTML;
        }

    },

    // ======================================
    // GET TOP CARD
    // ======================================
    getTopCard() {

        const cards =
            this.ui.cardsContainer.querySelectorAll(".swipe-card");

        return cards.length > 0
            ? cards[cards.length - 1]
            : null;

    },

    // ======================================
    // SWIPE
    // ======================================
    swipe(direction) {

        if (this.state.isAnimating) return;

        const card = this.getTopCard();

        if (!card) return;

        this.state.isAnimating = true;

        // ======================================
        // CARD DATA
        // ======================================

        const cardData = {

            title:
                card.querySelector(".swipe-title")?.innerText
                || "Rasa",

            icon:
                card.querySelector(".material-icons-round")?.innerText
                || "restaurant"

        };

        const moveX =
            direction === "right"
                ? 500
                : -500;

        const rotate =
            direction === "right"
                ? 30
                : -30;

        // ======================================
        // LABEL
        // ======================================

        this.addLabel(
            card,
            direction === "right"
                ? "like"
                : "nope"
        );

        // ======================================
        // ANIMATION
        // ======================================

        card.style.transition =
            "transform 0.5s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.4s";

        card.style.transform =
            `translateX(${moveX}px) rotate(${rotate}deg)`;

        card.style.opacity = "0";

        // ======================================
        // SAVE HISTORY
        // ======================================

        if (direction === "right") {

            this.state.likeCount++;

            this.state.likedHistory.push(cardData);

            this.updateUI();

        } else {

            this.state.dislikedHistory.push(cardData);

        }

        this.renderHistory();

        // ======================================
        // REMOVE CARD
        // ======================================

        card.addEventListener("transitionend", () => {

            card.remove();

            this.state.isAnimating = false;

            this.checkEmpty();
            this.checkLimit();

        }, { once: true });

    },

    // ======================================
    // LABEL
    // ======================================
    addLabel(card, type) {

        const existingLabel =
            card.querySelector(".swipe-label");

        if (existingLabel) {
            existingLabel.remove();
        }

        const label = document.createElement("div");

        label.className =
            `swipe-label ${type}`;

        label.innerText =
            type === "like"
                ? "LIKE"
                : "SKIP";

        card.appendChild(label);

    },

    // ======================================
    // EMPTY STATE
    // ======================================
    checkEmpty() {

        const remaining =
            this.ui.cardsContainer.querySelectorAll(".swipe-card");

        if (
            remaining.length === 0 &&
            this.ui.emptyState
        ) {

            this.ui.emptyState.style.display = "block";

        }

    },

    // ======================================
    // LIMIT
    // ======================================
    checkLimit() {

        if (
            this.state.likeCount >=
            this.state.maxLike
        ) {

            // Disable Button
            if (this.ui.likeBtn) {
                this.ui.likeBtn.disabled = true;
            }

            if (this.ui.dislikeBtn) {
                this.ui.dislikeBtn.disabled = true;
            }

            // Auto Redirect
            setTimeout(() => {

                this.ui.drawer?.classList.add("is-open");

                setTimeout(() => {

                    window.location.href =
                        this.state.redirectUrl;

                }, 1000);

            }, 400);

        }

    },

    // ======================================
    // DRAG
    // ======================================
    enableDragAll() {

        const cards =
            this.ui.cardsContainer.querySelectorAll(".swipe-card");

        cards.forEach(card => {

            this.setupDrag(card);

        });

    },

    // ======================================
    // SETUP DRAG
    // ======================================
    setupDrag(card) {

        let startX = 0;
        let currentX = 0;

        let isDragging = false;

        // ======================================
        // START
        // ======================================

        const onStart = (e) => {

            if (this.state.isAnimating) return;

            isDragging = true;

            startX =
                e.touches
                    ? e.touches[0].clientX
                    : e.clientX;

            card.style.transition = "none";

        };

        // ======================================
        // MOVE
        // ======================================

        const onMove = (e) => {

            if (!isDragging) return;

            currentX =
                e.touches
                    ? e.touches[0].clientX
                    : e.clientX;

            const diff =
                currentX - startX;

            const rotate =
                diff / 18;

            card.style.transform =
                `translateX(${diff}px) rotate(${rotate}deg)`;

            // PREVIEW
            if (diff > 60) {

                card.classList.add("preview-like");
                card.classList.remove("preview-nope");

            } else if (diff < -60) {

                card.classList.add("preview-nope");
                card.classList.remove("preview-like");

            } else {

                card.classList.remove(
                    "preview-like",
                    "preview-nope"
                );

            }

        };

        // ======================================
        // END
        // ======================================

        const onEnd = () => {

            if (!isDragging) return;

            isDragging = false;

            const diff =
                currentX - startX;

            card.classList.remove(
                "preview-like",
                "preview-nope"
            );

            // RIGHT
            if (diff > this.state.threshold) {

                this.swipe("right");

            }

            // LEFT
            else if (diff < -this.state.threshold) {

                this.swipe("left");

            }

            // RESET
            else {

                card.style.transition =
                    "transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)";

                card.style.transform = "";

            }

        };

        // ======================================
        // MOUSE
        // ======================================

        card.addEventListener(
            "mousedown",
            onStart
        );

        window.addEventListener(
            "mousemove",
            onMove
        );

        window.addEventListener(
            "mouseup",
            onEnd
        );

        // ======================================
        // TOUCH
        // ======================================

        card.addEventListener(
            "touchstart",
            onStart,
            { passive: true }
        );

        card.addEventListener(
            "touchmove",
            onMove,
            { passive: true }
        );

        card.addEventListener(
            "touchend",
            onEnd
        );

    }

};

// ======================================
// RUN
// ======================================

document.addEventListener("DOMContentLoaded", () => {

    swipeApp.init();

});