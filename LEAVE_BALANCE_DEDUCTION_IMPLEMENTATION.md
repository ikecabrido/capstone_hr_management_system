# Leave Balance Deduction - Implementation Complete ✅

**Date:** April 1, 2026  
**Status:** Implemented and Ready for Testing  
**Impact:** Critical Business Logic Fix

---

## Summary of Changes

Fixed the leave balance deduction system to properly validate, deduct, and track employee leave requests. The system now:

1. ✅ **Validates leave balance** before allowing submission
2. ✅ **Automatically deducts balance** when leave is submitted
3. ✅ **Creates daily leave records** for detailed tracking
4. ✅ **Handles holiday dates** (don't count toward balance deduction)
5. ✅ **Displays balance information** in the leave request form
6. ✅ **Shows detailed feedback** with new balance after deduction

---

## Changes Made

### 1. Backend - Leave Balance Deduction Logic
**File:** `employee_portal/time_attendance_portal/controllers/TimeAttendancePortalController.php`

**What was fixed:**
- Added comprehensive leave type validation
- Validates employee has sufficient balance before submission
- Automatically creates missing balance records (initial allocation)
- Deducts balance immediately upon submission (for deductible leave types)
- Creates `ta_leave_daily_records` for each day of leave
- Detects and marks holiday dates in daily records
- Returns detailed response with balance information

**Logic Flow:**
```
User submits leave request
    ↓
Check leave type exists
    ↓
Check/create leave balance for current year
    ↓
Validate sufficient balance (if deductible)
    ↓
Insert leave request record
    ↓
Deduct balance immediately
    ↓
Create daily leave records (with holiday detection)
    ↓
Return success with new balance and details
```

**Key Code:** Lines 505-605 in TimeAttendancePortalController.php

---

### 2. Database - Leave Balance Query
**File:** `employee_portal/time_attendance_portal/employee_dashboard.php`

**What was changed:**
- Modified leave balance query to use LEFT JOIN
- Handles cases where no balance record exists yet
- Shows leave types even if no balance allocated
- Auto-creates missing balance records on page load
- Calculates totals, used, and remaining days

**Query Logic:**
```sql
SELECT lt.leave_type_name, 
       COALESCE(lb.remaining_balance, lt.days_per_year) as remaining_days
FROM ta_leave_types lt
LEFT JOIN ta_leave_balances lb 
WHERE lt.is_deductible = 1
```

---

### 3. Frontend - Leave Request Form UI
**File:** `employee_portal/time_attendance_portal/employee_dashboard.php`

**Added Components:**

#### A. Balance Display Section
```html
<div id="leaveBalanceInfo">
    <strong>Available Balance:</strong> <span id="balanceDisplay">-</span> days
</div>
```
- Shows available balance for selected leave type
- Updates dynamically when leave type changes
- Only visible for deductible leave types

#### B. Leave Days Calculator
```html
<div id="leaveDaysInfo">
    <strong>Total Days:</strong> <span id="totalDaysDisplay">0</span> days
</div>
```
- Calculates total days between start and end date (inclusive)
- Updates as user changes dates
- Shows note about weekday calculation

#### C. Detailed Success Message
Shows after successful submission:
```
✓ Leave request submitted successfully
📊 Leave Details:
Days Requested: 5
Holiday Count: 1
Working Days: 4
New Balance: 10.00 days
```

---

### 4. Frontend - JavaScript Functions
**File:** `employee_portal/time_attendance_portal/employee_dashboard.php`

**New Functions:**

#### `updateLeaveBalance()`
- Triggers when user selects a leave type
- Retrieves balance data from PHP array
- Updates balance display
- Shows/hides balance info based on leave type deductibility

#### `calculateLeaveDays()`
- Triggers when start or end date changes
- Calculates total days requested
- Updates display with calculation

#### Enhanced `submitLeaveRequest()`
- Shows detailed response with balance info
- Better error messaging
- Displays holiday count and working days

---

## Data Flow

### Leave Type Reference
```
ta_leave_types
├── leave_type_id: 1
├── leave_type_name: "Vacation Leave"
├── days_per_year: 15
├── is_deductible: 1  ✅ Balance will be deducted
└── requires_approval: 1
```

### Leave Balance Record
```
ta_leave_balances
├── employee_id: 1
├── leave_type_id: 1
├── year: 2026
├── opening_balance: 15.00  (from ta_leave_types.days_per_year)
├── used_balance: 5.00      (from previous approved leave)
└── remaining_balance: 10.00 (opening - used)
```

### Leave Request Record
```
ta_leave_requests
├── id: 1
├── employee_id: 1
├── leave_type_id: 1
├── start_date: 2026-04-05
├── end_date: 2026-04-10
├── details: "Family vacation"
├── status: "Pending"  (awaiting approval)
└── date_submitted: 2026-04-01 14:30:00
```

### Daily Leave Records
```
ta_leave_daily_records (Created for each day)
├── leave_request_id: 1
├── employee_id: 1
├── leave_date: 2026-04-05 (Mon)
├── leave_type_id: 1
├── is_holiday: 0  (Regular day)
└── balance_deducted: 1

├── leave_request_id: 1
├── employee_id: 1
├── leave_date: 2026-04-07 (Wed) 
├── leave_type_id: 1
├── is_holiday: 1  (Holiday - detected automatically)
└── balance_deducted: 0  (Not deducted because it's a holiday)
```

---

## Behavior Changes

### Before Fix ❌
```
User submits 5-day leave
    ↓
Leave request inserted to database
    ↓
Balance NOT updated
    ↓
Employee still sees full balance available
    ↓
Employee can submit another leave exceeding allowance ❌
```

### After Fix ✅
```
User selects leave type
    ↓
Sees available balance: 10.00 days
    ↓
Enters 5-day leave request
    ↓
Balance validated ✓ (10 >= 5)
    ↓
Leave request inserted
    ↓
Balance immediately deducted (10.00 → 5.00)
    ↓
Daily records created (with holiday detection)
    ↓
User sees new balance: 5.00 days
    ↓
Cannot submit leave exceeding new balance ✓
```

---

## Leave Type Support

### Deductible Leave Types (is_deductible = 1)
- **Vacation Leave** - 15 days/year ✓ Balance deducted
- **Sick Leave** - 10 days/year ✓ Balance deducted
- **Maternity Leave** - 5 days/year ✓ Balance deducted

### Non-Deductible Leave Types (is_deductible = 0)
- **Emergency Leave** - 3 days/year ✓ Balance NOT deducted
  - No balance validation
  - No balance deduction
  - Still creates daily records

---

## Integration with Legal & Compliance

The system is ready for leave approval integration:

1. **Current Status:** "Pending"
   - Leave submitted and balance deducted
   - Awaits approval

2. **Approval Workflow Support:**
   - Admin can approve/reject from admin panel
   - Balance already reserved (deducted at submission)
   - If rejected, balance should be refunded

3. **Audit Trail:**
   - `ta_leave_daily_records` shows daily breakdown
   - `ta_leave_requests.date_submitted` shows submission time
   - `ta_leave_balances.updated_at` tracks balance changes

---

## Testing Checklist

- [ ] Submit 5-day Vacation Leave
  - Check `ta_leave_requests` has status "Pending"
  - Check `ta_leave_balances.remaining_balance` decreased by 5
  - Check 5 records in `ta_leave_daily_records`
  - Check balance display updates

- [ ] Submit Emergency Leave (non-deductible)
  - Check `ta_leave_requests` inserted
  - Check `ta_leave_balances` NOT updated
  - Check daily records created
  - Check no balance validation

- [ ] Try to exceed balance
  - Select Vacation Leave (assume 3 days available)
  - Try to submit 5-day request
  - Should fail with message: "Insufficient Vacation Leave balance"

- [ ] Holiday Detection
  - Submit leave spanning a holiday
  - Check holiday date has `is_holiday = 1` in daily records
  - Verify balance calculation accounts for holidays

- [ ] New Employee
  - No balance record exists yet
  - Submit leave request
  - System auto-creates balance from leave type default
  - Balance deducted correctly

---

## Success Indicators

### Balance Deducted ✓
```sql
SELECT * FROM ta_leave_balances 
WHERE employee_id = 1 AND leave_type_id = 1 AND year = 2026;

-- Should show decreased used_balance and remaining_balance
```

### Daily Records Created ✓
```sql
SELECT COUNT(*) FROM ta_leave_daily_records 
WHERE employee_id = 1 AND leave_request_id = [id];

-- Should show 5 records for 5-day leave
```

### No Duplicate Submissions ✓
```sql
SELECT * FROM ta_leave_requests 
WHERE employee_id = 1 AND status = 'Pending'
ORDER BY date_submitted DESC;

-- Should see new request with correct dates and balance deducted
```

---

## Database Updates Tracking

All database updates are recorded with timestamps:

```
ta_leave_balances.updated_at → Updated when balance changes
ta_leave_daily_records.created_at → Created for each day
ta_leave_requests.date_submitted → When request was submitted
```

This enables compliance auditing and legal tracking.

---

## Next Steps for Legal & Compliance Integration

1. **Approval Workflow**
   - Admin panel to approve/reject leaves
   - Log approver details
   - Refund balance if rejected

2. **Legal Compliance**
   - Track approval chain
   - Maintain audit trail
   - Generate compliance reports

3. **Policy Enforcement**
   - Enforce minimum balance rules
   - Validate blackout dates
   - Check policy exceptions

4. **Notifications**
   - Notify when balance is low
   - Alert when approval is needed
   - Confirm when approved

---

## Files Modified

1. ✅ `employee_portal/time_attendance_portal/controllers/TimeAttendancePortalController.php`
   - Added balance validation and deduction logic

2. ✅ `employee_portal/time_attendance_portal/employee_dashboard.php`
   - Enhanced balance query
   - Added balance display form fields
   - Added JavaScript for balance updates
   - Enhanced success message

---

## Performance Notes

- Balance deduction is immediate (no async delays)
- Daily record creation is per-day loop (6 records for week = minimal overhead)
- Holiday detection query is indexed on holiday_date
- All operations wrapped in try-catch for error handling

---

## Rollback Plan (if needed)

If issues arise, the system has built-in safeguards:

```sql
-- View pending leave requests (not yet approved)
SELECT * FROM ta_leave_requests WHERE status = 'Pending';

-- Check balance calculation
SELECT 
    lb.employee_id,
    lb.leave_type_id,
    lb.opening_balance,
    lb.used_balance,
    (lb.opening_balance - lb.used_balance) as calculated_remaining,
    lb.remaining_balance
FROM ta_leave_balances lb;

-- Verify daily records
SELECT COUNT(*) FROM ta_leave_daily_records 
GROUP BY leave_request_id;
```

---

## Support Notes

If leave balance appears incorrect:

1. Check `ta_leave_balances` for employee-year-type combination
2. Verify `ta_leave_daily_records` count matches leave duration
3. Check `ta_holidays` for holiday detection
4. Review `ta_leave_requests` status ("Pending" = deducted, not yet approved)

