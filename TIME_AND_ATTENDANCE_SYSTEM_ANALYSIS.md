# Time and Attendance System Analysis
## Employee Portal vs Admin Portal Integration

**Date:** April 1, 2026  
**System:** Capstone HR Management System  
**Database:** hr_management (Time Attendance Tables: ta_*)

---

## Executive Summary

The system has **TWO separate implementations** of Time & Attendance:

1. **Employee Portal** (`employee_portal/time_attendance_portal/`) - New, modern architecture
2. **Admin Portal** (`time_attendance/`) - Established system

Both are **reading and writing to the SAME database tables** but with **different controllers, models, and APIs**, creating potential **data synchronization issues**.

---

## System Architecture Comparison

### 1. EMPLOYEE PORTAL (`employee_portal/time_attendance_portal/`)

#### Structure
```
employee_portal/time_attendance_portal/
├── employee_dashboard.php          # Main view
├── index.php                        # Entry point
├── controllers/
│   ├── TimeAttendancePortalController.php
│   └── LeaveController.php
├── lib/
│   ├── models/
│   │   ├── Attendance.php
│   │   └── Leave.php
│   ├── helpers/
│   │   ├── AttendanceTracker.php
│   │   ├── Helper.php
│   │   └── LeaveAttendanceHelper.php
│   └── core/
└── api/
    └── attendance-confirm.php      # API endpoint
```

#### Key Characteristics
- **Modern MVC Architecture** with controllers and models
- **Single Controller Pattern**: `TimeAttendancePortalController::index()`
- **Database Integration**: Uses shared `Database` class from `/auth/database.php`
- **Session Management**: Supports both session formats (`$_SESSION['user_id']` and `$_SESSION['user']['id']`)
- **Error Handling**: PHP error logging enabled
- **Timezone**: Asia/Manila hardcoded

#### Database Tables Used (Employee Side)
- `ta_attendance` - Time in/out records
- `ta_leave_requests` - Leave request submissions
- `ta_leave_balances` - Leave balance tracking
- `ta_leave_types` - Leave type definitions
- `employees` - Employee master data

---

### 2. ADMIN PORTAL (`time_attendance/`)

#### Structure
```
time_attendance/
├── time_attendance.php             # Main entry
├── public/
│   ├── dashboard.php
│   ├── employee_dashboard.php
│   ├── approve_attendance.php
│   ├── leave_approvals.php
│   ├── leave_request.php
│   └── ... (many more pages)
├── app/
│   ├── controllers/
│   │   └── AttendanceController.php (among others)
│   ├── models/
│   │   ├── Attendance.php
│   │   ├── Leave.php
│   │   ├── Employee.php
│   │   └── ... (many more models)
│   ├── api/
│   │   ├── time_in.php
│   │   ├── time_out.php
│   │   ├── submit_leave.php
│   │   ├── approve_leave_head.php
│   │   ├── approve_leave_hr.php
│   │   └── ... (many more APIs)
│   └── helpers/
│       ├── MetricsCalculator.php
│       └── ... (various helpers)
```

#### Key Characteristics
- **Page-based Architecture** (traditional PHP)
- **Multiple Controllers & Models** for different functionalities
- **Complex API Layer** with separate endpoints for each action
- **Approval Workflow**: Multi-level approvals (Department Head → HR Admin)
- **Advanced Features**: Metrics, leave balance deduction, overtime tracking

#### Database Tables Used (Admin Side)
- `ta_attendance` - Time in/out records
- `ta_leave_requests` - Leave request handling
- `ta_leave_balances` - Balance management
- `ta_attendance_metrics` - Performance metrics
- `ta_absence_late_records` - Absence tracking
- `ta_overtime_tracking` - Overtime records
- `department_heads` - Department hierarchy
- `employees` - Employee data

---

## Data Flow Comparison

### EMPLOYEE SIDE: Recording Attendance

```
employee_dashboard.php (Frontend)
    ↓
Fetch to attendance-confirm.php (API)
    ↓
TimeAttendancePortalController::confirmAttendance()
    ↓
INSERT/UPDATE ta_attendance
    (Direct SQL execution)
```

**Issues:**
- No intermediate model layer in confirmation
- Direct SQL execution without validation
- Limited error handling

### ADMIN SIDE: Recording Attendance

```
time_attendance.php (Web interface)
    ↓
time_in.php / time_out.php (API endpoints)
    ↓
AttendanceController::timeIn() / timeOut()
    ↓
Attendance Model
    ↓
INSERT/UPDATE ta_attendance
    (Prepared statements, validation)
```

