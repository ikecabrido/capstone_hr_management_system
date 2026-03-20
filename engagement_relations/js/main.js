document.addEventListener('DOMContentLoaded', function() {
  const navLinks = document.querySelectorAll('.sidebar a');
  const currentUrl = new URL(window.location.href);

  navLinks.forEach(function(link) {
    if (link.href === currentUrl.href || link.href === currentUrl.origin + currentUrl.pathname + currentUrl.search) {
      link.classList.add('active');
    }
  });

  const darkToggle = document.getElementById('darkToggle');
  const body = document.body;
  const themeIcon = document.getElementById('themeIcon');

  if (darkToggle && themeIcon) {
    darkToggle.addEventListener('click', function(e) {
      e.preventDefault();
      body.classList.toggle('dark-mode');
      const isDark = body.classList.contains('dark-mode');
      themeIcon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
      localStorage.setItem('engagementTheme', isDark ? 'dark' : 'light');
    });

    const savedTheme = localStorage.getItem('engagementTheme');
    if (savedTheme === 'dark') {
      body.classList.add('dark-mode');
      themeIcon.className = 'fas fa-sun';
    }
  }

  const clockEl = document.getElementById('clock');
  if (clockEl) {
    setInterval(() => {
      const now = new Date();
      const formatted = now.toLocaleTimeString();
      clockEl.textContent = formatted;
    }, 1000);
  }
});