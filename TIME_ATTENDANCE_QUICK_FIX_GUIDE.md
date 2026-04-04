# Quick Connection Issues Summary

## 🔴 CRITICAL GAPS (Fix Immediately)

### 1. Leave Balance NOT Deducting
**Problem:** Employee submits leave → Portal doesn't update `ta_leave_balances`
```
Employee Portal: INSERT ta_leave_requests ❌ UPDATE ta_leave_balances
Admin Portal:    INSERT ta_leave_requests ✅ UPDATE ta_leave_balances ✅ INSERT ta_leave_daily_records
```
**Fix:** Add balance deduction in TimeAttendancePortalController

### 2. Attendance Approval Bypassed
**Problem:** Employee records time-in as 'PRESENT' directly (should be 'PENDING_APPROVAL')
```
ta_attendance.is_approved = 0 always (never updated by employee portal)
ta_attendance.approved_by = NULL always
```
**Fix:** Employee records with status='PENDING_APPROVAL', admin approves later

### 3. No Daily Leave Records
**Problem:** Employee submits 5-day leave but no daily breakdown created
```
ta_leave_daily_records: 0 records from employee portal
                        ✅ 5 records from admin portal (with holiday handling)
```
**Fix:** Create ta_leave_daily_records entries after leave approval

---

## ⚠️ MAJOR ISSUES (Fix Soon)

### 4. Duplicate Attendance Records
**Problem:** If both employee and admin record time-in on same day = 2 records
```sql
SELECT COUNT(*) FROM ta_attendance 
WHERE employee_id = 1 AND attendance_date = '2026-04-01'
-- Result: 2 (should be 1 with time_in + time_out)
```
**Fix:** Add unique constraint or check before insert

### 5. Inconsistent Column Mapping
**Problem:** Employee form uses `reason` but table expects `details`
```php
// Employee Portal
$reason = $_POST['reason'];  // ❌ Field name mismatch
INSERT INTO ta_leave_requests (details) VALUES (...)

// Admin Portal  
$details = $_POST['details'];  // ✅ Correct
```
**Fix:** Standardize on `details` field name

### 6. No Shift Context in Employee Attendance
**Problem:** Employee time-in doesn't include shift_id
```sql
ta_attendance.shift_id = NULL (from employee portal)
ta_attendance.shift_id = 3    (from admin portal)
```
**Fix:** Query employee's shift before recording time

### 7. API Endpoint Fragmentation
**Problem:** Two separate APIs doing same thing
```
Employee Portal: attendance-confirm.php → TimeAttendancePortalController
Admin Portal:    time_in.php, time_out.php → AttendanceController
```
**Fix:** Consolidate to use admin APIs as single source

---

## 📊 DATA COMPARISON

### Attendance Record Flow
```
EMPLOYEE SIDE:                          ADMIN SIDE:
Form submit                             API call
    ↓                                       ↓
attendance-confirm.php                  time_in.php/time_out.php
    ↓                                       ↓
INSERT ta_attendance                    AttendanceController validation
  (basic fields only)                     ↓
                                        INSERT ta_attendance
                                          (with shift, metrics)
                                        Update ta_attendance_metrics
```

### Leave Request Flow
```
EMPLOYEE SIDE:                          ADMIN SIDE:
Form submit                             API call
    ↓                                       ↓
employee_dashboard.php POST             submit_leave.php
    ↓                                       ↓
INSERT ta_leave_requests                Leave Model validation
  status = 'Pending'                      ↓
  (no approval workflow)                INSERT ta_leave_requests
                                          status = 'Pending'
                                        Later: Approval workflow
                                          - Dept Head → 'Approved'
                                          - HR Admin → 'Final-Approved'
                                        Deduct balance + daily records
```

---

## 🔧 QUICK FIX CHECKLIST

### For Employee Portal (`employee_portal/time_attendance_portal/`)

- [ ] **Attendance Recording** 
  - [ ] Check for existing record before inserting
  - [ ] Set status = 'PENDING_APPROVAL' instead of 'PRESENT'
  - [ ] Query and include shift_id
  - [ ] Don't set is_approved = 1

- [ ] **Leave Submission**
  - [ ] Use 'details' field (not 'reason')
  - [ ] Validate against ta_leave_balances before insert
  - [ ] Call admin API after approval for balance deduction
  - [ ] Create ta_leave_daily_records with holiday handling

- [ ] **API Consolidation**
  - [ ] Redirect attendance-confirm.php to admin APIs
  - [ ] Create wrapper for leave balance API
  - [ ] Route leave submission through admin API

