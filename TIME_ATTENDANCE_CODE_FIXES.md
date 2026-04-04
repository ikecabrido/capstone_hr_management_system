# Time & Attendance System - Code Fixes

## Overview
This document provides specific code changes to synchronize employee and admin portals.

---

## FIX 1: Employee Portal Leave Balance Deduction

**File:** `employee_portal/time_attendance_portal/controllers/TimeAttendancePortalController.php`

**Current Code (Lines 490-505):**
```php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'submit_leave') {
    header('Content-Type: application/json');
    
    $leave_type_id = trim($_POST['leave_type_id'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    $reason = trim($_POST['reason'] ?? '');
    
    // MISSING: Balance validation and deduction
    
    $insert_query = "INSERT INTO ta_leave_requests (employee_id, leave_type_id, start_date, end_date, reason, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, 'Pending', NOW())";
}
```

**Required Changes:**
Add balance validation and create daily records:

```php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'submit_leave') {
    header('Content-Type: application/json');
    
    $leave_type_id = (int)trim($_POST['leave_type_id'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    $reason = trim($_POST['reason'] ?? '');
    
    // Validate required fields
    if (empty($leave_type_id) || empty($start_date) || empty($end_date) || empty($reason)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    if (strtotime($start_date) > strtotime($end_date)) {
        echo json_encode(['success' => false, 'message' => 'Start date must be before end date']);
        exit;
    }
    
    if (strtotime($start_date) < strtotime('today')) {
        echo json_encode(['success' => false, 'message' => 'Cannot submit leave for past dates']);
        exit;
    }
    
    // ============ NEW: Validate balance ============
    $current_year = date('Y');
    $check_balance = "SELECT remaining_balance FROM ta_leave_balances 
                      WHERE employee_id = ? AND leave_type_id = ? AND year = ?";
    $balance_stmt = $conn->prepare($check_balance);
    $balance_stmt->execute([$employee_id, $leave_type_id, $current_year]);
    $balance_row = $balance_stmt->fetch(PDO::FETCH_ASSOC);
    
    // Calculate requested days
    $days_requested = (strtotime($end_date) - strtotime($start_date)) / 86400 + 1;
    
    // Check if employee has sufficient balance
    if (!$balance_row) {
        echo json_encode(['success' => false, 'message' => 'No leave balance found for this leave type']);
        exit;
    }
    
    if ($balance_row['remaining_balance'] < $days_requested) {
        echo json_encode(['success' => false, 'message' => 'Insufficient leave balance. Available: ' . $balance_row['remaining_balance'] . ', Requested: ' . $days_requested]);
        exit;
    }
    // ============ END: Balance validation ============
    
    try {
        // Use 'details' field to match database schema (not 'reason')
        $insert_query = "INSERT INTO ta_leave_requests (employee_id, leave_type_id, start_date, end_date, details, status, date_submitted) 
                        VALUES (?, ?, ?, ?, ?, 'Pending', NOW())";
        $insert_stmt = $conn->prepare($insert_query);
        $result = $insert_stmt->execute([$employee_id, $leave_type_id, $start_date, $end_date, $reason]);
        
        if ($result) {
            // Get the inserted leave request ID
            $leave_request_id = $conn->lastInsertId();
            
            // ============ NEW: Create daily leave records ============
            $current_date = strtotime($start_date);
            $end_timestamp = strtotime($end_date);
            
            while ($current_date <= $end_timestamp) {
                $check_holiday = "SELECT holiday_id FROM ta_holidays 
                                  WHERE holiday_date = ? AND is_active = 1";
                $holiday_stmt = $conn->prepare($check_holiday);
                $holiday_stmt->execute([date('Y-m-d', $current_date)]);
                $is_holiday = $holiday_stmt->fetch() ? 1 : 0;
                
                $daily_query = "INSERT INTO ta_leave_daily_records 
                               (leave_request_id, employee_id, leave_date, leave_type_id, is_holiday, balance_deducted)
                               VALUES (?, ?, ?, ?, ?, 0)";
                $daily_stmt = $conn->prepare($daily_query);
                $daily_stmt->execute([$leave_request_id, $employee_id, date('Y-m-d', $current_date), $leave_type_id, $is_holiday]);
                
                $current_date += 86400; // Add 1 day
            }
            // ============ END: Daily records creation ============
            
            echo json_encode(['success' => true, 'message' => 'Leave request submitted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error submitting request']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
```

---

## FIX 2: Employee Portal Attendance Status (PENDING_APPROVAL)

**File:** `employee_portal/time_attendance_portal/controllers/TimeAttendancePortalController.php`

**Current Code (Line ~720):**
```php
INSERT INTO ta_attendance (employee_id, attendance_date, time_in) 
VALUES (?, ?, ?)
// Missing: status, is_approved, etc.
```

**Required Changes:**
Change to record with PENDING_APPROVAL status:

