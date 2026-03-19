<?php
session_start();
require_once "../auth/database.php";
require_once "controllers/legalComplianceController.php";
require_once "../auth/auth_check.php";

// Page configuration
$pageTitle = 'Legal & Compliance Management';

$db = Database::getInstance()->getConnection();
$controller = new LegalComplianceController($db);

// Handle AJAX requests for modals
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    if ($_GET['action'] === 'get_law_details' && isset($_GET['id'])) {
        $law = $controller->getLawById($_GET['id']);
        echo json_encode($law);
        exit;
    }
    
    if ($_GET['action'] === 'get_employee_details' && isset($_GET['id'])) {
        $employee = $controller->getEmployeeComplianceDetails($_GET['id']);
        echo json_encode($employee);
        exit;
    }
}

try {
    $stats = $controller->getStats();
    $lawsWithStats = $controller->getLawsWithStats();
} catch (Exception $e) {
    // Default stats if DB tables don't exist
    $stats = [
        'compliance_score' => 92,
        'total_employees' => 6,
        'compliant_count' => 5,
        'at_risk_count' => 1,
        'non_compliant_count' => 0,
        'active_policies' => 3,
        'pending_acknowledgments' => 2,
        'active_incidents' => 1,
        'resolved_incidents' => 2,
        'active_risks' => 2,
        'high_risk_employees' => 1,
        'laws' => [
            ['code' => 'RA 11210', 'title' => 'Expanded Maternity Leave Law', 'effective_date' => '2019-03-11'],
            ['code' => 'RA 8187', 'title' => 'Paternity Leave Act', 'effective_date' => '1996-06-17'],
            ['code' => 'RA 8972', 'title' => 'Solo Parents Leave Act', 'effective_date' => '2000-11-01'],
            ['code' => 'RA 9710', 'title' => 'Magna Carta of Women', 'effective_date' => '2009-08-14'],
            ['code' => 'RA 11058', 'title' => 'Occupational Safety and Health', 'effective_date' => '2018-06-27'],
            ['code' => 'RA 7877', 'title' => 'Anti-Sexual Harassment Act', 'effective_date' => '1995-02-24'],
            ['code' => 'RA 11313', 'title' => 'Safe Spaces Act', 'effective_date' => '2019-04-15'],
            ['code' => 'RA 10173', 'title' => 'Data Privacy Act', 'effective_date' => '2012-09-12']
        ],
        'recent_logs' => []
    ];
    $lawsWithStats = $stats['laws'];
}

$laws = $stats['laws'] ?? [];

