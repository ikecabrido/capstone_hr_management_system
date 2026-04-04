# Shift Assignment & Validation System

## Overview
This system ensures that employees cannot clock in or out without having a valid shift assignment for the current day. It provides comprehensive shift management and validation across the entire time & attendance system.

## Key Components

### 1. **ShiftValidator Model** (`app/models/ShiftValidator.php`)
Core model for shift assignment validation with the following methods:

- `hasShiftAssignedToday($employee_id, $date = null)` - Check if employee has shift assigned
- `getEmployeesWithoutShift()` - Get all unassigned active employees
- `getUnassignedShiftCount()` - Get count of employees without shifts
- `getAvailableShifts()` - Get all active shifts in the system
- `assignShift($employee_id, $shift_id, $effective_from, $effective_to)` - Assign shift to employee

### 2. **Shift Management UI** (`views/shift_management.php`)
HR interface for managing shift assignments featuring:
- Alert banner showing count of unassigned employees
- List of all unassigned employees with details
- Table of all available shifts
- Modal dialog for assigning shifts to employees
- Real-time visual feedback

### 3. **Shift Assignment API** (`api/shift_assignment.php`)
REST API for shift assignment operations:

#### Endpoints:
- **GET** `?action=check_employee_shift&employee_id=X&date=YYYY-MM-DD`
  - Check if employee has shift on specific date
  - Returns shift details if assigned

- **POST** `?action=assign` (JSON body)
  - Assign shift to employee
  - Required: `employee_id`, `shift_id`, `effective_from`
  - Optional: `effective_to`

- **GET** `?action=get_unassigned_count`
  - Get total count of unassigned employees

- **GET** `?action=get_unassigned_employees`
  - Get list of all unassigned employees

- **GET** `?action=get_available_shifts`
  - Get list of all available shifts

### 4. **Attendance Model Updates** (`app/models/Attendance.php`)
Updated `timeIn()` and `timeOut()` methods to:
- Validate shift assignment BEFORE recording time
- Return error message if no shift assigned
- Support flexible schedule checking via `ta_shift_assignments` table
- Create detailed response objects with success/failure status

### 5. **QR Scan Validation** (`public/qr_scan.php`)
Integrated shift validation in QR code time tracking:
- Validates shift assignment on initial QR scan
- Checks again on confirmation
- Prevents time recording if no shift assigned
- Shows user-friendly error message to employee

### 6. **Dashboard Alert** (`public/dashboard.php`)
Added alert banner that:
- Shows count of unassigned employees
- Displays warning in prominent position
- Links directly to shift management page
- Can be dismissed by user
- Only shows when there are unassigned employees

## Workflow

### For HR:
1. Dashboard shows alert if employees lack shift assignments
2. Click "Assign shifts now" link to go to shift management page
3. View list of unassigned employees
4. Click "Assign Shift" button for each employee
5. Select shift, effective date, and end date (optional)
6. Submit to save assignment

### For Employees:
1. Try to clock in via QR code
2. System checks for shift assignment
3. If assigned: proceed to time recording
4. If NOT assigned: show error "No shift assigned for today. Please contact HR"
5. HR must assign shift before employee can clock in

## Database Tables

### `ta_shift_assignments`
```
- id (PK)
- employee_id (FK)
- shift_id (FK)
- effective_from (DATE) - When assignment starts
- effective_to (DATE, nullable) - When assignment ends (NULL = ongoing)
- created_at
- created_by
```

### `ta_shifts`
```
- shift_id (PK)
- shift_name
- start_time
- end_time
- is_active
```

## Validation Rules

✅ **Requirements:**
- Employee must have active shift assignment for today
- Shift must be effective (current date between effective_from and effective_to)
- Shift must be active in system
- Effective date range must be valid

❌ **Restrictions:**
- Cannot time in without assigned shift
- Cannot time out without assigned shift
- Same validation applies to QR scanning
- All time recording blocked without valid shift

## API Response Examples

### Successful Assignment:
```json
{
  "success": true,
  "message": "Shift assigned successfully"
}
```

### No Shift Found (Error):
```json
{
  "success": false,
  "message": "No shift assigned for today. Please contact HR to assign a shift before clocking in."
}
```

### Check Shift Response:
```json
{
  "success": true,
  "has_shift": true,
  "shift": {
    "shift_id": 1,
    "shift_name": "Morning Shift",
    "start_time": "08:00:00",
    "end_time": "16:00:00"
  }
}
```

## Security

- ✅ Authentication required for all API operations
- ✅ HR role only for assignment operations
- ✅ Employee can view own shift assignments
- ✅ Audit logging on all assignment changes
- ✅ Date range validation to prevent invalid assignments

## Features

### Real-Time Validation:
- Shift validation happens immediately on time in/out
- No delays or batch processing
- Instant error feedback to employees

### Flexible Scheduling:
- Support for varying shift assignments per employee
- Date-range based assignments (can change per day)
- Multiple shifts per day not supported (by design)

### Dashboard Integration:
- Visual alert for unassigned employees
- Direct link to assignment interface
- Real-time count updates

### Mobile Friendly:
- QR code validation works on mobile
- Error messages display clearly
- Dashboard alert responsive on all devices

## Future Enhancements

- [ ] Bulk shift assignment for multiple employees
- [ ] Shift swap requests between employees
- [ ] Shift templates for departments
- [ ] Recurring shift assignments
- [ ] Shift conflict detection
- [ ] Shift transition notifications

## Troubleshooting

**Q: Employee can't clock in**
A: Check if they have shift assigned in shift_management.php. Use check_employee_shift API to verify.

**Q: API returns "Unauthorized"**
A: Ensure user is logged in and has 'time' or 'HR' role for assignment operations.

**Q: Unassigned count showing wrong number**
A: Check that employees are marked as 'Active' status. Count only includes active employees.

**Q: Dashboard alert not showing**
A: Verify ShiftValidator model is properly included and database connection is working.

## Testing Checklist

- [ ] Assign shift to test employee via UI
- [ ] Verify employee can clock in after assignment
- [ ] Verify employee cannot clock in without assignment
- [ ] Test QR code validation with/without shift
- [ ] Check dashboard alert appears/disappears correctly
- [ ] Test with flexible schedule (multiple shift changes)
- [ ] Verify API endpoints return correct responses
- [ ] Test end-date shift expiration
