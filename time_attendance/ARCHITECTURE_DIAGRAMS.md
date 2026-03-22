# Schedule Calendar - Feature Architecture & Visual Guide

## 📊 System Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                   TIME & ATTENDANCE SYSTEM                      │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Frontend (Browser)                                             │
│  ┌────────────────────────────────────────────────────────┐   │
│  │ time_attendance.php (Main Page)                        │   │
│  │ ├─ [Dashboard Tab] [Schedule Calendar Tab] ← NEW     │   │
│  │ │                                                      │   │
│  │ │ Schedule Calendar Component                         │   │
│  │ │ (app/components/calendar_schedule.php)             │   │
│  │ │ ├─ Employee Search Form                            │   │
│  │ │ │  └─ Autocomplete suggestions                     │   │
│  │ │ │                                                   │   │
│  │ │ ├─ Month View (FullCalendar)                       │   │
│  │ │ │  └─ Click day → Open Daily Timeline              │   │
│  │ │ │                                                   │   │
│  │ │ ├─ Week View                                       │   │
│  │ │ │  └─ 7-day grid with shift info                   │   │
│  │ │ │                                                   │   │
│  │ │ └─ Daily Timeline Modal (Canvas)                   │   │
│  │ │    └─ 24-hour visualization of shifts & attendance │   │
│  │ │                                                     │   │
│  │ └─ JavaScript Logic                                  │   │
│  │    (app/js/calendar_schedule.js)                     │   │
│  │    ├─ Search handling                               │   │
│  │    ├─ Calendar rendering                            │   │
│  │    ├─ Timeline drawing                              │   │
│  │    └─ API communication                             │   │
│  │                                                      │   │
│  └─ CSS Styling                                        │   │
│     (app/css/calendar_schedule.css)                    │   │
│     ├─ Calendar styling                               │   │
│     ├─ Timeline styling                               │   │
│     └─ Responsive design                              │   │
│                                                         │   │
│  ┌───────────────────────────────────────────────┐    │   │
│  │ AJAX/JSON Requests                             │    │   │
│  └─────────────────────────────────────────────────    │   │
│        ↓                                    ↓           │   │
└───────────────────────────────────────────────────────┘   │
           │                          │                      │
           ↓                          ↓                      │
      ┌────────────────┐      ┌───────────────────────┐     │
      │ Get Schedule   │      │ Save Schedule         │     │
      │     API        │      │     API               │     │
      └────────────────┘      └───────────────────────┘     │
           ↓                          ↓                      │
      ┌────────────────────────────────────────────────┐   │
      │ Backend APIs (PHP)                             │   │
      │ ├─ get_employee_schedule.php                   │   │
      │ │  └─ Fetch shifts & attendance for date range│   │
      │ │                                               │   │
      │ └─ save_employee_schedule.php                  │   │
      │    └─ Save custom shift assignments            │   │
      │                                                 │   │
      └─────────────────────────────────────────────────┘   │
           ↓                                                  │
      ┌────────────────────────────────────────────────┐   │
      │ Database (MySQL)                               │   │
      │ ├─ employees                                   │   │
      │ ├─ shifts                                      │   │
      │ ├─ employee_shifts                             │   │
      │ ├─ attendance                                  │   │
      │ ├─ custom_shifts ← NEW                        │   │
      │ └─ custom_shift_times ← NEW                   │   │
      │                                                 │   │
      └────────────────────────────────────────────────┘   │
```

---

## 🔄 Data Flow Diagram

```
User Opens Time & Attendance
         ↓
    [Dashboard Tab] - Default view
    [Schedule Calendar Tab] ← NEW
         ↓
User enters search term
         ↓
JavaScript debounces input
         ↓
Sends AJAX to component
         ↓
PHP searches employees table
         ↓
Returns JSON with matches
         ↓
Display autocomplete dropdown
         ↓
User selects employee
         ↓
selectEmployee() function called
         ↓
Fetch schedule data via API
  ├─ employee_id
  ├─ start_date (1st of month)
  └─ end_date (last of month)
         ↓
API queries database:
  ├─ Get employee info
  ├─ Get active shift
  ├─ Get attendance records
  └─ Get available shifts
         ↓
API returns JSON
         ↓
Initialize FullCalendar
         ↓
Render Month View
  ├─ Green blocks (shifts)
  └─ Blue blocks (attendance)
         ↓
User clicks day or event
         ↓
openDayTimeline() called
         ↓
Canvas draws 24-hour timeline
  ├─ Hour grid (00:00 - 23:59)
  ├─ Shift block (if scheduled)
  └─ Attendance block (if present)
         ↓
User can save changes
         ↓
POST to save API
         ↓
API creates/updates custom_shifts
         ↓
Database stores custom shift times
         ↓
Success message displayed
         ↓
