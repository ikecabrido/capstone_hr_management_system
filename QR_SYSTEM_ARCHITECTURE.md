# 🔗 QR System Integration - Visual Guide

## System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                   EMPLOYEE DASHBOARD                             │
│              (time_attendance/public/)                            │
│                                                                   │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Time In/Out Section                                     │  │
│  │                                                          │  │
│  │  [Time In Completed]  [Time Out Completed]              │  │
│  │                                                          │  │
│  │  ┌──────────────────────────────────────────────────┐   │  │
│  │  │  QR Code Options                                 │   │  │
│  │  │                                                  │   │  │
│  │  │  [📱 QR Scanner]  [🟢 Generate QR] (HR Only)  │   │  │
│  │  └──────────────────────────────────────────────────┘   │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
         ↓                                    ↓
         │                                    │
         │ (Employee)                        │ (HR Admin)
         ↓                                    ↓
    ┌─────────────┐                    ┌─────────────────┐
    │qr_scanner.php│                   │qr_generate.php  │
    │              │                   │                 │
    │ • Camera     │                   │ • Token Gen     │
    │ • jsQR       │                   │ • Batch Create  │
    │ • Detect     │                   │ • URL Builder   │
    │ • Validate   │◄──────token────────┤ • IP Support    │
    │ • Time In/Out│                   │                 │
    │ • Modals     │                   │ OR              │
    └─────────────┘                   │                 │
         ↓                            │qr_display_kiosk.│
         │                            │ • Public display│
         │ (Submit Token)             │ • Auto-refresh  │
         ↓                            │ • 30-sec timer  │
    ┌──────────────┐                  └─────────────────┘
    │ qr_scan.php  │                         ↓
    │              │                    (Generate QR)
    │ • Validate   │
    │ • Check auth │
    │ • Redirect   │
    │ • Process    │
    └──────────────┘
         ↓
    ┌─────────────────────────┐
    │ DATABASE                │
    │                         │
    │ • attendance_tokens     │
    │ • attendance            │
    │ • employees             │
    │ • users                 │
    │ • audit_log             │
    └─────────────────────────┘
```

---

## User Journey - Employee Time In/Out

```
STEP 1: EMPLOYEE OPENS DASHBOARD
┌─────────────────────────────┐
│ Employee Dashboard          │
│                             │
│ [Time In/Out Section]       │
│                             │
│ [📱 QR Scanner] ←─── CLICK  │
│ [🟢 Generate QR]            │
└─────────────────────────────┘
           ↓

STEP 2: QR SCANNER PAGE LOADS
┌─────────────────────────────┐
│ qr_scanner.php              │
│                             │
│ ┌─────────────────────────┐ │
│ │ Camera View             │ │
│ │ [QR Detection Frame]    │ │
│ │ Scanning...             │ │
│ └─────────────────────────┘ │
│                             │
│ "Point camera at QR code"   │
└─────────────────────────────┘
           ↓

STEP 3: EMPLOYEE SCANS QR CODE
┌─────────────────────────────┐
│ jsQR Library Detects Code   │
│                             │
│ ✓ Token Found!              │
│                             │
│ Validation:                 │
│ • Token exists? YES          │
│ • Expired? NO                │
│ • Already used? NO           │
└─────────────────────────────┘
           ↓

STEP 4: AUTO-DETECT TIME IN/OUT
┌─────────────────────────────┐
│ Check Today's Attendance    │
│                             │
│ Has time_in? NO ───→ TIME IN │
│ Has time_out? NO ──→ TIME OUT│
│ Both exist? ──→ ALREADY DONE │
└─────────────────────────────┘
           ↓

STEP 5: SHOW CONFIRMATION MODAL
┌───────────────────────────────┐
│                               │
│    ✅ TIME IN CONFIRMED       │
│                               │
│    Employee: John Doe         │
│    Date: March 19, 2026       │
│    Time: 09:00 AM             │
│                               │
│           [OK]                │
└───────────────────────────────┘
           ↓

STEP 6: RECORD IN DATABASE
┌─────────────────────────────┐
│ Database Updates:           │
│                             │
│ INSERT into attendance:     │
│ • employee_id: 5            │
│ • time_in: 09:00:00         │
│ • status: ON_TIME           │
│                             │
│ DELETE from tokens:         │
│ • Mark as used              │
│ • Cleanup expired tokens    │
└─────────────────────────────┘
           ↓

STEP 7: EMPLOYEE SEES CONFIRMATION
┌─────────────────────────────┐
│ Dashboard Updated:          │
│                             │
│ Time In: 09:00 AM ✓         │
│ Time Out: -- : --           │
│ Duration: calculating...    │
│                             │
│ Ready for Time Out          │
└─────────────────────────────┘
```

---

## HR Admin QR Generation Flow

```
STEP 1: ADMIN OPENS DASHBOARD
┌────────────────────────────────┐
│ Employee Dashboard             │
│ (HR Admin View)                │
│                                │
│ [📱 QR Scanner]                │
│ [🟢 Generate QR] ←── CLICK     │
└────────────────────────────────┘
              ↓

