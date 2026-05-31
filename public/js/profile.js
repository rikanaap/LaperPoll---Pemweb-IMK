// profile.js — LaperPoll

// ─── FAB ─────────────────────────────────────────────────────────────────────
const fabBtn     = document.getElementById('fabBtn');
const fabIcon    = document.getElementById('fabIcon');
const fabMenu    = document.getElementById('fabMenu');
const fabOverlay = document.getElementById('fabOverlay');

let fabOpen = false;

function openFAB() {
    fabOpen = true;
    fabMenu?.classList.add('active');
    fabOverlay?.classList.add('active');
    if (fabIcon) { fabIcon.textContent = 'close'; fabIcon.classList.add('rotated'); }
}

function closeFAB() {
    fabOpen = false;
    fabMenu?.classList.remove('active');
    fabOverlay?.classList.remove('active');
    if (fabIcon) { fabIcon.textContent = 'add'; fabIcon.classList.remove('rotated'); }
}

fabBtn?.addEventListener('click', () => fabOpen ? closeFAB() : openFAB());
fabOverlay?.addEventListener('click', closeFAB);

// ─── HAMBURGER SIDEBAR ───────────────────────────────────────────────────────
const sidebar        = document.getElementById('profileSidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');
const hamburgerBtn   = document.getElementById('profileHamburger');
const sidebarClose   = document.getElementById('sidebarClose');

function openSidebar() {
    sidebar?.classList.add('open');
    sidebarOverlay?.classList.add('open');
    hamburgerBtn?.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeSidebar() {
    sidebar?.classList.remove('open');
    sidebarOverlay?.classList.remove('open');
    hamburgerBtn?.classList.remove('open');
    closeAllPanels();
    document.body.style.overflow = '';
}

hamburgerBtn?.addEventListener('click', (e) => {
    e.stopPropagation();
    sidebar?.classList.contains('open') ? closeSidebar() : openSidebar();
});
sidebarClose?.addEventListener('click', closeSidebar);
sidebarOverlay?.addEventListener('click', closeSidebar);

// ─── SIDEBAR PANELS ───────────────────────────────────────────────────────────
function showSidebarSection(name) {
    const panel = document.getElementById(`panel-${name}`);
    if (!panel) return;
    closeAllPanels();
    panel.classList.add('open');
}

function closeSidebarSection() {
    closeAllPanels();
}

function closeAllPanels() {
    document.querySelectorAll('.sidebar-panel').forEach(p => p.classList.remove('open'));
}

// ─── FAQ ACCORDION ────────────────────────────────────────────────────────────
function toggleFaq(item) {
    const isOpen = item.classList.contains('open');
    document.querySelectorAll('.sidebar-faq-item').forEach(i => i.classList.remove('open'));
    if (!isOpen) item.classList.add('open');
}

// ─── KEYBOARD ESC ────────────────────────────────────────────────────────────
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const anyPanelOpen = document.querySelector('.sidebar-panel.open');
        if (anyPanelOpen) { closeSidebarSection(); return; }
        if (sidebar?.classList.contains('open')) { closeSidebar(); return; }
        if (fabOpen) closeFAB();
    }
});

// ─── FOLLOW MODAL ─────────────────────────────────────────────────────────────
const followModal   = document.getElementById('followModal');
const followOverlay = document.getElementById('followOverlay');
const followTitle   = document.getElementById('followModalTitle');
const followList    = document.getElementById('followList');
const followLoading = document.getElementById('followLoading');
const followEmpty   = document.getElementById('followEmpty');
const followEmptyText = document.getElementById('followEmptyText');

const FOLLOW_URLS = {
    followers: document.querySelector('[data-followers-url]')?.dataset.followersUrl,
    following: document.querySelector('[data-following-url]')?.dataset.followingUrl,
};

const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;
const AUTH_USER_ID = typeof PROFILE_USER_ID !== 'undefined' ? PROFILE_USER_ID : null;

