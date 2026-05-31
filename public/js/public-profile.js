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