<?php
/**
 * Policy API - Handles AJAX requests
 */
require_once "../../auth/database.php";
require_once "policyWorkflowController.php";

header('Content-Type: application/json');

$controller = new PolicyWorkflowController();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'get':
        // Get single policy
        $id = $_GET['id'] ?? $_POST['id'] ?? null;
        if ($id) {
            $policy = $controller->getPolicy($id);
            if ($policy) {
                echo json_encode($policy);
            } else {
                echo json_encode(['error' => 'Policy not found']);
            }
        } else {
            echo json_encode(['error' => 'Policy ID required']);
        }
        break;
        
    case 'list':
        // Get all policies
        $status = $_GET['status'] ?? null;
        $policies = $controller->getAllPolicies($status);
        echo json_encode($policies);
        break;
        
    case 'categories':
        // Get categories
        $categories = $controller->getCategories();
        echo json_encode($categories);
        break;
        
    case 'stats':
        // Get compliance stats
        $policyId = $_GET['policy_id'] ?? null;
        if ($policyId) {
            $stats = $controller->getPolicyComplianceStats($policyId);
            echo json_encode($stats);
        } else {
            $stats = $controller->getAllComplianceStats();
            echo json_encode($stats);
        }
        break;
        
    case 'acknowledgments':
        // Get policy acknowledgments
        $policyId = $_GET['policy_id'] ?? null;
        if ($policyId) {
            $acks = $controller->getPolicyAcknowledgments($policyId);
            echo json_encode($acks);
        } else {
            echo json_encode(['error' => 'Policy ID required']);
        }
        break;
        
    case 'pending':
        // Get pending employees
        $policyId = $_GET['policy_id'] ?? null;
        if ($policyId) {
            $pending = $controller->getPendingAcknowledgmentEmployees($policyId);
            echo json_encode($pending);
        } else {
            echo json_encode(['error' => 'Policy ID required']);
        }
        break;
        
    case 'versions':
        // Get policy versions
        $policyId = $_GET['policy_id'] ?? null;
        if ($policyId) {
            $versions = $controller->getPolicyVersions($policyId);
            echo json_encode($versions);
        } else {
            echo json_encode(['error' => 'Policy ID required']);
        }
        break;
        
    case 'history':
        // Get approval history
        $policyId = $_GET['policy_id'] ?? null;
        if ($policyId) {
            $history = $controller->getApprovalHistory($policyId);
            echo json_encode($history);
        } else {
            echo json_encode(['error' => 'Policy ID required']);
        }
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
