document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('form');
  if (form && form.querySelector('[name="receiver_id"]')) {
    form.addEventListener('submit', function() {
      const button = form.querySelector('button[type=submit]');
      if (button) { button.textContent = 'Sending...'; button.disabled = true; }
    });
  }

  const sel = document.getElementById('rec-receiver');
  const btn = document.getElementById('send-recognition-btn');
  if (form) {
    const oldInput = form.querySelector('input[name="receiver_id"]');
    if (oldInput) oldInput.remove();
  }

  fetch('../api/employee_list.php')
    .then(res => res.json())
    .then(list => {
      if (!sel) return;
      list.forEach(emp => {
        const opt = document.createElement('option');
        opt.value = emp.employee_id;
        opt.textContent = emp.full_name + ' (' + emp.employee_id + ')';
        sel.appendChild(opt);
      });
      if (sel) {
        sel.addEventListener('change', function() {
          if (btn) btn.disabled = !sel.value;
        });
      }
    });

  if (form && sel && btn) {
    form.addEventListener('submit', function(e) {
      if (!sel.value || !/^EMP\d+$/i.test(sel.value)) {
        e.preventDefault();
        btn.disabled = true;
        alert('Please select a valid employee as receiver.');
      }
    });
  }

  loadRecognitionFeed();
  loadBadges();
  loadAwardHistory();
  loadRewards();
  loadRewardRedemptions();
  loadEmployeeBadges();
});

function escapeHtml(text) {
  var map = {
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
  };
  return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
}

function loadRecognitionFeed() {
  fetch('../api/recognition.php?action=list')
    .then(r => r.json())
    .then(res => {
      const feed = document.getElementById('recognition-feed');
      if (!feed) return;
      feed.innerHTML = '';
      if (res && res.data && res.data.length) {
        res.data.forEach(r => {
          const item = document.createElement('div');
          item.className = 'list-group-item';
          item.innerHTML = `<b>${escapeHtml(r.sender_name || r.sender_id)}</b> → <b>${escapeHtml(r.receiver_name || r.receiver_id)}</b><br><span>${escapeHtml(r.message)}</span> <span class='badge badge-success ml-2'>+${r.points} pts</span> <small class='text-muted float-right'>${escapeHtml(r.created_at || '')}</small>`;
          feed.appendChild(item);
        });
      } else {
        feed.innerHTML = '<div class="text-muted">No recognition found.</div>';
      }
    });
}

function loadBadges() {
  fetch('../api/badge.php')
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
  fetch('../api/award_history.php')
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
  fetch('../api/reward.php')
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
  fetch('../api/reward_redemption.php')
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

function loadEmployeeBadges() {
  fetch('../api/employee_badge.php')
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
