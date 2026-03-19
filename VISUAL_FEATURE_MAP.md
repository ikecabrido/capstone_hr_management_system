# 🎨 Visual Feature Map - Employee Dashboard

## Employee Dashboard Layout

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                   │
│                    Welcome back, [Employee Name]!                │
│                                                                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  [Live Clock: 00:00:00]                                          │
│                                                                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│              ⏰ TIME IN/OUT SECTION                              │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ Time In         Time Out        Duration                │   │
│  │ --:--           --:--           --                      │   │
│  │                                                          │   │
│  │ [Time In Button] OR [Time Out Button] - [Status Text]  │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│              📊 QUICK STATS (4 Cards)                           │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐       │
│  │ Present  │  │ Late     │  │ Hours    │  │Overtime │       │
│  │This Mon. │  │Arrivals  │  │ Worked   │  │ Hours   │       │
│  │   20     │  │   2      │  │  160.5   │  │  5.2    │       │
│  │  days    │  │ times    │  │  hours   │  │ hours   │       │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘       │
│                                                                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│              📅 TODAY'S SHIFT CARD  ← NEW!                      │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │                   Morning Shift                          │   │
│  │  ┌─────────────────┐        ┌─────────────────┐         │   │
│  │  │ Start Time      │        │ End Time        │         │   │
│  │  │ 09:00 AM        │        │ 05:00 PM        │         │   │
│  │  └─────────────────┘        └─────────────────┘         │   │
│  │         ┌──────────────────────┐                        │   │
│  │         │ Break Duration       │                        │   │
│  │         │ 1 Hour               │                        │   │
│  │         └──────────────────────┘                        │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│              📊 CHARTS                                           │
│  ┌──────────────────────────────┐  ┌──────────────────────┐    │
│  │ Attendance % This Month      │  │ 6-Month Trend       │    │
│  │      [Doughnut Chart]        │  │   [Line Chart]      │    │
│  │                              │  │                     │    │
│  │ Present  Late  Absent        │  │ Shows attendance    │    │
│  │   85%     10%    5%          │  │ pattern last 6 mo   │    │
│  └──────────────────────────────┘  └──────────────────────┘    │
│                                                                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  LEAVE BALANCE                          [➕ Request Leave] ← NEW! │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │ Sick Leave              Vacation Leave                  │    │
│  │ ┌─────────────────────┐ ┌─────────────────────────────┐ │    │
│  │ │ Total: 10 days      │ │ Total: 15 days             │ │    │
│  │ │ Used:  3 days       │ │ Used:  8 days              │ │    │
│  │ │ Remain: 7 days      │ │ Remain: 7 days             │ │    │
│  │ │ [Progress Bar: 30%] │ │ [Progress Bar: 53%]        │ │    │
│  │ └─────────────────────┘ └─────────────────────────────┘ │    │
│  └─────────────────────────────────────────────────────────┘    │
│                                                                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  📋 RECENT ATTENDANCE                                           │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │ Mar 18, 2026       Mar 17, 2026       Mar 16, 2026      │    │
│  │ In: 09:05 AM      In: 08:58 AM      In: 09:00 AM       │    │
│  │ Out: 05:15 PM     Out: 05:20 PM     Out: 05:10 PM      │    │
│  │ Status: ON TIME   Status: ON TIME   Status: LATE        │    │
│  │ Hours: 8.17h      Hours: 8.37h      Hours: 8.17h       │    │
│  └─────────────────────────────────────────────────────────┘    │
│                                                                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  📋 MY LEAVE REQUESTS  ← NEW!                                   │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ ┌────────────────────────────────────────────────────┐   │   │
│  │ │ Vacation Leave              Status: [PENDING] 🟡  │   │   │
│  │ │ Dates: Mar 25 - Mar 28, 2026                       │   │   │
│  │ │ Reason: Summer vacation                            │   │   │
│  │ │ Submitted: Mar 15, 2026 02:30 PM                   │   │   │
│  │ └────────────────────────────────────────────────────┘   │   │
│  │ ┌────────────────────────────────────────────────────┐   │   │
│  │ │ Sick Leave                  Status: [APPROVED] 🟢  │   │   │
│  │ │ Dates: Mar 10 - Mar 11, 2026                       │   │   │
│  │ │ Reason: Doctor appointment                         │   │   │
│  │ │ Submitted: Mar 08, 2026 10:15 AM                   │   │   │
│  │ └────────────────────────────────────────────────────┘   │   │
│  │ ┌────────────────────────────────────────────────────┐   │   │
│  │ │ Bereavement Leave           Status: [REJECTED] 🔴  │   │   │
│  │ │ Dates: Mar 05 - Mar 06, 2026                       │   │   │
│  │ │ Reason: Funeral arrangements                       │   │   │
│  │ │ Submitted: Mar 02, 2026 08:45 AM                   │   │   │
│  │ │ Remarks: Insufficient notice period                │   │   │
│  │ └────────────────────────────────────────────────────┘   │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔔 Time In Confirmation Modal

