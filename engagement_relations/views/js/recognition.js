document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('form');
  if (form && form.querySelector('[name="receiver_id"]')) {
    form.addEventListener('submit', function() {
      const button = form.querySelector('button[type=submit]');
      if (button) { button.textContent = 'Sending...'; button.disabled = true; }
    });
  }
});