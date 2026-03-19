document.addEventListener('DOMContentLoaded', function () {
  const feedbackContainer = document.getElementById('feedback-container');
  const suggestionsContainer = document.getElementById('suggestions-container');

  const apiBase = `${window.location.origin}${window.location.pathname.replace(/\/[^\/]*$/, '')}/api`;

  function setLoading(container, message) {
    if (!container) return;
    container.innerHTML = `<div style="padding:20px;color:#666;">${message}</div>`;
  }

  setLoading(feedbackContainer, 'Loading feedback...');
  setLoading(suggestionsContainer, 'Loading suggestions...');

  fetch(`${apiBase}/feedback_data.php`, { cache: 'no-store', credentials: 'same-origin' })
    .then((res) => {
      return res.text().then((text) => {
        if (!res.ok) {
          throw new Error(`HTTP ${res.status} ${res.statusText}: ${text}`);
        }
        try {
          return JSON.parse(text);
        } catch (err) {
          throw new Error(`Invalid JSON from server: ${err.message} | ${text}`);
        }
      });
    })
    .then((data) => {
      if (!data.success || !Array.isArray(data.feedback)) {
        throw new Error(data.error || 'Invalid data format for feedback');
      }

      if (!data.feedback.length) {
        feedbackContainer.innerHTML = '<div style="padding:20px;color:#666;">No feedback submitted yet.</div>';
        return;
      }

      feedbackContainer.innerHTML = data.feedback
        .map((item) => `
          <div class="feedback-card">
            <div class="feedback-heading">
              <div class="feedback-title">${item.is_anonymous ? 'Anonymous' : 'Employee #' + item.employee_id}</div>
              <div class="feedback-status status-${item.status || 'new'}">${item.status || 'new'}</div>
            </div>
            <div class="feedback-body">${(item.feedback_text || '').replace(/\n/g, '<br>')}</div>
            <div class="feedback-meta">Submitted: ${new Date(item.created_at).toLocaleDateString()}</div>
          </div>
        `)
        .join('');
    })
    .catch((err) => {
      feedbackContainer.innerHTML = `<div style="padding:20px;color:#a00;">Error loading feedback: ${err.message}</div>`;
      console.error(err);
    });

  fetch(`${apiBase}/suggestions_data.php`, { cache: 'no-store', credentials: 'same-origin' })
    .then((res) => {
      return res.text().then((text) => {
        if (!res.ok) {
          throw new Error(`HTTP ${res.status} ${res.statusText}: ${text}`);
        }
        try {
          return JSON.parse(text);
        } catch (err) {
          throw new Error(`Invalid JSON from server: ${err.message} | ${text}`);
        }
      });
    })
    .then((data) => {
      if (!data.success || !Array.isArray(data.suggestions)) {
        throw new Error(data.error || 'Invalid data format for suggestions');
      }

      if (!data.suggestions.length) {
        suggestionsContainer.innerHTML = '<div style="padding:20px;color:#666;">No suggestions submitted yet.</div>';
        return;
      }

      suggestionsContainer.innerHTML = data.suggestions
        .map((item) => `
          <div class="suggestion-card">
            <div class="suggestion-heading">
              <div class="suggestion-title">Employee #${item.employee_id || 'N/A'}</div>
              <div class="suggestion-status status-${item.status || 'pending'}">${item.status || 'pending'}</div>
            </div>
            <div class="suggestion-body">${(item.suggestion_text || '').replace(/\n/g, '<br>')}</div>
            <div class="suggestion-meta">Submitted: ${new Date(item.created_at).toLocaleDateString()}</div>
          </div>
        `)
        .join('');
    })
    .catch((err) => {
      suggestionsContainer.innerHTML = `<div style="padding:20px;color:#a00;">Error loading suggestions: ${err.message}</div>`;
      console.error(err);
    });
});
