document.addEventListener('DOMContentLoaded', function () {
  const containers = {
    surveys: document.getElementById('surveys-container'),
    questions: document.getElementById('questions-container'),
    responses: document.getElementById('responses-container'),
    answers: document.getElementById('answers-container'),
  };

  const apiBase = `${window.location.origin}${window.location.pathname.replace(/\/[^\/]*$/, '')}/api`;

  for (const key in containers) {
    if (!containers[key]) return;
    containers[key].innerHTML = '<div style="padding:20px; color:#666;">Loading ' + key + '...</div>';
  }

  function renderList(key, items, renderItem) {
    const container = containers[key];
    if (!Array.isArray(items)) {
      container.innerHTML = `<div style="padding:20px; color:#a00;">Invalid ${key} data</div>`;
      return;
    }
    if (items.length === 0) {
      container.innerHTML = `<div style="padding:20px; color:#666;">No ${key} available.</div>`;
      return;
    }
    container.innerHTML = items.map(renderItem).join('');
  }

  function formatDate(dateValue) {
    if (!dateValue) return 'N/A';
    const d = new Date(dateValue);
    if (Number.isNaN(d.getTime())) return dateValue;
    return d.toLocaleString();
  }

  function fetchData() {
    const endpoints = {
      surveys: `${apiBase}/surveys.php`,
      questions: `${apiBase}/survey-questions.php`,
      responses: `${apiBase}/survey-responses.php`,
      answers: `${apiBase}/survey-answers.php`,
    };

    const requests = Object.entries(endpoints).map(([key, url]) =>
      fetch(url, { cache: 'no-store', credentials: 'same-origin' })
        .then((res) => res.text().then((text) => {
          if (!res.ok) throw new Error(`HTTP ${res.status} ${res.statusText}: ${text}`);
          try {
            return JSON.parse(text);
          } catch (err) {
            throw new Error(`Invalid JSON from server: ${err.message} | ${text}`);
          }
        }))
        .then((json) => {
          const normalized = {
            surveys: json.surveys || json.data || [],
            questions: json.survey_questions || json.questions || [],
            responses: json.survey_responses || json.responses || [],
            answers: json.survey_answers || json.answers || [],
          };
          return { key, data: normalized[key] || [] };
        })
    );

    Promise.all(requests)
      .then((results) => {
        results.forEach(({ key, data }) => {
          if (key === 'surveys') {
            renderList('surveys', data, (survey) => {
              const status = (survey.status || 'draft').toLowerCase();
              return `
                <div class="survey-card">
                  <div class="survey-header">
                    <div class="survey-title">${survey.title || 'Untitled Survey'}</div>
                    <span class="survey-status ${status === 'active' ? 'status-active' : 'status-draft'}">${survey.status || 'Draft'}</span>
                  </div>
                  <div class="survey-description">${survey.description || ''}</div>
                </div>
              `;
            });
          } else if (key === 'questions') {
            renderList('questions', data, (q) => `
              <div class="question-card">
                <div class="question-text">${q.question_text || 'Question'}</div>
                <span class="question-type">Type: ${q.question_type || 'text'}</span>
                <div style="margin-top:10px;font-size:0.9em;color:#999;">Survey ID: ${q.survey_id || 'N/A'}</div>
              </div>
            `);
          } else if (key === 'responses') {
            renderList('responses', data, (r) => `
              <div class="response-card">
                <div class="response-header">Survey Response #${r.id || 'N/A'}</div>
                <div class="response-meta">Survey ID: ${r.survey_id || 'N/A'} | Employee ID: ${r.employee_id || 'N/A'} | Submitted: ${formatDate(r.submitted_at)}</div>
              </div>
            `);
          } else if (key === 'answers') {
            renderList('answers', data, (a) => `
              <div class="answer-card">
                <div style="font-weight:600;color:#333;margin-bottom:10px;">Question ID: ${a.question_id || 'N/A'}</div>
                <div class="answer-text">${a.answer || ''}</div>
                <div style="margin-top:10px;font-size:0.9em;color:#999;">Response ID: ${a.response_id || 'N/A'}</div>
              </div>
            `);
          }
        });
      })
      .catch((err) => {
        console.error(err);
        Object.values(containers).forEach((container) => {
          container.innerHTML = `<div style="padding:20px; color:#a00;">Error loading data: ${err.message}</div>`;
        });
      });
  }

  fetchData();
});