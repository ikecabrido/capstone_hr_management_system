// Single DOMContentLoaded setup (merge behaviors and avoid duplicate handlers)
document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('form');
  const sel = document.getElementById('rec-receiver');
  const sendBtn = document.getElementById('send-recognition-btn');

  if (form) {
    const oldInput = form.querySelector('input[name="receiver_id"]');
    if (oldInput) oldInput.remove();
  }

  // Populate receiver select
  fetch('../api/index.php?resource=employee_list')
    .then(res => res.json())
    .then(list => {
      if (!sel) return;
      list.forEach(emp => {
        const opt = document.createElement('option');
        opt.value = emp.employee_id;
        opt.setAttribute('data-employee-id', emp.employee_id);
        opt.textContent = emp.full_name + ' (' + emp.employee_id + ')';
        sel.appendChild(opt);
      });
      if (list.length > 0) {
        sel.removeAttribute('disabled');
        sel.removeAttribute('aria-disabled');
      }
      sel.addEventListener('change', function() {
        if (sendBtn) sendBtn.disabled = !sel.value;
      });
    }).catch(err => {
      console.error('Failed to load employee list', err);
      if (sel) {
        sel.innerHTML = '<option value="">Unable to load employees</option>';
      }
    });

  // Relax validation: allow numeric user_id or employee ids
  if (form && sel && sendBtn) {
    form.addEventListener('submit', function(e) {
      if (!sel.value) {
        e.preventDefault();
        sendBtn.disabled = true;
        alert('Please select a receiver.');
      }
    });
  }

  window.employeeMonthVotes = window.employeeMonthVotes || [];

  // Start initial loads and auto-refresh
  loadRecognitionFeed();
  loadBadges();
  loadAwardHistory();
  loadRewards();
  loadRewardRedemptions();
  loadEmployeeBadges();
  loadPerformanceRecommendations();
  loadTopPerformers();
  loadEmployeeOfTheMonthCandidates();
  startRecognitionAutoRefresh();

  // AJAX submit for send recognition modal
  const sendForm = document.querySelector('#sendRecognitionModal form');
  if (sendForm) {
    sendForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const btn = sendForm.querySelector('button[type=submit]');
      if (btn) { btn.textContent = 'Sending...'; btn.disabled = true; }

      const receiverEl = document.getElementById('rec-receiver');
      const receiverId = receiverEl ? receiverEl.value : null;
      const message = document.getElementById('rec-message') ? document.getElementById('rec-message').value.trim() : '';
      const points = document.getElementById('rec-points') ? parseInt(document.getElementById('rec-points').value, 10) : 10;

      if (!receiverId) {
        alert('Please select a recipient before sending recognition.');
        if (btn) { btn.textContent = 'Send Recognition'; btn.disabled = false; }
        return;
      }

      fetch('../api/index.php?resource=recognition&action=send', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ receiver_id: receiverId, message: message, points: points })
      })
      .then(async response => {
        const payloadText = await response.text();
        let payload;
        try {
          payload = JSON.parse(payloadText);
        } catch (jsonErr) {
          throw new Error('Invalid server response: ' + payloadText);
        }

        if (!response.ok) {
          throw new Error(payload.error || 'Server returned status ' + response.status);
        }

        return payload;
      })
      .then(res => {
        if (res && (res.id || res.success)) {
          if (window.jQuery) jQuery('#sendRecognitionModal').modal('hide');
          if (document.getElementById('rec-message')) document.getElementById('rec-message').value = '';
          if (document.getElementById('rec-points')) document.getElementById('rec-points').value = 10;
          refreshAllRecognitionSections();
          alert('Recognition sent successfully.');
        } else if (res && res.error) {
          alert('Error: ' + res.error);
        } else {
          throw new Error('Unexpected response from server');
        }
      })
      .catch(err => {
        console.error('Failed to send recognition', err);
        alert('Failed to send recognition. ' + err.message);
      })
      .finally(() => {
        if (btn) { btn.textContent = 'Send Recognition'; btn.disabled = false; }
      });
    });
  }

  const employeeMonthFilterForm = document.getElementById('employee-month-filter-form');
  if (employeeMonthFilterForm) {
    employeeMonthFilterForm.addEventListener('submit', function(e) {
      e.preventDefault();
      loadEmployeeOfTheMonthCandidates();
    });
  }

  const assignBadgeForm = document.getElementById('assign-badge-form');
  if (assignBadgeForm) {
    assignBadgeForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const btn = assignBadgeForm.querySelector('button[type=submit]');
      if (btn) { btn.textContent = 'Assigning...'; btn.disabled = true; }

      const employeeId = document.getElementById('badge_employee_id') ? document.getElementById('badge_employee_id').value : null;
      const badgeId = document.getElementById('badge_id') ? document.getElementById('badge_id').value : null;

      fetch('../api/index.php?resource=recognition&action=assign_badge', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ employee_id: employeeId, badge_id: badgeId })
      }).then(r => r.json())
        .then(res => {
          if (res && res.success) {
            if (window.jQuery) jQuery('#assignBadgeModal').modal('hide');
            assignBadgeForm.reset();
            refreshAllRecognitionSections();
            alert('Badge assigned successfully.');
          } else if (res && res.error) {
            alert('Error: ' + res.error);
          }
        }).catch(err => {
          console.error('Failed to assign badge', err);
          alert('Failed to assign badge.');
        }).finally(() => {
          if (btn) { btn.textContent = 'Assign Badge'; btn.disabled = false; }
        });
    });
  }

  // Keep nomination form submission on the server-side so the page can refresh with the new candidate list.
  // The form already posts to the current page and the server handles the nomination.

  document.addEventListener('submit', function(e) {
    if (e.target && e.target.matches('.vote-form')) {
      e.preventDefault();
      const form = e.target;
      const awardHistoryId = form.querySelector('input[name="award_history_id"]') ? form.querySelector('input[name="award_history_id"]').value : null;
      const btn = form.querySelector('button[type=submit]');
      if (btn) { btn.textContent = 'Voting...'; btn.disabled = true; }

      fetch('../api/index.php?resource=recognition&action=vote_employee_month', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ award_history_id: awardHistoryId })
      }).then(r => r.json())
        .then(res => {
          if (res && res.success) {
            window.employeeMonthVotes.push(awardHistoryId);
            refreshAllRecognitionSections();
            alert('Vote recorded successfully.');
          } else if (res && res.error) {
            alert('Error: ' + res.error);
          }
        }).catch(err => {
          console.error('Failed to record vote', err);
          alert('Failed to record vote.');
        }).finally(() => {
          if (btn) { btn.textContent = 'Voted (+5)'; btn.disabled = true; }
        });
    }
  });
});

