# Holiday Feature Implementation Guide

## Overview
Complete holiday management system with Nager.Date API integration for Philippine holidays.

## ✅ What's Been Created

### 1. **Database Layer**
- `ta_holidays` - Main holiday storage table
- `ta_holiday_sync_log` - Tracks API synchronizations

### 2. **Core Components**

#### Models (`app/models/`)
- **Holiday.php** - Holiday data model with:
  - CRUD operations
  - Date-based queries
  - Recurring holiday support
  - Bulk operations
  - Sync logging

#### Services (`app/services/`)
- **NagerDateService.php** - Nager.Date API integration:
  - Fetch holidays by year
  - Transform API responses
  - Auto-sync current/next year
  - Error handling with fallback
  - Sync timestamp tracking

#### Controllers (`app/controllers/`)
- **HolidayController.php** - REST API endpoints:
  - Get all/upcoming holidays
  - Check if date is holiday
  - CRUD operations
  - API sync management

#### Helpers (`app/helpers/`)
- **HolidayHelper.php** - Utility functions:
  - Holiday checking
  - Formatting functions
  - Calendar integration helpers
  - Statistics generation
  - Leave calculation support

#### Integrations (`app/integrations/`)
- **AttendanceHolidayIntegration.php**:
  - Skip time-in on holidays
  - Auto-mark as HOLIDAY status
  - Attendance summary without holidays
  - Report generation

- **LeaveHolidayIntegration.php**:
  - Validate leave against holidays
  - Calculate leave days excluding holidays
  - Leave balance checking
  - Absence tracking without holidays

#### Components (`app/components/`)
- **UpcomingHolidaysWidget.php** - Dashboard widget:
  - Upcoming holidays display (next 30 days)
  - Countdown to next holiday
  - Last sync info
  - Manual sync button
  - Color-coded categories

#### Frontend (`app/js/`)
- **holiday_calendar.js** - Calendar integration:
  - FullCalendar integration
  - Holiday event generation
  - Click handlers
  - Color coding by category
  - Popup display

#### API (`app/api/`)
- **holiday_api.php** - REST endpoints:
  - `/app/api/holiday_api.php?action=get_all` - All holidays
  - `/app/api/holiday_api.php?action=get_upcoming` - Next 30 days
  - `/app/api/holiday_api.php?action=is_holiday&date=YYYY-MM-DD` - Check date
  - `/app/api/holiday_api.php?action=sync` - Sync from API

#### Setup (`app/setup/`)
- **holiday_setup.php** - Initialization interface:
  - Check table existence
  - Initialize holidays
  - View statistics
  - Feature verification

---

## 🚀 Integration Steps

### Step 1: Initialize Holiday System
1. Go to `time_attendance/app/setup/holiday_setup.php`
2. Click "Sync Holidays from API"
3. This will fetch PH holidays for current and next year

### Step 2: Add Dashboard Widget

In your dashboard file (e.g., `time_attendance.php`), add:

```php
<?php
require_once "app/components/UpcomingHolidaysWidget.php";
use App\Components\UpcomingHolidaysWidget;

// ... in your dashboard content area ...
$widget = new UpcomingHolidaysWidget(Database::getInstance()->getConnection());
echo $widget->render();
?>
```

**Output:**
```html
<!-- Widget displays:
- Next holiday with countdown
- Upcoming 5 holidays
- Last sync info
- Sync button
-->
```

### Step 3: Integrate Calendar

In your calendar page, add:

```html
<!-- Include the holiday calendar script -->
<script src="app/js/holiday_calendar.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // After FullCalendar initialization
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        // ... your config ...
    });
    
    calendar.render();
    
    // Integrate holidays
    integrateHolidaysWithCalendar(calendar);
});
</script>
```

**Result:**
```
- Holidays marked with colored backgrounds
- Click to see holiday details
- Distinguishes national, regional, optional holidays
- Shows recurring indicators
```

### Step 4: Update Attendance Logic

In your attendance check-in code:

