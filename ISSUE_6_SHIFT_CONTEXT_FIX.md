# Issue #6: No Shift Context in Employee Attendance - FIXED ✅

## Problem
Employee time-in records were not including `shift_id`, while admin portal records did. This caused inconsistency and prevented proper overtime/shift calculations.

```sql
-- Employee Portal Records
SELECT shift_id FROM ta_attendance WHERE employee_id = 'EMP001' AND recorded_by = 'MANUAL'
-- Result: NULL (missing shift context)

-- Admin Portal Records
SELECT shift_id FROM ta_attendance WHERE employee_id = 'EMP001' AND recorded_by != 'MANUAL'
-- Result: 3 (has shift context)
```

## Solution
Added shift lookup logic that queries `ta_employee_shifts` before recording attendance to populate `shift_id` field consistently.

## Files Modified

### 1. controllers/TimeAttendancePortalController.php
**Change:** Added shift lookup query before `recordAttendance()` call

**Before (lines 195-207):**
```php
if ($action === 'time_in') {
    error_log('[TIME IN] Recording attendance for employee: ' . $employee_id);
    $result = $attendanceTracker->recordAttendance(
        $employee_id,
        date('Y-m-d'),
        date('Y-m-d H:i:s'),
        null,
        'MANUAL'
    );
```

**After (lines 195-220):**
```php
if ($action === 'time_in') {
    error_log('[TIME IN] Recording attendance for employee: ' . $employee_id);
    
    // Query employee's current shift
    $today = date('Y-m-d');
    $shiftQuery = $conn->prepare("
        SELECT shift_id FROM ta_employee_shifts 
        WHERE employee_id = ? 
        AND effective_from <= ? 
        AND (effective_to IS NULL OR effective_to >= ?)
        AND is_active = 1
        ORDER BY effective_from DESC 
        LIMIT 1
    ");
    $shiftQuery->execute([$employee_id, $today, $today]);
    $shiftRecord = $shiftQuery->fetch(PDO::FETCH_ASSOC);
    $shift_id = $shiftRecord['shift_id'] ?? null;
    error_log('[TIME IN] Shift ID for employee: ' . ($shift_id ?? 'NULL'));
    
    $result = $attendanceTracker->recordAttendance(
        $employee_id,
        date('Y-m-d'),
        date('Y-m-d H:i:s'),
        null,
        'MANUAL',
        $shift_id  // ✅ Now passing shift_id
    );
```

**Details:**
- Query checks `ta_employee_shifts` table
- Filters by `employee_id = ?` (current employee)
- Filters by `effective_from <= TODAY` (shift started)
- Filters by `effective_to >= TODAY OR IS NULL` (shift still active or indefinite)
- Filters by `is_active = 1` (only active shifts)
- Sorts by `effective_from DESC` to get the most recent shift
- Falls back to NULL if no active shift found

### 2. lib/helpers/AttendanceTracker.php
**Change:** Updated UPDATE query to include `shift_id` when updating existing records

**Before (lines 58-72):**
```php
$update_query = "UPDATE {$this->table} 
               SET time_in = ?,
                   time_out = ?,
                   recorded_by = ?,
                   status = 'PENDING_APPROVAL',
                   is_approved = 0,
                   approved_by = NULL,
                   updated_at = NOW()
               WHERE employee_id = ? AND attendance_date = ?";
$update_stmt = $this->conn->prepare($update_query);
$result = $update_stmt->execute([$time_in, $time_out, $recorded_by, $employee_id, $attendance_date]);
```

**After (lines 58-73):**
```php
$update_query = "UPDATE {$this->table} 
               SET time_in = ?,
                   time_out = ?,
                   recorded_by = ?,
                   shift_id = ?,
                   status = 'PENDING_APPROVAL',
                   is_approved = 0,
                   approved_by = NULL,
                   updated_at = NOW()
               WHERE employee_id = ? AND attendance_date = ?";
$update_stmt = $this->conn->prepare($update_query);
$result = $update_stmt->execute([$time_in, $time_out, $recorded_by, $shift_id, $employee_id, $attendance_date]);
```

**Details:**
- Added `shift_id = ?` to SET clause
- Added `$shift_id` parameter to execute() array
- Now when employee updates their time, the shift_id is also updated
- Ensures consistency if employee's shift assignment changes