```
┌────────────────────────────────────────────────┐
│                                                │
│               ✓ Time In Confirmation           │
│                                                │
│  ┌─────────────────────────────────────────┐  │
│  │ Employee:                               │  │
│  │ Juan Dela Cruz                          │  │
│  │                                         │  │
│  │ Date:                                   │  │
│  │ Wednesday, March 19, 2026               │  │
│  │                                         │  │
│  │ Time:                                   │  │
│  │ 08:45:30 AM                             │  │
│  └─────────────────────────────────────────┘  │
│                                                │
│  ✓ Successfully timed in!                     │
│                                                │
│  ┌──────────────────────────────────────────┐ │
│  │              OK                          │ │
│  └──────────────────────────────────────────┘ │
│                                                │
└────────────────────────────────────────────────┘
```

---

## 🔔 Time Out Confirmation Modal

```
┌────────────────────────────────────────────────┐
│                                                │
│              ✓ Time Out Confirmation           │
│                                                │
│  ┌─────────────────────────────────────────┐  │
│  │ Employee:                               │  │
│  │ Juan Dela Cruz                          │  │
│  │                                         │  │
│  │ Date:                                   │  │
│  │ Wednesday, March 19, 2026               │  │
│  │                                         │  │
│  │ Time:                                   │  │
│  │ 05:20:15 PM                             │  │
│  └─────────────────────────────────────────┘  │
│                                                │
│  ✓ Successfully timed out!                    │
│                                                │
│  ┌──────────────────────────────────────────┐ │
│  │              OK                          │ │
│  └──────────────────────────────────────────┘ │
│                                                │
└────────────────────────────────────────────────┘
```

---

## 📝 Leave Request Modal

```
┌──────────────────────────────────────────────────────┐
│                                                      │
│  📝 Request Leave                             [×]   │
│                                                      │
│  Leave Type*                                        │
│  ┌──────────────────────────────────────────────┐  │
│  │ -- Select Leave Type --                   ▼ │  │
│  │ Sick Leave                                    │  │
│  │ Vacation Leave                                │  │
│  │ Bereavement Leave                             │  │
│  │ Personal Leave                                │  │
│  └──────────────────────────────────────────────┘  │
│                                                      │
│  Start Date*                                        │
│  ┌──────────────────────────────────────────────┐  │
│  │ 2026-03-25                                    │  │
│  └──────────────────────────────────────────────┘  │
│                                                      │
│  End Date*                                          │
│  ┌──────────────────────────────────────────────┐  │
│  │ 2026-03-28                                    │  │
│  └──────────────────────────────────────────────┘  │
│                                                      │
│  Reason for Leave*                                  │
│  ┌──────────────────────────────────────────────┐  │
│  │ Please explain why you need this leave...    │  │
│  │                                              │  │
│  │ Summer vacation with family                  │  │
│  └──────────────────────────────────────────────┘  │
│                                                      │
│  [Cancel]                     [Submit Request]      │
│                                                      │
└──────────────────────────────────────────────────────┘
```

---

## 📱 QR Scanner Interface

```
┌─────────────────────────────────────────────┐
│                                             │
│            📱 QR Time Tracker                │
│   Point camera at the QR code               │
│                                             │
│  ┌─────────────────────────────────────┐   │
│  │                                     │   │
│  │      [Camera Feed Stream]           │   │
│  │                                     │   │
│  │         ┌───────────────┐           │   │
│  │         │   ╱ ╲        │           │   │
│  │         │  ╱QR╲        │           │   │
│  │         │ ╱    ╲       │           │   │
│  │         │         ╲    │           │   │
│  │         └───────────────┘           │   │
│  │                                     │   │
│  │      Point here to scan             │   │
│  │                                     │   │
│  └─────────────────────────────────────┘   │
│                                             │
│  ℹ️ Position the QR code within the frame  │
│                                             │
│  [← Back to Dashboard]  [Stop Camera]       │
│                                             │
└─────────────────────────────────────────────┘
```

