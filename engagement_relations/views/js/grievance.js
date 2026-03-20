document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('.grievance-form');
  if (form) {
    form.addEventListener('submit', function() {
      const button = form.querySelector('button[type=submit]');
      if (button) { button.textContent = 'Submitting...'; button.disabled = true; }
    });
  }
});