async function openFollowModal(type) {
    // Buka modal
    followModal?.classList.add('open');
    followOverlay?.classList.add('open');
    document.body.style.overflow = 'hidden';

    // Reset state
    if (followLoading) followLoading.style.display = 'flex';
    if (followList)    followList.innerHTML = '';
    if (followEmpty)   followEmpty.style.display = 'none';

    // Fetch data
    try {
        const url = FOLLOW_URLS[type];
        if (!url) return;

        const res  = await fetch(url, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();

        if (followTitle)   followTitle.textContent = data.title;
        if (followLoading) followLoading.style.display = 'none';

        if (!data.users || data.users.length === 0) {
            if (followEmpty) {
                followEmptyText.textContent = type === 'followers'
                    ? 'Belum ada pengikut' : 'Belum mengikuti siapapun';
                followEmpty.style.display = 'flex';
            }
            return;
        }

        if (followList) {
            followList.innerHTML = data.users.map(user => renderUserItem(user)).join('');
            bindFollowButtons();
        }

    } catch (err) {
        console.error('Gagal load follow data:', err);
        if (followLoading) followLoading.style.display = 'none';
    }
}

function closeFollowModal() {
    followModal?.classList.remove('open');
    followOverlay?.classList.remove('open');
    document.body.style.overflow = '';
}

function renderUserItem(user) {
    const avatar = user.profile_photo
        ? user.profile_photo
        : '/assets/images/Image_DummyProfile.png';

    const isSelf = AUTH_USER_ID && user.id === AUTH_USER_ID;
    const btnClass = isSelf ? 'self' : (user.is_following ? 'unfollow' : 'follow');
    const btnText  = isSelf ? '' : (user.is_following ? 'Mengikuti' : 'Ikuti');
    const profileUrl = isSelf ? '/profile' : `/profile/${user.id}`;

    return `
        <div class="follow-user-item">
            <a href="${profileUrl}" style="display:flex;align-items:center;gap:0.75rem;flex:1;text-decoration:none;">
                <img src="${avatar}" alt="${user.name}" class="follow-user-avatar"
                     onerror="this.src='/assets/images/Image_DummyProfile.png'">
                <div class="follow-user-info">
                    <p class="follow-user-name">${user.name}</p>
                </div>
            </a>
            <button class="follow-btn ${btnClass}"
                    data-user-id="${user.id}"
                    data-following="${user.is_following ? '1' : '0'}">
                ${btnText}
            </button>
        </div>`;
}

function bindFollowButtons() {
    document.querySelectorAll('.follow-btn:not(.self)').forEach(btn => {
        btn.addEventListener('click', () => {
            const isFollowing = btn.dataset.following === '1';
            if (isFollowing) {
                // Tampilkan konfirmasi unfollow
                openUnfollowConfirm(btn);
            } else {
                doFollow(btn);
            }
        });
    });
}

async function doFollow(btn) {
    const userId = btn.dataset.userId;

    // Loading state
    const originalHtml = btn.innerHTML;
    btn.disabled  = true;
    btn.innerHTML = '<span class="material-icons-round lp-spin">autorenew</span>';

    try {
        const res  = await fetch(`/follow/${userId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        });
        const data = await res.json();

        if (data.success) {
            btn.classList.toggle('follow',   !data.is_following);
            btn.classList.toggle('unfollow',  data.is_following);
            btn.innerHTML      = data.is_following ? 'Mengikuti' : 'Ikuti';
            btn.dataset.following = data.is_following ? '1' : '0';
            lpToast(data.is_following ? 'Berhasil mengikuti!' : 'Berhenti mengikuti.', data.is_following ? 'success' : 'info');
        }
    } catch (err) {
        console.error('Gagal toggle follow:', err);
        btn.innerHTML = originalHtml;
        lpToast('Gagal, coba lagi.', 'error');
    } finally {
        btn.disabled = false;
    }
}

// ── UNFOLLOW CONFIRM MODAL ────────────────────────────────────────────────────
let pendingUnfollowBtn = null;

function openUnfollowConfirm(btn) {
    pendingUnfollowBtn = btn;
    document.getElementById('unfollowModal')?.classList.add('open');
    document.getElementById('unfollowOverlay')?.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeUnfollowConfirm() {
    pendingUnfollowBtn = null;
    document.getElementById('unfollowModal')?.classList.remove('open');
    document.getElementById('unfollowOverlay')?.classList.remove('open');
    document.body.style.overflow = '';
}

function confirmUnfollow() {
    closeUnfollowConfirm();
    if (pendingUnfollowBtn) doFollow(pendingUnfollowBtn);
}

document.getElementById('unfollowOverlay')?.addEventListener('click', closeUnfollowConfirm);