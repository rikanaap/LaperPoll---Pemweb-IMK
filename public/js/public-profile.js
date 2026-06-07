// public-profile.js — LaperPoll

const pubFollowBtn     = document.getElementById('pubFollowBtn');
const pubFollowLabel   = document.getElementById('pubFollowLabel');
const pubFollowerCount = document.getElementById('pubFollowerCount');

// ── UNFOLLOW CONFIRM MODAL ────────────────────────────────────────────────────
function openPubUnfollowConfirm() {
    document.getElementById('pubUnfollowModal')?.classList.add('open');
    document.getElementById('pubUnfollowOverlay')?.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closePubUnfollowConfirm() {
    document.getElementById('pubUnfollowModal')?.classList.remove('open');
    document.getElementById('pubUnfollowOverlay')?.classList.remove('open');
    document.body.style.overflow = '';
}

function confirmPubUnfollow() {
    closePubUnfollowConfirm();
    doToggleFollow();
}

document.getElementById('pubUnfollowOverlay')?.addEventListener('click', closePubUnfollowConfirm);

// ── FOLLOW TOGGLE ─────────────────────────────────────────────────────────────
if (pubFollowBtn) {
    pubFollowBtn.addEventListener('click', () => {
        const isFollowing = pubFollowBtn.classList.contains('following');
        if (isFollowing) {
            openPubUnfollowConfirm();
        } else {
            doToggleFollow();
        }
    });
}

async function doToggleFollow() {
    if (!pubFollowBtn) return;
    if (!PUB_IS_AUTH) { window.location.href = PUB_SIGN_IN; return; }

    const userId = pubFollowBtn.dataset.userId;

    // Loading state
    const icon  = pubFollowBtn.querySelector('.material-icons-round');
    const label = pubFollowBtn.querySelector('#pubFollowLabel');
    const originalIcon  = icon?.textContent;
    const originalLabel = label?.textContent;

    pubFollowBtn.disabled = true;
    if (icon) icon.textContent = 'autorenew';
    icon?.classList.add('lp-spin');

    try {
        const res  = await fetch(`/follow/${userId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': PUB_CSRF,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        });
        const data = await res.json();

        if (data.success) {
            icon?.classList.remove('lp-spin');

            if (data.is_following) {
                pubFollowBtn.classList.add('following');
                if (icon)  icon.textContent  = 'person_remove';
                if (label) label.textContent = 'Mengikuti';
                lpToast('Berhasil mengikuti!', 'success');
            } else {
                pubFollowBtn.classList.remove('following');
                if (icon)  icon.textContent  = 'person_add';
                if (label) label.textContent = 'Ikuti';
                lpToast('Berhenti mengikuti.', 'info');
            }

            if (pubFollowerCount) {
                pubFollowerCount.textContent = data.follower_count;
            }

            // Animasi pop
            pubFollowBtn.style.transform = 'scale(1.1)';
            setTimeout(() => pubFollowBtn.style.transform = '', 200);
        }
    } catch (err) {
        console.error('Gagal toggle follow:', err);
        icon?.classList.remove('lp-spin');
        if (icon)  icon.textContent  = originalIcon;
        if (label) label.textContent = originalLabel;
        lpToast('Gagal, coba lagi.', 'error');
    } finally {
        pubFollowBtn.disabled = false;
    }
}

// ─── TOAST ────────────────────────────────────────────────────────────────────
function lpToast(msg, type = 'info') {
    const existing = document.querySelector('.pub-toast');
    if (existing) existing.remove();
    const colors = { info: '#172D23', warn: '#B45309', error: '#B91C1C', success: '#027A48' };
    const toast  = document.createElement('div');
    toast.className = 'pub-toast';
    toast.style.cssText = `
        position:fixed;bottom:5rem;left:50%;transform:translateX(-50%);
        background:${colors[type]||colors.info};color:white;
        padding:0.6rem 1.25rem;border-radius:2rem;
        font-size:0.8rem;font-family:var(--font-jakarta);
        z-index:999;box-shadow:0 4px 12px rgba(0,0,0,0.2);
        white-space:nowrap;pointer-events:none;`;
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// ─── FOLLOW MODAL (public profile) ────────────────────────────────────────────
function openPubFollowModal(type) {
    const modal    = document.getElementById('pubFollowModal');
    const overlay  = document.getElementById('pubFollowOverlay');
    const title    = document.getElementById('pubFollowModalTitle');
    const loading  = document.getElementById('pubFollowLoading');
    const list     = document.getElementById('pubFollowList');
    const empty    = document.getElementById('pubFollowEmpty');
    const emptyTxt = document.getElementById('pubFollowEmptyText');

    if (!modal) return;

    modal.classList.add('open');
    overlay?.classList.add('open');
    document.body.style.overflow = 'hidden';

    // Reset state
    loading.style.display = 'flex';
    list.innerHTML        = '';
    empty.style.display   = 'none';

    const url   = type === 'followers' ? PUB_FOLLOWERS_URL : PUB_FOLLOWING_URL;
    title.textContent     = type === 'followers' ? 'Pengikut' : 'Mengikuti';
    emptyTxt.textContent  = type === 'followers' ? 'Belum ada pengikut' : 'Belum mengikuti siapapun';

    fetch(url, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            loading.style.display = 'none';
            if (!data.users || data.users.length === 0) {
                empty.style.display = 'flex';
                return;
            }
            list.innerHTML = data.users.map(u => `
                <a href="${PUB_PROFILE_BASE_URL}/${u.id}"
                   class="follow-user-item"
                   title="Lihat profil ${escHtml(u.name)}">
                    <img src="${u.profile_photo || PUB_DUMMY_AVATAR}"
                         alt="${escHtml(u.name)}"
                         class="follow-user-avatar"
                         onerror="this.src='${PUB_DUMMY_AVATAR}'">
                    <div class="follow-user-info">
                        <span class="follow-user-name">${escHtml(u.name)}</span>
                    </div>
                    ${u.is_following
                        ? '<span class="follow-user-badge">Mengikuti</span>'
                        : ''}
                    <span class="material-icons-round follow-user-arrow">chevron_right</span>
                </a>
            `).join('');
        })
        .catch(() => {
            loading.style.display = 'none';
            empty.style.display   = 'flex';
        });
}

function closePubFollowModal() {
    document.getElementById('pubFollowModal')?.classList.remove('open');
    document.getElementById('pubFollowOverlay')?.classList.remove('open');
    document.body.style.overflow = '';
}

window.openPubFollowModal  = openPubFollowModal;
window.closePubFollowModal = closePubFollowModal;
window.lpToast             = lpToast;
function escHtml(str) {
    return String(str || '')
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}