let recognitionRefreshTimer = null;

function startRecognitionAutoRefresh(intervalMs) {
  if (recognitionRefreshTimer) {
    clearInterval(recognitionRefreshTimer);
  }
  const refreshInterval = intervalMs || 5000;
  recognitionRefreshTimer = setInterval(function() {
    refreshAllRecognitionSections();
  }, refreshInterval);
}

function refreshAllRecognitionSections() {
  loadRecognitionFeed();
  loadBadges();
  loadAwardHistory();
  loadRewards();
  loadRewardRedemptions();
  loadEmployeeBadges();
  loadPerformanceRecommendations();
  loadTopPerformers();
  loadEmployeeOfTheMonthCandidates();
}

(function injectHighlightStyle(){
  var css = '.new-recognition{box-shadow:0 0 0 4px rgba(76,175,80,0.12) inset; animation: rrHighlight 2s ease;} @keyframes rrHighlight{0%{background:#e9fff0}100%{background:transparent}}';
  var head = document.head || document.getElementsByTagName('head')[0];
  var style = document.createElement('style');
  style.type = 'text/css';
  style.appendChild(document.createTextNode(css));
  head.appendChild(style);
})();

function escapeHtml(text) {
  var map = {
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
  };
  return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
}

function findCardBodyByTitle(title) {
  var cards = document.querySelectorAll('.card');
  for (var i = 0; i < cards.length; i++) {
    var header = cards[i].querySelector('.card-title');
    if (!header) continue;
    if (header.textContent && header.textContent.trim().indexOf(title) !== -1) {
      return cards[i].querySelector('.card-body');
    }
  }
  return null;
}

function renderTopPerformers(container, items) {
  if (!container) return;
  container.innerHTML = '';
  if (!items || !items.length) {
    container.innerHTML = '<p class="text-muted text-center m-2">No top performers found.</p>';
    return;
  }
  var ul = document.createElement('ul');
  ul.className = 'list-group list-group-flush';
  items.forEach(function(tp){
    var li = document.createElement('li');
    li.className = 'list-group-item d-flex justify-content-between align-items-center';
    li.style.borderRadius = '12px'; li.style.marginBottom = '.5rem';
    var score = (tp.final_rating_percent !== undefined) ? tp.final_rating_percent : (tp.performance_score !== undefined ? tp.performance_score : null);
    var left = '<div><strong>' + escapeHtml(tp.employee_name || tp.employee_id) + '</strong>' +
      '<div class="text-muted small">Score: ' + (score !== null ? escapeHtml(score) + '%' : 'N/A') + ' • Grade: ' + escapeHtml(tp.final_grade || 'N/A') + '</div></div>';
    var right = '<div><span class="badge badge-success">+' + (score !== null ? escapeHtml(score) + '%' : 'N/A') + '</span></div>';
    li.innerHTML = left + right;
    ul.appendChild(li);
  });
  container.appendChild(ul);
}

