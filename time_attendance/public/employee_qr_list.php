<?php
/**
 * Employee QR List - separate tab for viewing and printing employee QR codes
 */
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/models/Employee.php';
require_once __DIR__ . '/../app/core/Session.php';

Session::start();

if (!AuthController::isAuthenticated()) {
    header('Location: ' . dirname(__DIR__) . '/../../login_form.php');
    exit;
}

if (!AuthController::hasRole('time')) {
    header('Location: ' . dirname(__DIR__) . '/../../employee_dashboard.php');
    exit;
}

$employeeModel = new Employee();
$activeEmployees = $employeeModel->getAll('Active');
$departmentList = [];
foreach ($activeEmployees as $emp) {
    $department = trim((string) ($emp['department'] ?? ''));
    if ($department !== '' && !in_array($department, $departmentList, true)) {
        $departmentList[] = $department;
    }
}
sort($departmentList, SORT_STRING);

$current_page = 'employee_qr_list.php';
$current_role = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? 'time';

$page_title = 'Employee QR';
$page_head_extra = "<link rel=\"stylesheet\" href=\"../assets/style.css\">\n<link rel=\"stylesheet\" href=\"../assets/hr-template.css\">\n<link rel=\"stylesheet\" href=\"../assets/adminlte-overrides.css\">\n<style>
  .stats-grid {
    display: grid;
    gap: 20px;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    margin-bottom: 24px;
  }

  .stat-card {
    position: relative;
    display: flex;
    align-items: center;
    gap: 16px;
    background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
    border: 1px solid rgba(13, 71, 161, 0.08);
    border-radius: 18px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    padding: 18px 20px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 36px rgba(15, 23, 42, 0.12);
  }

  .stat-card .info-box-icon {
    width: 54px;
    height: 54px;
    min-width: 54px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 16px;
    color: #fff;
    font-size: 22px;
    box-shadow: inset 0 -4px 12px rgba(0, 0, 0, 0.12);
  }

  .stat-card .info-box-icon.bg-primary {
    background: linear-gradient(135deg, #0d47a1 0%, #1976d2 100%);
  }

  .stat-card .info-box-icon.bg-success {
    background: linear-gradient(135deg, #2e7d32 0%, #43a047 100%);
  }

  .stat-card .info-box-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
    min-width: 0;
  }

  .stat-card .info-box-text {
    color: #64748b;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    line-height: 1.2;
  }

  .stat-card .info-box-number {
    font-size: 26px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
  }

  .records-table table thead,
  .records-table table th {
    background: linear-gradient(135deg, #003d82, #0066cc) !important;
    color: #ffffff !important;
  }

  body.dark-mode .stat-card,
  body.dark-mode .filter-section,
  body.dark-mode .records-table {
    background: #1e1e1e !important;
    color: #e5e7eb !important;
    border-color: #3f4650 !important;
  }

  body.dark-mode .stat-card .info-box-text,
  body.dark-mode .filter-section label {
    color: #c3d0df !important;
  }

  body.dark-mode .stat-card .info-box-number,
  body.dark-mode .records-table td,
  body.dark-mode .records-table th,
  body.dark-mode .filter-section input {
    color: #f3f4f6 !important;
  }

  body.dark-mode .filter-section input,
  body.dark-mode .filter-section select,
  body.dark-mode .form-control {
    background: #292929 !important;
    border-color: #596675 !important;
    color: #f3f4f6 !important;
  }

  body.dark-mode .filter-section input::placeholder {
    color: #aeb9c6 !important;
  }

  body.dark-mode .records-table table thead,
  body.dark-mode .records-table table th {
    background: #263b55 !important;
    color: #f3f4f6 !important;
  }

  body.dark-mode .records-table table td {
    background: #1e1e1e !important;
    border-color: #3f4650 !important;
  }

  body.dark-mode .records-table table tbody tr:hover td {
    background: #2a3440 !important;
  }

  body.dark-mode .modal-content {
    background: #1e1e1e !important;
    color: #f3f4f6 !important;
    border-color: #46515e !important;
  }

  body.dark-mode .modal-header,
  body.dark-mode .modal-footer {
    background: #263b55 !important;
    border-color: #46515e !important;
  }

  body.dark-mode .modal-title,
  body.dark-mode #empQrName {
    color: #f3f4f6 !important;
  }

  body.dark-mode .qr-preview-shell {
    background: #f8fafc !important;
  }

  .main-header.navbar,
  .brand-link {
    background: #1976d2 !important;
    color: #ffffff !important;
  }

  .main-header.navbar .nav-link,
  .brand-link .brand-text {
    color: #ffffff !important;
  }
</style>";
?>
<?php require_once __DIR__ . '/../layout/page_start.php'; ?>
<?php require_once __DIR__ . '/../layout/sidebar.php'; ?>
<?php $page_title = 'Employee QR'; $page_subtitle = 'View and print employee QR codes'; $page_icon = 'fa-qrcode'; ?>
<?php require_once __DIR__ . '/../layout/content_header.php'; ?>

<div class="stats-grid">
  <div class="stat-card">
    <div class="info-box-icon bg-primary">
      <i class="fas fa-user-friends"></i>
    </div>
    <div class="info-box-content">
      <span class="info-box-text">Total Employees</span>
      <span class="info-box-number"><?= count($activeEmployees) ?></span>
    </div>
  </div>
  <div class="stat-card">
    <div class="info-box-icon bg-success">
      <i class="fas fa-building"></i>
    </div>
    <div class="info-box-content">
      <span class="info-box-text">Departments</span>
      <span class="info-box-number"><?= count($departmentList) ?></span>
    </div>
  </div>
</div>

<div class="filter-section">
  <div class="form-group mb-0">
    <label class="font-weight-bold">Search employee</label>
    <input id="empSearch" type="search" class="form-control" placeholder="Search by name, employee no, or department...">
  </div>
</div>

<div class="records-table">
  <table id="empTable">
    <thead>
      <tr>
        <th>#</th>
        <th>Employee No</th>
        <th>Name</th>
        <th>Department</th>
        <th class="text-center">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($activeEmployees as $i => $emp): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= htmlspecialchars($emp['employee_no'] ?? $emp['employee_id']) ?></td>
          <td><?= htmlspecialchars($emp['full_name']) ?></td>
          <td><?= htmlspecialchars($emp['department'] ?? '') ?></td>
          <td class="text-center">
            <button class="btn btn-primary btn-sm viewQrBtn" data-id="<?= htmlspecialchars($emp['employee_id']) ?>" data-name="<?= htmlspecialchars($emp['full_name']) ?>">
              <i class="fas fa-qrcode mr-1"></i>View QR
            </button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="modal fade" id="empQrModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-qrcode mr-2"></i>Employee QR</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body text-center">
        <div class="qr-preview-shell rounded bg-light p-3 d-inline-block">
          <div id="empQrcode"></div>
        </div>
        <h5 id="empQrName" class="mt-3 mb-0"></h5>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success" id="printEmpQr"><i class="fas fa-print mr-1"></i>Print</button>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__. '/../layout/content_footer.php';?>
<?php require_once __DIR__. '/../layout/page_end.php';?>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
  const employees = <?= json_encode($activeEmployees); ?>;
  let currentQr = null;

  function renderQrFor(id, name) {
    const container = document.getElementById('empQrcode');
    container.innerHTML = '';
    currentQr = new QRCode(container, {
      text: String(id),
      width: 220,
      height: 220,
      correctLevel: QRCode.CorrectLevel.H,
      colorDark: '#0d47a1',
      colorLight: '#ffffff'
    });
    document.getElementById('empQrName').textContent = name;
    $('#empQrModal').modal('show');
  }

  function performPrint() {
    const content = document.getElementById('empQrcode').innerHTML;
    const name = document.getElementById('empQrName').textContent;
    const win = window.open('', '', 'width=420,height=560');
    win.document.write(`
      <html>
        <head>
          <title>Print QR</title>
          <style>
            body {
              margin: 0;
              min-height: 100vh;
              display: flex;
              flex-direction: column;
              align-items: center;
              justify-content: center;
              font-family: Arial, sans-serif;
              background: #f8fbff;
              color: #0d47a1;
            }
            .qr-wrap {
              text-align: center;
              padding: 18px;
              border-radius: 10px;
              background: #ffffff;
              box-shadow: 0 6px 18px rgba(13, 71, 161, 0.12);
            }
            h3 {
              margin: 10px 0 0;
            }
          </style>
        </head>
        <body>
          <div class="qr-wrap">
            ${content}
            <h3>${name}</h3>
          </div>
        </body>
      </html>
    `);
    win.document.close();
    win.focus();
    win.print();
  }

  document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.viewQrBtn').forEach(btn => {
      btn.addEventListener('click', () => {
        renderQrFor(btn.dataset.id, btn.dataset.name);
      });
    });

    const searchInput = document.getElementById('empSearch');
    const tableRows = document.querySelectorAll('#empTable tbody tr');

    searchInput.addEventListener('input', function() {
      const term = this.value.trim().toLowerCase();
      tableRows.forEach(tr => {
        const text = tr.textContent.toLowerCase();
        tr.style.display = text.includes(term) ? '' : 'none';
      });
    });

    document.getElementById('printEmpQr').addEventListener('click', performPrint);
  });
</script>
