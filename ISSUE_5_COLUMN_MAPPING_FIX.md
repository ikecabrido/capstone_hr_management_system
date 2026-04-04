# Issue #5: Inconsistent Column Mapping - FIXED ✅

## Problem
Employee portal forms were using `reason` field while the database table `ta_leave_requests` expects `details` field. This caused data mismatches and form processing errors.

```php
// ❌ BEFORE
$reason = $_POST['reason'];  // Field name mismatch
INSERT INTO ta_leave_requests (details) VALUES (...) // Inserting wrong field
```

## Solution
Standardized all field references across the employee portal to use `details` instead of `reason`.

## Files Modified

### 1. employee_dashboard.php
**Changes:**
- Line 1357: Changed form textarea `name="reason"` → `name="details"`
- Line 614: Changed variable from `$reason = $req['reason']` → `$details = $req['details']`

**Before:**
```php
<textarea name="reason" required placeholder="Explain why you need this leave..."></textarea>
```

**After:**
```php
<textarea name="details" required placeholder="Explain why you need this leave..."></textarea>
```

### 2. leave-request/leave_request.php
**Changes:**
- Line 92: Changed `$_POST['reason']` → `$_POST['details']`
- Line 124: Changed array key `'reason' => $reason` → `'details' => $details`
- Line 421: Changed form textarea `name="reason"` → `name="details"`

**Before:**
```php
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
$data = [
    ...
    'reason' => $reason,
    ...
];
<textarea name="reason" placeholder="..."></textarea>
```

**After:**
```php
$details = isset($_POST['details']) ? trim($_POST['details']) : '';
$data = [
    ...
    'details' => $details,
    ...
];
<textarea name="details" placeholder="..."></textarea>
```

### 3. controllers/TimeAttendancePortalController.php
**Changes:**
- Line 324: Changed `$_POST['reason']` → `$_POST['details']`
- Line 327: Updated validation to check `$details` variable
- Line 503: Changed `$_POST['reason']` → `$_POST['details']`
- Line 506: Updated validation to check `$details` variable
- Line 580: Changed execute parameter from `$reason` → `$details`

**Before:**
```php
$reason = trim($_POST['reason'] ?? '');

if (empty($leave_type_id) || empty($start_date) || empty($end_date) || empty($reason)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

$result = $insert_stmt->execute([$employee_id, $leave_type_id, $start_date, $end_date, $reason]);
```

**After:**
```php
$details = trim($_POST['details'] ?? '');

if (empty($leave_type_id) || empty($start_date) || empty($end_date) || empty($details)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

$result = $insert_stmt->execute([$employee_id, $leave_type_id, $start_date, $end_date, $details]);
```

## Impact

### ✅ What's Fixed
1. **Form Field Consistency** - All employee portal forms now use `details` field
2. **Database Mapping** - Form submission directly maps to `details` column in ta_leave_requests
3. **Data Display** - Leave history displays correctly from `details` column
4. **Controller Logic** - All POST processing uses correct field name

### ✅ Database Schema Compatibility
The database table `ta_leave_requests` was always expecting:
```sql
INSERT INTO ta_leave_requests (employee_id, leave_type_id, start_date, end_date, details, status, date_submitted)
```

Now all forms and controllers correctly use `details` field instead of `reason`.

### ✅ Admin Portal Already Correct
Admin portal (`time_attendance/` folder) was already using the correct `details` field name. This fix brings the employee portal in alignment.

## Testing Checklist

- [x] Employee submits leave through employee_dashboard.php form → `details` field captured ✅
- [x] Employee submits leave through leave_request.php form → `details` field captured ✅
- [x] Form validation checks `details` field ✅
- [x] Database insert uses `details` column ✅
- [x] Leave history display shows `details` from database ✅
- [x] TimeAttendancePortalController processes `details` field correctly ✅
- [x] Leave.php model accepts `details` parameter ✅

## Verification Commands

```sql
-- Verify leave requests have details (not reason)
SELECT leave_request_id, employee_id, details FROM ta_leave_requests LIMIT 5;

-- Count records with details populated
SELECT COUNT(*) as records_with_details 
FROM ta_leave_requests 
WHERE details IS NOT NULL AND details != '';
```

## Status
**COMPLETE ✅** - All field references standardized to use 'details' column across employee portal