// Handle quick recognize clicks (show informational modal)
document.addEventListener('click', function(e){
  var target = e.target || e.srcElement;
  if (target && target.matches && target.matches('.recommend-recognize')) {
    e.preventDefault();
    var employeeId = target.getAttribute('data-employee-id');
    var employeeName = target.getAttribute('data-employee-name') || 'Employee';
    var btn = document.getElementById('send-recognition-btn');
    // Set the receiver select to the recommended employee if available
    const sel = document.getElementById('rec-receiver');
    if (sel) {
      // Try to find option by data-employee-id first, otherwise by value
      let found = Array.from(sel.options).find(o => o.getAttribute('data-employee-id') === employeeId);
      if (!found) found = Array.from(sel.options).find(o => o.value === employeeId || o.value === (employeeId + ''));
      if (found) {
        sel.value = found.value;
        if (btn) btn.disabled = false;
      }
    }
    if (window.jQuery) {
      jQuery('#sendRecognitionModal').modal('show');
    }
  }
});

function loadRecognitionFeed() {
  fetch('../api/index.php?resource=recognition&action=list')
    .then(r => r.json())
    .then(res => {
      const feed = document.getElementById('recognition-feed');
      if (!feed) return;
      feed.innerHTML = '';
      if (res && res.data && res.data.length) {
        res.data.forEach(r => {
          const item = document.createElement('div');
          item.className = 'list-group-item';
          // store recognition id for change detection
          item.dataset.recognitionId = r.id || r.eer_recognition_id || '';
          item.innerHTML = `<b>${escapeHtml(r.sender_name || r.sender_id)}</b> → <b>${escapeHtml(r.receiver_name || r.receiver_id)}</b><br><span>${escapeHtml(r.message)}</span> <span class='badge badge-success ml-2'>+${r.points} pts</span> <small class='text-muted float-right'>${escapeHtml(r.created_at || '')}</small>`;
          feed.appendChild(item);
        });

        // highlight newest item if it's new since last load
        try {
          const newFirstId = res.data[0] && (res.data[0].id || res.data[0].eer_recognition_id) ? (res.data[0].id || res.data[0].eer_recognition_id) : null;
          if (typeof lastSeenRecognitionId !== 'undefined' && lastSeenRecognitionId !== null && newFirstId && newFirstId != lastSeenRecognitionId) {
            const newEl = feed.querySelector('[data-recognition-id="' + newFirstId + '"]');
            if (newEl) {
              newEl.style.transition = 'background-color 0.4s ease';
              newEl.style.backgroundColor = '#e6ffed';
              setTimeout(() => { newEl.style.backgroundColor = ''; }, 3000);
            }
          }
          // update last seen id
          window.lastSeenRecognitionId = res.data[0] && (res.data[0].id || res.data[0].eer_recognition_id) ? (res.data[0].id || res.data[0].eer_recognition_id) : window.lastSeenRecognitionId;
        } catch (err) {
          console.error('Error detecting new recognition', err);
        }
      } else {
        feed.innerHTML = '<div class="text-muted">No recognition found.</div>';
      }
    });
}

// Initialize tracking variable
window.lastSeenRecognitionId = window.lastSeenRecognitionId || null;

function loadBadges() {
  fetch('../api/index.php?resource=badge')
    .then(r => r.json())
    .then(res => {
      const feed = document.getElementById('badges-feed');
      if (!feed) return;
      feed.innerHTML = '';
      if (res && res.data && res.data.length) {
        res.data.forEach(b => {
          const item = document.createElement('div');
          item.className = 'list-group-item';
          item.innerHTML = `<b>${escapeHtml(b.name)}</b><br><span>${escapeHtml(b.description)}</span>`;
          feed.appendChild(item);
        });
      } else {
        feed.innerHTML = '<div class="text-muted">No badges found.</div>';
      }
    });
}