- [ ] **Data Validation**
  - [ ] Add unique constraint check (employee_id + date + recorded_by)
  - [ ] Validate shift exists
  - [ ] Validate leave type is active
  - [ ] Validate employee has sufficient balance

---

## 🔗 Key Files to Modify

### Priority 1: Attendance Logic
```
employee_portal/time_attendance_portal/controllers/TimeAttendancePortalController.php
  - Line: confirmAttendance() method
  - Add: Unique check, shift lookup, status='PENDING_APPROVAL'
```

### Priority 2: Leave Logic  
```
employee_portal/time_attendance_portal/controllers/TimeAttendancePortalController.php
  - Line: Leave submission handler
  - Add: Balance validation, daily records creation, approval workflow
```

### Priority 3: API Endpoints
```
employee_portal/api/attendance-confirm.php
  - Redirect to time_attendance/app/api/time_in.php
  
employee_portal/time_attendance_portal/
  - Create API wrappers for admin endpoints
```

---

## 📈 Implementation Steps

### Step 1: Add Balance Deduction (1 hour)
```php
// In TimeAttendancePortalController->confirmLeave()
$leaveBalance = $conn->prepare("
    SELECT remaining_balance FROM ta_leave_balances 
    WHERE employee_id = ? AND leave_type_id = ? AND year = ?
");
$leaveBalance->execute([$employee_id, $leave_type_id, date('Y')]);
$balance = $leaveBalance->fetch(PDO::FETCH_ASSOC);

if ($balance['remaining_balance'] < $requested_days) {
    return ['success' => false, 'message' => 'Insufficient balance'];
}

// After approval
$conn->prepare("
    UPDATE ta_leave_balances 
    SET remaining_balance = remaining_balance - ?
    WHERE employee_id = ? AND leave_type_id = ?
")->execute([$requested_days, $employee_id, $leave_type_id]);
```

### Step 2: Change Attendance Status (30 min)
```php
// In TimeAttendancePortalController->confirmAttendance()
// OLD:
$status = 'PRESENT';

// NEW:
$status = 'PENDING_APPROVAL';
$is_approved = 0;
$approved_by = NULL;
```

### Step 3: Add Daily Leave Records (1 hour)
```php
// After leave approval
for ($date = $start; $date <= $end; $date += 86400) {
    $isHoliday = checkIfHoliday($date);
    $conn->prepare("
        INSERT INTO ta_leave_daily_records 
        (leave_request_id, employee_id, leave_date, leave_type_id, is_holiday)
        VALUES (?, ?, ?, ?, ?)
    ")->execute([
        $leave_id, $employee_id, 
        date('Y-m-d', $date), 
        $leave_type_id, 
        $isHoliday ? 1 : 0
    ]);
}
```

### Step 4: Add Shift Lookup (30 min)
```php
// Before recording attendance
$shift = $conn->prepare("
    SELECT tes.shift_id FROM ta_employee_shifts tes
    WHERE tes.employee_id = ? AND tes.shift_date <= ? AND tes.end_date >= ?
    ORDER BY tes.shift_date DESC LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

$shift_id = $shift['shift_id'] ?? NULL;
```

### Step 5: Prevent Duplicates (30 min)
```php
// Before insert
$existing = $conn->prepare("
    SELECT attendance_id FROM ta_attendance
    WHERE employee_id = ? AND attendance_date = ? LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    // Update instead of insert
    $conn->prepare("UPDATE ta_attendance SET time_in = ? WHERE attendance_id = ?")->execute([$time_in, $existing['attendance_id']]);
} else {
    // Insert new record
}
```

---

## Testing Checklist

- [ ] Employee submits leave → Balance decreases ✅
- [ ] Employee records time-in → Shows as PENDING_APPROVAL ✅
- [ ] Admin approves time-in → Updates is_approved ✅
- [ ] Admin approves leave → Creates daily records ✅
- [ ] No duplicate attendance for same employee-date ✅
- [ ] Shift_id populated in attendance ✅
- [ ] Holiday handling in leave calculation ✅
- [ ] Leave balance validation before submission ✅

---

## Notes

- Database uses `date_submitted` not `created_at` for leave requests
- Database uses `details` not `reason` for leave request reasons
- Status workflow: Pending → Approved → Final-Approved (3 levels in admin)
- Balance deduction happens on APPROVAL, not submission
- Holidays don't deduct leave balance