STEP 2: QR GENERATION PAGE
┌────────────────────────────────┐
│ qr_generate.php                │
│                                │
│ How many tokens? [1-50]        │
│ Server IP: [auto-detect]       │
│ Custom IP: [optional]          │
│                                │
│ [Generate QR Codes]            │
└────────────────────────────────┘
              ↓

STEP 3: TOKEN CREATION
┌────────────────────────────────┐
│ For each token:                │
│                                │
│ • Generate: random_bytes(32)   │
│ • Hash: base64_encode()        │
│ • URL: build with IP           │
│ • Expiry: +1 minute            │
│                                │
│ INSERT INTO attendance_tokens  │
└────────────────────────────────┘
              ↓

STEP 4: QR CODE DISPLAY
┌────────────────────────────────┐
│ Display Options:               │
│                                │
│ ┌─────────────────────────────┐│
│ │ QR CODE (visual)            ││
│ │ http://IP/login?qr_token... ││
│ └─────────────────────────────┘│
│                                │
│ [Print]  [Download]  [Share]   │
│                                │
│ Expires in: 0:59 seconds       │
└────────────────────────────────┘
              ↓

STEP 5: DISPLAY ON SCREEN OR PRINT
┌────────────────────────────────┐
│ Option A: Print for Office     │
│  └─→ Print Dialog              │
│      └─→ QR Code on Paper      │
│                                │
│ Option B: Display on Monitor   │
│  └─→ qr_display_kiosk.php      │
│      └─→ Auto-refresh every 30s│
│      └─→ Continuous display    │
│                                │
│ Option C: Share (Mobile)       │
│  └─→ Device Sharing API        │
│      └─→ Share to AirDrop/etc  │
└────────────────────────────────┘
              ↓

STEP 6: EMPLOYEES SCAN
Employees with phones:
├─→ Open camera
├─→ Point at QR
├─→ Scan code
└─→ Auto time in/out
```

---

## Database Token Lifecycle

```
┌─────────────────────────────────────────────────────────┐
│ ATTENDANCE_TOKENS TABLE LIFECYCLE                       │
└─────────────────────────────────────────────────────────┘

TIME: 09:00:00
├─ Admin generates token
├─ INSERT into attendance_tokens
│  ├─ token: "abc123xyz..."
│  ├─ created_at: 2026-03-19 09:00:00
│  ├─ expires_at: 2026-03-19 09:01:00
│  ├─ used: 0 (false)
│  └─ ip_address: 192.168.1.100
│
└─ Status: ACTIVE ✓

TIME: 09:00:30
├─ Employee scans QR
├─ System finds token
├─ Validates:
│  ├─ exists? YES ✓
│  ├─ expired? NO ✓
│  ├─ used? NO ✓
│
└─ Status: FOUND & VALID ✓

TIME: 09:00:45
├─ Employee time in recorded
├─ UPDATE attendance_tokens
│  ├─ used: 1 (true)
│  └─ used_at: 2026-03-19 09:00:45
│
└─ Status: USED ✓

TIME: 09:02:00 (2 minutes later)
├─ Cleanup process runs
├─ DELETE expired tokens
│  ├─ WHERE expires_at < NOW()
│  └─ OR (used=1 AND used_at < NOW()-5min)
│
└─ Status: DELETED 🗑️

TIME: 09:05:00 (if employee tries again)
├─ Token no longer exists
├─ Error message shown:
│  └─ "Invalid or expired QR token"
│
└─ Status: REJECTED ✗
```

---

## Security Flow

```
QR TOKEN FLOW WITH SECURITY CHECKS

Step 1: Generate
┌──────────────────────────────┐
│ Is user HR_ADMIN?            │ ← Role Check
│ ✓ YES → Continue             │
│ ✗ NO → Redirect              │
└──────────────────────────────┘
         ↓
Create token using:
- random_bytes(32)  ← Cryptographic
- base64_encode()   ← Secure encoding
- +1 min expiry     ← Time limit
- Store in DB       ← Tracked

Step 2: Scan
┌──────────────────────────────┐
│ Token valid?                 │
│ ✓ YES → Continue             │
│ ✗ NO → Error                 │
└──────────────────────────────┘
         ↓
┌──────────────────────────────┐
│ Not expired?                 │
│ ✓ YES → Continue             │
│ ✗ NO → Error                 │
└──────────────────────────────┘
         ↓