function loadAwardHistory() {
  fetch('../api/index.php?resource=award_history')
    .then(r => r.json())
    .then(res => {
      const feed = document.getElementById('award-history-feed');
      if (!feed) return;
      feed.innerHTML = '';
      if (res && res.data && res.data.length) {
        res.data.forEach(a => {
          const item = document.createElement('div');
          item.className = 'list-group-item';
          item.innerHTML = `<b>${escapeHtml(a.award_name || '')}</b> to <b>${escapeHtml(a.employee_name || a.employee_id)}</b> <small class='text-muted float-right'>${escapeHtml(a.awarded_at || '')}</small>`;
          feed.appendChild(item);
        });
      } else {
        feed.innerHTML = '<div class="text-muted">No award history found.</div>';
      }
    });
}

function loadRewards() {
  fetch('../api/index.php?resource=reward')
    .then(r => r.json())
    .then(res => {
      const feed = document.getElementById('rewards-feed');
      if (!feed) return;
      feed.innerHTML = '';
      if (res && res.data && res.data.length) {
        res.data.forEach(rw => {
          const item = document.createElement('div');
          item.className = 'list-group-item';
          item.innerHTML = `<b>${escapeHtml(rw.name)}</b> <span class='badge badge-info ml-2'>${rw.points_required} pts</span><br><span>${escapeHtml(rw.description || '')}</span>`;
          feed.appendChild(item);
        });
      } else {
        feed.innerHTML = '<div class="text-muted">No rewards found.</div>';
      }
    });
}

function loadRewardRedemptions() {
  fetch('../api/index.php?resource=reward_redemption')
    .then(r => r.json())
    .then(res => {
      const feed = document.getElementById('reward-redemptions-feed');
      if (!feed) return;
      feed.innerHTML = '';
      if (res && res.data && res.data.length) {
        res.data.forEach(rr => {
          const item = document.createElement('div');
          item.className = 'list-group-item';
          item.innerHTML = `<b>${escapeHtml(rr.employee_name || rr.employee_id)}</b> redeemed <b>${escapeHtml(rr.reward_name || rr.reward_id)}</b> <small class='text-muted float-right'>${escapeHtml(rr.redeemed_at || '')}</small>`;
          feed.appendChild(item);
        });
      } else {
        feed.innerHTML = '<div class="text-muted">No reward redemptions found.</div>';
      }
    });
}

function loadPerformanceRecommendations() {
  fetch('../api/index.php?resource=recognition&action=recommendations&limit=10')
    .then(r => r.json())
    .then(res => {
      const container = document.getElementById('performance-recommendations-list');
      if (!container) return;
      if (!res || !res.data) {
        container.innerHTML = '<p class="text-muted text-center m-2">No recommendations available yet.</p>';
        return;
      }

      if (!res.data.length) {
        container.innerHTML = '<p class="text-muted text-center m-2">No recommendations available yet.</p>';
        return;
      }

      const list = document.createElement('ul');
      list.className = 'list-group list-group-flush';
      res.data.forEach(function(rec) {
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center';
        li.style.borderRadius = '12px';
        li.style.marginBottom = '.5rem';
        const score = rec.final_rating_percent !== undefined ? rec.final_rating_percent : 'N/A';
        li.innerHTML = `<div><strong>${escapeHtml(rec.employee_name || rec.employee_id)}</strong><div class="text-muted small">${escapeHtml(rec.evaluation_period || 'Performance Report')} • Grade: ${escapeHtml(rec.final_grade || 'N/A')} • Score: ${escapeHtml(score)}%</div>${rec.period_end ? '<div class="text-muted extra-small">Period end: ' + escapeHtml(rec.period_end) + '</div>' : ''}</div><div><button class="btn btn-sm btn-outline-success recommend-recognize" data-employee-id="${escapeHtml(rec.employee_id)}" data-employee-name="${escapeHtml(rec.employee_name)}">Recognize</button></div>`;
        list.appendChild(li);
      });
      container.innerHTML = '';
      container.appendChild(list);
    });
}