**Advantages:**
- Clear separation of concerns
- Model-based validation
- Better error handling

---

## Leave Request Workflow Comparison

### EMPLOYEE SIDE: Submit Leave

```
employee_dashboard.php (Form)
    ↓
FormData → TimeAttendancePortalController::index() POST handler
    ↓
Leave Model::createRequest()
    ↓
INSERT ta_leave_requests (status = 'Pending')
    ↓
Response JSON to frontend
```

**Column Mapping (Employee Portal):**
```php
INSERT INTO ta_leave_requests 
(employee_id, leave_type_id, start_date, end_date, details, status)
VALUES (?, ?, ?, ?, ?, 'Pending')
```

### ADMIN SIDE: Submit/Approve Leave

```
leave_request.php (Form) OR
submit_leave.php (API)
    ↓
LeaveController OR API endpoint
    ↓
Leave Model
    ↓
INSERT ta_leave_requests OR UPDATE ta_leave_requests
```

**Column Mapping (Admin Portal):**
```php
INSERT INTO ta_leave_requests 
(employee_id, leave_type_id, start_date, end_date, details, status)
VALUES (?, ?, ?, ?, ?, 'Pending')
```

**Status Options:**
- Employee Portal: 'Pending' (direct)
- Admin Portal: 'Pending' → 'Approved' → Approved (with multi-level approval)

---

## Critical Issues & Missing Connections

### ⚠️ ISSUE 1: Inconsistent Column Names in Leave Requests

**Database Schema:**
```sql
CREATE TABLE `ta_leave_requests` (
  `id` int(11) NOT NULL,                      -- Primary key
  `employee_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `date_submitted` timestamp,                 -- NOT created_at
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `details` varchar(255),                     -- NOT reason
  `supporting_document` varchar(255),         -- MISSING in employee side
  `reject_reason` varchar(255),               -- MISSING handling
  `balance_deducted` tinyint(1) DEFAULT 0     -- CRITICAL: Not deducting balance!
)
```

**Employee Portal Issue:**
```php
// employee_dashboard.php Line 227-230
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'submit_leave') {
    // Using 'reason' field but table expects 'details'
    $reason = trim($_POST['reason'] ?? '');
    
    // No balance deduction logic
    // No approval workflow
}
```

**Gap:** Employee uses `$_POST['reason']` but database schema uses `details` column.

### ⚠️ ISSUE 2: Leave Balance NOT Being Deducted

**Database Column:**
```sql
`balance_deducted` tinyint(1) DEFAULT 0
```

**Employee Portal:** ❌ NO balance deduction logic
- Submits leave but never updates `ta_leave_balances`
- No validation against remaining balance
- No `balance_deducted` flag update

**Admin Portal:** ✅ Has balance deduction (LeaveHolidayIntegration.php)
```php
// Deducts balance when leave is approved
// Updates ta_leave_balances.remaining_balance
// Records daily leave in ta_leave_daily_records
```

**Gap:** Employee-submitted leaves don't automatically deduct balance when approved!

### ⚠️ ISSUE 3: Leave Request Status Workflow Mismatch

**Employee Portal:**
```php
// All leaves submitted as 'Pending'
$status = 'Pending'  // No further workflow
```

**Admin Portal:**
```
Employee submits → Pending
    ↓
Department Head approves → Approved (intermediate)
    ↓
