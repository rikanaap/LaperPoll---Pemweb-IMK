document.addEventListener('DOMContentLoaded', async () => {
  const swipeCards = document.getElementById('swipeCards');
  const likedHistory = document.getElementById('likedContainer');
  const dislikedHistory = document.getElementById('dislikedContainer');
  const mobileLikedHistory = document.getElementById('mobileLikedContainer');
  const mobileDislikedHistory = document.getElementById('mobileDislikedContainer');
  const counterText = document.getElementById('counterText');
  const progressBar = document.getElementById('progressBar');
  const mobileProgressBar = document.getElementById('mobileProgressBar');
  const emptyState = document.getElementById('emptyState');
  const likeBtn = document.getElementById('likeBtn');
  const dislikeBtn = document.getElementById('dislikeBtn');
  const historyDrawer = document.getElementById('historyDrawer');
  const drawerHeader = document.getElementById('drawerHeader');
  const drawerOverlay = document.getElementById('drawerOverlay');
  const arrowIcon = document.getElementById('drawerArrow');

  const state = {
    cards: [],
    disliked: [],
    currentLiked: [],
    likedGroups: [],
    redirecting: false,
    drawerOpen: false
  };

  init();

  async function init() {
    resetCurrentLiked();
    loadState();
    await fetchRasa();
    renderCards();
    updateHistory();
    updateProgress();
    initHistoryDrawer();
  }

  function resetCurrentLiked() {
    state.currentLiked = [];
  }

  function loadState() {
    const saved = sessionStorage.getItem('swipeRasaState');
    if (!saved) return;
    try {
      const parsed = JSON.parse(saved);
      state.disliked = parsed.disliked || [];
      state.likedGroups = parsed.likedGroups || [];
    } catch (err) {
      console.error(err);
    }
  }

  function saveState() {
    sessionStorage.setItem(
      'swipeRasaState',
      JSON.stringify({
        disliked: state.disliked,
        likedGroups: state.likedGroups
      })
    );
  }

  async function fetchRasa() {
    try {
      const res = await fetch(window.swipeConfig.apiUrl);
      const result = await res.json();
      if (!result.success) return;
      const excludedIds = [
        ...state.disliked.map(item => item.id)
      ];
      state.cards = result.data.filter(
        item => !excludedIds.includes(item.id)
      );
    } catch (err) {
      console.error(err);
    }
  }

  function renderCards() {
    swipeCards.innerHTML = '';
    if (!state.cards.length) {
      emptyState.style.display = 'flex';
      swipeCards.appendChild(emptyState);
      if (state.currentLiked.length > 0 && !state.redirecting) {
        navigateToFilterPage();
      }
      return;
    }
    emptyState.style.display = 'none';
    const visible = state.cards.slice(0, 3);
    visible.forEach((rasa, index) => {
      const card = createCard(rasa, index);
      swipeCards.appendChild(card);
    });
  }

  function createCard(rasa, index) {
    const card = document.createElement('div');
    card.className = 'swipe-card';
    card.style.zIndex = 100 - index;
    card.innerHTML = `
      <div class="swipe-icon-wrapper">
        <span class="material-icons-round">restaurant</span>
      </div>
      <h2 class="swipe-title">${rasa.title ?? '-'}</h2>
      <p class="swipe-desc">${rasa.description ?? '-'}</p>
    `;
    addSwipeEvents(card, rasa);
    return card;
  }

  function addSwipeEvents(card, rasa) {
    let startX = 0;
    let currentX = 0;
    let dragging = false;

    card.addEventListener('pointerdown', (e) => {
      if (card !== swipeCards.querySelector('.swipe-card:first-child')) return;
      startX = e.clientX;
      dragging = true;
      card.style.transition = 'none';
    });

    window.addEventListener('pointermove', (e) => {
      if (!dragging) return;
      currentX = e.clientX - startX;
      const x = Math.max(-180, Math.min(180, currentX));
      card.style.transform = `translateX(${x}px) rotate(${x / 18}deg)`;
    });

    window.addEventListener('pointerup', () => {
      if (!dragging) return;
      dragging = false;
      card.style.transition = '.3s ease';
      if (currentX > 120) {
        swipeRight(card, rasa);
      } else if (currentX < -120) {
        swipeLeft(card, rasa);
      } else {
        card.style.transform = '';
      }
      currentX = 0;
    });
  }

  function swipeRight(card, rasa) {
    card.style.transform = 'translateX(420px) rotate(25deg)';
    card.style.opacity = '0';
    setTimeout(() => {
      likeCard(rasa);
    }, 200);
  }

  function swipeLeft(card, rasa) {
    card.style.transform = 'translateX(-420px) rotate(-25deg)';
    card.style.opacity = '0';
    setTimeout(() => {
      dislikeCard(rasa);
    }, 200);
  }

  likeBtn?.addEventListener('click', () => {
    const rasa = state.cards[0];
    const card = swipeCards.querySelector('.swipe-card:first-child');
    if (rasa && card) swipeRight(card, rasa);
  });

  dislikeBtn?.addEventListener('click', () => {
    const rasa = state.cards[0];
    const card = swipeCards.querySelector('.swipe-card:first-child');
    if (rasa && card) swipeLeft(card, rasa);
  });

  function likeCard(rasa) {
    if (!rasa) return;
    state.currentLiked.push(rasa);
    removeCard(rasa.id);
    updateHistory();
    updateProgress();
    
    if (state.currentLiked.length >= 3 || state.cards.length === 0) {
      const group = {
        id: Date.now(),
        items: [...state.currentLiked]
      };
      state.likedGroups.unshift(group);
      saveState();
      sessionStorage.setItem('selectedRasa', JSON.stringify(state.currentLiked));
      state.redirecting = true;
      setTimeout(() => {
        navigateToFilterPage();
      }, 700);
    }
  }

  function dislikeCard(rasa) {
    if (!rasa) return;
    state.disliked.push(rasa);
    removeCard(rasa.id);
    saveState();
    updateHistory();
  }

  function removeCard(id) {
    state.cards = state.cards.filter(item => item.id !== id);
    renderCards();
  }

  function navigateToFilterPage() {
    sessionStorage.setItem('selectedRasa', JSON.stringify(state.currentLiked));
    const ids = state.currentLiked.map(r => r.id).join(',');
    window.location.href = `${window.swipeConfig.redirectUrl}?filters=${ids}`;
  }

  function updateHistory() {
    const likedHTML = state.likedGroups.length
      ? state.likedGroups.map(group => {
          const names = group.items.map(item => item.title).join(' • ');
          return `
            <button class="history-chip liked liked-group-chip" data-ids="${group.items.map(i => i.id).join(',')}">
              ❤️ ${names}
            </button>
          `;
        }).join('')
      : `<p class="empty-history">Belum ada history rasa</p>`;

    const dislikedHTML = state.disliked.length
      ? state.disliked.map(item => `
          <div class="history-chip disliked">
            <span>❌ ${item.title}</span>
            <button class="remove-disliked" data-id="${item.id}">×</button>
          </div>
        `).join('')
      : `<p class="empty-history">Belum ada rasa dilewati</p>`;

    likedHistory.innerHTML = likedHTML;
    dislikedHistory.innerHTML = dislikedHTML;
    if (mobileLikedHistory) mobileLikedHistory.innerHTML = likedHTML;
    if (mobileDislikedHistory) mobileDislikedHistory.innerHTML = dislikedHTML;
    bindHistoryEvents();
  }

  function bindHistoryEvents() {
    document.querySelectorAll('.liked-group-chip').forEach(chip => {
      chip.addEventListener('click', () => {
        const ids = chip.dataset.ids;
        window.location.href = `${window.swipeConfig.redirectUrl}?filters=${ids}`;
      });
    });

    document.querySelectorAll('.remove-disliked').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = Number(btn.dataset.id);
        restoreDisliked(id);
      });
    });
  }

  function restoreDisliked(id) {
    const item = state.disliked.find(i => i.id === id);
    if (!item) return;
    state.disliked = state.disliked.filter(i => i.id !== id);
    state.cards.unshift(item);
    saveState();
    updateHistory();
    renderCards();
  }

  function updateProgress() {
    const total = state.currentLiked.length;
    counterText.innerText = `${total} / 3`;
    const percent = (total / 3) * 100;
    progressBar.style.width = `${percent}%`;
    if (mobileProgressBar) mobileProgressBar.style.width = `${percent}%`;
  }

  function initHistoryDrawer() {
    if (!historyDrawer || !drawerHeader) return;
    drawerHeader.addEventListener('click', toggleDrawer);
    drawerOverlay?.addEventListener('click', closeDrawer);
  }

  function toggleDrawer() {
    state.drawerOpen = !state.drawerOpen;
    historyDrawer.classList.toggle('is-open', state.drawerOpen);
    drawerOverlay?.classList.toggle('active', state.drawerOpen);
    if (arrowIcon) {
      arrowIcon.style.transform = state.drawerOpen ? 'rotate(180deg)' : 'rotate(0deg)';
    }
    document.body.style.overflow = state.drawerOpen ? 'hidden' : '';
  }

  function closeDrawer() {
    state.drawerOpen = false;
    historyDrawer.classList.remove('is-open');
    drawerOverlay?.classList.remove('active');
    if (arrowIcon) arrowIcon.style.transform = 'rotate(0deg)';
    document.body.style.overflow = '';
  }
});