```php
<?php
require_once "app/integrations/AttendanceHolidayIntegration.php";
use App\Integrations\AttendanceHolidayIntegration;

$db = Database::getInstance()->getConnection();
$attendanceIntegration = new AttendanceHolidayIntegration($db);

// Check status for today
$status = $attendanceIntegration->getAttendanceStatus(
    $employeeId,
    date('Y-m-d'),
    $hasCheckedIn,
    $checkInTime
);

if ($status['status'] === 'HOLIDAY') {
    echo "No time-in required today - it's a holiday!";
    echo $status['holiday']['name'];
} else if ($status['status'] === 'PRESENT') {
    echo "Checked in successfully";
} else {
    echo "Not checked in yet";
}

// Process attendance with holiday awareness
$result = $attendanceIntegration->processAttendance($employeeId, date('Y-m-d'));
?>
```

**Features:**
```
- Automatically skip time-in on holidays
- Auto-mark as "HOLIDAY" status in ta_attendance
- Exclude holidays from absence calculations
- Generate reports with holiday awareness
```

### Step 5: Integrate with Leave Management

In your leave request form:

```php
<?php
require_once "app/integrations/LeaveHolidayIntegration.php";
use App\Integrations\LeaveHolidayIntegration;

$db = Database::getInstance()->getConnection();
$leaveIntegration = new LeaveHolidayIntegration($db);

// When user submits leave request
$preview = $leaveIntegration->getLeaveRequestPreview(
    $_POST['start_date'],
    $_POST['end_date']
);

// Display to user
echo "Total days: " . $preview['total_calendar_days'];
echo "Leave days (excluding holidays/weekends): " . $preview['actual_leave_days'];
echo "Holidays in period: " . count($preview['holidays_in_period']);

// Create leave request
$result = $leaveIntegration->createLeaveRequest([
    'employee_id' => $employeeId,
    'start_date' => $_POST['start_date'],
    'end_date' => $_POST['end_date'],
    'leave_type' => $_POST['leave_type'],
    'reason' => $_POST['reason']
]);

if ($result['success']) {
    echo "Leave request created successfully!";
    echo "Days deducted: " . $result['leave_days'];
}
?>
```

**Features:**
```
- Calculate leave days excluding holidays
- Validate against holiday overlaps
- Check leave balance
- Show preview before submitting
- Exclude holidays from absence records
```

---

## 📊 API Endpoints Reference

### Get All Holidays
```
GET /app/api/holiday_api.php?action=get_all&year=2026&month=1
```

**Response:**
```json
{
    "success": true,
    "message": "Holidays retrieved",
    "data": {
        "holidays": [
            {
                "id": 1,
                "name": "New Year's Day",
                "holiday_date": "2026-01-01",
                "is_recurring": 1,
                "country_code": "PH",
                "category": "national"
            }
        ],
        "total": 15
    }
}
```

### Get Upcoming Holidays
```
GET /app/api/holiday_api.php?action=get_upcoming&days=30
```

### Check if Date is Holiday
```
GET /app/api/holiday_api.php?action=is_holiday&date=2026-01-01
```

**Response:**
```json
{
    "success": true,
    "data": {
        "isHoliday": true,
        "holiday": {
            "id": 1,
            "name": "New Year's Day",
            "holiday_date": "2026-01-01"
        }
    }
}
```

### Sync Holidays
```
POST /app/api/holiday_api.php?action=sync
```

### Get Sync Info
```
GET /app/api/holiday_api.php?action=sync_info
```

---

## 🎨 Customization

### Change Holiday Colors

Edit `app/helpers/HolidayHelper.php`:
```php
public static function getCategoryColor($category)
{
    $colors = [
        'national' => '#e74c3c',   // Red
        'regional' => '#f39c12',   // Orange
        'optional' => '#3498db',   // Blue
        'special' => '#9b59b6'     // Purple
    ];
    return $colors[$category] ?? '#95a5a6';
}
```

### Adjust Widget Size

Edit `app/components/UpcomingHolidaysWidget.php`:
```php
.holiday-list {
    max-height: 400px;  // Adjust this
    overflow-y: auto;
}
```

### Customize API Timeout

Edit `app/services/NagerDateService.php`:
```php
private $timeout = 10; // seconds
```

---

## 🔧 Helper Functions Quick Reference

