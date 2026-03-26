# Leave Request to Absence Management Integration - Summary

## What Was Added

When an **approved leave request** is confirmed by HR, employees are **automatically marked as excused** for any absences during that leave period.

---

## Quick Example

**Scenario:** Maria takes 3 days of leave (March 10-12)

1. **Before Approval:**
   - No records (not yet approved)

2. **After HR Approves:**
   - System automatically checks if Maria had any absences/late arrivals those days
   - If yes, marks them as "Excused - Leave Approved"
   - Maria doesn't need to submit any manual appeal

3. **Result for Maria:**
   - No unexcused absences on her record
   - Leave days don't count against her absence statistics
   - Clear label showing "Leave Approved"

---

## Files Changed

### Database
- Added 2 new columns to `ta_absence_late_records`:
  - `excuse_type` - Distinguishes between MANUAL_APPEAL and APPROVED_LEAVE
  - `leave_request_id` - Links to the leave request

### New Helper
- `app/helpers/LeaveAbsenceHelper.php`
  - Handles the integration between leave and absence systems
  - Called automatically when leave is approved/rejected

### Updated Model
- `app/models/AbsenceLateMgmt.php`
  - `markAbsencesExcusedByLeave()` - Auto-marks absences as excused
  - `reverseLeaveExcuse()` - Reverts if leave is rejected

### Updated Controller
- `app/controllers/LeaveController.php`
  - Now calls `LeaveAbsenceHelper::onLeaveApproved()` when HR approves
  - Now calls `LeaveAbsenceHelper::onLeaveRejected()` when HR rejects

### Updated UI
- `public/absence_late_management.php`
  - Shows green "Leave Approved" badge instead of "Yes"
  - Prevents manual actions on leave-approved absences
  
- `public/my_absence_appeals.php`
  - Shows "Excused - Leave Approved" for leave periods
  - Distinguishes from manual appeals

---

## User Experience

### For Employees

**Before Integration:**
- Takes leave for March 10-12
- Even after leave approved, might see "Absent" on record if they didn't clock in
- Had to manually appeal each absence

**After Integration:**
- Takes leave for March 10-12
- Leave approved
- Automatically marked as excused
- Clear label: "Excused - Leave Approved"
- No manual appeal needed

### For HR

**Before Integration:**
- Approve leave
- Manually mark absences as excused
- Risk of forgetting or missing days

**After Integration:**
- Approve leave
- System automatically handles all absences for those dates
- See clear "Leave Approved" label in dashboard
- Cannot accidentally reject leave-approved absences

---

## Features

✅ **Automatic:** No manual entry needed
✅ **Linked:** Each excuse linked to the leave request
✅ **Reversible:** If leave rejected, excuse is undone
✅ **Clear Labels:** "Excused - Leave Approved" vs "Excused - Manual Appeal"
✅ **Audit Trail:** Tracks which leave request caused the excuse
✅ **Smart:** Only marks actual absences, not just the leave days

---

## Technical Implementation

### When Leave is Approved:

```
HR clicks "Approve" 
   ↓
LeaveController::approve() called
   ↓
LeaveAbsenceHelper::onLeaveApproved() triggered
   ↓
System checks each day of leave period
   ↓
For each day:
  - If no time_in (ABSENT): mark as excused
  - If time_in > 9:00 AM (LATE): mark as excused
  - If not found: create new excused record
   ↓
All linked to the leave request ID
   ↓
Monthly thresholds updated
```

### When Leave is Rejected:

```
HR clicks "Reject"
   ↓
LeaveController::reject() called
   ↓
LeaveAbsenceHelper::onLeaveRejected() triggered
   ↓
All linked records are reverted
   ↓
Marks changed back to unexcused
   ↓
Employee can now manually appeal if needed
```

---

## Database Changes

### Migration File
```
migrations/002_add_absence_late_management.sql
```

**Run this in phpMyAdmin to add the new columns:**
```sql
ALTER TABLE `ta_absence_late_records` 
ADD COLUMN `excuse_type` ENUM('MANUAL_APPEAL', 'APPROVED_LEAVE') DEFAULT 'MANUAL_APPEAL',
ADD COLUMN `leave_request_id` INT,
ADD FOREIGN KEY (`leave_request_id`) REFERENCES `ta_leave_requests`(`id`) ON DELETE SET NULL;
```

---

## Display Examples

### HR Dashboard Badge

| Status | Display |
|--------|---------|
| Leave Approved | 🟢 Leave Approved (Green badge) |
| Manual Appeal Approved | ✓ Excused (Green badge) |
| Manual Appeal Pending | ⏳ Pending Review (Yellow badge) |
| Manual Appeal Rejected | ✗ Rejected (Red badge) |

### Employee Dashboard Label

| Scenario | Label |
|----------|-------|
| Absent on approved leave | Excused - Leave Approved |
| Late on approved leave | Excused - Leave Approved |
| Manual appeal approved | Excused |
| Manual appeal pending | Pending Review |
| No excuse | Unexcused |

---

## Reports

CSV reports now include:
- Employee name
- Type (ABSENT/LATE)
- Date
- Excuse type (MANUAL_APPEAL or APPROVED_LEAVE)
- Leave request ID (if applicable)
- Reason

---

## Testing

### Test 1: Basic Leave Approval
1. Employee takes leave March 1-3
2. Employee has no time_in those days (ABSENT)
3. HR approves leave
4. Check absence_late_management.php
5. Should show "Leave Approved" badge

### Test 2: Rejection
1. Leave approved and marked as excused
2. HR rejects leave
3. Check absence_late_management.php
4. Should show "Unexcused" again

### Test 3: Late During Leave
1. Employee takes leave March 1-3
2. Late arrival on March 1 (time_in 10:30 AM)
3. HR approves leave
4. Should show excused despite late time

---

## Documentation Files

- **LEAVE_ABSENCE_INTEGRATION_GUIDE.md** - Detailed technical guide
- **ABSENCE_LATE_MANAGEMENT_GUIDE.md** - Full system guide
- **SETUP_ABSENCE_LATE_SYSTEM.md** - Setup instructions

---

## Next Steps

1. ✅ Database migration applied (run SQL)
2. ✅ Code integrated (auto-triggers on leave approval)
3. ✅ UI updated (shows "Leave Approved" label)
4. ✅ Logic complete (reverses on rejection)

**Ready to test!** No additional configuration needed.

---

## FAQ

**Q: Will existing approved leaves be retroactively processed?**
A: No, only new leave approvals will trigger the automatic excuse marking. For existing leaves, manually mark absences as needed.

**Q: What if an employee has time_in during leave days?**
A: Leave-marked excuses only apply to days with no time_in (ABSENT) or late time_in (LATE). If employee worked those days, no excuse is created.

**Q: Can HR manually edit a leave-approved absence?**
A: Leave-approved absences cannot be manually rejected. HR can only view them or delete the entire record if needed.

**Q: What happens if leave is cancelled?**
A: If leave is rejected/cancelled, the associated excuses are automatically reverted to unexcused.

**Q: Do all leave types grant automatic excuses?**
A: Yes, currently all leave types (Vacation, Sick, etc.) grant automatic excuses. Can be configured per type if needed.

---

**Status:** ✅ Complete and Ready to Use
