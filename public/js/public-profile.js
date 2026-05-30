// public-profile.js — LaperPoll

const pubFollowBtn     = document.getElementById('pubFollowBtn');
const pubFollowLabel   = document.getElementById('pubFollowLabel');
const pubFollowerCount = document.getElementById('pubFollowerCount');

if (pubFollowBtn) {
    pubFollowBtn.addEventListener('click', async () => {
        if (!PUB_IS_AUTH) { window.location.href = PUB_SIGN_IN; return; }

        const userId = pubFollowBtn.dataset.userId;
        pubFollowBtn.disabled = true;

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
                const icon = pubFollowBtn.querySelector('.material-icons-round');

                if (data.is_following) {
                    pubFollowBtn.classList.add('following');
                    icon.textContent          = 'person_remove';
                    pubFollowLabel.textContent = 'Mengikuti';
                } else {
                    pubFollowBtn.classList.remove('following');
                    icon.textContent          = 'person_add';
                    pubFollowLabel.textContent = 'Ikuti';
                }

                // Update follower count realtime
                if (pubFollowerCount) {
                    pubFollowerCount.textContent = data.follower_count;
                }

                // Animasi pop
                pubFollowBtn.style.transform = 'scale(1.1)';
                setTimeout(() => pubFollowBtn.style.transform = '', 200);
            }
        } catch (err) {
            console.error('Gagal toggle follow:', err);
        } finally {
            pubFollowBtn.disabled = false;
        }
    });
}