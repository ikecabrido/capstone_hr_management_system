document.addEventListener('DOMContentLoaded', function () {
  const recognitionContainer = document.getElementById('recognitions-container');
  const rewardsContainer = document.getElementById('rewards-container');

  if (!recognitionContainer || !rewardsContainer) return;

  const apiBase = `${window.location.origin}${window.location.pathname.replace(/\/[^\/]*$/, '')}/api`;

  recognitionContainer.innerHTML = '<div style="padding:20px; color:#666;">Loading recognitions...</div>';
  rewardsContainer.innerHTML = '<div style="padding:20px; color:#666;">Loading rewards...</div>';

  function renderRecognitions(items) {
    if (!Array.isArray(items)) {
      recognitionContainer.innerHTML = '<div style="padding:20px; color:#a00;">Invalid recognition data</div>';
      return;
    }

    if (items.length === 0) {
      recognitionContainer.innerHTML = '<div style="padding:20px; color:#666;">No recognitions yet.</div>';
      return;
    }

    recognitionContainer.innerHTML = items.map((rec) => {
      return `
        <div class="recognition-card">
          <div class="recognition-badge">⭐ Recognition</div>
          <div class="recipient-name">To Employee ID: ${rec.to_employee_id || 'Unknown'}</div>
          <div class="recognition-reason">${rec.message ? rec.message.replace(/\n/g, '<br>') : 'No details provided'}</div>
          <div class="recognition-meta">
            <div class="meta-item"><div class="meta-label">From Employee</div><div class="meta-value">${rec.from_employee_id || 'Admin'}</div></div>
            <div class="meta-item"><div class="meta-label">Type</div><div class="meta-value">${rec.type || 'General'}</div></div>
            <div class="meta-item"><div class="meta-label">Date</div><div class="meta-value">${rec.created_at ? new Date(rec.created_at).toLocaleDateString() : 'N/A'}</div></div>
          </div>
        </div>
      `;
    }).join('');
  }

  function renderRewards(items) {
    if (!Array.isArray(items)) {
      rewardsContainer.innerHTML = '<div style="padding:20px; color:#a00;">Invalid rewards data</div>';
      return;
    }

    if (items.length === 0) {
      rewardsContainer.innerHTML = '<div style="padding:20px; color:#666;">No rewards configured yet.</div>';
      return;
    }

    rewardsContainer.innerHTML = items.map((reward) => {
      return `
        <div class="reward-card">
          <div class="reward-header">
            <div class="reward-name">${reward.name || 'Reward'}</div>
            <div class="reward-points">${reward.points !== undefined ? reward.points : 0} pts</div>
          </div>
          ${reward.description ? `<div class="reward-description">${reward.description}</div>` : ''}
        </div>
      `;
    }).join('');
  }

  function fetchData() {
    const recognitionReq = fetch(`${apiBase}/recognitions.php`, { cache: 'no-store', credentials: 'same-origin' })
      .then((res) => res.text().then((text) => {
        if (!res.ok) throw new Error(`HTTP ${res.status} ${res.statusText}: ${text}`);
        try {
          return JSON.parse(text);
        } catch (err) {
          throw new Error(`Invalid JSON from server: ${err.message} | ${text}`);
        }
      }));

    const rewardsReq = fetch(`${apiBase}/rewards.php`, { cache: 'no-store', credentials: 'same-origin' })
      .then((res) => res.text().then((text) => {
        if (!res.ok) throw new Error(`HTTP ${res.status} ${res.statusText}: ${text}`);
        try {
          return JSON.parse(text);
        } catch (err) {
          throw new Error(`Invalid JSON from server: ${err.message} | ${text}`);
        }
      }));

    Promise.all([recognitionReq, rewardsReq])
      .then(([recognitionData, rewardsData]) => {
        renderRecognitions(recognitionData.recognitions || []);
        renderRewards(rewardsData.rewards || []);
      })
      .catch((error) => {
        console.error(error);
        recognitionContainer.innerHTML = `<div style="padding:20px; color:#a00;">Error loading data: ${error.message}</div>`;
        rewardsContainer.innerHTML = `<div style="padding:20px; color:#a00;">Error loading data: ${error.message}</div>`;
      });
  }

  fetchData();
});