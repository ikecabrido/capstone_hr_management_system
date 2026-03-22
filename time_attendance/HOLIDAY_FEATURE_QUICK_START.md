# Holiday Feature - Quick Start Guide

## 🚀 Get Started in 5 Minutes

### 1. Initialize the System (1 minute)

Go to: `http://localhost/capstone_hr_management_system/time_attendance/app/setup/holiday_setup.php`

Click **"Sync Holidays from API"**

Done! Your database now has PH holidays for current and next year.

---

### 2. Add Dashboard Widget (2 minutes)

In your `time_attendance.php` or dashboard file, find where you want to display holidays:

```php
<?php
// At the top of your file
require_once "auth/database.php";
require_once "time_attendance/app/components/UpcomingHolidaysWidget.php";
use App\Components\UpcomingHolidaysWidget;

// ... your HTML content ...

<!-- Add this where you want the widget to appear -->
<div class="row">
    <div class="col-md-4">
        <?php
        $db = Database::getInstance()->getConnection();
        $widget = new UpcomingHolidaysWidget($db);
        echo $widget->render();
        ?>
    </div>
</div>
```

**Result:** Beautiful widget showing next holidays with countdown!

---

### 3. Integrate with Calendar (2 minutes)

In your calendar page:

```html
<!-- Add this in your <head> section -->
<script src="time_attendance/app/js/holiday_calendar.js"></script>

<!-- In your JavaScript after FullCalendar initializes -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ... your FullCalendar initialization ...
    
    const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        // ... your config
    });
    
    calendar.render();
    
    // ADD THIS LINE to show holidays on calendar
    integrateHolidaysWithCalendar(calendar);
});
</script>
```

**Result:** Holidays automatically marked on calendar with colors!

---

### 4. Update Attendance Checking (Optional but Recommended)

In your attendance check-in code (wherever employees click "Time In"):

```php
<?php
require_once "time_attendance/app/integrations/AttendanceHolidayIntegration.php";
use App\Integrations\AttendanceHolidayIntegration;

$db = Database::getInstance()->getConnection();
$attendance = new AttendanceHolidayIntegration($db);

// Check if today is a holiday
$status = $attendance->getAttendanceStatus($employeeId, date('Y-m-d'));

if ($status['status'] === 'HOLIDAY') {
    // Don't show time-in button
    echo "✓ No time-in required today - " . $status['holiday']['name'];
} else {
    // Show normal time-in form
    echo "Please click Time In to record your attendance";
}

// When recording attendance
$result = $attendance->processAttendance($employeeId, date('Y-m-d'));
?>
```

**Result:** Employees won't be marked absent on holidays!

---

### 5. Update Leave Form (Optional but Recommended)

In your leave request form:

```php
<?php
require_once "time_attendance/app/integrations/LeaveHolidayIntegration.php";
use App\Integrations\LeaveHolidayIntegration;

$db = Database::getInstance()->getConnection();
$leave = new LeaveHolidayIntegration($db);

if ($_POST['action'] === 'preview') {
    // Show preview when user selects dates
    $preview = $leave->getLeaveRequestPreview(
        $_POST['start_date'],
        $_POST['end_date']
    );
    
    echo "From: " . $_POST['start_date'] . " to " . $_POST['end_date'];
    echo "Leave days to deduct: " . $preview['actual_leave_days'];
    echo "Holidays in period: " . $preview['holiday_days'];
}

if ($_POST['action'] === 'submit') {
    // Create leave request
    $result = $leave->createLeaveRequest([
        'employee_id' => $employeeId,
        'start_date' => $_POST['start_date'],
        'end_date' => $_POST['end_date'],
        'leave_type' => 'VACATION',
        'reason' => $_POST['reason']
    ]);
    
    if ($result['success']) {
        echo "Leave approved! Days deducted: " . $result['leave_days'];
    }
}
?>
```

**Result:** Holidays won't be counted when calculating leave days!

---

## 📋 File Locations

