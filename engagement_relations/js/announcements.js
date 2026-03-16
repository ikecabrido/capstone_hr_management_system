// ============================================
// ANNOUNCEMENT TRACKING & READING
// Marks announcements as read when viewed
// ============================================

window.addEventListener('load', function() {
    const token = document.querySelector('script[data-token]')?.getAttribute('data-token') || '';
    const userId = document.querySelector('script[data-user-id]')?.getAttribute('data-user-id') || null;
    const apiUrl = 'http://localhost/New%20folder/New/EER/api/announcement-reads.php';

    // Mark announcements as read when they come into view
    document.querySelectorAll('.announcement-card').forEach(card => {
        const announcementId = card.getAttribute('data-announcement-id');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !card.classList.contains('read')) {
                    markAnnouncementAsRead(announcementId);
                    observer.unobserve(card);
                }
            });
        }, { threshold: 0.5 });
        
        observer.observe(card);
    });

    function markAnnouncementAsRead(announcementId) {
        const payload = {
            announcement_id: announcementId,
            employee_id: userId
        };

        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI to show as read
                document.querySelector(`[data-announcement-id="${announcementId}"]`)?.classList.add('read');
                document.querySelector(`[data-announcement-id="${announcementId}"]`)?.classList.remove('unread');
                const badge = document.querySelector(`[data-announcement-id="${announcementId}"] .read-badge`);
                if (badge) {
                    badge.classList.add('read');
                    badge.classList.remove('unread');
                    badge.textContent = '✓ Read';
                }
            }
        })
        .catch(error => console.error('Error marking announcement as read:', error));
    }
});
