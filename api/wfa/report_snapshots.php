<?php
/**
 * Report Snapshots Management API
 * Manages storage and retrieval of historical report snapshots
 */

header('Content-Type: application/json');
error_reporting(0);

$host = 'localhost';
$db = 'hr_management';
$user = 'root';
$pass = '';

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    $action = $_GET['action'] ?? $_POST['action'] ?? 'list';
    
    switch ($action) {
        case 'list':
            // Get all snapshots with pagination
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            $offset = ($page - 1) * $limit;
            $type = $_GET['type'] ?? '';
            
            $whereClause = $type ? "WHERE snapshot_type = '" . $conn->real_escape_string($type) . "'" : '';
            
            $sql = "SELECT * FROM wfa_report_snapshots $whereClause ORDER BY snapshot_date DESC LIMIT $limit OFFSET $offset";
            $result = $conn->query($sql);
            
            $snapshots = [];
            while ($row = $result->fetch_assoc()) {
                $snapshots[] = $row;
            }
            
            // Get total count
            $countSql = "SELECT COUNT(*) as total FROM wfa_report_snapshots $whereClause";
            $countResult = $conn->query($countSql);
            $countRow = $countResult->fetch_assoc();
            
            echo json_encode([
                'success' => true,
                'data' => $snapshots,
                'total' => $countRow['total'],
                'page' => $page,
                'limit' => $limit
            ]);
            break;
            
        case 'save':
            // Save a new snapshot
            $data = json_decode(file_get_contents('php://input'), true);
            
            $type = $conn->real_escape_string($data['type']);
            $name = $conn->real_escape_string($data['name']);
            $totalEmps = $data['total_employees'] ?? 0;
            $activeEmps = $data['active_employees'] ?? 0;
            $inactiveEmps = $data['inactive_employees'] ?? 0;
            $attritionRate = $data['attrition_rate'] ?? 0;
            $avgTenure = $data['average_tenure'] ?? 0;
            $deptCount = $data['department_count'] ?? 0;
            $posCount = $data['position_count'] ?? 0;
            $snapshotDataJson = json_encode($data['snapshot_data'] ?? []);
            $createdBy = $conn->real_escape_string($data['created_by'] ?? 'System');
            $notes = $conn->real_escape_string($data['notes'] ?? '');
            
            $sql = "INSERT INTO wfa_report_snapshots 
                    (snapshot_type, snapshot_name, total_employees, active_employees, 
                     inactive_employees, attrition_rate, average_tenure, department_count, 
                     position_count, snapshot_data, created_by, notes)
                    VALUES 
                    ('$type', '$name', $totalEmps, $activeEmps, $inactiveEmps, 
                     $attritionRate, $avgTenure, $deptCount, $posCount, 
                     '$snapshotDataJson', '$createdBy', '$notes')";
            
            if (!$conn->query($sql)) {
                throw new Exception("Insert failed: " . $conn->error);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Snapshot saved successfully',
                'snapshot_id' => $conn->insert_id
            ]);
            break;
            
        case 'get':
            // Get specific snapshot
            $id = intval($_GET['id'] ?? 0);
            
            if ($id <= 0) {
                throw new Exception("Invalid snapshot ID");
            }
            
            $sql = "SELECT * FROM wfa_report_snapshots WHERE snapshot_id = $id";
            $result = $conn->query($sql);
            
            if ($result->num_rows === 0) {
                throw new Exception("Snapshot not found");
            }
            
            $snapshot = $result->fetch_assoc();
            $snapshot['snapshot_data'] = json_decode($snapshot['snapshot_data'], true);
            
            echo json_encode([
                'success' => true,
                'data' => $snapshot
            ]);
            break;
            
        case 'delete':
            // Delete snapshot
            $id = intval($_GET['id'] ?? 0);
            
            if ($id <= 0) {
                throw new Exception("Invalid snapshot ID");
            }
            
            $sql = "DELETE FROM wfa_report_snapshots WHERE snapshot_id = $id";
            
            if (!$conn->query($sql)) {
                throw new Exception("Delete failed: " . $conn->error);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Snapshot deleted successfully'
            ]);
            break;
            
        case 'compare':
            // Compare two snapshots
            $id1 = intval($_GET['id1'] ?? 0);
            $id2 = intval($_GET['id2'] ?? 0);
            
            if ($id1 <= 0 || $id2 <= 0) {
                throw new Exception("Invalid snapshot IDs");
            }
            
            $sql = "SELECT * FROM wfa_report_snapshots WHERE snapshot_id IN ($id1, $id2)";
            $result = $conn->query($sql);
            
            $snapshots = [];
            while ($row = $result->fetch_assoc()) {
                $row['snapshot_data'] = json_decode($row['snapshot_data'], true);
                $snapshots[] = $row;
            }
            
            if (count($snapshots) !== 2) {
                throw new Exception("One or both snapshots not found");
            }
            
            $comparison = [
                'snapshot1' => $snapshots[0],
                'snapshot2' => $snapshots[1],
                'changes' => [
                    'employee_change' => $snapshots[1]['total_employees'] - $snapshots[0]['total_employees'],
                    'active_change' => $snapshots[1]['active_employees'] - $snapshots[0]['active_employees'],
                    'attrition_change' => round($snapshots[1]['attrition_rate'] - $snapshots[0]['attrition_rate'], 2),
                    'tenure_change' => round($snapshots[1]['average_tenure'] - $snapshots[0]['average_tenure'], 1)
                ]
            ];
            
            echo json_encode([
                'success' => true,
                'data' => $comparison
            ]);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'error' => 'Unknown action'
            ]);
    }
    
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