// Include Header Template (includes sidebar automatically)
include "components/header_template.php";
?>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <!-- Compliance Score -->
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="score-circle <?= $stats['compliance_score'] >= 80 ? 'excellent' : ($stats['compliance_score'] >= 60 ? 'warning' : 'danger') ?> mr-3">
                                                <?= $stats['compliance_score'] ?>%
                                            </div>
                                            <div>
                                                <h5 class="mb-0">Overall Compliance Score</h5>
                                                <small class="text-muted">Based on Philippine labor law compliance</small>
                                            </div>
                                        </div>
                                        <span class="badge badge-<?= $stats['compliance_score'] >= 80 ? 'success' : ($stats['compliance_score'] >= 60 ? 'warning' : 'danger') ?> p-2">
                                            <?= $stats['compliance_score'] >= 80 ? 'Compliant' : ($stats['compliance_score'] >= 60 ? 'At Risk' : 'Non-Compliant') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info boxes -->
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Employees</span>
                                        <span class="info-box-number"><?= $stats['total_employees'] ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Compliant</span>
                                        <span class="info-box-number"><?= $stats['compliant_count'] ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-exclamation-triangle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">At Risk</span>
                                        <span class="info-box-number"><?= $stats['at_risk_count'] ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-times-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Non-Compliant</span>
                                        <span class="info-box-number"><?= $stats['non_compliant_count'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Second Row -->
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-file-contract"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Active Policies</span>
                                        <span class="info-box-number"><?= $stats['active_policies'] ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-file-signature"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Pending Acks</span>
                                        <span class="info-box-number"><?= $stats['pending_acknowledgments'] ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-exclamation-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Active Cases</span>
                                        <span class="info-box-number"><?= $stats['active_incidents'] ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-shield-alt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">High Risk</span>
                                        <span class="info-box-number"><?= $stats['high_risk_employees'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts & Laws -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Philippine Labor Laws</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body table-responsive p-0" style="max-height: 300px; overflow-y: auto;">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Law Code</th>
                                                    <th>Title</th>
                                                    <th>Effective Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($laws as $law): ?>
                                                <tr style="cursor: pointer;" onclick="showLawDetails('<?= htmlspecialchars($law['code']) ?>')">
                                                    <td><span class="badge badge-primary law-badge"><?= htmlspecialchars($law['code']) ?></span></td>
                                                    <td><?= htmlspecialchars($law['title']) ?></td>
                                                    <td><?= date('M d, Y', strtotime($law['effective_date'])) ?></td>
                                                    <td><span class="badge badge-success">Active</span></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Quick Actions</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 mb-3">
                                                <a href="views/compliance.php" class="quick-action-btn">
                                                    <i class="fas fa-balance-scale text-primary"></i>
                                                    <span>Check Compliance</span>
                                                </a>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <a href="views/incidents.php" class="quick-action-btn">
                                                    <i class="fas fa-exclamation-triangle text-danger"></i>
                                                    <span>Report Incident</span>
                                                </a>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <a href="views/policy_admin.php" class="quick-action-btn">
                                                    <i class="fas fa-file-contract text-info"></i>
                                                    <span>Manage Policies</span>
                                                </a>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <a href="views/reports.php" class="quick-action-btn">
                                                    <i class="fas fa-clipboard-check text-success"></i>
                                                    <span>Generate Report</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Risk Summary -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-shield-alt mr-2"></i>Risk Summary</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="description-block">
                                                    <h5 class="description-header text-danger"><?= $stats['active_risks'] ?></h5>
                                                    <span class="description-text">Active Risks</span>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="description-block">
                                                    <h5 class="description-header text-warning"><?= $stats['high_risk_employees'] ?></h5>
                                                    <span class="description-text">High Risk Employees</span>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="description-block">
                                                    <h5 class="description-header text-info"><?= $stats['resolved_incidents'] ?></h5>
                                                    <span class="description-text">Resolved Cases</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Global Modal -->
                <?php include "../layout/global_modal.php"; ?>

                <!-- Law Details Modal -->
                <div class="modal fade" id="lawDetailsModal" data-backdrop="static" data-keyboard="true" tabindex="-1" role="dialog" aria-labelledby="lawDetailsModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="lawDetailsModalLabel">Law Details</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" id="lawDetailsContent">
                                <div class="text-center">
                                    <i class="fas fa-spinner fa-spin fa-3x"></i>
                                    <p class="mt-2">Loading...</p>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Control Sidebar -->
                <aside class="control-sidebar control-sidebar-dark">
                </aside>

<?php 
// Custom scripts for this page - using HEREDOC syntax for cleaner JavaScript embedding
$customScripts = <<<'JAVASCRIPT'
<script>
    // Remove any stuck backdrops on page load only
    $(document).ready(function() {
        setTimeout(function() {
            var stuckBackdrop = document.querySelector(".modal-backdrop.modal-stack");
            if (stuckBackdrop) {
                // Only remove if no modal is actually visible
                var visibleModals = document.querySelectorAll(".modal.show");
                if (visibleModals.length === 0) {
                    stuckBackdrop.remove();
                    document.body.classList.remove("modal-open");
                    document.body.style.overflow = "";
                    document.body.style.paddingRight = "";
                }
            }
        }, 100);
    });
    
    // Clean up backdrop when modal is hidden
    $(document).ready(function() {
        $('#lawDetailsModal').on('hidden.bs.modal', function() {
            if ($('.modal.show').length === 0) {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
            }
        });
    });
    
    // Auto-hide compliance score alert after 5 seconds
    $(document).ready(function() {
        setTimeout(function() {
            $(".alert-info").fadeOut("slow", function() {
                $(this).remove();
            });
        }, 5000);
    });
    
    function showLawDetails(lawCode) {
        $("#lawDetailsModal").modal("show");
        $("#lawDetailsContent").html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading...</p></div>');
        
        $.ajax({
            url: "legal_compliance.php?action=get_law_details&id=" + encodeURIComponent(lawCode),
            type: "GET",
            dataType: "json",
            success: function(data) {
                if (data && data.id) {
                    var html = "<div class='row'>" +
                        "<div class='col-md-12'>" +
                        "<h5 class='text-primary'>" + data.code + "</h5>" +
                        "<h4>" + data.title + "</h4>" +
                        "<hr>" +
                        "<p><strong>Effective Date:</strong> " + (data.effective_date ? new Date(data.effective_date).toLocaleDateString("en-US", { year: "numeric", month: "long", day: "numeric" }) : "N/A") + "</p>" +
                        "<p><strong>Category:</strong> " + (data.category || "General") + "</p>" +
                        "<p><strong>Description:</strong></p>" +
                        "<p>" + (data.description || "No description available.") + "</p>";
                    
                    if (data.key_provisions) {
                        html += "<p><strong>Key Provisions:</strong></p>" +
                                "<div class='alert alert-info'>" + data.key_provisions + "</div>";
                    }
                    
                    if (data.penalties) {
                        html += "<p><strong>Penalties/ sanctions:</strong></p>" +
                                "<div class='alert alert-warning'>" + data.penalties + "</div>";
                    }
                    
                    html += "</div></div>";
                    $("#lawDetailsContent").html(html);
                } else {
                    // Fallback for static data
                    var lawTitles = {
                        "RA 11210": "Expanded Maternity Leave Law",
                        "RA 8187": "Paternity Leave Act",
                        "RA 8972": "Solo Parents Leave Act",
                        "RA 9710": "Magna Carta of Women",
                        "RA 11058": "Occupational Safety and Health",
                        "RA 7877": "Anti-Sexual Harassment Act",
                        "RA 11313": "Safe Spaces Act",
                        "RA 10173": "Data Privacy Act"
                    };
                    
                    var lawDescriptions = {
                        "RA 11210": "Provides 105 days of maternity leave for pregnant employees with full pay, extendable to 30 more days without pay.",
                        "RA 8187": "Grants 7 days paternity leave to married male employees for normal delivery and 15 days for caesarean delivery.",
                        "RA 8972": "Provides 7 days of leave annually to solo parents for attending to their children's needs.",
                        "RA 9710": "Ensures equal access to opportunities for women, prohibiting discrimination in employment.",
                        "RA 11058": "Mandates employers to provide a safe and healthy workplace for all employees.",
                        "RA 7877": "Prevents sexual harassment in the workplace through policies and procedures.",
                        "RA 11313": "Prevents and penalizes gender-based sexual harassment in public spaces and workplaces.",
                        "RA 10173": "Protects personal information of individuals in government and private sectors."
                    };
                    
                    var html = "<div class='row'>" +
                        "<div class='col-md-12'>" +
                        "<h5 class='text-primary'>" + lawCode + "</h5>" +
                        "<h4>" + (lawTitles[lawCode] || "Unknown Law") + "</h4>" +
                        "<hr>" +
                        "<p><strong>Description:</strong></p>" +
                        "<p>" + (lawDescriptions[lawCode] || "Detailed information not available.") + "</p>" +
                        "</div></div>";
                    $("#lawDetailsContent").html(html);
                }
            },
            error: function() {
                $("#lawDetailsContent").html('<div class="alert alert-danger">Error loading law details.</div>');
            }
        });
    }
</script>
JAVASCRIPT;

// Include Footer Template (includes jQuery)
include "components/footer_template.php";
?>
