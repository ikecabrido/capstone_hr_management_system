# Shift Validation System - Quick Start Guide

## 🚀 Getting Started

The Shift Validation system is now ready to use! Follow these steps to initialize it.

---

## 1️⃣ Initialize Database Tables

**Run the setup script to create all required tables:**

```
1. Log in as HR user to the system
2. Go to: http://localhost/capstone_hr_management_system/time_attendance/setup.php
3. You should see "Setup Complete!" message
4. Verify all tables were created successfully
```

**What the setup does:**
- ✅ Creates `ta_shift_assignments` table (stores shift assignments)
- ✅ Creates `ta_unexpected_holidays` table (stores emergency holidays)
- ✅ Verifies shifts exist in the system
- ✅ Counts active employees
- ✅ Shows count of unassigned employees

---

## 2️⃣ Assign Shifts to Employees

**Go to the Shift Management interface:**

```
1. Log in as HR
2. Go to: http://localhost/capstone_hr_management_system/time_attendance/views/shift_management.php
   OR Click "Dashboard" → See alert banner → Click "Assign shifts now"
3. You should see list of unassigned employees
4. Click "Assign Shift" button for each employee
5. Select shift, effective date, and submit
6. Alert disappears when all employees are assigned
```

---

## 3️⃣ Test the System

### Test 1: Employee Time In Without Shift
```
1. Create a new test employee (no shift assigned yet)
2. Try to clock in (QR scan or manual)
3. Expected: ERROR - "No shift assigned for today"
4. Assign shift to employee
5. Try again → SUCCESS
```

### Test 2: Dashboard Alert
```
1. Go to dashboard
2. If unassigned employees exist → Orange alert banner shows count
3. After assigning all shifts → Alert disappears
```

### Test 3: API Endpoint
```
1. Open browser console
2. Call: GET /time_attendance/api/shift_assignment.php?action=get_unassigned_count
3. Should return: {"success": true, "count": X}
```

---

## 📋 System Requirements

### Database Tables
- ✅ `ta_shift_assignments` - Auto-created by ShiftValidator
- ✅ `ta_unexpected_holidays` - Auto-created by UnexpectedHoliday
- ✅ `ta_shifts` - Should already exist
- ✅ `employees` - Should already exist

### Database Columns in `ta_shifts`
Must have:
- `shift_id` (INT, PRIMARY KEY)
- `shift_name` (VARCHAR)
- `start_time` (TIME)
- `end_time` (TIME)
- `is_active` (BOOLEAN)

### PHP Requirements
- PDO enabled
- InnoDB database engine

---

## 🔗 Key URLs

| Purpose | URL |
|---------|-----|
| Setup Database | `/time_attendance/setup.php` |
| Shift Management | `/time_attendance/views/shift_management.php` |
| Dashboard | `/time_attendance/public/dashboard.php` |
| QR Time In | `/time_attendance/public/qr_scan.php` |
| API: Get Unassigned | `/time_attendance/api/shift_assignment.php?action=get_unassigned_count` |
| API: Check Shift | `/time_attendance/api/shift_assignment.php?action=check_employee_shift&employee_id=1` |

---

## ⚠️ Troubleshooting

### Issue: "Table doesn't exist" error
**Solution:**
1. Run setup.php again
2. Check if HR role is set correctly
3. Verify database connection

### Issue: No shifts shown in assignment modal
**Solution:**
1. Create shifts in `ta_shifts` table first
2. Ensure shifts have `is_active = 1`
3. Verify shifts have valid `start_time` and `end_time`

### Issue: Employees still appear in unassigned list
**Solution:**
1. Verify shift was actually assigned (check `ta_shift_assignments` table)
2. Check `effective_from` date is today or past
3. Check `effective_to` is NULL or today or future

### Issue: Setup script shows "Unauthorized"
**Solution:**
1. Must be logged in as HR user
2. Check session is active
3. Verify user role is 'HR'

---

## 🎯 Next Steps

After setup, you can:

1. **Configure Shifts** - Create/edit shifts for your organization
2. **Assign Shifts** - Use shift_management.php to assign employees
3. **Add Emergency Holidays** - Use unexpected_holidays.php API
4. **Monitor Attendance** - Dashboard shows real-time status
5. **Automate Detection** - Set up cron jobs for hourly detection

---

## 📞 Support

If you encounter issues:

1. Check `/logs/` directory for error logs
2. Verify database tables exist: `SHOW TABLES LIKE 'ta_%'`
3. Test API endpoints directly
4. Check browser console for JavaScript errors
5. Review code comments in respective PHP files

---

## ✅ Verification Checklist

- [ ] Setup script runs without errors
- [ ] Dashboard shows alert for unassigned employees
- [ ] Can assign shift via UI
- [ ] Employee can clock in after shift assignment
- [ ] Employee gets error before shift assignment
- [ ] QR code works with shift validation
- [ ] API endpoints respond correctly

---

**Status**: ✅ Ready to use
**Version**: 1.0
**Last Updated**: 2024
