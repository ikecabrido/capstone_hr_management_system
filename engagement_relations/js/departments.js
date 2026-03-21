document.addEventListener('DOMContentLoaded', function () {
  const container = document.getElementById('departments-container');
  if (!container) return;

  container.innerHTML = '<div class="loading">Loading departments...</div>';

  fetch('api/departments_data.php', { cache: 'no-store' })
    .then((res) => {
      if (!res.ok) throw new Error('Failed to fetch departments');
      return res.json();
    })
    .then((data) => {
      if (!data.success || !Array.isArray(data.departments)) {
        throw new Error(data.error || 'Invalid data format');
      }

      if (data.departments.length === 0) {
        container.innerHTML = '<div style="padding:20px;color:#666;">No departments found.</div>';
        return;
      }

      const cards = data.departments
        .map((dept) => `
          <div class="department-card">
            <div class="department-name">${dept.name || 'N/A'}</div>
          </div>
        `)
        .join('');

      container.innerHTML = cards;
    })
    .catch((err) => {
      container.innerHTML = `<div style="padding:20px; color:#a00;">Error loading departments: ${err.message}</div>`;
      console.error(err);
    });
});