```php
// When employee confirms attendance
INSERT INTO ta_attendance 
(employee_id, shift_id, attendance_date, time_in, recorded_by, status, is_approved, created_at, updated_at)
VALUES (?, ?, ?, ?, 'MANUAL', 'PENDING_APPROVAL', 0, NOW(), NOW())

// When updating time_out
UPDATE ta_attendance 
SET time_out = ?, status = 'PENDING_APPROVAL', is_approved = 0
WHERE attendance_id = ?
AND is_approved = 0  // Only update if not yet approved
```

---

## FIX 3: Add Shift Lookup Before Recording Attendance

**File:** `employee_portal/time_attendance_portal/controllers/TimeAttendancePortalController.php`

**Add function before attendance recording:**

```php
/**
 * Get employee's shift for a given date
 */
private function getEmployeeShift($conn, $employee_id, $date) {
    try {
        $query = "SELECT tes.shift_id, ts.shift_name, ts.start_time, ts.end_time
                  FROM ta_employee_shifts tes
                  INNER JOIN ta_shifts ts ON tes.shift_id = ts.shift_id
                  WHERE tes.employee_id = ? 
                  AND ? BETWEEN tes.shift_date AND COALESCE(tes.end_date, '2099-12-31')
                  ORDER BY tes.shift_date DESC
                  LIMIT 1";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$employee_id, $date]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error getting employee shift: " . $e->getMessage());
        return null;
    }
}
```

**Use in attendance recording:**
```php
// Get shift before recording
$shift = $this->getEmployeeShift($conn, $employee_id, date('Y-m-d'));
$shift_id = $shift['shift_id'] ?? null;

// Insert with shift_id
INSERT INTO ta_attendance 
(employee_id, shift_id, attendance_date, time_in, recorded_by, status, is_approved)
VALUES (?, ?, ?, ?, 'MANUAL', 'PENDING_APPROVAL', 0)
// Pass: $shift_id
```

---

## FIX 4: Prevent Duplicate Attendance Records

**File:** `employee_portal/time_attendance_portal/controllers/TimeAttendancePortalController.php`

**Add before INSERT:**

```php
// Check for existing record
$check_query = "SELECT attendance_id, time_in, time_out FROM ta_attendance 
                WHERE employee_id = ? AND attendance_date = ? AND recorded_by = 'MANUAL'
                LIMIT 1";
$check_stmt = $conn->prepare($check_query);
$check_stmt->execute([$employee_id, $date]);
$existing = $check_stmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    // Update existing record instead
    $update_query = "UPDATE ta_attendance 
                    SET time_in = ?, updated_at = NOW()
                    WHERE attendance_id = ? AND is_approved = 0";
    $update_stmt = $conn->prepare($update_query);
    $result = $update_stmt->execute([$time_in, $existing['attendance_id']]);
    
    echo json_encode(['success' => true, 'message' => 'Attendance record updated']);
} else {
    // Insert new record
    $insert_query = "INSERT INTO ta_attendance 
                    (employee_id, shift_id, attendance_date, time_in, recorded_by, status, is_approved, created_at, updated_at)
                    VALUES (?, ?, ?, ?, 'MANUAL', 'PENDING_APPROVAL', 0, NOW(), NOW())";
    // ... execute insert
    
    echo json_encode(['success' => true, 'message' => 'Attendance recorded']);
}
```

---

## FIX 5: Standardize Column Names in Leave

**File:** Multiple locations

**Change FROM:**
```php
$_POST['reason']          // Wrong field name
INSERT details VALUE      // Right column
```

**Change TO:**
```php
$_POST['details']         // Or normalize in processing:
$details = $_POST['reason'] ?? $_POST['details'];

// Always use 'details' column
INSERT INTO ta_leave_requests (details) VALUES (?)
```

---

## FIX 6: Add Database Constraints

**Run these SQL queries to prevent data issues:**

```sql
-- Prevent duplicate attendance records
ALTER TABLE ta_attendance 
ADD UNIQUE INDEX `unique_manual_attendance` 
(employee_id, attendance_date, recorded_by) 
WHERE recorded_by = 'MANUAL';

-- Ensure leave balance consistency
ALTER TABLE ta_leave_balances
ADD CONSTRAINT `chk_balance`
CHECK (remaining_balance >= 0 AND remaining_balance <= opening_balance);

-- Ensure leave dates are valid
ALTER TABLE ta_leave_requests
ADD CONSTRAINT `chk_leave_dates`
CHECK (start_date <= end_date);

-- Add index for faster queries
CREATE INDEX `idx_ta_attendance_emp_date` 
ON ta_attendance(employee_id, attendance_date);

CREATE INDEX `idx_ta_leave_employee` 
ON ta_leave_requests(employee_id, status);

CREATE INDEX `idx_ta_leave_balance` 
ON ta_leave_balances(employee_id, leave_type_id, year);
```

---

## FIX 7: Complete Leave Submission Handler

**Recommended complete implementation for TimeAttendancePortalController:**