All files use the **`ta_` prefix** for easy identification:

```
time_attendance/
├── app/
│   ├── models/
│   │   └── Holiday.php
│   ├── services/
│   │   └── NagerDateService.php
│   ├── controllers/
│   │   └── HolidayController.php
│   ├── helpers/
│   │   └── HolidayHelper.php
│   ├── integrations/
│   │   ├── AttendanceHolidayIntegration.php
│   │   └── LeaveHolidayIntegration.php
│   ├── components/
│   │   └── UpcomingHolidaysWidget.php
│   ├── api/
│   │   └── holiday_api.php
│   ├── js/
│   │   └── holiday_calendar.js
│   └── setup/
│       └── holiday_setup.php
└── migrations/
    └── 003_create_holidays_table.sql
```

---

## 🔑 Key Features at a Glance

| Feature | Usage | Benefit |
|---------|-------|---------|
| **Automatic Holidays** | Syncs from Nager.Date API | No manual entry needed |
| **Dashboard Widget** | Shows next holidays + countdown | Employees know what's coming |
| **Calendar Marking** | Holidays show on calendar | Clear visual distinction |
| **Attendance Exemption** | Skip time-in on holidays | No false absences |
| **Leave Calculation** | Exclude holidays from days | Fair leave balance |
| **Recurring Support** | Auto-applies each year | Maintenance-free |

---

## 🔗 Quick API Calls

Test the API in your browser:

```
Get all holidays:
http://localhost/capstone_hr_management_system/time_attendance/app/api/holiday_api.php?action=get_all&year=2026

Get upcoming:
http://localhost/capstone_hr_management_system/time_attendance/app/api/holiday_api.php?action=get_upcoming&days=30

Check if date is holiday:
http://localhost/capstone_hr_management_system/time_attendance/app/api/holiday_api.php?action=is_holiday&date=2026-01-01
```

---

## ✅ Verification Checklist

- [ ] Ran `holiday_setup.php` and synced holidays
- [ ] Widget appears on dashboard
- [ ] Calendar shows holiday dates with colors
- [ ] Attendance code uses `AttendanceHolidayIntegration`
- [ ] Leave form uses `LeaveHolidayIntegration`
- [ ] Tested with a holiday date (e.g., Jan 1)
- [ ] Verified employees aren't marked absent on holidays
- [ ] Verified holidays don't count as leave days

---

## 🐛 Quick Troubleshooting

**"Table doesn't exist" error?**
- Run the migration: `migrations/003_create_holidays_table.sql`

**"No holidays showing up?"**
- Go to setup page and click sync button
- Check database has records in `ta_holidays`

**"Calendar holidays not showing?"**
- Make sure to call `integrateHolidaysWithCalendar(calendar)`
- Check browser console for errors

**"Leave calculation seems off?"**
- Verify `ta_holidays` has current year holidays
- Try syncing again from setup page

---

## 🎯 Next Level (Advanced)

Want more customization? Check:
- `app/config/holiday_config.php` - Adjust colors, API timeout, etc.
- `app/helpers/HolidayHelper.php` - Use static methods for custom logic
- `app/integrations/` - Extend attendance/leave logic for your needs

---

## 📞 Support

**Common Questions:**

Q: Can I add custom holidays?
A: Yes! Use the controller or manually insert into `ta_holidays` table.

Q: How often does it sync?
A: Manual via setup page. Or set up a cron job to run sync automatically.

Q: What if Nager.Date API is down?
A: Your existing holidays stay in database. Just won't get new ones.

Q: Can I modify holiday dates?
A: Yes! Edit `ta_holidays` table directly or use the CRUD API.

---

## 🎉 You're Done!

Your holiday feature is now live! Employees will:
- ✅ See upcoming holidays on dashboard
- ✅ See holidays marked on calendar
- ✅ Not be marked absent on holidays
- ✅ Not lose leave balance on holidays
- ✅ Know exactly how many leave days they'll use

**Enjoy! 🎊**
