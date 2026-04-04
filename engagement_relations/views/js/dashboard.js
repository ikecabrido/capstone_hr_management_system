document.addEventListener('DOMContentLoaded', function() {
  console.log('Dashboard module loaded');
  loadNotifications();
});

function loadNotifications() {
  const notificationsList = document.getElementById('notifications-list');
  if (!notificationsList) return;

  fetch('../api/communication.php?action=notifications')
    .then(response => {
      if (!response.ok) {
        throw new Error('Failed to fetch notifications');
      }
      return response.json();
    })
    .then(data => {
      if (!Array.isArray(data) || data.length === 0) {
        notificationsList.innerHTML = '<li class="text-muted">No notifications found.</li>';
        return;
      }

      notificationsList.innerHTML = '';
      data.forEach(notification => {
        const listItem = document.createElement('li');
        listItem.textContent = notification.message || 'Untitled notification';
        if (!notification.is_read) {
          listItem.style.fontWeight = 'bold';
          listItem.addEventListener('click', function() {
            fetch('../api/communication.php?action=mark_notification_read', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ notification_id: notification.id })
            })
              .then(() => {
                listItem.style.fontWeight = 'normal';
              })
              .catch(err => {
                console.error('Unable to mark notification read:', err);
              });
          });
        }
        notificationsList.appendChild(listItem);
      });
    })
    .catch(error => {
      console.error('Error loading notifications:', error);
      notificationsList.innerHTML = '<li class="text-danger">Failed to load notifications.</li>';
    });
}

