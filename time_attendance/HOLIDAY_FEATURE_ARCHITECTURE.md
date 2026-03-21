# Holiday Feature Architecture

## System Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                     USER INTERFACE LAYER                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌──────────────────┐  ┌──────────────────┐  ┌────────────────┐ │
│  │ Dashboard Widget │  │  Calendar View   │  │  Setup Page    │ │
│  │                  │  │                  │  │                │ │
│  │ • Next Holiday   │  │ • Color-coded    │  │ • Initialize   │ │
│  │ • Countdown      │  │ • Popup details  │  │ • Sync Status  │ │
│  │ • Sync Button    │  │ • Category info  │  │ • Manual Sync  │ │
│  └──────────────────┘  └──────────────────┘  └────────────────┘ │
│         ↓                    ↓                        ↓           │
└─────────────────────────────────────────────────────────────────┘
          │                    │                        │
          ↓                    ↓                        ↓
┌─────────────────────────────────────────────────────────────────┐
│                     FRONTEND LAYER (JS)                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │            holiday_calendar.js                             │ │
│  │ • Event generation • Color mapping • Click handlers        │ │
│  └────────────────────────────────────────────────────────────┘ │
│                           ↓                                       │
└─────────────────────────────────────────────────────────────────┘
          │
          ↓
┌─────────────────────────────────────────────────────────────────┐
│                        API LAYER (REST)                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌─────────────────────────────────────────────────────────────┐│
│  │               holiday_api.php                               ││
│  │  ┌───────────┬──────────┬──────────┬─────────┬────────┐   ││
│  │  │ get_all   │ upcoming │ is_hol   │ create  │ delete │   ││
│  │  ├───────────┼──────────┼──────────┼─────────┼────────┤   ││
│  │  │ update    │ sync     │sync_info │ range   │        │   ││
│  │  └───────────┴──────────┴──────────┴─────────┴────────┘   ││
│  └─────────────────────────────────────────────────────────────┘│
│                           ↓                                       │
└─────────────────────────────────────────────────────────────────┘
          │
          ↓
┌─────────────────────────────────────────────────────────────────┐
│                     CONTROLLER LAYER                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌──────────────────────────────────────────────────────────────┐│
│  │            HolidayController.php                             ││
│  │                                                               ││
│  │  • getAllHolidays()      • getUpcomingHolidays()             ││
│  │  • isHoliday()           • create()                          ││
│  │  • update()              • delete()                          ││
│  │  • syncHolidays()        • getSyncInfo()                     ││
│  └──────────────────────────────────────────────────────────────┘│
│                           ↓                                       │
└─────────────────────────────────────────────────────────────────┘
          │
          ↓
┌─────────────────────────────────────────────────────────────────┐
│                    BUSINESS LOGIC LAYER                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────────────┐  │
│  │  Holiday.php │  │HolidayHelper │  │ NagerDateService.php │  │
│  │   (Model)    │  │  (Helpers)   │  │  (API Service)       │  │
│  │              │  │              │  │                      │  │
│  │ • CRUD Ops   │  │ • Formatting │  │ • Fetch holidays     │  │
│  │ • Queries    │  │ • Statistics │  │ • Transform data     │  │
│  │ • Validation │  │ • Utility    │  │ • Sync logic         │  │
│  │ • Bulk Ops   │  │   functions  │  │ • Error handling     │  │
│  └──────────────┘  └──────────────┘  └──────────────────────┘  │
│                           ↓                                       │
└─────────────────────────────────────────────────────────────────┘
          │
          ↓
┌─────────────────────────────────────────────────────────────────┐
│                    INTEGRATION LAYER                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌─────────────────────────────┐  ┌──────────────────────────┐  │
│  │AttendanceHolidayIntegration │  │LeaveHolidayIntegration   │  │
│  │                             │  │                          │  │
│  │ • Skip time-in              │  │ • Validate leave         │  │
│  │ • Auto-mark as HOLIDAY      │  │ • Calculate days exclude │  │
│  │ • Absence calculation       │  │ • Check balance          │  │
│  │ • Report generation         │  │ • Mark absent correctly  │  │
│  └─────────────────────────────┘  └──────────────────────────┘  │
│                           ↓                                       │
└─────────────────────────────────────────────────────────────────┘
          │
          ↓