HR Admin approves → Final-Approved
```

**Gap:** Employee portal doesn't support multi-level approval workflow.

### ⚠️ ISSUE 4: No Attendance Approval in Employee Portal

**Database Field:**
```sql
`is_approved` tinyint(1) DEFAULT 0
`approved_by` int(11) DEFAULT NULL
`approval_remarks` varchar(255) DEFAULT NULL
`approved_at` datetime DEFAULT NULL
`status` enum('PRESENT','ABSENT','LATE','EARLY_OUT','ON_LEAVE','PENDING_APPROVAL')
```

**Employee Portal:**
- ✅ Records `time_in` and `time_out`
- ❌ Does NOT handle approval workflow
- ❌ Always sets status to direct values (not PENDING_APPROVAL)
- ❌ No `approved_by` or `approved_at` tracking

**Admin Portal:**
- ✅ Records attendance
- ✅ Has approval system
- ✅ Updates `is_approved`, `approved_by`, `approved_at`

**Gap:** Employee-recorded attendance bypasses approval workflow!

### ⚠️ ISSUE 5: Missing API Endpoint Coordination

**Employee Portal API:**
- `attendance-confirm.php` - JSON endpoint for confirming attendance

**Admin Portal APIs:**
- `time_in.php` - Record time in
- `time_out.php` - Record time out
- `submit_leave.php` - Submit leave
- `approve_leave_head.php` - Department head approval
- `approve_leave_hr.php` - HR admin approval
- `get_leave_balance.php` - Get balance
- `get_pending_leaves.php` - Get pending leaves

**Gap:** Employee portal doesn't have endpoints for:
- Leave approval (only submission)
- Leave balance retrieval
- Pending leave retrieval
- Attendance metrics

### ⚠️ ISSUE 6: Inconsistent Employee ID Handling

**Database Transition Issue:**
Both systems have notes about `employee_id_new` column (INT migration):

```sql
`employee_id` int(11) NOT NULL,        -- Old/current
`employee_id_new` int(11) DEFAULT NULL -- New INT version
```

**Employee Portal:**
```php
// Using INT directly
$employee_id = (int)$employee['employee_id'];
```

**Admin Portal:**
```php
// Has conversion logic for old/new ID handling
if (isset($_SESSION['user']['employee_id']) && is_string($_SESSION['user']['employee_id'])) {
    // Convert string to INT
}
```

**Gap:** Inconsistent data type handling could cause lookup failures.

### ⚠️ ISSUE 7: Missing Leave Daily Records

**Admin Portal:** ✅ Creates detailed leave records
```php
// ta_leave_daily_records table
INSERT INTO ta_leave_daily_records 
(leave_request_id, employee_id, leave_date, leave_type_id, is_holiday, balance_deducted)
```

**Employee Portal:** ❌ Does NOT create daily records
- Only inserts to `ta_leave_requests`
- No daily breakdown of leave
- Cannot track holidays vs regular leave days

**Gap:** Employee-submitted leaves lack granular daily tracking.

### ⚠️ ISSUE 8: No Shift Assignment Handling

**Attendance Record Requires:**
```sql
`shift_id` int(11) DEFAULT NULL
```

**Employee Portal:** ❌ Not checking/assigning shifts
```php
// No shift lookup or assignment
INSERT INTO ta_attendance (employee_id, attendance_date, time_in)
```

**Admin Portal:** ✅ Handles shift assignment
```php
// Gets employee's shift
$shift = $employeeShift->getByEmployeeAndDate($employee_id, $date);
```

**Gap:** Employee-recorded attendance lacks shift context for duration/overtime calculation.

---

## Data Synchronization Issues

### Issue Type 1: Duplicate Record Insertion
If employee records time-in AND admin records time-in for same date, creates duplicates:

```sql
-- From employee portal
INSERT INTO ta_attendance (employee_id, attendance_date, time_in) 
VALUES (1, '2026-04-01', '2026-04-01 09:00:00');

-- From admin portal (same employee/date)
INSERT INTO ta_attendance (employee_id, attendance_date, time_in) 
VALUES (1, '2026-04-01', '2026-04-01 08:55:00');