```php
/**
 * Handle leave request submission
 */
private function handleLeaveSubmission($conn, $employee_id) {
    header('Content-Type: application/json');
    
    try {
        // Validate input
        $leave_type_id = (int)($_POST['leave_type_id'] ?? 0);
        $start_date = trim($_POST['start_date'] ?? '');
        $end_date = trim($_POST['end_date'] ?? '');
        $reason = trim($_POST['reason'] ?? '');
        
        if (!$leave_type_id || !$start_date || !$end_date || !$reason) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'All fields required']);
            exit;
        }
        
        // Validate dates
        if (!strtotime($start_date) || !strtotime($end_date)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid date format']);
            exit;
        }
        
        if (strtotime($start_date) > strtotime($end_date)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Start date must be before end date']);
            exit;
        }
        
        if (strtotime($start_date) < strtotime('today')) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Cannot submit leave for past dates']);
            exit;
        }
        
        // Check leave type exists
        $type_check = $conn->prepare("SELECT * FROM ta_leave_types WHERE leave_type_id = ?");
        $type_check->execute([$leave_type_id]);
        if (!$type_check->fetch()) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid leave type']);
            exit;
        }
        
        // Check balance (for deductible leaves)
        $current_year = date('Y');
        $days_requested = ceil((strtotime($end_date) - strtotime($start_date)) / 86400) + 1;
        
        $balance_check = $conn->prepare(
            "SELECT remaining_balance FROM ta_leave_balances 
             WHERE employee_id = ? AND leave_type_id = ? AND year = ?"
        );
        $balance_check->execute([$employee_id, $leave_type_id, $current_year]);
        $balance = $balance_check->fetch(PDO::FETCH_ASSOC);
        
        if (!$balance) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No leave balance set for this year']);
            exit;
        }
        
        if ($balance['remaining_balance'] < $days_requested) {
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'message' => 'Insufficient balance. Available: ' . $balance['remaining_balance'] . ', Requested: ' . $days_requested
            ]);
            exit;
        }
        
        // Insert leave request
        $insert = $conn->prepare(
            "INSERT INTO ta_leave_requests 
             (employee_id, leave_type_id, start_date, end_date, details, status, date_submitted)
             VALUES (?, ?, ?, ?, ?, 'Pending', NOW())"
        );
        $insert->execute([$employee_id, $leave_type_id, $start_date, $end_date, $reason]);
        $leave_id = $conn->lastInsertId();
        
        // Create daily records
        $current = strtotime($start_date);
        $end_ts = strtotime($end_date);
        
        while ($current <= $end_ts) {
            $date = date('Y-m-d', $current);
            
            // Check if holiday
            $holiday_check = $conn->prepare(
                "SELECT 1 FROM ta_holidays WHERE holiday_date = ? AND is_active = 1"
            );
            $holiday_check->execute([$date]);
            $is_holiday = $holiday_check->fetch() ? 1 : 0;
            
            // Insert daily record
            $daily = $conn->prepare(
                "INSERT INTO ta_leave_daily_records 
                 (leave_request_id, employee_id, leave_date, leave_type_id, is_holiday, balance_deducted)
                 VALUES (?, ?, ?, ?, ?, 0)"
            );
            $daily->execute([$leave_id, $employee_id, $date, $leave_type_id, $is_holiday]);
            
            $current += 86400;
        }
        
        http_response_code(201);
        echo json_encode([
            'success' => true, 
            'message' => 'Leave request submitted successfully',
            'leave_request_id' => $leave_id,
            'status' => 'Pending',
            'days_requested' => $days_requested,
            'remaining_after_approval' => $balance['remaining_balance'] - $days_requested
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    
    exit;
}
```

---

## Implementation Order

1. **First:** Add balance validation and prevent duplicates (low risk, high impact)
2. **Second:** Change status to PENDING_APPROVAL (affects approval workflow)
3. **Third:** Add shift lookup (data enrichment)
4. **Fourth:** Create daily records (workflow completion)
5. **Fifth:** Database constraints (prevents future data corruption)

---

## Testing Commands

```sql
-- Test: Check if leave balance is deducting
SELECT * FROM ta_leave_requests WHERE employee_id = 1 AND status = 'Pending';
SELECT * FROM ta_leave_daily_records WHERE employee_id = 1;
SELECT * FROM ta_leave_balances WHERE employee_id = 1;

-- Test: Check if attendance is PENDING_APPROVAL
SELECT * FROM ta_attendance WHERE employee_id = 1 ORDER BY created_at DESC;
SELECT COUNT(*) as pending_count FROM ta_attendance 
WHERE employee_id = 1 AND is_approved = 0;

-- Test: Check for duplicates
SELECT employee_id, attendance_date, COUNT(*) as count 
FROM ta_attendance 
GROUP BY employee_id, attendance_date, recorded_by 
HAVING count > 1;

-- Test: Check shift assignment
SELECT a.attendance_id, a.employee_id, a.shift_id, a.attendance_date, a.status
FROM ta_attendance a 
WHERE a.shift_id IS NULL AND a.recorded_by = 'MANUAL'
LIMIT 10;
```

