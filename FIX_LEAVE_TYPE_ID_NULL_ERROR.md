# SQL Error Fix: Leave Type ID Null Constraint Violation

**Date:** April 1, 2026  
**Status:** FIXED ✅  
**Error:** SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'leave_type_id' cannot be null

---

## Error Details

```
Fatal error: Uncaught PDOException: SQLSTATE[23000]: 
Integrity constraint violation: 1048 
Column 'leave_type_id' cannot be null 
in employee_dashboard.php:229
```

### Root Cause
When creating missing leave balance records for employees, the query was selecting `lb.*` (all columns from the LEFT JOINed table). When there's no matching balance record, all `lb.*` columns are NULL, including if `leave_type_id` was coming from that table.

The INSERT statement then tried to insert NULL for `leave_type_id`, violating the NOT NULL constraint.

---

## The Fix

### Query Change

**Before (Broken):**
```php
$query_balance = "SELECT lb.*, lt.leave_type_name, lt.is_deductible, lt.days_per_year,
                         COALESCE(lb.opening_balance, lt.days_per_year) as total_days,
                         COALESCE(lb.used_balance, 0) as used_days,
                         COALESCE(lb.remaining_balance, lt.days_per_year) as remaining_days
                  FROM ta_leave_types lt
                  LEFT JOIN ta_leave_balances lb ON lb.employee_id = ? AND lb.leave_type_id = lt.leave_type_id AND lb.year = ?
                  WHERE lt.is_deductible = 1";
```

**Problem:** `lb.*` includes `leave_type_id` from `ta_leave_balances`, which is NULL when no balance record exists.

**After (Fixed):**
```php
$query_balance = "SELECT lt.leave_type_id, lt.leave_type_name, lt.is_deductible, lt.days_per_year,
                         lb.leave_balance_id,
                         COALESCE(lb.opening_balance, lt.days_per_year) as total_days,
                         COALESCE(lb.used_balance, 0) as used_days,
                         COALESCE(lb.remaining_balance, lt.days_per_year) as remaining_days
                  FROM ta_leave_types lt
                  LEFT JOIN ta_leave_balances lb ON lb.employee_id = ? AND lb.leave_type_id = lt.leave_type_id AND lb.year = ?
                  WHERE lt.is_deductible = 1";
```

**Solution:** Explicitly select columns from `ta_leave_types` (like `lt.leave_type_id`, `lt.days_per_year`) and only specific columns from `ta_leave_balances` (like `lb.leave_balance_id`).

---

## What Changed

| Aspect | Before | After |
|--------|--------|-------|
| **Query Selection** | `lb.*` (all balance table columns) | `lt.leave_type_id` (from leave types) |
| **leave_type_id Source** | Nullable from balance table | Not null from types table |
| **days_per_year Source** | Explicitly from types table | Explicitly from types table |
| **leave_balance_id** | Implicit in lb.* | Explicitly selected |

---

## How It Works Now

### Query Execution Flow

```
Query runs:
    SELECT lt.leave_type_id (from leave_types)
           lt.days_per_year (from leave_types)
           lb.leave_balance_id (from balances - may be NULL)
    FROM ta_leave_types lt
    LEFT JOIN ta_leave_balances lb
    
Results:
For Vacation Leave (leave_type_id=1):
├─ Employee has balance record:
│  ├─ leave_type_id: 1 ✅
│  ├─ leave_balance_id: 5 ✅
│  └─ days_per_year: 15 ✅
│
└─ Employee doesn't have balance record:
   ├─ leave_type_id: 1 ✅ (from ta_leave_types)
   ├─ leave_balance_id: NULL ✅
   └─ days_per_year: 15 ✅ (from ta_leave_types)
```

### Auto-Create Logic

```php
foreach ($leave_balances as $balance) {
    if ($balance['leave_balance_id'] === null) {
        // leave_type_id is now guaranteed NOT NULL ✅
        // days_per_year is now guaranteed NOT NULL ✅
        
        INSERT INTO ta_leave_balances (
            employee_id,
            leave_type_id,        ← Now safe (not null)
            year,
            opening_balance,
            used_balance,
            remaining_balance
        ) VALUES (
            $employee_id,
            $balance['leave_type_id'],    ← Has value ✅
            $current_year,
            $balance['days_per_year'],    ← Has value ✅
            0,
            $balance['days_per_year']     ← Has value ✅
        );
    }
}
```

---

## Why This Works

1. **Explicit Column Selection**
   - Instead of `lb.*` (which includes NULL values from LEFT JOIN)
   - We select specific columns from each table
   - `lt.leave_type_id` always has a value (from ta_leave_types)

2. **Proper NULL Handling**
   - `lb.leave_balance_id` can be NULL (that's the indicator for "no balance record")
   - But `lt.leave_type_id` is never NULL (every leave type must exist)

3. **Data Integrity**
   - INSERT now has all required NOT NULL fields with actual values
   - No constraint violations
   - Balance records created successfully

---

## Testing Verification

### Check if Auto-Create Works

```sql
-- Query that triggered the error (now fixed)
SELECT lt.leave_type_id, 
       lt.leave_type_name, 
       lt.days_per_year,
       lb.leave_balance_id
FROM ta_leave_types lt
LEFT JOIN ta_leave_balances lb 
  ON lb.employee_id = 1 
  AND lb.leave_type_id = lt.leave_type_id 
  AND lb.year = 2026
WHERE lt.is_deductible = 1;

-- Should show:
-- leave_type_id | leave_type_name | days_per_year | leave_balance_id
-- 1             | Vacation Leave  | 15            | NULL (for new employee)
-- 2             | Sick Leave      | 10            | NULL
-- 3             | Maternity Leave | 5             | NULL

-- After page load, balance records auto-created:
SELECT * FROM ta_leave_balances 
WHERE employee_id = 1 AND year = 2026;

-- Should show 3 records (one for each leave type)
```

---

## Files Modified

| File | Change | Lines |
|------|--------|-------|
| `employee_portal/time_attendance_portal/employee_dashboard.php` | Fixed query to explicitly select lt.leave_type_id instead of lb.* | 208-217 |

---

## Impact

✅ **Fixed:** Leave balance auto-creation for new employees  
✅ **Fixed:** NULL constraint violation error  
✅ **Improved:** Query clarity (explicit column selection)  
✅ **Maintained:** All existing functionality  

---

## Related Features

This fix enables:
- ✅ Automatic balance record creation on first page load
- ✅ Display of available balance in leave form
- ✅ Balance validation before leave submission
- ✅ Holiday-aware leave calculations

All now work without database errors.
