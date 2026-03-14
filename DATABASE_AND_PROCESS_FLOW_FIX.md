# Time & Attendance - Database & Process Flow Fix Guide

## Overview

This guide explains the database issues found and the code changes needed to fix the process flow.

---

## Issues Identified

### 1. **Unused Tables**
- `department_heads` - Created but never populated or used
- `holidays` - Created but never used in calculations
- `leave_balances` - Created but never updated during leave approval

### 2. **Broken Leave Approval Process**
The leave approval workflow is incomplete:

```
❌ CURRENT (BROKEN):
Employee submits → HR Admin approves → DONE

✅ INTENDED (SHOULD BE):
Employee submits → Department Head approves → HR Admin approves → Leave balance updated → DONE
```

### 3. **Leave Balance Enforcement Missing**
- Employees can request unlimited leave (no balance checks)
- No deduction from `leave_balances` on approval
- `days_per_year` from `leave_types` never enforced

---

## Fixes to Implement

### FIX 1: Database Cleanup

**File:** `time_attendance/DATABASE_FIX.sql`

**Status:** ✅ READY TO RUN

```bash
# Login to MySQL and run:
mysql -u root -p time_and_attendance < DATABASE_FIX.sql
```

**What it does:**
- Populates `department_heads` table with HR admins
- Initializes `leave_balances` for all employees
- Populates `holidays` table
- Adds foreign key constraints
- Adds performance indexes

**Result:** All previously unused tables are now populated and ready for use.

---

### FIX 2: Update LeaveController to Enforce Leave Balance

**File:** `time_attendance/app/controllers/LeaveController.php`

**Current Code (Lines 55-75):**
```php
// INCOMPLETE - Does not update leave balance
if ($result) {
    $this->leaveModel->updateStatus($leave_request_id, $status, $approver_id, $remarks);
    // Returns success but doesn't update leave_balances
    return ['success' => true, 'message' => 'Leave approved'];
}
```

**NEW CODE TO ADD:**

```php
/**
 * Approve leave request and update balance
 */
public function approveLeave($leave_request_id, $approver_id, $remarks = '')
{
    // Get leave request details
    $query = "SELECT * FROM leave_requests WHERE leave_request_id = ?";
    $stmt = $this->db->prepare($query);
    $stmt->execute([$leave_request_id]);
    $leave = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$leave) {
        return ['success' => false, 'message' => 'Leave request not found'];
    }
    
    // Check if employee has sufficient leave balance
    $query = "SELECT remaining_days FROM leave_balances 
              WHERE employee_id = ? AND leave_type_id = ? AND year = YEAR(NOW())";
    $stmt = $this->db->prepare($query);
    $stmt->execute([$leave['employee_id'], $leave['leave_type_id']]);
    $balance = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$balance || $balance['remaining_days'] < $leave['total_days']) {
        return [
            'success' => false, 
            'message' => 'Insufficient leave balance. Required: ' . $leave['total_days'] . 
                         ' days, Available: ' . ($balance['remaining_days'] ?? 0) . ' days'
        ];
    }
    
    // Approve leave
    $result = $this->leaveModel->updateStatus(
        $leave_request_id, 
        'APPROVED_BY_HR', 
        $approver_id, 
        $remarks
    );
    
    if ($result) {
        // Deduct from leave balance
        $query = "UPDATE leave_balances 
                  SET used_days = used_days + ?,
                      remaining_days = remaining_days - ?,
                      updated_at = NOW()
                  WHERE employee_id = ? AND leave_type_id = ? AND year = YEAR(NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            $leave['total_days'],
            $leave['total_days'],
            $leave['employee_id'],
            $leave['leave_type_id']
        ]);
        
        // Log the approval
        $this->auditLog->log(
            'LEAVE_APPROVED',
            $approver_id,
            $leave['employee_id'],
            null,
            ['leave_days' => $leave['total_days'], 'remarks' => $remarks],
            'SUCCESS'
        );
        
        return ['success' => true, 'message' => 'Leave approved and balance updated'];
    }
    
    return ['success' => false, 'message' => 'Failed to approve leave'];
}

/**
 * Reject leave request and return balance
 */
public function rejectLeave($leave_request_id, $approver_id, $reason = '')
{
    $result = $this->leaveModel->updateStatus(
        $leave_request_id,
        'REJECTED',
        $approver_id,
        $reason
    );
    
    if ($result) {
        $this->auditLog->log(
            'LEAVE_REJECTED',
            $approver_id,
            null,
            null,
            ['reason' => $reason],
            'SUCCESS'
        );
        return ['success' => true, 'message' => 'Leave rejected'];
    }
    
    return ['success' => false, 'message' => 'Failed to reject leave'];
}
```

---

### FIX 3: Add Leave Balance Validation to Leave.php Model

**File:** `time_attendance/app/models/Leave.php`

