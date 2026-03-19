<?php
/**
 * Policy Acknowledgment System
 * 
 * Allows employees to view and acknowledge company policies
 * Tracks acknowledgment history in the database
 * Refactored version with improved structure and features
 */

require_once "../auth/Auth.php";
require_once "../auth/database.php";

// Check authentication
$auth = new Auth();
if (!$auth->check()) {
    header("Location: ../login_form.php");
    exit;
}

$user = $_SESSION['user'];
$userId = $user['id'] ?? 0;
$userRole = $user['role'] ?? 'employee';

// Database connection
$db = Database::getInstance()->getConnection();

// Initialize variables
$message = '';
$messageType = '';
$selectedPolicy = null;
$policies = [];
$acknowledgments = [];

// Get filter parameters
$categoryFilter = $_GET['category'] ?? 'all';
$searchQuery = $_GET['search'] ?? '';

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    if ($_GET['action'] === 'get_policy' && isset($_GET['id'])) {
        $stmt = $db->prepare("SELECT * FROM policies WHERE id = ? AND is_active = 1");
        $stmt->execute([$_GET['id']]);
        $policy = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($policy);
        exit;
    }
    
    if ($_GET['action'] === 'get_acknowledgments') {
        $stmt = $db->prepare("SELECT policy_id, acknowledged_at FROM policy_acknowledgments WHERE user_id = ? AND acknowledged = 'yes'");
        $stmt->execute([$userId]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_KEY_PAIR));
        exit;
    }
    
    if ($_GET['action'] === 'get_categories') {
        $stmt = $db->query("SELECT DISTINCT category FROM policies WHERE is_active = 1 ORDER BY category");
        echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));
        exit;
    }
    
    if ($_GET['action'] === 'acknowledge') {
        $policyId = $_POST['policy_id'] ?? null;
        
        if (!$policyId) {
            echo json_encode(['success' => false, 'message' => 'Policy ID is required']);
            exit;
        }
        
        // Check if already acknowledged
        $checkStmt = $db->prepare("SELECT id FROM policy_acknowledgments WHERE user_id = ? AND policy_id = ? AND acknowledged = 'yes'");
        $checkStmt->execute([$userId, $policyId]);
        
        if ($checkStmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'You have already acknowledged this policy']);
            exit;
        }
        
        // Insert acknowledgment
        $insertStmt = $db->prepare("INSERT INTO policy_acknowledgments (user_id, policy_id, acknowledged, acknowledged_at) VALUES (?, ?, 'yes', NOW())");
        if ($insertStmt->execute([$userId, $policyId])) {
            echo json_encode(['success' => true, 'message' => 'Policy acknowledged successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error saving acknowledgment']);
        }
        exit;
    }
    
    // Handle delete policy (admin only)
    if ($_GET['action'] === 'delete_policy' && isset($_GET['id'])) {
        // Check if user is admin
        if ($userRole !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        $policyId = $_GET['id'];
        $deleteStmt = $db->prepare("UPDATE policies SET is_active = 0 WHERE id = ?");
        if ($deleteStmt->execute([$policyId])) {
            echo json_encode(['success' => true, 'message' => 'Policy deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting policy']);
        }
        exit;
    }
}

