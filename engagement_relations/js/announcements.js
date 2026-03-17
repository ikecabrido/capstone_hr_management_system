// ============================================
// ANNOUNCEMENTS LIST + READ TRACKING
// Loads announcements from API and marks them read when viewed
// ============================================

const API_BASE = 'api';

function escapeHtml(unsafe) {
    return String(unsafe)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return dateStr;
    return date.toLocaleString(undefined, {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

async function loadAnnouncements() {
    const container = document.getElementById('announcements-container');
    if (!container) return;

    container.innerHTML = `<div style="padding:20px; color:#666;">Loading announcements...</div>`;

    try {
        const response = await fetch(`${API_BASE}/announcements_data.php?t=${Date.now()}`, {
            cache: 'no-store'
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();
        if (!data.success) {
            throw new Error(data.error || 'Unexpected response');
        }

        renderAnnouncements(data.announcements || []);
    } catch (error) {
        console.error('Error loading announcements:', error);
        container.innerHTML = `<div style="padding:20px; color:#c00;">Failed to load announcements: ${escapeHtml(error.message)}</div>`;
    }
}

function renderAnnouncements(items) {
    const container = document.getElementById('announcements-container');
    if (!container) return;

    if (!items || items.length === 0) {
        container.innerHTML = `<div style="padding:20px; color:#666;">No announcements found.</div>`;
        return;
    }

    container.innerHTML = '';

    const userId = document.querySelector('script[data-user-id]')?.getAttribute('data-user-id');

    items.forEach(item => {
        const isRead = item.is_read || false;
        const card = document.createElement('div');
        card.className = `announcement-card ${isRead ? 'read' : 'unread'}`;
        card.dataset.announcementId = item.id;
        card.innerHTML = `
            <span class="read-badge ${isRead ? 'read' : 'unread'}">${isRead ? 'Read' : 'Unread'}</span>
            <div class="announcement-header">
                <h3 class="announcement-title">${escapeHtml(item.title)}</h3>
                <span class="announcement-date">${formatDate(item.created_at)}</span>
            </div>
            <div class="announcement-body">${escapeHtml(item.content)}</div>
            <div class="announcement-meta">
                <span class="announcement-status ${isRead ? 'read' : 'unread'}">
                    ${isRead ? 'Read' : 'Unread'}
                </span>
            </div>
        `;
        container.appendChild(card);
    });

    setupReadTracking(userId);
}

function setupReadTracking(userId) {
    const apiUrl = `${API_BASE}/announcement-reads.php`;
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const card = entry.target;
            if (card.classList.contains('read')) {
                observer.unobserve(card);
                return;
            }
            const announcementId = card.dataset.announcementId;
            if (!announcementId) return;
            markAnnouncementAsRead(announcementId, userId, card, apiUrl)
                .finally(() => observer.unobserve(card));
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.announcement-card').forEach(card => observer.observe(card));
}

async function markAnnouncementAsRead(announcementId, userId, card, apiUrl) {
    if (!userId) return;

    try {
        const payload = {
            announcement_id: announcementId,
            employee_id: userId
        };

        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();
        if (data.success) {
            card.classList.add('read');
            card.classList.remove('unread');
            const status = card.querySelector('.announcement-status');
            if (status) {
                status.textContent = 'Read';
                status.classList.add('read');
                status.classList.remove('unread');
            }
        }
    } catch (error) {
        console.error('Error marking announcement as read:', error);
    }
}

window.addEventListener('load', loadAnnouncements);
