<?php
session_start();
require_once "../../auth/database.php";
require_once "../controllers/legalComplianceController.php";
require_once "../../auth/auth_check.php";

$db = Database::getInstance()->getConnection();
$controller = new LegalComplianceController($db);

$userId = $_SESSION['user']['id'] ?? 0;

// Get policy ID from URL if viewing specific policy
$policyId = $_GET['id'] ?? null;
$viewMode = $_GET['view'] ?? 'list';

// Initialize variables
$acknowledgments = [];
$message = '';
$messageType = '';

try {
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

    $policies = $controller->getPolicies();

    // Get user's acknowledgment status
    $ackStmt = $db->prepare("SELECT policy_id, acknowledged_at FROM policy_acknowledgments 
                              WHERE user_id = ? AND acknowledged = 'yes'");
    $ackStmt->execute([$userId]);
    $acknowledgments = $ackStmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
} catch (PDOException $e) {
    // Table might not exist - set empty arrays
    $policies = $controller->getPolicies() ?? [];
    $acknowledgments = [];
    $message = 'Please run the policy SQL setup first.';
    $messageType = 'warning';
}

// Get specific policy if ID provided
$selectedPolicy = null;
if ($policyId) {
    try {
        $policyStmt = $db->prepare("SELECT * FROM policies WHERE id = ? AND is_active = 1");
        $policyStmt->execute([$policyId]);
        $selectedPolicy = $policyStmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $selectedPolicy = null;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Policies | BCP</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
    <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
    <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
    <link rel="stylesheet" href="../custom.css" />
    <style>
        .policy-card {
            border-left: 4px solid #007bff;
            margin-bottom: 15px;
            padding: 15px;
            background: #fff;
            border-radius: 4px;
            transition: all 0.3s;
        }
        .policy-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .policy-card.acknowledged {
            border-left-color: #28a745;
        }
        .policy-content {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin-top: 20px;
            white-space: pre-wrap;
            font-family: inherit;
            line-height: 1.8;
            font-size: 14px;
        }
        .policy-content .policy-header {
            color: #007bff;
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }
        .policy-content .section-title {
            color: #28a745;
            font-weight: bold;
            font-size: 1rem;
            margin-top: 15px;
            margin-bottom: 8px;
        }
        .ack-badge {
            font-size: 12px;
        }
        .policy-version {
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>

<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed">
    <div class="wrapper">
        <?php include "../components/sidebar.php"; ?>
        
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Policy Acknowledgment</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../legal_compliance.php">Home</a></li>
                                <li class="breadcrumb-item active">Policies</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($selectedPolicy && $viewMode === 'view'): ?>
                        <!-- View Single Policy -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-file-contract mr-2"></i>
                                    <?php echo htmlspecialchars($selectedPolicy['title']); ?>
                                </h3>
                                <a href="policies.php" class="btn btn-sm btn-secondary float-right">
                                    <i class="fas fa-arrow-left"></i> Back to All Policies
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <span class="badge badge-info"><?php echo htmlspecialchars($selectedPolicy['category']); ?></span>
                                    <span class="badge badge-secondary ml-2">Version <?php echo htmlspecialchars($selectedPolicy['version']); ?></span>
                                    <?php if (isset($acknowledgments[$selectedPolicy['id']])): ?>
                                        <span class="badge badge-success ack-badge ml-2">
                                            <i class="fas fa-check"></i> Acknowledged on <?php echo date('M d, Y', strtotime($acknowledgments[$selectedPolicy['id']])); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="policy-content">
                                    <?php echo htmlspecialchars($selectedPolicy['content']); ?>
                                </div>

                                <?php if (!isset($acknowledgments[$selectedPolicy['id']])): ?>
                                    <form method="POST" class="mt-4">
                                        <input type="hidden" name="policy_id" value="<?php echo $selectedPolicy['id']; ?>">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="acknowledge" name="acknowledged" value="yes" required>
                                                <label class="custom-control-label" for="acknowledge">
                                                    <strong>I have read and understood this policy</strong>
                                                </label>
                                            </div>
                                        </div>
                                        <button type="submit" name="acknowledge_policy" class="btn btn-primary">
                                            <i class="fas fa-check"></i> Acknowledge Policy
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <div class="alert alert-success mt-3">
                                        <i class="fas fa-check-circle"></i> <strong>You have acknowledged this policy.</strong>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Policy List -->
                        <div class="row">
                            <?php foreach ($policies as $policy): ?>
                                <div class="col-md-6">
                                    <div class="policy-card <?php echo isset($acknowledgments[$policy['id']]) ? 'acknowledged' : ''; ?>">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="mb-1"><?php echo htmlspecialchars($policy['title']); ?></h5>
                                                <span class="policy-version">Version <?php echo htmlspecialchars($policy['version']); ?></span>
                                                <span class="badge badge-info ml-2"><?php echo ucfirst($policy['category']); ?></span>
                                            </div>
                                            <div>
                                                <?php if ($policy['is_active']): ?>
                                                <span class="badge badge-success">Active</span>
                                                <?php else: ?>
                                                <span class="badge badge-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <?php if (isset($acknowledgments[$policy['id']])): ?>
                                                <span class="text-success">
                                                    <i class="fas fa-check-circle"></i> Acknowledged on <?php echo date('M d, Y', strtotime($acknowledgments[$policy['id']])); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-warning">
                                                    <i class="fas fa-exclamation-circle"></i> Pending Acknowledgment
                                                </span>
                                            <?php endif; ?>
                                            <a href="policies.php?id=<?php echo $policy['id']; ?>&view=view" class="btn btn-sm btn-primary float-right">
                                                <i class="fas fa-eye"></i> View & Acknowledge
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($policies)): ?>
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> No policies found. Please contact HR to add company policies.
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>

    <script src="../../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <script src="../../assets/dist/js/adminlte.js"></script>
    <script src="../../assets/dist/js/time.js"></script>
</body>
</html>
