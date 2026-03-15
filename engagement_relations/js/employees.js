document.addEventListener('DOMContentLoaded', function () {
  const container = document.getElementById('employees-container');
  if (!container) return;

  container.innerHTML = '<div class="loading">Loading employees...</div>';

  fetch('api/employees_data.php', { cache: 'no-store' })
    .then((res) => {
      if (!res.ok) throw new Error('Failed to fetch employees');
      return res.json();
    })
    .then((data) => {
      if (!data.success || !Array.isArray(data.employees)) {
        throw new Error(data.error || 'Invalid data format');
      }

      if (data.employees.length === 0) {
        container.innerHTML = '<div style="padding:20px;color:#666;">No employees found.</div>';
        return;
      }

      const rows = data.employees
        .map((emp) => {
          const status = emp.status ? emp.status.toLowerCase() : 'active';
          return `
            <tr>
              <td>${emp.name || 'N/A'}</td>
              <td>${emp.email || 'N/A'}</td>
              <td>${(emp.role || 'employee').charAt(0).toUpperCase() + (emp.role || 'employee').slice(1)}</td>
              <td><span class="employee-status status-${status}">${emp.status || 'Active'}</span></td>
            </tr>
          `;
        })
        .join('');

      container.innerHTML = `
        <div class="employees-table">
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>${rows}</tbody>
          </table>
        </div>
      `;
    })
    .catch((err) => {
      container.innerHTML = `<div style="padding:20px; color: #a00;">Error loading employees: ${err.message}</div>`;
      console.error(err);
    });
});
