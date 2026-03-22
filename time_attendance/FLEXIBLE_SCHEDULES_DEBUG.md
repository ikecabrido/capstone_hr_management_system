# Flexible Schedules Debugging Guide

## Issue Summary
Creating or deleting a flexible schedule for ONE employee is affecting ALL employees. Additionally, the repeat_until column shows "-0001" year.

## Fixes Applied

### 1. Fixed repeat_until NULL Handling
**Problem**: Empty strings were being stored as invalid dates instead of NULL, causing "-0001" to appear in the display.

**Solution**: Changed from:
```php
$repeat_until = $_POST['flex_repeat_end_date'] ?? null;
```

To:
```php
$repeat_until = !empty($_POST['flex_repeat_end_date']) ? $_POST['flex_repeat_end_date'] : null;
```

This ensures that when the "Set Repeat End Date" checkbox is unchecked, the value is truly NULL instead of an empty string.

### 2. Added Debug Logging
Debug logs have been added to track what data is being submitted and inserted. Check your PHP error log for entries like:
```
DEBUG: Creating flexible schedule - Employee: EMP001, Date: 2024-03-25, Time: 10:30-15:30, Repeat Days: 0,1,2
DEBUG: Inserted for Employee: EMP001, Date: 2024-03-25, Day: 0
```

## How to Diagnose the Multi-Employee Issue

### Step 1: Check Database State
1. Navigate to: `http://localhost/capstone_hr_management_system/time_attendance/public/debug_flexible_schedules.php`
2. This page displays:
   - All flexible schedules in the database with full details
   - Schedules grouped by employee
   - Unique schedule combinations showing which employees have the same schedule

### Step 2: Check PHP Error Log
1. Find your PHP error log (usually in `xampp/php/logs/`)
2. Look for recent "DEBUG:" entries
3. Verify that:
   - Only ONE employee_id is being processed
   - Only the expected number of records are being inserted
   - employee_id values are correct (e.g., "EMP001" not empty or NULL)

### Step 3: Replicate the Issue
1. Go to Shifts Management page
2. Create a flexible schedule for ONE employee (e.g., "John Doe")
3. Select specific repeat days (e.g., Monday, Wednesday, Friday)
4. Submit the form
5. Check the debug page to see what was created
6. Check PHP error log for debug messages

### Step 4: Key Things to Verify

#### Are Multiple Employees Being Created?
- If YES: The employee_id is not being properly isolated. Check that `$employee_id` is not empty and is correctly passed from the form.
- If NO: The data is being created correctly per employee.

#### Is repeat_until Still Showing "-0001"?
- If YES: The form is still sending empty strings. Clear browser cache and try again.
- If NO: The fix is working, and NULL values are now stored correctly.

#### Are Duplicate Dates Being Created?
- If the same schedule date is appearing multiple times per employee:
  - This is expected if you selected multiple repeat days (Mon, Wed, Fri = 3 entries)
  - Each entry for a different day_of_week is correct behavior

## What's Happening Under the Hood

### Normal Behavior (Correct):
```
User selects:
- Employee: John Doe (EMP001)
- Date: March 25, 2024
- Repeat Days: Monday (1), Wednesday (3), Friday (5)

Result in Database:
- 3 records inserted, all with employee_id = "EMP001"
- Record 1: March 25, 2024, day_of_week=1
- Record 2: March 26, 2024, day_of_week=3
- Record 3: March 28, 2024, day_of_week=5
```

### Bug Behavior (Wrong):
```
Result would be:
- 9 records inserted (3 per employee)
- Or records appearing with empty/NULL employee_id
```

## Next Steps

1. **Visit the debug page** to understand current state
2. **Check PHP error log** for DEBUG messages
3. **Try creating one schedule** and report what you see
4. **Share the output** from the debug page so we can identify the exact issue

## Rollback Instructions
If you need to revert changes:
```
Original code was:
$repeat_until = $_POST['flex_repeat_end_date'] ?? null;
```

Changes made:
```
Line 142 (create): $repeat_until = !empty($_POST['flex_repeat_end_date']) ? $_POST['flex_repeat_end_date'] : null;
Line 255 (edit): $repeat_until = !empty($_POST['edit_flex_repeat_end_date']) ? $_POST['edit_flex_repeat_end_date'] : null;
```

