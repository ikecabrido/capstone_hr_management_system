// ============================================
// ANNOUNCEMENTS LOADING + READ TRACKING
// ============================================

document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('announcements-container');
    const userId = document.currentScript?.dataset.userId || null;

    if (!container) return;
    container.innerHTML = '<div style="padding:20px; color:#666;">Loading announcements...</div>';

    fetch('api/announcements_data.php', { cache: 'no-store' })
        .then((res) => {
            if (!res.ok) throw new Error('Failed to fetch announcements');
            return res.json();
        })
        .then((data) => {
            if (!data.success || !Array.isArray(data.announcements)) {
                throw new Error(data.error || 'Invalid data format');
            }

            if (data.announcements.length === 0) {
                container.innerHTML = '<div style="padding:20px; color:#666;">No announcements yet.</div>';
                return;
            }

            const cardsHTML = data.announcements.map((announcement) => {
                const isRead = Boolean(announcement.is_read);
                const readAt = announcement.read_at ? `\n                        <div class="read-info"><strong>You read this:</strong> ${new Date(announcement.read_at).toLocaleString()}</div>` : '';
                return `
                    <div class="announcement-card ${isRead ? 'read' : 'unread'}" data-announcement-id="${announcement.id}">
                        <div class="read-badge ${isRead ? 'read' : 'unread'}">${isRead ? '✓ Read' : 'NEW'}</div>
                        <div class="announcement-header"><div><div class="announcement-title">${announcement.title || 'Untitled'}</div><div class="announcement-date">${new Date(announcement.created_at).toLocaleDateString()}</div></div></div>
                        <div class="announcement-content">${announcement.content ? announcement.content.replace(/\n/g, '<br>') : ''}</div>
                        <div class="announcement-meta"><strong>Posted by:</strong> ${announcement.created_by || 'Admin'}</div>
                        ${readAt}
                    </div>
                `;
            }).join('');

            container.innerHTML = cardsHTML;
            attachReadObserver(userId);
        })
        .catch((err) => {
            container.innerHTML = `<div style="padding:20px; color:#a00;">Error loading announcements: ${err.message}</div>`;
            console.error(err);
        });

    function attachReadObserver(userId) {
        const apiUrl = 'api/announcement-reads.php';

        document.querySelectorAll('.announcement-card').forEach((card) => {
            const announcementId = card.getAttribute('data-announcement-id');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting && !card.classList.contains('read')) {
                        if (userId) {
                            markAnnouncementAsRead(announcementId, userId);
                        }
                        card.classList.add('read');
                        card.classList.remove('unread');
                        const badge = card.querySelector('.read-badge');
                        if (badge) {
                            badge.classList.add('read');
                            badge.classList.remove('unread');
                            badge.textContent = '✓ Read';
                        }
                        observer.unobserve(card);
                    }
                });
            }, { threshold: 0.5 });

            observer.observe(card);
        });

        function markAnnouncementAsRead(announcementId, userId) {
            fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ announcement_id: announcementId, employee_id: userId })
            }).catch((error) => {
                console.error('Error marking announcement as read:', error);
            });
        }
    }
});