---

## 🎫 QR Code Generator Interface

```
┌─────────────────────────────────────────────┐
│                                             │
│         📱 ✓ QR Code Generator              │
│   Generate daily QR codes for tracking     │
│                                             │
│  ┌───────────────────────────────────────┐ │
│  │  How to use:                          │ │
│  │  • Click "Generate QR Code"           │ │
│  │  • Display to employees               │ │
│  │  • Employees scan to time in or out   │ │
│  │  • Valid for 1 minute only            │ │
│  └───────────────────────────────────────┘ │
│                                             │
│  ┌────────────────────────────────────┐   │
│  │        [Generate QR Code]          │   │
│  │           [← Back]                 │   │
│  └────────────────────────────────────┘   │
│                                             │
│  ┌────────────────────────────────────┐   │
│  │                                    │   │
│  │         ┌──────────────┐           │   │
│  │         │ ▀ ▄  ▄ ▀ ▄  │           │   │
│  │         │ ▀  ▀▀  ▀ ▀  │           │   │
│  │         │ ▄ ▀  ▄ ▀ ▄  │           │   │
│  │         │  ▀ ▀▀▀ ▀ ▀  │           │   │
│  │         └──────────────┘           │   │
│  │                                    │   │
│  │  Expires in: 60 seconds            │   │
│  │                                    │   │
│  │  [🖨️ Print] [⬇️ Download] [📤 Share] │   │
│  └────────────────────────────────────┘   │
│                                             │
└─────────────────────────────────────────────┘
```

---

## 🎯 Status Badge Colors

```
Pending Request
┌──────────────────┐
│ PENDING       🟡  │  ← Yellow background
└──────────────────┘

Approved Request
┌──────────────────┐
│ APPROVED      🟢  │  ← Green background
└──────────────────┘

Rejected Request
┌──────────────────┐
│ REJECTED      🔴  │  ← Red background
└──────────────────┘
```

---

## 🔄 Data Flow Diagram

```
EMPLOYEE ACTIONS
    ↓
Employee fills form & clicks "Submit Leave"
    ↓
AJAX POST to employee_dashboard.php
    ↓
Server validates all fields
    ↓
Server inserts into leave_requests table
    ↓
Returns JSON response
    ↓
JavaScript shows success/error message
    ↓
Modal auto-closes after 2 seconds
    ↓
Page reloads
    ↓
New request appears in "My Leave Requests"
    ↓
Status badge shows "Pending"
    ↓
Employee can track through dashboard
```

---

## 📊 Feature Completeness

### Dashboard Page
- [x] Shift Schedule Card - 100%
- [x] Leave Balance Display - 100%
- [x] Leave Request Form - 100%
- [x] Leave History Display - 100%
- [x] Time In Button - 100%
- [x] Time Out Button - 100%
- [x] Time In Modal - 100%
- [x] Time Out Modal - 100%
- [x] Charts & Stats - 100%

### QR Scanner Page
- [x] Camera Integration - 100%
- [x] Code Detection - 100%
- [x] Token Validation - 100%
- [x] Time In/Out Logic - 100%
- [x] Confirmation Modals - 100%
- [x] Error Handling - 100%

### QR Generator Page
- [x] Code Generation - 100%
- [x] Timer Display - 100%
- [x] Print Option - 100%
- [x] Download Option - 100%
- [x] Share Option - 100%

---

## 🎨 Color Scheme

```
Primary Colors:
- Green (#27ae60) - Success, Time In, Approved
- Orange (#e67e22) - Time Out, Warning
- Purple (#667eea) - Primary, Shifts
- Red (#e74c3c) - Rejected

Status Colors:
- Yellow (#f39c12) - Pending
- Green (#27ae60) - Approved
- Red (#e74c3c) - Rejected

Dark Mode:
- Background: Dark gray
- Text: Light gray
- Adjusts all colors for contrast
```

---

## 📱 Responsive Breakpoints

```
Mobile (<600px)
- Stack all cards vertically
- Full-width buttons
- Larger touch targets
- Simplified layouts

Tablet (600px - 1024px)
- 2-column grid for stats
- Optimized spacing
- Readable font sizes

Desktop (>1024px)
- Full layout with sidebars
- Multi-column grids
- Optimal spacing
- All features visible
```

---

**Visual Documentation Complete**  
**All Features Visible & Interactive**  
**Ready for Production Deployment** ✅