Modal closes
```

---

## 🎨 UI Component Hierarchy

```
time_attendance.php (Main Page)
│
├─ Navigation Tabs
│  ├─ Dashboard
│  └─ Schedule Calendar ← NEW
│
└─ Schedule Calendar Component
   │
   ├─ Search Section
   │  ├─ Input field
   │  ├─ Search button
   │  └─ Dropdown results
   │
   ├─ Calendar Section (Hidden until employee selected)
   │  │
   │  ├─ View Tabs
   │  │  ├─ Month View
   │  │  │  └─ FullCalendar
   │  │  │     ├─ Navigation (prev/next)
   │  │  │     └─ Event display
   │  │  │
   │  │  └─ Week View
   │  │     ├─ 7-day grid
   │  │     ├─ Navigation buttons
   │  │     └─ Shift cards
   │  │
   │  └─ Timeline Modal (Popup)
   │     ├─ Header (Date & Employee)
   │     ├─ Canvas (Timeline display)
   │     │  ├─ Hour markers
   │     │  ├─ Shift blocks
   │     │  └─ Attendance blocks
   │     └─ Footer
   │        ├─ Close button
   │        └─ Save button
   │
   └─ Supporting Elements
      ├─ Styling (CSS)
      └─ Logic (JavaScript)
```

---

## 📅 Calendar Views Comparison

### Month View
```
┌─ Month Navigation ───────────┐
│ < March 2026 >              │
├──────────────────────────────┤
│ Sun │ Mon │ Tue │ Wed │ ... │
├──────────────────────────────┤
│  1  │ 🟢  │ 🟢  │ 🔵  │ ... │  ← 🟢 Shift, 🔵 Attendance
│  8  │ 🟢  │ 🟢  │     │ ... │
│ 15  │ 🟢  │ 🟢  │ 🟢  │ ... │
│ 22  │ 🟢  │     │ 🔵  │ ... │
│ 29  │ 🟢  │ 🟢  │ 🟢  │ ... │
└──────────────────────────────┘

Click any cell → Daily Timeline Modal
```

### Week View
```
┌ Week Navigation ─────────────────────────┐
│ < Previous   Current Week   Next >       │
├──────────────────────────────────────────┤
│                                          │
│ [Mon 16]  [Tue 17]  [Wed 18]  [Thu 19]  │
│ Morning   Evening   Day Off    Morning   │
│ 9:00-17:00 14:00-22:00  -----  8:00-16:00│
│                                          │
│ [Fri 20]  [Sat 21]  [Sun 22]            │
│ Morning   Day Off   Day Off              │
│ 9:00-17:00  -----    -----               │
│                                          │
└──────────────────────────────────────────┘
```

### Daily Timeline (Inside Modal)
```
┌─ Daily Schedule - Friday, March 20, 2026 - John Smith ──┐
│                                                           │
│ 00:00 ├─────────────────────────────────────────────┤   │
│       │                                             │   │
│ 04:00 ├─────────────────────────────────────────────┤   │
│       │                                             │   │
│ 08:00 ├─────────────────────────────────────────────┤   │
│       │  ┌─ Morning Shift ──────────┐              │   │
│ 09:00 │  │ 09:00 - 13:00            │              │   │
│       │  │ Morning Support          │              │   │
│ 13:00 │  └──────────────────────────┘              │   │
│       │                                             │   │
│ 14:00 ├─────────────────────────────────────────────┤   │
│       │  ┌─ Check-in ───────────────┐              │   │
│       │  │ 14:25 - 22:15             │              │   │
│       │  │ (Actual Attendance)       │              │   │
│ 22:00 │  └──────────────────────────┘              │   │
│       │                                             │   │
│ 23:59 └─────────────────────────────────────────────┘   │
│                                                           │
│                    [Close]  [Save Changes]               │
│                                                           │
└───────────────────────────────────────────────────────────┘

Green = Scheduled Shift
Blue = Actual Attendance
```

---

## 🗄️ Database Schema Diagram

```
employees (existing)
│
├─ employee_id (PK)
├─ first_name
├─ last_name
├─ employee_number
├─ status
└─ ...

    │
    ├─→ employee_shifts (existing)
    │   ├─ employee_shift_id
    │   ├─ employee_id (FK)
    │   ├─ shift_id
    │   └─ ...
    │
    └─→ custom_shifts (NEW)        ──→ custom_shift_times (NEW)
        ├─ custom_shift_id (PK)        ├─ custom_shift_time_id (PK)
        ├─ employee_id (FK)            ├─ custom_shift_id (FK)
        ├─ shift_date                  ├─ start_time
        ├─ created_at                  ├─ end_time
        └─ updated_at                  └─ created_at

attendance (existing)
│
├─ attendance_id (PK)
├─ employee_id (FK)
├─ attendance_date
├─ time_in
├─ time_out
└─ ...