```php
use App\Helpers\HolidayHelper;

// Initialize
HolidayHelper::init($database);

// Check if date is holiday
HolidayHelper::isHoliday('2026-01-01'); // true/false

// Get holiday by date
$holiday = HolidayHelper::getHolidayByDate('2026-01-01');

// Get next holiday
$nextHoliday = HolidayHelper::getNextHoliday();

// Days until next holiday
$days = HolidayHelper::daysUntilNextHoliday();

// Check holidays between dates
$hasHolidays = HolidayHelper::hasHolidaysBetween('2026-01-01', '2026-01-31');

// Get holidays for calendar
$events = HolidayHelper::getHolidaysForCalendar(2026, 1);

// Get statistics
$stats = HolidayHelper::getHolidayStats(2026);

// Format holiday for display
$formatted = HolidayHelper::formatHoliday($holiday);
```

---

## 🔄 Recurring Holiday Logic

Recurring holidays are marked in the database with `is_recurring = 1`. The system:

1. **Fetches** holidays marked as fixed/recurring from Nager.Date API
2. **Stores** them with recurring flag
3. **Generates** entries for each year (current + next)
4. **Auto-applies** each year without manual entry

Example:
```
New Year (Jan 1) - recurring
↓
Stored in database with is_recurring = 1
↓
Appears as holiday every January 1st
```

---

## 📋 Database Tables

### ta_holidays
```sql
id              INT PRIMARY KEY AUTO_INCREMENT
name            VARCHAR(255) - Holiday name
holiday_date    DATE - The holiday date
is_recurring    BOOLEAN - Whether it repeats yearly
country_code    VARCHAR(10) - Country code (PH)
description     TEXT - Holiday description
category        VARCHAR(50) - national/regional/optional
is_active       BOOLEAN - Active flag
created_by      INT - User who created it
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### ta_holiday_sync_log
```sql
id              INT PRIMARY KEY AUTO_INCREMENT
sync_date       DATE - Date of sync
total_holidays  INT - Total holidays synced
country_code    VARCHAR(10)
last_synced     TIMESTAMP
UNIQUE KEY unique_sync (sync_date, country_code)
```

---

## ⚙️ System Configuration

### Environment
- Database: MySQL/MariaDB with ta_ prefix tables
- API: Nager.Date (free, no authentication required)
- PHP: 7.4+ (namespaces, type hints, PDO)

### Dependencies
- FullCalendar (for calendar integration)
- Bootstrap (for styling)
- Font Awesome (for icons)

---

## 🧪 Testing

### Test Holiday Checking
```php
$holidayHelper->isHoliday('2026-01-01'); // Should return true
$holidayHelper->isHoliday('2026-01-02'); // Should return false
```

### Test API Endpoint
```bash
# Check API response
curl "http://localhost/time_attendance/app/api/holiday_api.php?action=get_upcoming&days=30"
```

### Test Attendance Integration
```php
$integration = new AttendanceHolidayIntegration($db);
$status = $integration->getAttendanceStatus($employeeId, '2026-01-01');
// Should return status: 'HOLIDAY'
```

---

## 📝 File Structure
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
├── migrations/
│   └── 003_create_holidays_table.sql
└── [main files]
```

---

## 🎯 Next Steps

1. ✅ Run `holiday_setup.php` to sync holidays
2. ✅ Add widget to dashboard
3. ✅ Integrate with calendar
4. ✅ Update attendance logic
5. ✅ Enable leave integration
6. ✅ Test all features
7. ✅ Monitor sync logs

---

## 📞 Troubleshooting

### API Sync Fails
- Check internet connection
- Verify Nager.Date API is accessible
- Check database permissions

### Holidays Not Showing
- Verify `ta_holidays` table exists
- Run sync from `holiday_setup.php`
- Check `is_active` flag is 1

### Calendar Not Displaying
- Include `holiday_calendar.js`
- Call `integrateHolidaysWithCalendar()` after calendar init
- Check browser console for errors

### Leave Calculation Wrong
- Verify holidays are synced
- Check date format (YYYY-MM-DD)
- Ensure ta_holidays table has data

---

**Version:** 1.0  
**Created:** 2026-03-20  
**Last Updated:** 2026-03-20