// Handle policy acknowledgment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acknowledge_policy'])) {
    $policyIdPost = $_POST['policy_id'] ?? null;
    $acknowledged = $_POST['acknowledged'] ?? 'no';
    
    if ($policyIdPost && $acknowledged === 'yes') {
        // Check if already acknowledged
        $checkStmt = $db->prepare("SELECT id FROM policy_acknowledgments 
                                    WHERE user_id = ? AND policy_id = ? 
                                    AND acknowledged = 'yes'");
        $checkStmt->execute([$userId, $policyIdPost]);
        
        if ($checkStmt->fetch()) {
            $message = 'You have already acknowledged this policy.';
            $messageType = 'info';
        } else {
            // Insert acknowledgment
            $insertStmt = $db->prepare("INSERT INTO policy_acknowledgments 
                                        (user_id, policy_id, acknowledged, acknowledged_at) 
                                        VALUES (?, ?, 'yes', NOW())");
            if ($insertStmt->execute([$userId, $policyIdPost])) {
                $message = 'Thank you! You have successfully acknowledged this policy.';
                $messageType = 'success';
            } else {
                $message = 'Error saving acknowledgment. Please try again.';
                $messageType = 'danger';
            }
        }
    }
}

// Get all policies with optional filtering
try {
    $sql = "SELECT * FROM policies WHERE is_active = 1";
    $params = [];
    
    if ($categoryFilter !== 'all' && !empty($categoryFilter)) {
        $sql .= " AND category = ?";
        $params[] = $categoryFilter;
    }
    
    if (!empty($searchQuery)) {
        $sql .= " AND (title LIKE ? OR content LIKE ?)";
        $params[] = "%$searchQuery%";
        $params[] = "%$searchQuery%";
    }
    
    $sql .= " ORDER BY category, title";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $policies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $policies = [];
}

// Get user's acknowledgment status
try {
    $ackStmt = $db->prepare("SELECT policy_id, acknowledged_at FROM policy_acknowledgments 
                              WHERE user_id = ? AND acknowledged = 'yes'");
    $ackStmt->execute([$userId]);
    $acknowledgments = $ackStmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    $acknowledgments = [];
}

// Get available categories
$categories = [];
try {
    $catStmt = $db->query("SELECT DISTINCT category FROM policies WHERE is_active = 1 ORDER BY category");
    $categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $categories = [];
}

// Get specific policy if ID provided
$policyId = $_GET['id'] ?? null;
if ($policyId) {
    try {
        $policyStmt = $db->prepare("SELECT * FROM policies WHERE id = ? AND is_active = 1");
        $policyStmt->execute([$policyId]);
        $selectedPolicy = $policyStmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $selectedPolicy = null;
    }
}

// Calculate stats
$stats = [
    'total' => count($policies),
    'acknowledged' => 0,
    'pending' => 0
];

foreach ($policies as $policy) {
    if (isset($acknowledgments[$policy['id']])) {
        $stats['acknowledged']++;
    } else {
        $stats['pending']++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Policies - Bestlink College HR</title>
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
    <style>
        .policy-card {
            border-left: 4px solid #007bff;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .policy-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        .policy-card.acknowledged {
            border-left-color: #28a745;
        }
        .policy-content {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin-top: 15px;
            white-space: pre-wrap;
            font-family: inherit;
            line-height: 1.8;
            font-size: 14px;
        }
        .policy-content h3 {
            color: #007bff;
            font-size: 1.3rem;
            margin-top: 20px;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #007bff;
        }
        .policy-content h4 {
            color: #28a745;
            font-size: 1.1rem;
            margin-top: 15px;
            margin-bottom: 10px;
        }
        .policy-content p {
            margin-bottom: 12px;
        }
        .policy-content ul, .policy-content ol {
            margin-bottom: 15px;
            padding-left: 25px;
        }
        .policy-content li {
            margin-bottom: 5px;
        }
        .ack-badge {
            font-size: 12px;
        }
        .stats-card {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        .stats-card h3 {
            margin: 0;
            font-size: 2rem;
            font-weight: bold;
        }
        .stats-card p {
            margin: 5px 0 0 0;
            font-size: 0.9rem;
        }
        .filter-btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .search-box {
            max-width: 300px;
        }
        .policy-preview {
            max-height: 80px;
            overflow: hidden;
            position: relative;
        }
        .policy-preview::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            background: linear-gradient(transparent, #fff);
        }
        /* Disabled button styles */
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed !important;
        }
        a.btn:disabled {
            pointer-events: none;
        }
        /* Action column styles */
        .action-buttons .btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .action-buttons .btn:last-child {
            margin-right: 0;
        }
        /* Table styles */
        .policy-table th {
            background-color: #f8f9fa;
        }
        .policy-table .status-acknowledged {
            color: #28a745;
            font-weight: bold;
        }
        .policy-table .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .policy-table .status-draft {
            color: #6c757d;
            font-weight: bold;
        }
        .policy-table .status-published {
            color: #28a745;
            font-weight: bold;
        }
        .policy-table .status-rejected {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">
    <?php include '../layout/navbar.php'; ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Company Policies</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container">
                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="stats-card bg-primary text-white">
                            <h3><?php echo $stats['total']; ?></h3>
                            <p>Total Policies</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card bg-success text-white">
                            <h3><?php echo $stats['acknowledged']; ?></h3>
                            <p>Acknowledged</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card bg-warning text-white">
                            <h3><?php echo $stats['pending']; ?></h3>
                            <p>Pending</p>
                        </div>
                    </div>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <?php if ($selectedPolicy): ?>
                    <!-- View Single Policy -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-alt mr-2"></i>
                                <?php echo htmlspecialchars($selectedPolicy['title']); ?>
                            </h3>
                            <a href="policy.php" class="btn btn-sm btn-secondary float-right">
                                <i class="fas fa-arrow-left"></i> Back to All Policies
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <span class="badge badge-info"><?php echo htmlspecialchars($selectedPolicy['category']); ?></span>
                                <span class="badge badge-secondary">Version <?php echo htmlspecialchars($selectedPolicy['version']); ?></span>
                                <?php if (isset($acknowledgments[$selectedPolicy['id']])): ?>
                                    <span class="badge badge-success ack-badge">
                                        <i class="fas fa-check"></i> Acknowledged on <?php echo date('M d, Y', strtotime($acknowledgments[$selectedPolicy['id']])); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="policy-content">
                                <?php echo nl2br(htmlspecialchars($selectedPolicy['content'])); ?>
                            </div>

                            <?php if (!isset($acknowledgments[$selectedPolicy['id']])): ?>
                                <form method="POST" class="mt-4" id="acknowledgeForm">
                                    <input type="hidden" name="policy_id" value="<?php echo $selectedPolicy['id']; ?>">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="acknowledge" name="acknowledged" value="yes" required>
                                            <label class="custom-control-label" for="acknowledge">
                                                I have read and understood this policy
                                            </label>
                                        </div>
                                    </div>
                                    <button type="submit" name="acknowledge_policy" class="btn btn-primary" disabled>
                                        <i class="fas fa-check"></i> Acknowledge Policy
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="alert alert-success mt-3">
                                    <i class="fas fa-check-circle"></i> You have acknowledged this policy.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Filters and Search -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="btn-group">
                                        <a href="?category=all" class="btn btn-sm btn-outline-primary <?php echo $categoryFilter === 'all' ? 'active' : ''; ?>">All</a>
                                        <?php foreach ($categories as $cat): ?>
                                            <a href="?category=<?php echo urlencode($cat); ?>" class="btn btn-sm btn-outline-primary <?php echo $categoryFilter === $cat ? 'active' : ''; ?>">
                                                <?php echo htmlspecialchars($cat); ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <form method="GET" class="search-box float-right">
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control" placeholder="Search policies..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                                            <?php if ($categoryFilter !== 'all'): ?>
                                            <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoryFilter); ?>">
                                            <?php endif; ?>
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Policy List as Table with Action Column -->
                    <?php if (empty($policies)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No policies found. Please contact HR to add company policies.
                        </div>
                    <?php else: ?>
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                <table class="table table-bordered table-striped policy-table">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Version</th>
                                            <th>Status</th>
                                            <th>Acknowledged Date</th>
                                            <th style="width: 280px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($policies as $policy): ?>
                                            <?php 
                                            $isAcknowledged = isset($acknowledgments[$policy['id']]);
                                            $ackDate = $isAcknowledged ? date('M d, Y', strtotime($acknowledgments[$policy['id']])) : '-';
                                            
                                            // Determine policy status (default to 'draft' if not set)
                                            $policyStatus = isset($policy['status']) ? strtolower($policy['status']) : 'draft';
                                            
                                            // Check if Edit/Delete should be enabled
                                            $canEdit = in_array($policyStatus, ['draft', 'pending']);
                                            $canDelete = in_array($policyStatus, ['draft', 'pending']);
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($policy['title']); ?></td>
                                                <td><span class="badge badge-info"><?php echo htmlspecialchars($policy['category']); ?></span></td>
                                                <td><?php echo htmlspecialchars($policy['version']); ?></td>
                                                <td>
                                                    <?php if ($isAcknowledged): ?>
                                                        <span class="status-acknowledged"><i class="fas fa-check-circle"></i> Acknowledged</span>
                                                    <?php else: ?>
                                                        <span class="status-pending"><i class="fas fa-exclamation-circle"></i> Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $ackDate; ?></td>
                                                <td class="action-buttons">
                                                    <!-- View Button - Always enabled -->
                                                    <a href="policy.php?id=<?php echo $policy['id']; ?>" class="btn btn-sm btn-info" title="View">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    
                                                    <!-- Edit Button - Enabled for draft/pending, disabled for published/rejected -->
                                                    <?php if ($canEdit): ?>
                                                        <a href="policy.php?edit=<?php echo $policy['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-sm btn-warning" title="Edit (Disabled for <?php echo ucfirst($policyStatus); ?>)" disabled>
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Submit/Acknowledge Button - Enabled when pending, disabled when acknowledged -->
                                                    <?php if ($isAcknowledged): ?>
                                                        <button type="button" class="btn btn-sm btn-success" title="Already Acknowledged" disabled>
                                                            <i class="fas fa-check"></i> Submitted
                                                        </button>
                                                    <?php else: ?>
                                                        <a href="policy.php?id=<?php echo $policy['id']; ?>" class="btn btn-sm btn-primary" title="Submit/Acknowledge">
                                                            <i class="fas fa-paper-plane"></i> Submit
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Delete Button - Enabled for draft/pending, disabled for published/rejected -->
                                                    <?php if ($canDelete): ?>
                                                        <button type="button" class="btn btn-sm btn-danger" 
                                                                onclick="deletePolicy(<?php echo $policy['id']; ?>)" 
                                                                title="Delete">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-sm btn-danger" 
                                                                title="Delete (Disabled for <?php echo ucfirst($policyStatus); ?>)" disabled>
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include '../layout/footer.php'; ?>
</div>

<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/dist/js/adminlte.min.js"></script>
<script>
// Global cleanup - Remove any leftover modal backdrops when page loads
$(document).ready(function() {
    // Force cleanup of ALL modal artifacts on page load
    setTimeout(function() {
        $('.modal-backdrop').each(function() {
            $(this).remove();
        });
        $('body').removeClass('modal-open');
        $('body').css({'overflow': '', 'padding-right': ''});
        console.log('Policy page cleanup - all modal backdrops removed');
    }, 100);
    
    // Handle acknowledgment form submission via AJAX
    $('#acknowledgeForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: 'policy.php?action=acknowledge',
            type: 'POST',
            data: formData,
            success: function(response) {
                try {
                    var result = typeof response === 'string' ? JSON.parse(response) : response;
                    if (result.success) {
                        alert('Thank you! You have successfully acknowledged this policy.');
                        location.reload();
                    } else {
                        alert(result.message || 'Error saving acknowledgment');
                    }
                } catch (e) {
                    // If not JSON, reload the page
                    location.reload();
                }
            },
            error: function() {
                alert('Error saving acknowledgment. Please try again.');
            }
        });
    });
});

// Delete policy function
function deletePolicy(policyId) {
    if (confirm('Are you sure you want to delete this policy? This action cannot be undone.')) {
        $.ajax({
            url: 'policy.php?action=delete_policy&id=' + policyId,
            type: 'GET',
            success: function(response) {
                try {
                    var result = typeof response === 'string' ? JSON.parse(response) : response;
                    if (result.success) {
                        alert('Policy deleted successfully.');
                        location.reload();
                    } else {
                        alert(result.message || 'Error deleting policy');
                    }
                } catch (e) {
                    location.reload();
                }
            },
            error: function() {
                alert('Error deleting policy. Please try again.');
            }
        });
    }
}
</script>
</body>
</html>