┌──────────────────────────────┐
│ Not already used?            │
│ ✓ YES → Continue             │
│ ✗ NO → Error                 │
└──────────────────────────────┘
         ↓
┌──────────────────────────────┐
│ Employee authenticated?      │ ← Session Check
│ ✓ YES → Process              │
│ ✗ NO → Redirect to login     │
└──────────────────────────────┘
         ↓
Mark token as used
Record attendance
Log action in audit_log
```

---

## File Integration Map

```
ROOT FOLDER
└─ capstone_hr_management_system/
   ├─ login_form.php ..................... Login entry point
   ├─ TIME_ATTENDANCE/PUBLIC/
   │  ├─ employee_dashboard.php .......... UPDATED ✅
   │  │  └─ Added QR buttons linking to:
   │  │     ├─ qr_scanner.php
   │  │     └─ qr_generate.php (HR only)
   │  │
   │  ├─ qr_scanner.php .................. Employee scanning
   │  │  └─ Uses jsQR library
   │  │  └─ Processes tokens
   │  │  └─ Shows confirmation modals
   │  │
   │  ├─ qr_scan.php .................... Handler page
   │  │  └─ Validates tokens
   │  │  └─ Redirects on error
   │  │  └─ Processes attendance
   │  │
   │  ├─ qr_generate.php ................ Admin generation
   │  │  └─ HR Admin only
   │  │  └─ Batch token creation
   │  │  └─ Multiple format export
   │  │
   │  └─ qr_display_kiosk.php ........... Public display
   │     └─ Auto-refresh QR
   │     └─ Office bulletin board
   │     └─ HR Admin only
   │
   ├─ TIME_ATTENDANCE/APP/
   │  ├─ MODELS/
   │  │  └─ Attendance.php ............ Time records
   │  │
   │  ├─ HELPERS/
   │  │  └─ QRHelper.php ............. Token generation
   │  │                             & validation
   │  │
   │  ├─ CONTROLLERS/
   │  │  └─ AuthController.php ....... Role checking
   │  │  └─ AttendanceController.php . Time in/out
   │  │
   │  └─ CONFIG/
   │     └─ Database.php ............. Connection
   │
   └─ DATABASE/
      └─ attendance_tokens ............ Token storage
      └─ attendance ................... Time records
      └─ employees ................... Employee data
      └─ audit_log ................... Action logging
```

---

## Dashboard Integration Points

```
┌─────────────────────────────────────────────────┐
│ EMPLOYEE DASHBOARD (time_attendance/public/)     │
├─────────────────────────────────────────────────┤
│                                                 │
│ ┌───────────────────────────────────────────┐  │
│ │ Time In/Out Section                       │  │
│ ├───────────────────────────────────────────┤  │
│ │                                           │  │
│ │ [Time In: 09:00]  [Time Out: --:--]       │  │
│ │                                           │  │
│ │ ┌─────────────────────────────────────┐   │  │
│ │ │ Manual Actions                      │   │  │
│ │ │ [Timed In Button] [Timed Out Button]│   │  │
│ │ └─────────────────────────────────────┘   │  │
│ │                                           │  │
│ │ ┌─────────────────────────────────────┐   │  │
│ │ │ QR CODE OPTIONS ← NEW INTEGRATION   │   │  │
│ │ │                                     │   │  │
│ │ │ [📱 QR Scanner] → qr_scanner.php   │   │  │
│ │ │ [🟢 Generate QR] → qr_generate.php │   │  │
│ │ │ (HR only)                           │   │  │
│ │ └─────────────────────────────────────┘   │  │
│ │                                           │  │
│ └───────────────────────────────────────────┘  │
│                                                 │
└─────────────────────────────────────────────────┘
```

---

## Access Control Matrix

```
                        Employee    HR_ADMIN    Manager
─────────────────────────────────────────────────────────
View Dashboard            ✓           ✓           ✓
Manual Time In/Out        ✓           ✓           ✓
QR Scanner (camera)       ✓           ✓           ✓
Generate QR               ✗           ✓           ✗
Display Kiosk             ✗           ✓           ✗
View Tokens               ✗           ✓           ✗
Edit Token Expiry         ✗           ✓           ✗
─────────────────────────────────────────────────────────
```

---

## Summary

✅ **Complete QR System Integrated**
- Employees: Click "QR Scanner" → Scan QR → Time in/out
- HR Admins: Click "Generate QR" → Create tokens → Display

✅ **Security Throughout**
- Token validation at every step
- Role-based access control
- Auto-expiry and cleanup
- Session authentication

✅ **Database Connected**
- All tokens tracked
- All actions logged
- All time records saved

✅ **User-Friendly**
- One-click access
- Clear confirmations
- Mobile camera support
- Error messages

**Status**: 🟢 **PRODUCTION READY**

