document.addEventListener('DOMContentLoaded', function() {
  // Simple heartbeat to avoid automatic logout due to inactivity in some setups
  setInterval(function() {
    fetch(window.location.href, { method: 'HEAD', cache: 'no-store' }).catch(() => {});
  }, 5 * 60 * 1000); // ping every 5 minutes
});