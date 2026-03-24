<?php
session_start();
require_once "../../auth/database.php";
require_once "../controllers/legalComplianceController.php";


// Page configuration
$pageTitle = 'Compliance Monitoring';
$currentPage = 'Compliance';

$db = Database::getInstance()->getConnection();
$controller = new LegalComplianceController($db);

// Handle AJAX requests for modals
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    $action = $_GET['action'];
    
    if ($action === 'get_law_details' && isset($_GET['id'])) {
        $law = $controller->getLawById($_GET['id']);
        echo json_encode(['success' => true, 'data' => $law]);
        exit;
    }
    
    if ($action === 'get_employee_details' && isset($_GET['employee_id'])) {
        $employee = $controller->getEmployeeComplianceDetails($_GET['employee_id']);
        echo json_encode(['success' => true, 'data' => $employee]);
        exit;
    }
    
    if ($action === 'get_risk_flag_details' && (isset($_GET['flag_id']) || isset($_GET['id']))) {
        $flagId = $_GET['flag_id'] ?? $_GET['id'];
        $riskFlag = $controller->getRiskFlagById($flagId);
        echo json_encode(['success' => true, 'data' => $riskFlag]);
        exit;
    }
    
    if ($action === 'resolve_risk_flag' && (isset($_GET['flag_id']) || isset($_GET['id']))) {
        $flagId = $_GET['flag_id'] ?? $_GET['id'];
        $result = $controller->resolveRiskFlag($flagId);
        echo json_encode(['success' => $result]);
        exit;
    }
    
    if ($action === 'escalate_risk_flag' && (isset($_GET['flag_id']) || isset($_GET['id']))) {
        $flagId = $_GET['flag_id'] ?? $_GET['id'];
        $notes = $_POST['notes'] ?? '';
        $result = $controller->escalateRiskFlag($flagId, $notes);
        echo json_encode(['success' => $result]);
        exit;
    }
    
    if ($action === 'get_employee_detailed' && isset($_GET['employee_id'])) {
        $details = $controller->getEmployeeDetailedCompliance($_GET['employee_id']);
        echo json_encode(['success' => true, 'data' => $details]);
        exit;
    }
    
    if ($action === 'send_reminder' && isset($_GET['employee_id'])) {
        $subject = $_POST['subject'] ?? 'Compliance Reminder - Action Required';
        $message = $_POST['message'] ?? 'Please complete your pending compliance requirements.';
        $result = $controller->sendReminder($_GET['employee_id'], $message, $subject);
        echo json_encode(['success' => $result]);
        exit;
    }
    
    // Default response for unknown actions
    echo json_encode(['success' => false, 'message' => 'Unknown action']);
    exit;
}

// Get dashboard data
$stats = $controller->getDashboardStats();
$categories = $controller->getCategories();
$riskFlags = $controller->getRiskFlags();
$currentFilter = $_GET['filter'] ?? 'all';
$employees = $controller->getFilteredEmployees($currentFilter);
$laws = $controller->getPhilippineLaws();

// Calculate overall stats
$totalEmployees = $stats['total_employees'] ?? 0;
$compliantCount = $stats['compliant_count'] ?? 0;
$atRiskCount = $stats['at_risk_count'] ?? 0;
$nonCompliantCount = $stats['non_compliant_count'] ?? 0;
$overallScore = $stats['overall_score'] ?? 0;
$criticalIssues = $stats['critical_issues'] ?? 0;
$highRisks = $stats['high_risks'] ?? 0;
$pendingAcks = $stats['pending_acks'] ?? 0;
$activeCases = $stats['active_cases'] ?? 0;
$categoryScores = $stats['category_scores'] ?? [];

// Include Header Template (from views folder, go up to legal_compliance, then components)
include "../components/header_template.php";
?>

<!-- Load external CSS -->
<link rel="stylesheet" href="compliance.css">

<!-- Main content starts here -->
<?php 
// Include the HTML template
// Pass current filter to template
$GLOBALS['currentFilter'] = $currentFilter;
include "compliance_template.php";
?>

<?php
// Include Footer Template (loads jQuery and Bootstrap JS first)
include "../components/footer_template.php";
?>

<!-- Load compliance JavaScript after jQuery and Bootstrap are loaded -->
<script src="compliance.js"></script>