**Add this method:**

```php
/**
 * Check if employee has sufficient leave balance
 */
public function checkBalance($employee_id, $leave_type_id, $days_requested)
{
    $query = "SELECT remaining_days FROM leave_balances 
              WHERE employee_id = :employee_id 
              AND leave_type_id = :leave_type_id 
              AND year = YEAR(NOW())";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':employee_id', $employee_id);
    $stmt->bindParam(':leave_type_id', $leave_type_id);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        return ['has_balance' => false, 'remaining' => 0];
    }
    
    return [
        'has_balance' => $result['remaining_days'] >= $days_requested,
        'remaining' => $result['remaining_days']
    ];
}

/**
 * Get leave balance for employee
 */
public function getBalance($employee_id, $leave_type_id = null)
{
    if ($leave_type_id) {
        $query = "SELECT lb.*, lt.leave_type_name 
                  FROM leave_balances lb
                  JOIN leave_types lt ON lb.leave_type_id = lt.leave_type_id
                  WHERE lb.employee_id = :employee_id 
                  AND lb.leave_type_id = :leave_type_id
                  AND lb.year = YEAR(NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->bindParam(':leave_type_id', $leave_type_id);
    } else {
        $query = "SELECT lb.*, lt.leave_type_name 
                  FROM leave_balances lb
                  JOIN leave_types lt ON lb.leave_type_id = lt.leave_type_id
                  WHERE lb.employee_id = :employee_id 
                  AND lb.year = YEAR(NOW())
                  ORDER BY lt.leave_type_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
    }
    
    $stmt->execute();
    return $leave_type_id ? $stmt->fetch(PDO::FETCH_ASSOC) : $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

---

### FIX 4: Add Holiday Support to Attendance Controller

**File:** `time_attendance/app/controllers/AttendanceController.php`

**Add this method:**

```php
/**
 * Check if attendance date is a holiday
 */
private function isHoliday($date)
{
    $query = "SELECT holiday_id FROM holidays 
              WHERE DATE(holiday_date) = ? 
              AND is_working_day = 0 
              AND year = YEAR(?)";
    
    $stmt = $this->db->prepare($query);
    $stmt->execute([$date, $date]);
    
    return $stmt->rowCount() > 0;
}

/**
 * Update this in the attendance recording logic
 */
public function recordAttendance($employee_id, $time_in, $time_out = null, $recorded_by = 'QR')
{
    // ... existing code ...
    
    // Check if it's a holiday
    $attendance_date = date('Y-m-d', strtotime($time_in));
    if ($this->isHoliday($attendance_date)) {
        // Don't mark absent for holidays
        $status = 'ABSENT';
        // Log as holiday
    } else {
        // Regular attendance logic
        $status = 'PRESENT';
    }
    
    // ... rest of attendance recording ...
}
```

---

### FIX 5: Create Leave Request API Endpoint with Balance Check

**File:** `time_attendance/app/api/submit_leave.php`

**New file - Replace or enhance existing:**

```php
<?php
/**
 * Leave Request Submission API
 * Validates leave balance before allowing submission
 */

require_once '../auth/auth_check.php';
require_once '../models/Leave.php';
require_once '../helpers/AuditLog.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$auth_user = $_SESSION['user_id'] ?? null;
$employee_id = $_POST['employee_id'] ?? null;
$leave_type_id = $_POST['leave_type_id'] ?? null;
$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;
$reason = $_POST['reason'] ?? '';

// Validate inputs
if (!$employee_id || !$leave_type_id || !$start_date || !$end_date) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