shifts (existing)
│
├─ shift_id (PK)
├─ shift_name
├─ start_time
├─ end_time
└─ ...
```

---

## 🔗 API Endpoint Flow

```
CLIENT REQUEST
    │
    ├─→ GET /app/api/get_employee_schedule.php
    │   ?employee_id=5&start_date=2026-03-01&end_date=2026-03-31
    │   │
    │   └─→ Queries database:
    │       ├─ SELECT FROM employees
    │       ├─ SELECT FROM employee_shifts
    │       ├─ SELECT FROM attendance
    │       └─ SELECT FROM shifts
    │   │
    │   └─ Returns JSON:
    │       {
    │         success: true,
    │         employee: {...},
    │         current_shift: {...},
    │         available_shifts: [...],
    │         schedule: [...]
    │       }
    │
    └─→ POST /app/api/save_employee_schedule.php
        │
        ├─ Receives JSON:
        │  {
        │    employee_id: 5,
        │    date: "2026-03-20",
        │    shifts: [
        │      { start_time: "08:00", end_time: "17:00" }
        │    ]
        │  }
        │
        └─→ Database Operations:
            ├─ Start transaction
            ├─ Check/Insert custom_shifts record
            ├─ Insert custom_shift_times records
            ├─ Commit transaction
            │
            └─ Returns JSON:
                {
                  success: true,
                  message: "Schedule saved successfully"
                }
```

---

## 🎯 User Interaction Flow

```
START
  │
  ├─→ User opens Time & Attendance
  │   │
  │   └─→ Default: Dashboard Tab shown
  │
  ├─→ User clicks "Schedule Calendar" tab
  │   │
  │   └─→ Component loads (search form visible)
  │
  ├─→ User types employee name
  │   │
  │   └─→ Autocomplete shows matching employees
  │
  ├─→ User clicks employee in dropdown
  │   │
  │   ├─→ Selection stored
  │   │
  │   └─→ Calendar section appears
  │       │
  │       ├─→ Month view loads
  │       │   ├─ Green blocks = Shifts
  │       │   └─ Blue blocks = Check-ins
  │       │
  │       └─→ Week view tab available
  │
  ├─→ User clicks specific day
  │   │
  │   ├─→ Daily Timeline Modal opens
  │   │
  │   ├─→ Canvas draws 24-hour timeline
  │   │   ├─ Hour grid
  │   │   ├─ Shift blocks (if any)
  │   │   └─ Attendance blocks (if any)
  │   │
  │   └─→ User reviews schedule
  │
  ├─→ User can optionally save changes
  │   │
  │   └─→ Click "Save Changes"
  │       │
  │       ├─→ Data sent to API
  │       │
  │       ├─→ Database updated
  │       │
  │       └─→ Success message shown
  │
  └─→ User closes modal
      │
      └─→ Calendar view remains loaded
         (Can click other days or clear selection)

END
```

---

## 📊 State Management

```
Application State:
├─ selectedEmployee
│  ├─ id: number
│  └─ name: string
│
├─ currentCalendar
│  └─ FullCalendar instance
│
├─ timelineData
│  ├─ shifts: array
│  └─ attendance: object
│
├─ selectedDate
│  └─ date string (YYYY-MM-DD)
│
└─ schedule Data
   ├─ employee: object
   ├─ current_shift: object
   ├─ available_shifts: array
   └─ schedule: array
```

---

## 🎨 Color & Visual Coding

```
Element           │ Color   │ Meaning              │ Used In
──────────────────┼─────────┼──────────────────────┼──────────────
Shift Block       │ 🟢 Green│ Scheduled work time  │ Calendar, Timeline
Attendance Block  │ 🔵 Blue │ Actual check-in time │ Calendar, Timeline
Timeline Back     │ ⚪ Gray │ Available hours      │ Timeline
Hour Markers      │ 🔲 Gray │ Time reference       │ Timeline
Active Button     │ 🔷 Blue │ Selected/Active      │ Navigation
Hover State       │ ✨ Light│ Interactive element  │ All interactive
Success Message   │ 🟢 Green│ Success feedback     │ Toast/Alert
Error Message     │ 🔴 Red  │ Error feedback       │ Toast/Alert
```

---

## 📱 Responsive Design Points

```
Desktop (1200px+)
├─ Full calendar grid
├─ Side-by-side views
└─ Large timeline canvas

Tablet (768px - 1199px)
├─ Adjusted calendar size
├─ Stacked elements
└─ Medium timeline canvas

Mobile (< 768px)
├─ Single column layout
├─ Simplified calendar
├─ Swipeable views
└─ Smaller timeline canvas
```

---

## 🔐 Security Flow

```
User Request
    │
    ├─→ Session Check (auth_check.php)
    │   └─ Verify user is logged in
    │
    ├─→ AJAX Request Handler
    │   ├─ Validate employee_id
    │   ├─ Prepare SQL statement
    │   └─ Use parameterized queries
    │
    ├─→ Database Query
    │   ├─ Use prepared statements
    │   ├─ Bind parameters
    │   └─ Execute safely
    │
    └─→ Response
        ├─ Return JSON
        ├─ No direct SQL
        └─ Sanitized output
```

---

## 📈 Performance Optimization

```
Frontend:
├─ Debounced search input (300ms delay)
├─ Lazy loading calendar only when needed
├─ Canvas rendering for timeline (efficient)
└─ Event delegation for click handlers

Backend:
├─ Indexed queries on employee_id
├─ Indexed queries on shift_date
├─ Efficient date range queries
└─ Single database round-trip per view

Caching:
├─ Calendar data cached in memory
├─ Minimize re-renders
└─ Efficient state management
```

---

This architecture ensures a scalable, maintainable, and user-friendly Schedule Calendar feature!
