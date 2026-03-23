# Leave & Absence Integration Guide

## Overview

When an employee's **leave request is approved by HR**, they are automatically marked as **excused** for all absences/late arrivals during the leave period.

This ensures that approved leave days don't count against an employee's absence/late records.

---

## How It Works

### Scenario 1: Employee Takes Approved Leave

1. **Employee submits leave request** for March 10-12, 2026
2. **Department Head approves** the leave
3. **HR approves the leave** (Final approval - `APPROVED_BY_HR`)
4. **System automatically:**
   - Checks for any ABSENT/LATE records on March 10, 11, 12
   - Marks them as excused
   - Tags them as "Leave Approved"
   - Links them to the leave request

5. **Employee sees:**
   - In "My Absence & Late Appeals" → "Excused - Leave Approved"
   - No manual appeal needed

6. **HR sees:**
   - In "Absence & Late Management" → Green "Leave Approved" badge
   - Reason shows "Approved Leave"
   - Cannot be manually rejected

### Scenario 2: Leave is Rejected

1. **Employee submits leave request**
2. **HR rejects the leave**
3. **System automatically:**
   - Removes the "Leave Approved" excuse tag
   - Reverts records to unexcused
   - Employee can now submit manual excuse if needed

---

## Database Integration

### New Columns in `ta_absence_late_records`

```sql
excuse_type ENUM('MANUAL_APPEAL', 'APPROVED_LEAVE')
leave_request_id INT (FK to ta_leave_requests)
```

### Data Flow

```
Leave Request Submitted
         ↓
   Department Head Reviews
         ↓
   HR Final Approval
         ↓
   LeaveAbsenceHelper::onLeaveApproved()
         ↓
   Check attendance for leave dates
         ↓
   Mark ABSENT/LATE as excused
         ↓
   Link to leave request
         ↓
   Update monthly thresholds
```

---

## UI Labels & Badges

### Employee Dashboard (`my_absence_appeals.php`)

| Scenario | Display |
|----------|---------|
| Leave Approved, Absent | "Excused - Leave Approved" (Green) |
| Leave Approved, Late | "Excused - Leave Approved" (Green) |
| Manual Appeal Pending | "PENDING" (Yellow) |
| Manual Appeal Approved | "APPROVED" (Green) |
| Manual Appeal Rejected | "REJECTED" (Red) |

### HR Dashboard (`absence_late_management.php`)

| Scenario | Badge | Reason | Actions |
|----------|-------|--------|---------|
| Leave Approved | ✓ Leave Approved | Approved Leave | View only |
| Manual Appeal Pending | Pending Review | (Employee reason) | Approve/Reject |
| Manual Appeal Approved | Approved | (Employee reason) | View only |
| Manual Appeal Rejected | Rejected | (Employee reason) | View only |

---

## Key Features

### ✅ Automatic Processing
- No manual entry needed when leave is approved
- Real-time updates to absence records
- Automatic threshold recalculation

### ✅ Linked Records
- Each excused record links to the leave request
- Can view related leave request details
- Audit trail shows approval source

### ✅ Reversal Support
- If leave is rejected, excuse status is reversed
- Employee can then submit manual appeal
- No data loss

### ✅ Distinction
- System distinguishes between:
  - Leaves that granted an excuse automatically
  - Manual appeals processed by HR
- Different labels and colors for clarity

---

## Implementation Details

### Files Modified

1. **Database Migration**
   ```
   migrations/002_add_absence_late_management.sql
   ```
   - Added `excuse_type` column
   - Added `leave_request_id` foreign key

2. **Models**
   ```
   app/models/AbsenceLateMgmt.php
   ```
   - `markAbsencesExcusedByLeave()` - Mark absences as excused
   - `reverseLeaveExcuse()` - Reverse when leave rejected
   - `getExcuseLabel()` - Get display label

3. **Controllers**
   ```
   app/controllers/LeaveController.php
   ```
   - Modified `approve()` method
   - Modified `reject()` method
   - Calls LeaveAbsenceHelper on approve/reject

4. **Helpers**
   ```
   app/helpers/LeaveAbsenceHelper.php (NEW)
   ```
   - `onLeaveApproved()` - Main integration point
   - `onLeaveRejected()` - Reversal handling
   - Badge/label generators

5. **UI**
   ```
   public/absence_late_management.php
   public/my_absence_appeals.php
   ```
   - Show "Leave Approved" label
   - Distinguish from manual appeals

---

## Technical Flow

### When Leave is Approved (HR clicks Approve)

```php
// In LeaveController.php - approve() method
if ($is_hr && $status === 'APPROVED_BY_HR') {
    // Deduct leave balance
    $this->leaveModel->deductLeaveBalance(...);
    
    // NEW: Mark absences as excused
    LeaveAbsenceHelper::onLeaveApproved($leave_request_id);
}
```

### In LeaveAbsenceHelper.php