function loadTopPerformers() {
  fetch('../api/index.php?resource=recognition&action=performance_leaderboard&limit=10')
    .then(r => r.json())
    .then(res => {
      const container = document.getElementById('top-performers-list');
      if (!container) return;
      if (!res || !res.data || !res.data.length) {
        container.innerHTML = '<p class="text-muted text-center m-2">No top performers found.</p>';
        return;
      }

      const list = document.createElement('ul');
      list.className = 'list-group list-group-flush';
      res.data.forEach(function(tp) {
        const score = tp.final_rating_percent !== undefined ? tp.final_rating_percent : (tp.performance_score !== undefined ? tp.performance_score : 'N/A');
        const scoreLabel = score !== 'N/A' ? escapeHtml(score) + '%' : 'N/A';
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center';
        li.style.borderRadius = '12px';
        li.style.marginBottom = '.5rem';
        li.innerHTML = `<div><strong>${escapeHtml(tp.employee_name || tp.employee_id)}</strong><div class="text-muted small">Score: ${scoreLabel} • Grade: ${escapeHtml(tp.final_grade || 'N/A')}</div></div><div><span class="badge badge-success">+${scoreLabel}</span></div>`;
        list.appendChild(li);
      });
      container.innerHTML = '';
      container.appendChild(list);
    });
}

function loadEmployeeOfTheMonthCandidates() {
  const monthSelect = document.getElementById('employee-of-month-month');
  const yearSelect = document.getElementById('employee-of-month-year');
  const month = monthSelect ? monthSelect.value : new Date().getMonth() + 1;
  const year = yearSelect ? yearSelect.value : new Date().getFullYear();

  fetch(`../api/index.php?resource=recognition&action=employee_of_month&month=${encodeURIComponent(month)}&year=${encodeURIComponent(year)}`)
    .then(r => r.json())
    .then(res => {
      const container = document.getElementById('employee-month-candidates-list');
      if (!container) return;
      if (!res || !res.data || !res.data.length) {
        container.innerHTML = '<p class="text-muted text-center">No nominations yet.</p>';
        return;
      }

      container.innerHTML = '';
      res.data.forEach(function(candidate) {
        const hasVoted = Boolean(candidate.has_voted);
        const statusBadge = candidate.status === 'winner' ? 'badge-success' : (candidate.status === 'shortlisted' ? 'badge-info' : 'badge-warning');
        const item = document.createElement('div');
        item.className = 'list-group-item d-flex justify-content-between align-items-start candidate-item';
        item.dataset.awardHistoryId = candidate.eer_award_history_id || '';
        const voteButton = candidate.eer_award_history_id
          ? '<form method="POST" action="" class="mt-2 vote-form"><input type="hidden" name="action" value="vote_employee_month"><input type="hidden" name="award_history_id" value="' + escapeHtml(candidate.eer_award_history_id) + '"><button type="submit" class="btn btn-sm ' + (hasVoted ? 'btn-success disabled' : 'btn-outline-warning') + '" ' + (hasVoted ? 'disabled' : '') + '><i class="fas fa-vote-yea"></i> ' + (hasVoted ? 'Voted (+5)' : 'Vote +5') + '</button></form>'
          : '<button type="button" class="btn btn-sm btn-outline-secondary mt-2" disabled>Not Nominated</button>';
        item.innerHTML = `<div><strong>${escapeHtml(candidate.employee_name || candidate.employee_id)}</strong><br><small class="text-muted">Department: ${escapeHtml(candidate.department || 'N/A')} • Votes: ${escapeHtml(candidate.votes || 0)} • Performance: ${escapeHtml(candidate.performance_score || 0)}%</small><div class="mt-1"><span class="badge ${statusBadge}">${escapeHtml((candidate.status || 'nominated').charAt(0).toUpperCase() + (candidate.status || 'nominated').slice(1))}</span>${candidate.nomination_reason ? ' <span class="text-muted small">Reason: ' + escapeHtml(candidate.nomination_reason) + '</span>' : ''}</div></div><div class="text-right"><span class="badge badge-info">Recognition: ${escapeHtml(candidate.recognition_total || 0)} pts</span>${voteButton}</div>`;
        container.appendChild(item);
      });
    });
}

function loadEmployeeBadges() {
  fetch('../api/index.php?resource=employee_badge')
    .then(r => r.json())
    .then(res => {
      const feed = document.getElementById('employee-badges-feed');
      if (!feed) return;
      feed.innerHTML = '';
      if (res && res.data && res.data.length) {
        res.data.forEach(eb => {
          const item = document.createElement('div');
          item.className = 'list-group-item';
          item.innerHTML = `<b>${escapeHtml(eb.employee_name || eb.employee_id)}</b> earned <b>${escapeHtml(eb.badge_name || eb.badge_id)}</b> <small class='text-muted float-right'>${escapeHtml(eb.earned_at || '')}</small>`;
          feed.appendChild(item);
        });
      } else {
        feed.innerHTML = '<div class="text-muted">No employee badges found.</div>';
      }
    });
}
