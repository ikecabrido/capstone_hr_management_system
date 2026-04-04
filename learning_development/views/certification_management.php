<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../controllers/CertificationController.php";
require_once "../../auth/user.php";
require_once "../models/Course.php";

$controller = new CertificationController();

// Show different certifications based on role
if ($_SESSION['user']['role'] === 'learning') {
    // Learning admins see all active certifications
    $allCertifications = $controller->index();
    $certifications = array_filter($allCertifications, function($cert) {
        return $cert['status'] !== 'inactive' && $cert['status'] !== 'revoked';
    });
} else {
    // Regular employees see only their own active certifications
    $allCertifications = $controller->getByEmployee($_SESSION['user']['id']);
    $certifications = array_filter($allCertifications, function($cert) {
        return $cert['status'] !== 'inactive' && $cert['status'] !== 'revoked';
    });
}

$userModel = new User();
$users = $userModel->getAllUsers();

$courseModel = new Course();
$allCourses = $courseModel->getAllCourses();
$courses = array_filter($allCourses, function($course) {
    return $course['status'] !== 'inactive';
});

$theme = $_SESSION['user']['theme'] ?? 'light';

function getStatusClass($status) {
    $statusClasses = [
        'active' => 'status-active',
        'inactive' => 'status-inactive',
        'expired' => 'status-inactive',
        'revoked' => 'status-inactive',
        'pending' => 'status-pending',
        'issued' => 'status-active'
    ];
    return $statusClasses[$status] ?? 'status-default';
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Certification Management - Learning and Development</title>

  <!-- Google Font: Source Sans Pro -->
  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <!-- Font Awesome Icons -->
  <link
    rel="stylesheet"
    href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <!-- overlayScrollbars -->
  <link
    rel="stylesheet"
    href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="../custom.css" />
  <link rel="stylesheet" href="../../layout/toast.css" />
</head>

<body
  class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed <?= $theme === 'dark' ? 'dark-mode' : '' ?>">
  <div class="wrapper">
    <!-- Preloader -->
    <div
      class="preloader flex-column justify-content-center align-items-center">
      <img
        class="animation__wobble"
        src="../../assets/pics/bcpLogo.png"
        alt="AdminLTELogo"
        height="60"
        width="60" />
    </div>

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="../learning_development.php" class="nav-link">Home</a>
        </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item">
          <div class="nav-link" id="clock">--:--:--</div>
        </li>

        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>

        <li class="nav-item">
          <a
            class="nav-link"
            href="#"
            id="darkToggle"
            role="button"
            title="Toggle Dark Mode">
            <i class="fas fa-moon" id="themeIcon"></i>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="../learning_development.php" class="brand-link">

        <img
          src="../../assets/pics/bcpLogo.png"
          alt="AdminLTE Logo"
          class="brand-image elevation-3"
          style="opacity: 0.9" />
        <span class="brand-text font-weight-light">BCP Bulacan </span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
          <div class="image">
          </div>
          <div class="info">
            <a href="#" onclick="openGlobalModal('Profile Settings ','../../user_profile/profile_form.php')" class="d-block">
              <?= htmlspecialchars($_SESSION['user']['name']) ?>
            </a>
          </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul
            class="nav nav-pills nav-sidebar flex-column"
            data-widget="treeview"
            role="menu"
            data-accordion="false">
            <li class="nav-item">
              <a href="../learning_development.php" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="browse.php" class="nav-link">
                <i class="nav-icon fas fa-book"></i>
                <p>Browse</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="certification_management.php" class="nav-link active">
                <i class="nav-icon fas fa-certificate"></i>
                <p>Certification</p>
              </a>
            </li>
            <?php if ($_SESSION['user']['role'] === 'learning'): ?>
            <li class="nav-item">
              <a href="manage_learning.php" class="nav-link">
                <i class="nav-icon fas fa-tasks"></i>
                <p>Learning Management</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="archive.php" class="nav-link">
                <i class="nav-icon fas fa-archive"></i>
                <p>Archive</p>
              </a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
              <a href="../../logout.php" class="nav-link">
                <i class="nav-icon fas fa-sign-out-alt"></i>
                <p>Logout</p>
              </a>
            </li>
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-12">
              <h1 class="m-0"><?php echo $_SESSION['user']['role'] === 'learning' ? 'Certification Management' : 'My Certifications'; ?></h1>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Success/Error Messages (Toast Notifications) -->
      <div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999; pointer-events: none;"></div>

      <!-- Script to populate toast -->
      <script>
        $(document).ready(function() {
          <?php if (isset($_SESSION['success_message'])): ?>
            var toastHtml = `
              <div class="toast" style="pointer-events: auto; margin-bottom: 10px; min-width: 300px;">
                <div class="toast-header bg-success text-white">
                  <i class="icon fas fa-check mr-2"></i>
                  <strong class="mr-auto">Success!</strong>
                  <button type="button" class="close text-white" data-dismiss="toast" aria-hidden="true">&times;</button>
                </div>
                <div class="toast-body">
                  <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
              </div>
            `;
            $('#toastContainer').append(toastHtml);
            
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
              $('#toastContainer .toast').fadeOut(500, function() {
                $(this).remove();
              });
            }, 5000);
          <?php endif; ?>

          <?php if (isset($_SESSION['error_message'])): ?>
            var toastHtml = `
              <div class="toast" style="pointer-events: auto; margin-bottom: 10px; min-width: 300px;">
                <div class="toast-header bg-danger text-white">
                  <i class="icon fas fa-ban mr-2"></i>
                  <strong class="mr-auto">Error!</strong>
                  <button type="button" class="close text-white" data-dismiss="toast" aria-hidden="true">&times;</button>
                </div>
                <div class="toast-body">
                  <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
              </div>
            `;
            $('#toastContainer').append(toastHtml);
            
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
              $('#toastContainer .toast').fadeOut(500, function() {
                $(this).remove();
              });
            }, 5000);
          <?php endif; ?>
        });
      </script>

      <script>
        function archiveCertification(certificationId) {
          if (confirm('Are you sure you want to archive this certification?')) {
            fetch('archive_certification.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: 'certification_id=' + certificationId
            })
            .then(response => {
              if (!response.ok) {
                throw new Error('HTTP ' + response.status + ': ' + response.statusText);
              }
              return response.text().then(text => {
                try {
                  return JSON.parse(text);
                } catch (e) {
                  throw new Error('Invalid JSON response: ' + text.substring(0, 200));
                }
              });
            })
            .then(data => {
              if (data.success) {
                alert('Certification archived successfully!');
                location.reload();
              } else {
                alert('Error archiving certification: ' + data.message);
              }
            })
            .catch(error => {
              console.error('Archive error:', error);
              alert('Error archiving certification: ' + error.message);
            });
          }
        }

        function revokeCertification(certificationId) {
          if (confirm('Are you sure you want to revoke this certification?')) {
            fetch('revoke_certification.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: 'certification_id=' + certificationId
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                alert(data.message);
                location.reload();
              } else {
                alert('Error revoking certification: ' + data.message);
              }
            })
            .catch(error => {
              console.error('Revoke error:', error);
              alert('Error revoking certification: ' + error.message);
            });
          }
        }

        // Data map for modal view/edit; populated via PHP JSON encode
        const certificationData = <?php echo json_encode(array_column($certifications, null, 'ld_certification_id')); ?>;

        function viewCertification(certificationId) {
          const cert = certificationData[certificationId];
          if (!cert) {
            alert('Certification details not available.');
            return;
          }

          $('#certificateModalLabel').text(cert.certification_name || 'Certificate Details');
          $('#viewCertName').text(cert.certification_name || 'N/A');
          $('#viewCertEmployee').text(cert.employee_name || 'N/A');
          $('#viewCertCourse').text(cert.course_title || 'N/A');
          $('#viewCertIssuedBy').text(cert.issued_by_name || 'N/A');
          $('#viewCertIssuedDate').text(cert.issued_date ? new Date(cert.issued_date).toLocaleDateString() : 'N/A');
          $('#viewCertExpiryDate').text(cert.expiry_date ? new Date(cert.expiry_date).toLocaleDateString() : 'Never');
          $('#viewCertStatus').text(cert.status || 'N/A');

          $('#viewCertificationModal').modal('show');
        }

        function printCertificate() {
          // Create a new window for printing
          const printWindow = window.open('', '_blank');
          const cert = certificationData[Object.keys(certificationData).find(id => certificationData[id].certification_name === $('#viewCertName').text())];
          
          if (!cert) {
            alert('Certificate data not available for printing.');
            return;
          }

          const printContent = `
            <!DOCTYPE html>
            <html>
            <head>
              <title>Certificate of Completion</title>
              <style>
                body {
                  font-family: 'Times New Roman', serif;
                  text-align: center;
                  padding: 50px;
                  background: white;
                  color: #333;
                }
                .certificate {
                  border: 5px solid #007bff;
                  padding: 40px;
                  max-width: 800px;
                  margin: 0 auto;
                  background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
                }
                .header {
                  font-size: 36px;
                  font-weight: bold;
                  margin-bottom: 20px;
                  color: #007bff;
                }
                .title {
                  font-size: 28px;
                  margin: 20px 0;
                  font-weight: bold;
                }
                .recipient {
                  font-size: 24px;
                  margin: 20px 0;
                  font-style: italic;
                }
                .details {
                  font-size: 18px;
                  margin: 15px 0;
                }
                .signature {
                  margin-top: 50px;
                  font-size: 16px;
                }
                .date {
                  margin-top: 30px;
                  font-size: 16px;
                }
                @media print {
                  body { padding: 20px; }
                  .certificate { border-width: 3px; }
                }
              </style>
            </head>
            <body>
              <div class="certificate">
                <div class="header">Certificate of Completion</div>
                <div class="title">This is to certify that</div>
                <div class="recipient">${cert.employee_name || 'N/A'}</div>
                <div class="title">has successfully completed the course</div>
                <div class="recipient">${cert.course_title || 'N/A'}</div>
                <div class="details">Certification: ${cert.certification_name || 'N/A'}</div>
                <div class="details">Issued Date: ${cert.issued_date ? new Date(cert.issued_date).toLocaleDateString() : 'N/A'}</div>
                <div class="details">Expiry Date: ${cert.expiry_date ? new Date(cert.expiry_date).toLocaleDateString() : 'Never'}</div>
                <div class="signature">
                  Issued by: ${cert.issued_by_name || 'N/A'}
                </div>
                <div class="date">
                  Date: ${new Date().toLocaleDateString()}
                </div>
              </div>
            </body>
            </html>
          `;

          printWindow.document.write(printContent);
          printWindow.document.close();
          printWindow.focus();
          
          // Wait for content to load then print
          setTimeout(() => {
            printWindow.print();
            printWindow.close();
          }, 500);
        }

        function editCertification(certificationId) {
          const cert = certificationData[certificationId];
          if (!cert) {
            alert('Certification data not available.');
            return;
          }

          $('#editCertificationId').val(cert.ld_certification_id);
          $('#editEmployeeId').val(cert.employee_id);
          $('#editCourseId').val(cert.course_id);
          $('#editCertificationName').val(cert.certification_name);
          $('#editIssuedDate').val(cert.issued_date);
          $('#editExpiryDate').val(cert.expiry_date);
          $('#editIssuedBy').val(cert.issued_by);
          $('#editStatus').val(cert.status || 'active');

          $('#editCertificationModal').modal('show');
        }
      </script>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <?php if ($_SESSION['user']['role'] === 'learning'): ?>
          <div class="row mb-3">
            <div class="col-12">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#issueCertificationModal">
                <i class="fas fa-plus"></i> Issue New Certification
              </button>
            </div>
          </div>
          <?php endif; ?>

          <div class="row">
            <?php if (empty($certifications)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> No Certifications Found</h5>
                        <?php if ($_SESSION['user']['role'] === 'learning'): ?>
                            No certifications have been issued yet. Use the form below to issue your first certification.
                        <?php else: ?>
                            You haven't received any certifications yet. Contact your learning administrator to enroll in courses.
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <?php
                // Array of local placeholder GIFs
                $placeholderGifs = [
                    '../img/cover-placeholder/cover-placeholder-1.gif',
                    '../img/cover-placeholder/cover-placeholder-2.gif',
                    '../img/cover-placeholder/cover-placeholder-3.gif',
                    '../img/cover-placeholder/cover-placeholder-4.gif',
                    '../img/cover-placeholder/cover-placeholder-5.gif',
                    '../img/cover-placeholder/cover-placeholder-6.gif',
                    '../img/cover-placeholder/cover-placeholder-7.gif',
                    '../img/cover-placeholder/cover-placeholder-8.gif',
                    '../img/cover-placeholder/cover-placeholder-9.gif',
                    '../img/cover-placeholder/cover-placeholder-10.gif',
                    '../img/cover-placeholder/cover-placeholder-11.gif',
                    '../img/cover-placeholder/cover-placeholder-12.gif',
                    '../img/cover-placeholder/cover-placeholder-13.gif',
                    '../img/cover-placeholder/cover-placeholder-14.gif',
                    '../img/cover-placeholder/cover-placeholder-15.gif',
                    '../img/cover-placeholder/cover-placeholder-16.gif',
                    '../img/cover-placeholder/cover-placeholder-17.gif',
                    '../img/cover-placeholder/cover-placeholder-18.gif',
                    '../img/cover-placeholder/cover-placeholder-19.gif',
                    '../img/cover-placeholder/cover-placeholder-20.gif'
                ];
                ?>
                <?php foreach ($certifications as $cert): ?>
                    <?php
                    // Use random placeholder GIF for cover
                    $coverUrl = $placeholderGifs[array_rand($placeholderGifs)];
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card certification-card">
                            <!-- Status Badge -->
                            <div class="status-badge">
                                <span class="badge-custom <?php echo getStatusClass($cert['status']); ?>"><?php echo ucfirst($cert['status']); ?></span>
                            </div>

                            <!-- Cover Photo -->
                            <div class="card-img-top" style="height: 200px; overflow: hidden;">
                                <img src="<?php echo htmlspecialchars($coverUrl); ?>" alt="Certification Cover" class="w-100 h-100" style="object-fit: cover;">
                            </div>

                            <!-- Card Body with Information -->
                            <div class="card-body card-info">
                                <div class="card-content">
                                    <h5 class="card-title"><?php echo htmlspecialchars($cert['certification_name']); ?></h5>

                                    <!-- Certification Description -->
                                    <div class="enrollment-description mb-3">
                                        <p class="description-text">Official certification for completed course training.</p>
                                    </div>

                                    <!-- Certification Info Grid -->
                                    <div class="info-grid">
                                        <div class="info-row">
                                            <div class="info-item">
                                                <i class="fas fa-graduation-cap text-primary"></i>
                                                <span class="info-label">Course</span>
                                                <span class="info-value"><?php echo htmlspecialchars($cert['course_title'] ?? 'N/A'); ?></span>
                                            </div>
                                            <?php if (isset($cert['employee_name'])): ?>
                                            <div class="info-item">
                                                <i class="fas fa-user text-info"></i>
                                                <span class="info-label">Employee</span>
                                                <span class="info-value"><?php echo htmlspecialchars($cert['employee_name']); ?></span>
                                            </div>
                                            <?php else: ?>
                                            <div class="info-item full-width">
                                                <i class="fas fa-user text-info"></i>
                                                <span class="info-label">Employee</span>
                                                <span class="info-value"><?php echo htmlspecialchars($cert['employee_name'] ?? 'N/A'); ?></span>
                                            </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="info-row">
                                            <div class="info-item">
                                                <i class="fas fa-calendar-check text-success"></i>
                                                <span class="info-label">Issued</span>
                                                <span class="info-value"><?php echo date('M d, Y', strtotime($cert['issued_date'])); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-calendar-times text-warning"></i>
                                                <span class="info-label">Expires</span>
                                                <span class="info-value"><?php echo $cert['expiry_date'] ? date('M d, Y', strtotime($cert['expiry_date'])) : 'Never'; ?></span>
                                            </div>
                                        </div>

                                        <div class="info-row">
                                            <?php if (isset($cert['issued_by_name'])): ?>
                                            <div class="info-item">
                                                <i class="fas fa-user-tie text-muted"></i>
                                                <span class="info-label">Issued by</span>
                                                <span class="info-value"><?php echo htmlspecialchars($cert['issued_by_name']); ?></span>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-actions mt-auto">
                                    <button class="btn btn-info btn-sm" onclick="viewCertification(<?php echo $cert['ld_certification_id']; ?>)">
                                        <i class="fas fa-eye"></i> View Certificate
                                    </button>
                                    <?php if ($_SESSION['user']['role'] === 'learning' || $_SESSION['user']['role'] === 'admin'): ?>
                                        <button class="btn btn-secondary btn-sm ml-1" onclick="editCertification(<?php echo $cert['ld_certification_id']; ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($_SESSION['user']['role'] === 'learning' && $cert['status'] === 'active'): ?>
                                        <button class="btn btn-warning btn-sm ml-1" onclick="archiveCertification(<?php echo $cert['ld_certification_id']; ?>)">
                                            <i class="fas fa-archive"></i> Archive
                                        </button>
                                        <button class="btn btn-danger btn-sm ml-1" onclick="revokeCertification(<?php echo $cert['ld_certification_id']; ?>)">
                                            <i class="fas fa-ban"></i> Revoke
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <!-- View Certification Modal -->
          <div class="modal fade" id="viewCertificationModal" tabindex="-1" role="dialog" aria-labelledby="certificateModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="certificateModalLabel">Certificate Details</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <dl class="row">
                    <dt class="col-sm-4">Certification</dt>
                    <dd class="col-sm-8" id="viewCertName"></dd>
                    <dt class="col-sm-4">Employee</dt>
                    <dd class="col-sm-8" id="viewCertEmployee"></dd>
                    <dt class="col-sm-4">Course</dt>
                    <dd class="col-sm-8" id="viewCertCourse"></dd>
                    <dt class="col-sm-4">Issued By</dt>
                    <dd class="col-sm-8" id="viewCertIssuedBy"></dd>
                    <dt class="col-sm-4">Issued</dt>
                    <dd class="col-sm-8" id="viewCertIssuedDate"></dd>
                    <dt class="col-sm-4">Expires</dt>
                    <dd class="col-sm-8" id="viewCertExpiryDate"></dd>
                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8" id="viewCertStatus"></dd>
                  </dl>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary" onclick="printCertificate()">
                    <i class="fas fa-print"></i> Print Certificate
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Edit Certification Modal -->
          <div class="modal fade" id="editCertificationModal" tabindex="-1" role="dialog" aria-labelledby="editCertificationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editCertificationModalLabel">Edit Certification</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form id="editCertificationForm" action="process_update_certification.php?v=<?php echo time(); ?>" method="POST">
                    <input type="hidden" id="editCertificationId" name="id">
                    <div class="form-group">
                      <label for="editEmployeeId">Employee</label>
                      <select class="form-control" id="editEmployeeId" name="employee_id" required>
                        <option value="">Select Employee</option>
                        <?php foreach ($users as $user): ?>
                          <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['full_name'] . ' (' . $user['role'] . ')'); ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="editCourseId">Course</label>
                      <select class="form-control" id="editCourseId" name="course_id" required>
                        <option value="">Select Course</option>
                        <?php foreach ($courses as $course): ?>
                          <option value="<?php echo $course['ld_courses_id']; ?>"><?php echo htmlspecialchars($course['title']); ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="editCertificationName">Certification Name</label>
                      <input type="text" class="form-control" id="editCertificationName" name="certification_name" required>
                    </div>
                    <div class="form-group">
                      <label for="editIssuedDate">Issued Date</label>
                      <input type="date" class="form-control" id="editIssuedDate" name="issued_date" required>
                    </div>
                    <div class="form-group">
                      <label for="editExpiryDate">Expiry Date</label>
                      <input type="date" class="form-control" id="editExpiryDate" name="expiry_date">
                    </div>
                    <div class="form-group">
                      <label for="editStatus">Status</label>
                      <select class="form-control" id="editStatus" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="expired">Expired</option>
                        <option value="revoked">Revoked</option>
                      </select>
                    </div>
                    <input type="hidden" id="editIssuedBy" name="issued_by" value="<?php echo $_SESSION['user']['id']; ?>">
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" form="editCertificationForm" class="btn btn-primary">Update Certification</button>
                </div>
              </div>
            </div>
          </div>

          <?php if ($_SESSION['user']['role'] === 'learning'): ?>
          <!-- Issue Certification Modal -->
          <div class="modal fade" id="issueCertificationModal" tabindex="-1" role="dialog" aria-labelledby="issueCertificationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="issueCertificationModalLabel">Issue New Certification</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form id="issueCertificationForm" action="process_issue_certification.php?v=<?php echo time(); ?>" method="POST">
                    <div class="form-group">
                      <label for="employee_id">Employee</label>
                      <select class="form-control" id="employee_id" name="employee_id" required>
                        <option value="">Select Employee</option>
                        <?php foreach ($users as $user): ?>
                          <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['full_name'] . ' (' . $user['role'] . ')'); ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="course_id">Course</label>
                      <select class="form-control" id="course_id" name="course_id" required>
                        <option value="">Select Course</option>
                        <?php foreach ($courses as $course): ?>
                          <option value="<?php echo $course['ld_courses_id']; ?>"><?php echo htmlspecialchars($course['title']); ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="certification_name">Certification Name</label>
                      <input type="text" class="form-control" id="certification_name" name="certification_name" required>
                    </div>
                    <div class="form-group">
                      <label for="issued_date">Issued Date</label>
                      <input type="date" class="form-control" id="issued_date" name="issued_date" required>
                    </div>
                    <div class="form-group">
                      <label for="expiry_date">Expiry Date</label>
                      <input type="date" class="form-control" id="expiry_date" name="expiry_date">
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" form="issueCertificationForm" class="btn btn-primary">Issue Certification</button>
                </div>
              </div>
            </div>
          </div>
          <?php endif; ?>
        </div>
        <!--/. container-fluid -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <?php include "../../layout/global_modal.php"; ?>
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

  </div>
  <!-- ./wrapper -->

  <!-- REQUIRED SCRIPTS -->
  <!-- jQuery -->
  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="../../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../../assets/dist/js/adminlte.js"></script>

  <!-- PAGE PLUGINS -->
  <!-- jQuery Mapael -->
  <script src="../../assets/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
  <script src="../../assets/plugins/raphael/raphael.min.js"></script>
  <script src="../../assets/plugins/jquery-mapael/jquery.mapael.min.js"></script>
  <script src="../../assets/plugins/jquery-mapael/maps/usa_states.min.js"></script>
  <!-- ChartJS -->
  <script src="../../assets/plugins/chart.js/Chart.min.js"></script>

  <!-- AdminLTE for demo purposes -->
  <!-- <script src="assets/dist/js/demo.js"></script> -->
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <!-- <script src="assets/dist/js/pages/dashboard2.js"></script> -->
  <script src="../../assets/dist/js/theme.js"></script>
  <script src="../../assets/dist/js/time.js"></script>
  <script src="../../assets/dist/js/global_modal.js"></script>
  <script src="../../assets/dist/js/profile.js"></script>

</body>

</html>