-- Result: TWO records for same employee-date!
```

### Issue Type 2: Leave Balance Out of Sync
- Employee submits leave (no balance deduction)
- Admin approves leave (deducts balance)
- Employee sees balance still available in portal (cached/not updated)
- Employee submits another leave, exceeding actual allowance

### Issue Type 3: Approval Status Inconsistency
- Employee records time-in (status = 'PRESENT')
- Admin later marks as 'ABSENT' in approval screen
- Two different statuses in same record

---

## Recommendations for Connection

### 1. **UNIFIED ATTENDANCE API** (CRITICAL)
Create single source of truth:
```
DELETE employee_portal/api/attendance-confirm.php
REDIRECT → time_attendance/app/api/time_in.php & time_out.php
```

### 2. **CONSOLIDATED LEAVE API** (CRITICAL)
```
DELETE or REDIRECT employee-specific leave submission
REDIRECT → time_attendance/app/api/submit_leave.php
```

### 3. **SHARED LEAVE BALANCE SERVICE**
Create endpoint in admin portal:
```
time_attendance/app/api/get_leave_balance.php
ACCESS FROM: employee_portal/api/get_balance.php (wrapper)
```

### 4. **COMPLETE LEAVE WORKFLOW**
Employee portal MUST:
- ✅ Support status: 'Pending'
- ✅ Reference dept_head approval status
- ✅ Reference hr_admin approval status
- ✅ Show 'Final-Approved' status
- ✅ Implement balance deduction
- ✅ Create ta_leave_daily_records

### 5. **ATTENDANCE APPROVAL WORKFLOW**
Employee portal MUST:
- ✅ Record with status = 'PENDING_APPROVAL'
- ✅ Store NO approval by default
- ✅ Admin portal handles approval
- ✅ Employee portal displays approval status

### 6. **DUPLICATE PREVENTION**
Implement unique constraint:
```sql
ALTER TABLE ta_attendance 
ADD UNIQUE KEY `unique_attendance` 
(employee_id, attendance_date, recorded_by);
```

### 7. **SHARED REFERENCE DATA**
Both must use same:
- `ta_leave_types` - ✅ Already shared
- `ta_employee_shifts` - ⚠️ Check consistency
- `employees` - ✅ Already shared
- `ta_holidays` - ⚠️ Verify sync

### 8. **AUDIT TRAIL**
Add to attendance/leave operations:
```sql
`recorded_by_source` ENUM('EMPLOYEE_PORTAL', 'ADMIN_PORTAL', 'QR_KIOSK')
`last_modified_source` ENUM(...)
```

---

## Implementation Priority

### PHASE 1: Critical Data Integrity (Week 1)
1. ✅ Fix leave balance deduction logic
2. ✅ Implement approval workflow in employee portal
3. ✅ Create daily leave records
4. ✅ Add duplicate prevention

### PHASE 2: API Consolidation (Week 2)
1. ✅ Route employee time-in through admin API
2. ✅ Route employee leave submission through admin API
3. ✅ Create read-only balance API

### PHASE 3: Feature Parity (Week 3)
1. ✅ Multi-level approval display in employee portal
2. ✅ Metrics/reports consistency
3. ✅ Shift assignment consistency

### PHASE 4: Testing & Validation (Week 4)
1. ✅ Data sync tests
2. ✅ Approval workflow tests
3. ✅ Balance deduction tests
4. ✅ Duplicate prevention tests

---

## Database Integrity Check Script

```sql
-- Find duplicate attendance records
SELECT employee_id, attendance_date, recorded_by, COUNT(*) as count
FROM ta_attendance
GROUP BY employee_id, attendance_date, recorded_by
HAVING count > 1;

-- Check leave balance consistency
SELECT lb.employee_id, lb.leave_type_id, 
       lb.opening_balance, lb.used_balance, lb.remaining_balance,
       (lb.opening_balance - lb.used_balance) as calculated_remaining
FROM ta_leave_balances lb
WHERE lb.remaining_balance != (lb.opening_balance - lb.used_balance);

-- Check approved leaves not deducting balance
SELECT lr.*, lb.remaining_balance
FROM ta_leave_requests lr
LEFT JOIN ta_leave_balances lb ON lr.employee_id = lb.employee_id 
                                AND lr.leave_type_id = lb.leave_type_id
WHERE lr.status IN ('Approved', 'Final-Approved')
AND lr.balance_deducted = 0;
```

---

## Summary Table

| Feature | Employee Portal | Admin Portal | Status |
|---------|---|---|---|
| Time In/Out Recording | ✅ | ✅ | Separate implementations |
| Attendance Approval | ❌ | ✅ | **Missing in Employee** |
| Leave Submission | ✅ | ✅ | Separate implementations |
| Multi-level Approval | ❌ | ✅ | **Missing in Employee** |
| Balance Deduction | ❌ | ✅ | **Missing in Employee** |
| Daily Leave Records | ❌ | ✅ | **Missing in Employee** |
| Shift Assignment | ❌ | ✅ | **Missing in Employee** |
| Leave Balance Retrieval | Limited | ✅ | **Limited in Employee** |
| Metrics Calculation | Limited | ✅ | **Limited in Employee** |
| Overtime Tracking | ❌ | ✅ | **Missing in Employee** |
| Mobile Responsive | ✅ | ✅ | Both optimized |
| API-based | Partial | Full | **Inconsistent** |

---

## Conclusion

The system has **two parallel implementations** of the same functionality with **different maturity levels**:

- **Employee Portal**: Modern UI/UX but missing critical business logic
- **Admin Portal**: Complete functionality but harder to use for employees

**Recommendation:** Consolidate by having **Employee Portal use Admin Portal APIs** as backend while maintaining separate frontend experiences.

