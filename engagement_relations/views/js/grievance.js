// Initialize charts when analytics tab is shown
$('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
  if (e.target.id === 'analytics-tab') {
    initializeCharts();
  }
});

function initializeCharts() {
  const grievances = window.grievancesData || [];
  window.reportData = grievances;

  const statusCounts = { Pending: 0, 'Under Review': 0, Resolved: 0, Closed: 0, Escalated: 0 };
  grievances.forEach(g => {
    const status = (g.status || 'Pending').toString();
    if (statusCounts.hasOwnProperty(status)) {
      statusCounts[status]++;
    } else if (status.toLowerCase() === 'submitted' || status.toLowerCase() === 'pending') {
      statusCounts['Pending']++;
    } else if (status.toLowerCase() === 'investigation' || status.toLowerCase() === 'investigating') {
      statusCounts['Under Review']++;
    }
  });

  const statusCtx = document.getElementById('statusChart').getContext('2d');
  new Chart(statusCtx, {
    type: 'pie',
    data: {
      labels: ['Pending', 'Under Review', 'Resolved', 'Closed', 'Escalated'],
      datasets: [{
        data: [statusCounts['Pending'], statusCounts['Under Review'], statusCounts['Resolved'], statusCounts['Closed'], statusCounts['Escalated']],
        backgroundColor: ['#f39c12', '#3498db', '#27ae60', '#95a5a6', '#e74c3c']
      }]
    }
  });

  const categoryCounts = {};
  grievances.forEach(g => {
    const category = g.category || 'Other';
    categoryCounts[category] = (categoryCounts[category] || 0) + 1;
  });
  const categoryLabels = Object.keys(categoryCounts);
  const categoryValues = categoryLabels.map(label => categoryCounts[label]);

  const categoryCtx = document.getElementById('categoryChart').getContext('2d');
  new Chart(categoryCtx, {
    type: 'bar',
    data: {
      labels: categoryLabels.length ? categoryLabels : ['No Data'],
      datasets: [{
        label: 'Grievances by Category',
        data: categoryValues.length ? categoryValues : [0],
        backgroundColor: '#3498db'
      }]
    }
  });

  const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  const monthCounts = {};
  grievances.forEach(g => {
    const date = g.created_at ? new Date(g.created_at) : null;
    if (date instanceof Date && !isNaN(date)) {
      monthCounts[monthNames[date.getMonth()]] = (monthCounts[monthNames[date.getMonth()]] || 0) + 1;
    }
  });
  const trendValues = monthNames.map(name => monthCounts[name] || 0);

  const trendCtx = document.getElementById('trendChart').getContext('2d');
  new Chart(trendCtx, {
    type: 'line',
    data: {
      labels: monthNames,
      datasets: [{
        label: 'Grievances Filed',
        data: trendValues,
        borderColor: '#e74c3c',
        backgroundColor: 'rgba(231, 76, 60, 0.2)',
        fill: true
      }]
    }
  });

  const departmentCounts = {};
  grievances.forEach(g => {
    const department = g.department || 'Unassigned';
    departmentCounts[department] = (departmentCounts[department] || 0) + 1;
  });
  const deptLabels = Object.keys(departmentCounts);
  const deptValues = deptLabels.map(label => departmentCounts[label]);

  const departmentCtx = document.getElementById('departmentChart').getContext('2d');
  new Chart(departmentCtx, {
    type: 'bar',
    data: {
      labels: deptLabels.length ? deptLabels : ['No Data'],
      datasets: [{
        label: 'Grievances by Department',
        data: deptValues.length ? deptValues : [0],
        backgroundColor: '#6f42c1'
      }]
    }
  });

  const resolvedItems = grievances.filter(g => g.status === 'Resolved' || g.status === 'Closed' || g.resolved_at);
  let avgResolutionDays = 0;
  if (resolvedItems.length) {
    const totalDays = resolvedItems.reduce((sum, item) => {
      if (!item.created_at || !item.resolved_at) {
        return sum;
      }
      const created = new Date(item.created_at);
      const resolved = new Date(item.resolved_at);
      return sum + Math.max(0, Math.round((resolved - created) / (1000 * 60 * 60 * 24)));
    }, 0);
    avgResolutionDays = Math.round(totalDays / resolvedItems.length);
  }

  const resolutionCtx = document.getElementById('resolutionChart').getContext('2d');
  new Chart(resolutionCtx, {
    type: 'bar',
    data: {
      labels: ['Avg. Days to Resolution'],
      datasets: [{
        label: 'Resolution Time',
        data: [avgResolutionDays],
        backgroundColor: '#20c997'
      }]
    }
  });
}