┌─────────────────────────────────────────────────────────────────┐
│                    DATABASE LAYER (ta_)                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌───────────────────────┐    ┌──────────────────────────────┐  │
│  │    ta_holidays        │    │  ta_holiday_sync_log         │  │
│  │                       │    │                              │  │
│  │ • id                  │    │ • id                         │  │
│  │ • name                │    │ • sync_date                  │  │
│  │ • holiday_date        │    │ • total_holidays             │  │
│  │ • is_recurring        │    │ • country_code               │  │
│  │ • country_code (PH)   │    │ • last_synced               │  │
│  │ • description         │    │ • UNIQUE(sync_date, code)   │  │
│  │ • category            │    └──────────────────────────────┘  │
│  │ • is_active           │                                      │
│  │ • created_by          │    Related Tables:                   │
│  │ • created_at          │    • ta_attendance                   │
│  │ • updated_at          │    • ta_leave_requests               │
│  │                       │    • ta_leave_balances               │
│  │ INDEX: holiday_date   │                                      │
│  │ INDEX: is_recurring   │                                      │
│  │ INDEX: is_active      │                                      │
│  └───────────────────────┘                                      │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
          │
          ↓
┌─────────────────────────────────────────────────────────────────┐
│                    EXTERNAL API LAYER                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌──────────────────────────────────────────────────────────────┐│
│  │ Nager.Date API (https://date.nager.at/api/v3)               ││
│  │                                                               ││
│  │ GET /PublicHolidays/{year}/{countryCode}                     ││
│  │                                                               ││
│  │ Response: JSON array of holidays with:                       ││
│  │ • date, localName, name, countryCode, fixed, types          ││
│  └──────────────────────────────────────────────────────────────┘│
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
```

---

## Data Flow Diagram

```
INITIALIZATION FLOW
═════════════════════════════════════════════════════════════════

Setup Page
    ↓
[Click "Sync Holidays"]
    ↓
NagerDateService::syncHolidays()
    ↓
Fetch from Nager.Date API (current year + next year)
    ↓
Transform API data
    ↓
Clear old non-recurring holidays
    ↓
Bulk insert into ta_holidays
    ↓
Log sync in ta_holiday_sync_log
    ↓
Display: "Synced X holidays"
    ↓
Database Ready ✓


ATTENDANCE FLOW (On Check-in)
═════════════════════════════════════════════════════════════════

Employee clicks "Time In"
    ↓
Check: Is today a holiday?
    ├─ YES → AttendanceHolidayIntegration
    │           ↓
    │        Skip time-in
    │           ↓
    │        Auto-record as "HOLIDAY"
    │           ↓
    │        Message: "No time-in required"
    │
    └─ NO  → Normal attendance processing
                ↓
             Record check-in
                ↓
             Mark as "PRESENT"


LEAVE REQUEST FLOW
═════════════════════════════════════════════════════════════════

Employee selects dates
    ↓
LeaveHolidayIntegration::getLeaveRequestPreview()
    ↓
Calculate:
├─ Total calendar days
├─ Weekend days (exclude)
├─ Holiday days (exclude)
└─ Actual leave days
    ↓
Show preview to employee
    ↓
Employee approves
    ↓
LeaveHolidayIntegration::createLeaveRequest()
    ↓
Validate request
    ├─ Check date order
    ├─ Check past dates
    ├─ Check holidays
    └─ Check leave balance
    ↓
Deduct calculated leave days (excluding holidays)
    ↓
Record leave request
    ↓
Confirmation: "X days deducted (Y holidays excluded)"


CALENDAR VIEW FLOW
═════════════════════════════════════════════════════════════════

User opens calendar
    ↓
FullCalendar initializes
    ↓
Call: integrateHolidaysWithCalendar(calendar)
    ↓
HolidayCalendarManager.init()
    ↓
Fetch holidays via API
    ↓
Generate FullCalendar events
    ↓
Add event styling (colors by category)
    ↓
Render on calendar
    ↓
User clicks holiday
    ↓
Show popup with:
├─ Holiday name
├─ Category badge
├─ Date & days left
└─ Recurring indicator
    ↓
User sees holiday info ✓
```

---

## Component Dependency Graph

```
                    External API
                  (Nager.Date)
                        ↑
                        │
                  NagerDateService
                        ↑
                        │
                  HolidayController
                        ↑
          ┌─────────────┼─────────────┐
          ↓             ↓             ↓
     Holiday.php  HolidayHelper  UpcomingHolidaysWidget
          ↑             ↑             ↑
          │             │             │
          └─────────────┼─────────────┘
                        │
          ┌─────────────┼─────────────┐
          ↓             ↓             ↓
      Dashboard    Calendar JS   Attendance
      Widget      Integration    Integration
          ↓             ↓             ↓
      Frontend      FullCalendar  ta_attendance
                                      ↑
                    ┌─────────────────┼──────────────┐
                    ↓                 ↓              ↓
                Leave             Absence         Holiday
              Integration       Integration      Management
                    ↓                 ↓
              ta_leave_*          ta_*            ta_holidays
```

---

## Configuration Cascade

```
holiday_config.php (Central Configuration)
        │
        ├─→ NagerDateService (API settings)
        ├─→ HolidayHelper (Display settings)
        ├─→ UpcomingHolidaysWidget (UI customization)
        ├─→ holiday_calendar.js (Colors & behavior)
        ├─→ AttendanceHolidayIntegration (Logic)
        └─→ LeaveHolidayIntegration (Logic)
```

---

## Database Relationship Diagram

```
┌──────────────────────┐
│    ta_holidays       │
├──────────────────────┤
│ id (PK)              │
│ name                 │
│ holiday_date         │ ←──── Unique by: date (no duplicate dates)
│ is_recurring         │
│ country_code (PH)    │
│ category             │
│ is_active            │
│ created_by (FK)      │ ─────→ user.id
│ created_at           │
│ updated_at           │
└──────────────────────┘
         ↑
         │ (References)
         │
    ┌────┴─────────────────────────────┐
    │                                   │
    ↓                                   ↓
┌──────────────────┐          ┌─────────────────┐
│ ta_attendance    │          │ ta_leave_*      │
├──────────────────┤          ├─────────────────┤
│ employee_id      │          │ employee_id     │
│ attendance_date  │          │ start_date      │
│ attendance_status│          │ end_date        │
│ notes            │          │ leave_days      │
└──────────────────┘          └─────────────────┘
    (HOLIDAY status)          (Calculated w/o holidays)

┌──────────────────────────┐
│ ta_holiday_sync_log      │
├──────────────────────────┤
│ id (PK)                  │
│ sync_date (part of UK)   │
│ total_holidays           │
│ country_code (part of UK)│
│ last_synced              │
└──────────────────────────┘
  (Tracks API sync history)
```

---

## Feature Matrix

```
┌──────────────────┬─────────┬─────────┬──────────┬──────────┐
│ Feature          │ Done    │ Tested  │ Docs     │ Ready    │
├──────────────────┼─────────┼─────────┼──────────┼──────────┤
│ Holiday Fetching │    ✅   │   ✅    │    ✅    │    ✅    │
│ Recurring Support│    ✅   │   ✅    │    ✅    │    ✅    │
│ Dashboard Widget │    ✅   │   ✅    │    ✅    │    ✅    │
│ Calendar Mark    │    ✅   │   ✅    │    ✅    │    ✅    │
│ Attendance Skip  │    ✅   │   ✅    │    ✅    │    ✅    │
│ Leave Calc       │    ✅   │   ✅    │    ✅    │    ✅    │
│ API Endpoints    │    ✅   │   ✅    │    ✅    │    ✅    │
│ CRUD Operations  │    ✅   │   ✅    │    ✅    │    ✅    │
│ Error Handling   │    ✅   │   ✅    │    ✅    │    ✅    │
│ ta_ Prefix       │    ✅   │   ✅    │    ✅    │    ✅    │
└──────────────────┴─────────┴─────────┴──────────┴──────────┘
```

---

**Architecture Version:** 1.0  
**Created:** March 20, 2026  
**Status:** Production Ready ✅
