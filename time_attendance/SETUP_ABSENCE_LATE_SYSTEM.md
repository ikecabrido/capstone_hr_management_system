# Absence & Late Management System - Setup Instructions

## Quick Setup Guide

### Step 1: Install Database Tables

1. Open **phpMyAdmin**
2. Select your `hr_management` database
3. Go to **SQL** tab
4. Copy and paste the contents of:
   ```
   time_attendance/migrations/002_add_absence_late_management.sql
   ```
5. Click **Execute**

**Expected Result:** 3 new tables created:
- `ta_absence_late_records`
- `ta_absence_late_thresholds`
- `ta_absence_late_policies`

---

### Step 2: Navigate to New Pages

#### For Employees:
- **URL:** `http://localhost/time_attendance/public/my_absence_appeals.php`
- **Access:** Any logged-in employee
- **Features:**
  - View personal absence/late records
  - Submit excuses with reasons
  - Track approval status
  - Upload supporting documents

#### For HR/Time Staff:
- **URL:** `http://localhost/time_attendance/public/absence_late_management.php`
- **Access:** Users with `time` or `hr` role
- **Features:**
  - View all absence/late records
  - Filter by date, employee, type, status
  - Approve/reject excuses
  - Add notes
  - Generate CSV reports

---

### Step 3: Verify Sidebar Links

**Sidebar updates have been added automatically:**

✅ **Employee Dashboard** → Shows "My Absence & Late Appeals" link
- Visible only to employees (not HR)
- Uses calendar-times icon

✅ **HR Dashboard** → Shows "Absence & Late Management" link  
- Visible only to HR/Time staff
- Uses calendar-times icon
- Link added under "Approve Leave Requests"

---

### Step 4: Access the System

**As an Employee:**
1. Login to employee dashboard
2. Click "My Absence & Late Appeals" in sidebar
3. View your absence/late records
4. Submit excuses if needed

**As HR:**
1. Login to HR dashboard
2. Click "Absence & Late Management" in sidebar
3. View all employee records
4. Filter and review pending excuses
5. Approve/reject with notes

---

## Quick Test

### Test 1: Employee Submits Excuse
1. Go to "My Absence & Late Appeals"
2. Click "Edit Excuse" on any pending record
3. Enter reason (e.g., "Doctor appointment")
4. Click "Submit Excuse"
5. Verify status shows "PENDING"

### Test 2: HR Reviews Excuse
1. Go to "Absence & Late Management"
2. Filter for "PENDING" status
3. Click "Approve" button
4. Add review notes
5. Click "Submit Review"
6. Verify status changed to "APPROVED"

### Test 3: Generate Report
1. Go to "Absence & Late Management"
2. Set date range
3. Click "Generate Report"
4. CSV file downloads with all records

---

## Files Created/Modified

### New Files Created:
```
time_attendance/
├── migrations/
│   └── 002_add_absence_late_management.sql
├── app/
│   ├── models/
│   │   └── AbsenceLateMgmt.php
│   └── api/
│       └── absence_late_management.php
└── public/
    ├── absence_late_management.php (HR UI)
    └── my_absence_appeals.php (Employee UI)
```

### Files Modified:
```
time_attendance/
└── app/
    └── components/
        └── Sidebar.php
            ├── Added employee link: "My Absence & Late Appeals"
            └── Added HR link: "Absence & Late Management"
```

---

## Feature Summary

### Employee Can:
✅ View all their absence/late records  
✅ Submit excuse with reason  
✅ Upload supporting documents  
✅ Track approval status  
✅ See HR review notes  
✅ Edit pending excuses  
✅ Filter by status (Pending/Approved/Rejected)

### HR Can:
✅ View all absence/late records  
✅ Filter by employee, date, type, status  
✅ View employee-submitted reasons  
✅ Approve/reject excuses  
✅ Add approval notes  
✅ View monthly statistics  
✅ Generate CSV reports  
✅ Track pending reviews

---

## Configuration (Optional)

### Change Late Threshold Time
Default is 9:00 AM. To change:

1. Open `public/analytics.php`
2. Find: `getHours() > 9`
3. Change `9` to desired hour (e.g., `8` for 8:00 AM)

### Update Company Policies
In phpMyAdmin:
```sql
UPDATE ta_absence_late_policies 
SET max_late_per_month = 5,
    max_absent_per_month = 3,
    max_excused_late_per_month = 8
WHERE policy_id = 1;
```

---

## Troubleshooting

### Issue: "No tables found"
- **Solution:** Make sure SQL migration was executed successfully
- Check phpMyAdmin to verify 3 new tables exist

### Issue: Sidebar links not showing
- **Solution:** Refresh the page or clear browser cache
- Check sidebar.php was updated

### Issue: Can't see employee records
- **Solution:** Make sure you're logged in with TIME or HR role
- Verify employment status is ACTIVE

### Issue: Report download fails
- **Solution:** Check browser console for errors
- Verify CSV generation is working in API

---

## API Endpoints (For Developers)

All endpoints require authentication via session.

```
GET  /app/api/absence_late_management.php?action=get_records
GET  /app/api/absence_late_management.php?action=get_record&record_id=1
POST /app/api/absence_late_management.php?action=submit_excuse
POST /app/api/absence_late_management.php?action=review_excuse
POST /app/api/absence_late_management.php?action=add_notes
GET  /app/api/absence_late_management.php?action=get_employee_summary
GET  /app/api/absence_late_management.php?action=get_report
GET  /app/api/absence_late_management.php?action=get_summary_stats
GET  /app/api/absence_late_management.php?action=get_pending
```

---

## Support

For issues or questions:
1. Check the comprehensive guide: `ABSENCE_LATE_MANAGEMENT_GUIDE.md`
2. Review API documentation in guide
3. Check database schema: `ta_absence_late_records` table structure
4. Verify user permissions and role

---

**Status:** ✅ Ready to Use

All files are installed. The system is now ready for employees to submit absence/late excuses and HR to manage approvals!
