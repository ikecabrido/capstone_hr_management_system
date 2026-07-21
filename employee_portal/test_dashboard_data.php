<?php
/**
 * Dashboard Data Fetch Test
 * Tests whether leave balances, attendance, and leave requests load correctly
 */

require_once 'app/config/Database.php';
require_once 'app/models/Leave.php';
require_once 'app/models/Attendance.php';
require_once 'app/models/Employee.php';

echo "=============================================================\n";
echo "  DASHBOARD DATA FETCH TEST\n";
echo "=============================================================\n\n";

// Test Database Connection
echo "[TEST 1] Database Connection\n";
try {
    $database = new Database();
    $conn = $database->getConnection();
    echo "✅ Database connected successfully\n\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n\n";
    exit;
}

// Test with Employee ID 1 (John Doe)
$test_employee_id = 1;

// Test 1: Leave Balances
echo "[TEST 2] Leave Balances\n";
try {
    $leaveModel = new Leave();
    $leave_balances = $leaveModel->getLeaveBalances($test_employee_id);
    
    if (is_array($leave_balances) && !empty($leave_balances)) {
        echo "✅ Leave balances fetched successfully\n";
        echo "   Records found: " . count($leave_balances) . "\n";
        foreach ($leave_balances as $balance) {
            echo "   • " . $balance['leave_type_name'] . ": " . 
                 $balance['remaining_days'] . "/" . $balance['total_days'] . " days\n";
        }
    } else {
        echo "⚠️  No leave balances found for employee ID " . $test_employee_id . "\n";
        echo "   Available data type: " . gettype($leave_balances) . "\n";
        if (empty($leave_balances)) {
            echo "   (This is expected if no balances have been allocated)\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error fetching leave balances: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 2: Monthly Attendance
echo "[TEST 3] Monthly Attendance\n";
try {
    $attendanceModel = new Attendance();
    $monthly_attendance = $attendanceModel->getMonthlyAttendance($test_employee_id);
    
    if (is_array($monthly_attendance) && !empty($monthly_attendance)) {
        echo "✅ Monthly attendance fetched successfully\n";
        echo "   Records found: " . count($monthly_attendance) . "\n";
        foreach (array_slice($monthly_attendance, 0, 3) as $record) {
            $date = isset($record['attendance_date']) ? $record['attendance_date'] : 'N/A';
            $timeIn = isset($record['time_in']) ? substr($record['time_in'], 11) : 'N/A';
            $timeOut = isset($record['time_out']) ? substr($record['time_out'], 11) : 'Not yet';
            echo "   • " . $date . ": IN=" . $timeIn . " OUT=" . $timeOut . "\n";
        }
    } else {
        echo "⚠️  No attendance records found for this month\n";
        echo "   Available data type: " . gettype($monthly_attendance) . "\n";
    }
} catch (Exception $e) {
    echo "❌ Error fetching monthly attendance: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Leave Requests
echo "[TEST 4] Leave Requests\n";
try {
    $leaveModel = new Leave();
    $leave_requests = $leaveModel->getLeaveRequestsByEmployee($test_employee_id);
    
    if (is_array($leave_requests) && !empty($leave_requests)) {
        echo "✅ Leave requests fetched successfully\n";
        echo "   Records found: " . count($leave_requests) . "\n";
        foreach (array_slice($leave_requests, 0, 3) as $req) {
            $type = isset($req['leave_type_name']) ? $req['leave_type_name'] : 'N/A';
            $startDate = isset($req['start_date']) ? $req['start_date'] : 'N/A';
            $status = isset($req['status']) ? $req['status'] : 'N/A';
            echo "   • " . $type . ": " . $startDate . " (Status: " . $status . ")\n";
        }
    } else {
        echo "⚠️  No leave requests found\n";
        echo "   Available data type: " . gettype($leave_requests) . "\n";
    }
} catch (Exception $e) {
    echo "❌ Error fetching leave requests: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Database Table Verification
echo "[TEST 5] Database Tables Verification\n";
try {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM ta_leave_balances WHERE employee_id = ?");
    $stmt->execute([$test_employee_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ ta_leave_balances: " . $result['count'] . " records for employee\n";

    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM ta_leave_requests WHERE employee_id = ?");
    $stmt->execute([$test_employee_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ ta_leave_requests: " . $result['count'] . " records for employee\n";

    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM ta_attendance WHERE employee_id = ?");
    $stmt->execute([$test_employee_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ ta_attendance: " . $result['count'] . " records for employee\n";

} catch (Exception $e) {
    echo "❌ Error verifying tables: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 5: Leave Balance Structure
echo "[TEST 6] Leave Balance Data Structure\n";
try {
    $leaveModel = new Leave();
    $balances = $leaveModel->getLeaveBalances($test_employee_id);
    
    if (!empty($balances)) {
        $sample = $balances[0];
        echo "✅ Sample leave balance record:\n";
        foreach ($sample as $key => $value) {
            echo "   • " . $key . ": " . $value . "\n";
        }
    } else {
        echo "⚠️  No balance data to show structure\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

echo "=============================================================\n";
echo "  TEST SUMMARY\n";
echo "=============================================================\n";
echo "✅ Dashboard data fetch test completed\n";
echo "\nNote: If no data is found, ensure:\n";
echo "  1. Employee with ID " . $test_employee_id . " exists\n";
echo "  2. Leave balances have been allocated (run leave_standards.sql)\n";
echo "  3. Attendance records exist for current month\n";
echo "  4. Leave requests have been submitted\n";
echo "\n";
echo "=============================================================\n";
?>