$(document).ready(function() {
  window.originalGrievanceRows = $('#all-grievances-table tbody .grievance-row').toArray();
  window.reportData = window.grievancesData || [];

  $('#status-filter, #category-filter, #date-filter, #sort-filter, #department-filter').on('change', filterGrievances);
  $('#search-filter').on('input keyup', filterGrievances);
  $('#grievance-employee-select').on('change', populatePayslips);
  $('#grievance-category-select').on('change', populatePayslips);
  $('#grievance-payslip-select').on('change', updatePayslipSummary);
  $('#management-grievance-select').on('change', function() {
    $('#management-grievance-id').val($(this).val() || '');
    updateManagementFormState();
  });
  $('#management-update-form').on('submit', handleManagementFormSubmit);
  populatePayslips();
  updateManagementFormState();
  filterGrievances();
});

function isFinalizedStatus(status) {
  return ['resolved', 'closed'].includes((String(status) || '').toLowerCase().trim());
}

function updateManagementFormState() {
  const selectedOption = $('#management-grievance-select option:selected');
  const status = selectedOption.data('status') || '';
  const finalized = isFinalizedStatus(status);
  const form = $('#management-update-form');
  const inputs = form.find('select[name="status"], textarea[name="hr_remarks"], textarea[name="final_resolution"], input[name="supporting_document"], input[name="confidential"], button[type="submit"]');
  const alertBox = $('#management-form-alert');

  if (finalized) {
    inputs.prop('disabled', true);
    $('#management-grievance-select').prop('disabled', false);
    $('#management-grievance-id').prop('disabled', false);
    alertBox.removeClass('alert-success alert-danger alert-info d-none').addClass('alert-warning').html('<i class="fas fa-lock"></i> This grievance is resolved or closed and cannot be edited.');
  } else if (selectedOption.val()) {
    inputs.prop('disabled', false);
    alertBox.addClass('d-none').removeClass('alert-success alert-danger alert-warning alert-info').html('');
  } else {
    inputs.prop('disabled', false);
    alertBox.addClass('d-none').removeClass('alert-success alert-danger alert-warning alert-info').html('');
  }
}

function updateGrievanceRow(grievanceId, status) {
  const row = $('#all-grievances-table tbody .grievance-row[data-id="' + grievanceId + '"]');
  if (!row.length) {
    return;
  }

  const badgeClass = (status || '').toLowerCase() === 'resolved' ? 'success'
    : (status || '').toLowerCase() === 'closed' ? 'secondary'
    : (status || '').toLowerCase() === 'under review' ? 'info'
    : (status || '').toLowerCase() === 'escalated' ? 'danger'
    : 'warning';

  row.attr('data-status', (status || '').toLowerCase());
  row.find('td:nth-child(5)').html('<span class="badge badge-' + badgeClass + '">' + htmlspecialchars(status || 'Pending') + '</span>');

  if ($('.grievance-row').length) {
    filterGrievances();
  }
}