## Database Schema Reference

### ta_employee_shifts Table
```sql
CREATE TABLE `ta_employee_shifts` (
  `employee_shift_id` int(11) NOT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `shift_id` int(11) NOT NULL,
  `effective_from` date NOT NULL,       -- Date shift starts
  `effective_to` date DEFAULT NULL,     -- Date shift ends (NULL = indefinite)
  `is_active` tinyint(1) DEFAULT 1,     -- Whether shift is currently active
  `created_at` timestamp,
  `updated_at` timestamp
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### ta_attendance Table (Updated By)
```sql
ALTER TABLE ta_attendance ADD shift_id INT(11) DEFAULT NULL;
-- Now populated on INSERT and UPDATE for employee portal records
```

## Impact

### ✅ What's Fixed
1. **Shift Context Captured** - Employee time-in now includes shift_id from ta_employee_shifts
2. **Consistency** - Both employee and admin portals now populate shift_id
3. **Shift Filtering** - Only active shifts within effective_from/effective_to dates are matched
4. **Update Handling** - When employee updates time, shift_id is also updated to current assignment
5. **Fallback Logic** - If no active shift found, shift_id remains NULL (graceful degradation)

### ✅ Data Flow
```
Employee clicks "Time In"
    ↓
TimeAttendancePortalController.time_in action
    ↓
Query ta_employee_shifts for active shift
    ↓
Get shift_id (or NULL if none found)
    ↓
Call attendanceTracker.recordAttendance(..., $shift_id)
    ↓
INSERT/UPDATE ta_attendance with shift_id populated
    ↓
Shift context now captured for all analysis
```

## Testing Checklist

- [x] Shift lookup query filters correctly by employee_id ✅
- [x] Shift lookup checks effective_from and effective_to dates ✅
- [x] Shift lookup only returns is_active=1 records ✅
- [x] recordAttendance() receives shift_id parameter ✅
- [x] INSERT includes shift_id in ta_attendance ✅
- [x] UPDATE includes shift_id in ta_attendance ✅
- [x] Graceful fallback to NULL if no shift found ✅
- [x] Error logging for shift_id lookup ✅

## Verification Queries

```sql
-- Verify shift_id is now populated for employee records
SELECT employee_id, attendance_date, time_in, recorded_by, shift_id 
FROM ta_attendance 
WHERE recorded_by = 'MANUAL' 
ORDER BY attendance_date DESC 
LIMIT 10;

-- Count records with shift_id populated
SELECT COUNT(*) as total_employee_records,
       SUM(CASE WHEN shift_id IS NOT NULL THEN 1 ELSE 0 END) as with_shift_id,
       SUM(CASE WHEN shift_id IS NULL THEN 1 ELSE 0 END) as without_shift_id
FROM ta_attendance 
WHERE recorded_by = 'MANUAL';

-- Check if employee has active shift
SELECT employee_id, shift_id, effective_from, effective_to, is_active
FROM ta_employee_shifts
WHERE employee_id = 'EMP001'
AND effective_from <= CURDATE()
AND (effective_to IS NULL OR effective_to >= CURDATE())
AND is_active = 1;
```

## Shift Assignment Rules

The fix handles these scenarios:
1. **Active Shift** - Employee has one active shift → Use that shift_id
2. **Multiple Shifts** - Employee has overlapping shifts → Use most recent (ORDER BY effective_from DESC)
3. **No Active Shift** - Employee has no active shift → shift_id = NULL
4. **Indefinite Shift** - effective_to IS NULL → Continues indefinitely (or until is_active = 0)
5. **Inactive Shift** - is_active = 0 → Ignored even if dates match

## Future Enhancements

Once shift_id is consistently populated:
1. **Overtime Calculations** - Compare actual hours vs shift hours
2. **Shift Reports** - Analyze attendance by shift
3. **Shift Swaps** - Track unscheduled shift changes
4. **On-Time Analysis** - Calculate tardiness against shift start time
5. **Attendance Metrics** - Per-shift attendance rates

## Status
**COMPLETE ✅** - shift_id now populated on employee time-in records with proper shift lookup and validation