```php
public static function onLeaveApproved($leave_request_id)
{
    // Get leave request details
    $leaveRequest = $leaveModel->getById($leave_request_id);
    
    // Mark all absences during leave period
    $absenceLateMgmt->markAbsencesExcusedByLeave(
        $leaveRequest['employee_id'],
        $leave_request_id,
        $leaveRequest['start_date'],
        $leaveRequest['end_date']
    );
}
```

### In AbsenceLateMgmt.php

```php
public function markAbsencesExcusedByLeave(...)
{
    // For each day in leave period:
    // 1. Check if absence/late record exists
    // 2. If exists: UPDATE to mark as excused
    // 3. If not exists: INSERT new excused record
    // 4. Link to leave request
    // 5. Update monthly thresholds
}
```

---

## API Endpoints

No new endpoints needed - integration handled internally.

The existing approval endpoints now trigger the integration automatically:

```bash
POST /app/api/approve_leave_hr.php
{
    "leave_request_id": 5,
    "action": "APPROVE"
}
```

---

## Examples

### Example 1: 5-Day Leave Approved

**Input:**
- Leave Request ID: 123
- Employee: John Doe
- Period: March 10-14, 2026 (5 business days)
- John had no time_in on March 10, 11, 12

**Result:**
- Records created/updated for March 10, 11, 12
- Marked as ABSENT (because no time_in)
- Marked as excused with reason "Approved Leave"
- Linked to Leave Request #123

**Display in HR Dashboard:**
- Type: ABSENT
- Status: APPROVED
- Excused: ✓ Leave Approved (Green Badge)
- Reason: "Approved Leave"

---

### Example 2: Leave Rejected After Initial Approval

**Input:**
- Leave Request ID: 125
- Initially was APPROVED_BY_HR
- HR then rejects it

**Result:**
- Records that were marked as excused are reverted
- is_excused set to 0
- excuse_status set to PENDING
- leave_request_id cleared
- Employee can now manually submit excuse

---

## Testing

### Test Case 1: Approve Leave with Absences

1. Employee takes leave March 1-3
2. Employee was absent those days (no time_in)
3. HR approves leave
4. Check `absence_late_management.php`
   - Should show "Leave Approved" badge
   - No action buttons
   - Linked to leave request

### Test Case 2: Reject Leave

1. Employee takes approved leave March 1-3
2. Absence records marked as excused
3. HR rejects the leave
4. Check `absence_late_management.php`
   - Should show unexcused
   - Status back to normal

### Test Case 3: Late Arrival During Leave

1. Employee takes leave March 1-3
2. Employee arrives late on March 1 (time_in 10:30 AM)
3. HR approves leave
4. Check records
   - Should be marked as excused
   - Type still shows "LATE"
   - But labeled as "Excused - Leave Approved"

---

## Reports

CSV reports include:
- Excuse Type (MANUAL_APPEAL / APPROVED_LEAVE)
- Leave Request ID (if applicable)
- All other existing fields

**Report Example:**
```
Employee,Type,Date,Status,Excused,Excuse Type,Leave Request ID,Reason
John Doe,ABSENT,2026-03-10,APPROVED,Yes,APPROVED_LEAVE,123,Approved Leave
Jane Smith,ABSENT,2026-03-11,PENDING,No,MANUAL_APPEAL,,Doctor appointment
```

---

## Permissions

- **Employee:** Can view their own records (including leave-based excuses)
- **HR/Time:** Can view all records, but cannot manually reject leave-based excuses
- **System:** Automatically creates and manages leave-based excuses

---

## Troubleshooting

### Issue: Leave approved but absences not marked as excused

**Solution:**
1. Check if `LeaveAbsenceHelper::onLeaveApproved()` is being called
2. Verify database has the new columns: `excuse_type`, `leave_request_id`
3. Check error logs for exceptions
4. Manually run migration: `002_add_absence_late_management.sql`

### Issue: Leave rejected but absences still show as excused

**Solution:**
1. Check if `LeaveAbsenceHelper::onLeaveRejected()` is being called
2. Verify SQL has correct WHERE clause for `leave_request_id`
3. Clear browser cache and refresh

### Issue: Absences created but leave_request_id is NULL

**Solution:**
1. Verify `leave_request_id` parameter is passed correctly
2. Check if database foreign key is set up correctly
3. Confirm leave request exists with correct ID

---

## Future Enhancements

1. **Notification:** Notify employee when leave-based excuse is applied
2. **Dashboard Widget:** Show "X days excused by leave"
3. **Bulk Operations:** Approve multiple leaves at once
4. **Policies:** Configure which leave types grant automatic excuses
5. **History:** Track all excuse changes with timestamp
6. **Appeal Override:** Allow HR to override leave-based excuse if needed

---

## Summary

The integration between Leave Requests and Absence/Late Management ensures:

✅ **Automated Processing** - No manual entry when leave approved
✅ **Accurate Records** - Approved leave days don't count against employee
✅ **Clear Labels** - Employees see "Excused - Leave Approved"
✅ **Audit Trail** - Links absence to leave request
✅ **Reversible** - Can undo if leave is rejected
✅ **Distinction** - Separates auto-excuses from manual appeals

This creates a seamless, transparent system that employees understand and HR can easily manage!