function htmlspecialchars(str) {
  if (typeof str !== 'string') {
    return '';
  }
  return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

function handleManagementFormSubmit(e) {
  e.preventDefault();

  const form = this;
  const alertBox = $('#management-form-alert');
  const submitButton = $(form).find('button[type="submit"]');
  const originalButtonHtml = submitButton.html();

  const selectedGrievance = $('#management-grievance-select').val();
  const statusValue = $(form).find('select[name="status"]').val();
  const hrRemarksValue = $(form).find('textarea[name="hr_remarks"]').val().trim();
  const resolutionValue = $(form).find('textarea[name="final_resolution"]').val().trim();

  if (!selectedGrievance || !statusValue || !hrRemarksValue || !resolutionValue) {
    alertBox.removeClass('alert-success alert-danger alert-info d-none').addClass('alert-danger').html('<i class="fas fa-exclamation-triangle"></i> Please fill all fields before saving.');
    return;
  }

  // Hide any existing top page flash alert to avoid duplicate notifications
  $('.content-wrapper .alert.alert-success, .content-wrapper .alert.alert-danger').remove();

  alertBox.removeClass('alert-success alert-danger alert-info d-none').addClass('alert-info').html('<i class="fas fa-spinner fa-spin"></i> Saving management update...');
  submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

  const formData = new FormData(form);

  fetch(window.location.href, {
    method: 'POST',
    body: formData,
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
    .then(async response => {
      const data = await response.json().catch(() => ({}));
      if (!response.ok || data.success === false) {
        throw new Error(data.message || 'Unable to save management update.');
      }
      return data;
    })
    .then(data => {
      alertBox.removeClass('alert-info alert-danger').addClass('alert-success').html('<i class="fas fa-check-circle"></i> ' + (data.message || 'Management update saved successfully.'));
      const savedGrievanceId = $('#management-grievance-id').val();
      const savedStatus = $(form).find('select[name="status"]').val();
      if (savedGrievanceId) {
        updateGrievanceRow(savedGrievanceId, savedStatus);
      }
      form.reset();
      $('#management-grievance-id').val('');
      $('#management-grievance-select').val('');
      $(form).find('select[name="status"]').val('Pending');
    })
    .catch(error => {
      alertBox.removeClass('alert-info alert-success').addClass('alert-danger').html('<i class="fas fa-exclamation-triangle"></i> ' + error.message);
    })
    .finally(() => {
      submitButton.prop('disabled', false).html(originalButtonHtml);
    });
}

function populatePayslips() {
  const employeeId = String($('#grievance-employee-select').val() || '');
  const payslipSelect = $('#grievance-payslip-select');
  const previousPayslipId = payslipSelect.val();
  const summaryBox = $('#grievance-payslip-summary');
  const hiddenField = $('#grievance-payslip-information');
  const payslips = employeeId && window.grievancePayslipsData ? (window.grievancePayslipsData[employeeId] || []) : [];

  payslipSelect.empty().append('<option value="">Select payslip (optional)</option>');
  payslipSelect.prop('disabled', true);
  hiddenField.val('');
  summaryBox.hide().html('');

  if (!employeeId) {
    summaryBox.show().html('<small class="text-muted">Select an employee to view available payslips.</small>');
    return;
  }

  if (!payslips.length) {
    summaryBox.show().html('<small class="text-muted">No payslips were found for this employee.</small>');
    return;
  }

  payslips.forEach(function(payslip) {
    const generatedDate = payslip.generated_at ? payslip.generated_at.split(' ')[0] : 'N/A';
    payslipSelect.append(
      $('<option></option>').val(payslip.id).text('Payslip #' + payslip.id + ' - ' + generatedDate + ' | Gross ' + formatCurrency(payslip.gross_pay) + ' | Net ' + formatCurrency(payslip.net_pay))
    );
  });
  payslipSelect.prop('disabled', false);

  if (previousPayslipId && payslipSelect.find('option[value="' + previousPayslipId + '"]').length) {
    payslipSelect.val(previousPayslipId);
  }

  updatePayslipSummary();
}

function updatePayslipSummary() {
  const payslipSelect = $('#grievance-payslip-select');
  const summaryBox = $('#grievance-payslip-summary');
  const hiddenField = $('#grievance-payslip-information');
  const selectedId = payslipSelect.val();
  const employeeId = String($('#grievance-employee-select').val() || '');
  const payslips = employeeId && window.grievancePayslipsData ? (window.grievancePayslipsData[employeeId] || []) : [];
  const selectedPayslip = payslips.find(function(item) { return String(item.id) === String(selectedId); });

  if (!selectedPayslip) {
    summaryBox.hide().html('');
    hiddenField.val('');
    return;
  }

  const info = 'Payslip ' + selectedPayslip.id + ': gross=' + formatCurrency(selectedPayslip.gross_pay) + ', deductions=' + formatCurrency(selectedPayslip.total_deductions) + ', net=' + formatCurrency(selectedPayslip.net_pay);
  hiddenField.val(info);
  summaryBox.show().html('<strong>Payslip Selected</strong><br>' + info);
}

function formatCurrency(value) {
  const amount = Number(value || 0);
  return '₱' + amount.toFixed(2);
}

function filterGrievances() {
  function normalize(val) {
    return String(val || '').toLowerCase().trim().replace(/[^a-z0-9 ]/g, '');
  }

  function compact(val) {
    return normalize(val).replace(/\s+/g, '');
  }

  function canonicalStatus(val) {
    const status = compact(val);
    if (status === 'submitted') {
      return 'pending';
    }
    if (status === 'investigation' || status === 'investigating') {
      return 'under review';
    }
    if (status === 'underreview') {
      return 'under review';
    }
    return status;
  }

  const statusFilter = canonicalStatus($('#status-filter').val() || '');
  const categoryFilter = normalize($('#category-filter').val() || '');
  const departmentFilter = normalize($('#department-filter').val() || '');
  const dateFilter = String($('#date-filter').val() || '').trim();
  const searchFilter = normalize($('#search-filter').val() || '');
  const sortFilter = String($('#sort-filter').val() || 'date_desc');
  const tbody = $('#all-grievances-table tbody');
  const rows = [];

  const sourceRows = window.originalGrievanceRows || $('#all-grievances-table tbody .grievance-row').toArray();
  $(sourceRows).each(function() {
    const row = $(this);
    const status = canonicalStatus(String(row.attr('data-status') || ''));
    const category = normalize(String(row.attr('data-category') || ''));
    const date = String(row.attr('data-date') || '').trim();
    const search = normalize(String(row.attr('data-search') || ''));

    const statusMatch = !statusFilter || status === statusFilter;
    const categoryMatch = !categoryFilter || category === categoryFilter;
    const department = normalize(String(row.attr('data-department') || ''));
    const departmentMatch = !departmentFilter || department === departmentFilter;
    const dateMatch = !dateFilter || date === dateFilter;
    const searchMatch = !searchFilter || search.includes(searchFilter);

    if (statusMatch && categoryMatch && departmentMatch && dateMatch && searchMatch) {
      rows.push(row);
    }
  });

  rows.sort(function(a, b) {
    const aDate = String(a.data('date') || '').trim();
    const bDate = String(b.data('date') || '').trim();
    const aSearch = String(a.data('search') || '').trim().toLowerCase();
    const bSearch = String(b.data('search') || '').trim().toLowerCase();

    if (sortFilter === 'subject_asc') {
      return aSearch.localeCompare(bSearch);
    }
    if (sortFilter === 'subject_desc') {
      return bSearch.localeCompare(aSearch);
    }
    if (sortFilter === 'date_asc') {
      return aDate.localeCompare(bDate);
    }
    return bDate.localeCompare(aDate);
  });

  tbody.empty();
  rows.forEach(function(row) {
    tbody.append(row);
  });

  if (!rows.length) {
    tbody.append('<tr class="no-results-row"><td colspan="10" class="text-center text-muted">No grievance records match the current filters.</td></tr>');
  }
}

function viewGrievanceDetails(id) {
  openGlobalModal('Grievance Details', 'grievance_detail.php?id=' + id);
}

function manageGrievance(id) {
  openGlobalModal('Manage Grievance', 'grievance_manage.php?id=' + id, refreshGrievances);
}

function generateCustomReport() {
  const type = document.getElementById('report-type').value;
  const startDate = document.getElementById('report-start-date').value;
  const endDate = document.getElementById('report-end-date').value;
  const department = document.getElementById('report-department').value;
  const category = document.getElementById('report-category').value;
  const status = document.getElementById('report-status').value;
  const employee = document.getElementById('report-employee').value.toLowerCase();
  const format = document.getElementById('report-format').value;
  const outputCard = document.getElementById('generated-report');
  const summary = document.getElementById('generated-report-summary');
  const table = document.getElementById('generated-report-table');
  const data = window.reportData || [];

  let filtered = data.slice();
  if (startDate) {
    filtered = filtered.filter(item => item.created_at && item.created_at >= startDate);
  }
  if (endDate) {
    filtered = filtered.filter(item => item.created_at && item.created_at <= endDate + ' 23:59:59');
  }
  if (department) {
    filtered = filtered.filter(item => (item.department || '').toLowerCase() === department.toLowerCase());
  }
  if (category) {
    filtered = filtered.filter(item => (item.category || '').toLowerCase() === category.toLowerCase());
  }
  if (status) {
    filtered = filtered.filter(item => (item.status || '').toLowerCase() === status.toLowerCase());
  }
  if (employee) {
    filtered = filtered.filter(item => ((item.employee_name || '') + ' ' + (item.subject || '')).toLowerCase().includes(employee));
  }

  let headers = [];
  let rows = [];
  let reportTitle = '';

  switch (type) {
    case 'summary':
      reportTitle = 'Grievance Summary Report';
      const statusCounts = {};
      filtered.forEach(item => {
        const key = item.status || 'Unknown';
        statusCounts[key] = (statusCounts[key] || 0) + 1;
      });
      summary.innerHTML = `<strong>Total Records:</strong> ${filtered.length} <br /><strong>Format:</strong> ${format.toUpperCase()}`;
      headers = ['Status', 'Count'];
      rows = Object.keys(statusCounts).map(status => [status, statusCounts[status]]);
      break;
    case 'detailed':
      reportTitle = 'Detailed Grievance Report';
      summary.innerHTML = `<strong>Total Records:</strong> ${filtered.length} <br /><strong>Format:</strong> ${format.toUpperCase()}`;
      headers = ['Subject', 'Employee', 'Category', 'Date Submitted', 'Status'];
      rows = filtered.map(item => [item.subject || '', item.employee_name || 'Unknown', item.category || 'N/A', item.created_at ? item.created_at.split(' ')[0] : 'N/A', item.status || 'N/A']);
      break;
    case 'category':
      reportTitle = 'Category Analysis Report';
      const categoryCounts = {};
      filtered.forEach(item => {
        const categoryName = item.category || 'Uncategorized';
        categoryCounts[categoryName] = (categoryCounts[categoryName] || 0) + 1;
      });
      summary.innerHTML = `<strong>Total Categories:</strong> ${Object.keys(categoryCounts).length} <br /><strong>Format:</strong> ${format.toUpperCase()}`;
      headers = ['Category', 'Count'];
      rows = Object.keys(categoryCounts).map(cat => [cat, categoryCounts[cat]]);
      break;
    case 'resolution':
      reportTitle = 'Resolution Report';
      summary.innerHTML = `<strong>Total Resolved:</strong> ${filtered.filter(item => item.status === 'Resolved' || item.status === 'Closed').length} <br /><strong>Format:</strong> ${format.toUpperCase()}`;
      headers = ['Subject', 'Status', 'Resolution', 'Resolved At'];
      rows = filtered.filter(item => item.status === 'Resolved' || item.status === 'Closed').map(item => [item.subject || '', item.status || '', item.resolution || 'N/A', item.resolved_at ? item.resolved_at.split(' ')[0] : 'N/A']);
      break;
    default:
      reportTitle = 'Custom Grievance Report';
      summary.innerHTML = `<strong>Total Records:</strong> ${filtered.length} <br /><strong>Format:</strong> ${format.toUpperCase()}`;
      headers = ['Subject', 'Status', 'Category', 'Date Submitted'];
      rows = filtered.map(item => [item.subject || '', item.status || '', item.category || 'N/A', item.created_at ? item.created_at.split(' ')[0] : 'N/A']);
      break;
  }

  const thead = table.querySelector('thead');
  const tbody = table.querySelector('tbody');
  thead.innerHTML = '<tr>' + headers.map(h => `<th>${h}</th>`).join('') + '</tr>';
  tbody.innerHTML = rows.length ? rows.map(row => `<tr>${row.map(cell => `<td>${cell}</td>`).join('')}</tr>`).join('') : `<tr><td colspan="${headers.length}" class="text-center text-muted">No records found for this report.</td></tr>`;
  outputCard.style.display = 'block';
  outputCard.querySelector('.card-title').innerText = reportTitle;
  downloadReport(reportTitle, headers, rows, format);
}

function downloadReport(reportTitle, headers, rows, format) {
  const safeName = reportTitle.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
  const filename = safeName + (format === 'excel' ? '.csv' : '.pdf');

  if (format === 'excel') {
    downloadCsv(filename, headers, rows);
    return;
  }

  downloadPdf(filename, reportTitle, headers, rows);
}

function downloadCsv(filename, headers, rows) {
  const csvLines = [headers.map(h => `"${String(h).replace(/"/g, '""')}"`).join(',')];
  rows.forEach(row => {
    csvLines.push(row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(','));
  });
  const blob = new Blob([csvLines.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.setAttribute('download', filename);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(link.href);
}

function downloadPdf(filename, title, headers, rows) {
  const jsPdfConstructor = window.jspdf?.jsPDF || window.jsPDF;
  if (typeof jsPdfConstructor !== 'function') {
    console.warn('jsPDF not loaded; falling back to CSV.');
    downloadCsv(filename.replace(/\.pdf$/i, '.csv'), headers, rows);
    return;
  }

  const doc = new jsPdfConstructor();
  doc.setFontSize(14);
  doc.text(title, 14, 20);
  const startY = 30;

  if (typeof doc.autoTable === 'function') {
    doc.autoTable({
      head: [headers],
      body: rows.length ? rows : [['No records found for this report.']],
      startY,
      styles: { fontSize: 9, cellPadding: 3 },
      headStyles: { fillColor: [41, 128, 185], textColor: 255 }
    });
  } else {
    doc.setFontSize(10);
    let y = startY;
    doc.text(headers.join(' | '), 14, y);
    y += 8;
    rows.length ? rows : [['No records found for this report.']];
    (rows.length ? rows : [['No records found for this report.']]).forEach(row => {
      if (y > 270) {
        doc.addPage();
        y = 20;
      }
      doc.text(row.map(cell => String(cell)).join(' | '), 14, y);
      y += 7;
    });
  }

  doc.save(filename);
}

function refreshGrievances() {
  location.reload();
}