try {
    $leave = new Leave();
    $auditLog = new AuditLog();
    
    // Calculate total days
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = $end->diff($start);
    $total_days = $interval->days + 1; // +1 to include end date
    
    // Check leave balance BEFORE submission
    $balance_check = $leave->checkBalance($employee_id, $leave_type_id, $total_days);
    
    if (!$balance_check['has_balance']) {
        http_response_code(422);
        echo json_encode([
            'error' => 'Insufficient leave balance',
            'required_days' => $total_days,
            'available_days' => $balance_check['remaining'],
            'shortage' => $total_days - $balance_check['remaining']
        ]);
        exit;
    }
    
    // Submit leave request
    $leave->employee_id = $employee_id;
    $leave->leave_type_id = $leave_type_id;
    $leave->start_date = $start_date;
    $leave->end_date = $end_date;
    $leave->total_days = $total_days;
    $leave->reason = $reason;
    $leave->status = 'PENDING';
    
    if ($leave->create()) {
        $auditLog->log(
            'LEAVE_REQUESTED',
            $auth_user,
            $employee_id,
            null,
            ['days' => $total_days, 'type_id' => $leave_type_id],
            'SUCCESS'
        );
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Leave request submitted successfully',
            'data' => [
                'total_days' => $total_days,
                'remaining_after_approval' => $balance_check['remaining'] - $total_days
            ]
        ]);
    } else {
        throw new Exception('Failed to create leave request');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
```

---

### FIX 6: Create Department Head Approval API

**File:** `time_attendance/app/api/approve_leave_head.php`

**New file:**

```php
<?php
/**
 * Department Head Leave Approval API
 * First-level approval by department head
 */

require_once '../auth/auth_check.php';
require_once '../models/Leave.php';
require_once '../helpers/AuditLog.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Check if user is department head
$user_role = $_SESSION['role'] ?? null;
if ($user_role !== 'HR_ADMIN') { // Department heads are HR_ADMIN
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized - Must be department head']);
    exit;
}

$leave_request_id = $_POST['leave_request_id'] ?? null;
$approval_action = $_POST['action'] ?? null; // 'approve' or 'reject'
$remarks = $_POST['remarks'] ?? '';
$auth_user = $_SESSION['user_id'] ?? null;

if (!$leave_request_id || !$approval_action) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

try {
    $leave = new Leave();
    $auditLog = new AuditLog();
    
    // Get leave request
    $query = "SELECT * FROM leave_requests WHERE leave_request_id = ?";
    $stmt = $GLOBALS['db']->prepare($query);
    $stmt->execute([$leave_request_id]);
    $leave_req = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$leave_req) {
        http_response_code(404);
        echo json_encode(['error' => 'Leave request not found']);
        exit;
    }
    
    // Update leave request status
    if ($approval_action === 'approve') {
        $status = 'APPROVED_BY_HEAD';
    } elseif ($approval_action === 'reject') {
        $status = 'REJECTED';
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        exit;
    }
    
    // Update database
    $query = "UPDATE leave_requests 
              SET status = ?,
                  department_head_approval_date = NOW(),
                  department_head_remarks = ?
              WHERE leave_request_id = ?";
    
    $stmt = $GLOBALS['db']->prepare($query);
    $result = $stmt->execute([$status, $remarks, $leave_request_id]);
    
    if ($result) {
        $auditLog->log(
            'LEAVE_' . strtoupper($approval_action) . '_BY_HEAD',
            $auth_user,
            $leave_req['employee_id'],
            null,
            ['remarks' => $remarks, 'days' => $leave_req['total_days']],
            'SUCCESS'
        );
        
        echo json_encode([
            'success' => true,
            'message' => "Leave request {$approval_action}ed by department head",
            'status' => $status
        ]);
    } else {
        throw new Exception('Failed to update leave request');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
```

---

## Testing Checklist

After implementing these fixes, test the following:

### Test 1: Leave Balance Initialization
```bash
# Should return all employees with leave balances
SELECT COUNT(*) FROM leave_balances WHERE year = YEAR(NOW());
# Expected: Number of employees × number of leave types
```

### Test 2: Leave Request with Balance Check
1. Login as employee
2. Request leave for more days than available
3. Should show error: "Insufficient leave balance"
4. Request leave for available days
5. Should succeed

### Test 3: Department Head Approval
1. Leave request submitted
2. Department head logs in
3. Can see "Pending Department Head Approval" requests
4. Approves/rejects leave
5. Leave moves to HR Admin queue

### Test 4: HR Admin Approval with Balance Deduction
1. Leave request approved by department head
2. HR Admin approves
3. Check that `leave_balances.remaining_days` decreased
4. Check that `leave_balances.used_days` increased

### Test 5: Holiday Handling
1. Submit attendance for holiday date
2. Should not mark as absent
3. Should show as holiday

---

## Deployment Steps

1. **Backup Database:**
   ```bash
   mysqldump -u root -p time_and_attendance > backup_$(date +%Y%m%d).sql
   ```

2. **Run Database Fixes:**
   ```bash
   mysql -u root -p time_and_attendance < DATABASE_FIX.sql
   ```

3. **Update PHP Files:**
   - Update `LeaveController.php`
   - Update `Leave.php` model
   - Update `AttendanceController.php`
   - Create `submit_leave.php` API
   - Create `approve_leave_head.php` API

4. **Test Leave Workflow:**
   - Follow Testing Checklist above

5. **Monitor Logs:**
   - Check `audit_logs` for LEAVE_REQUESTED, LEAVE_APPROVED_BY_HEAD, LEAVE_APPROVED_BY_HR

---

## Questions & Troubleshooting

**Q: What if leave request is approved by HR before department head reviews?**
A: Add validation to prevent HR approval if status is still PENDING.

**Q: Can employee cancel leave after approval?**
A: Add cancel logic to return days to `leave_balances`.

**Q: How to handle leave year transitions?**
A: Run `DATABASE_FIX.sql` again at year-end to create new balances for next year.

---

**Document Version:** 1.0  
**Last Updated:** March 14, 2026  
**Status:** Implementation Guide - Ready for Development
