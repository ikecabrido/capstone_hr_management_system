# QR Attendance System - Smooth Implementation

## Overview

This is the **smooth QR attendance workflow** adapted from the hakdog backup and optimized for the employee_portal with clean architectural separation:

- **UI Layer**: `employee_portal/` - Shows confirmation modal and redirects
- **API Layer**: `time_attendance/portal/` - Handles QR logic and database operations
- **Operations**: Keep all database operations in `time_attendance` folder for centralization

## Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                           QR Scan Process                       │
└─────────────────────────────────────────────────────────────────┘

1. QR CODE SCANNED (from kiosk)
   └─> qr_handler.php
       ├─ Check if user logged in
       ├─ If YES: Redirect to employee_portal/index.php?url=qr-attendance&token=TOKEN
       └─ If NO: Redirect to employee_portal/index.php?url=auth-index&qr_token=TOKEN

2. LOGIN FORM (if not logged in)
   └─> AuthController->login()
       ├─ Validate employee_no and password
       ├─ If QR token present: Store in session & redirect to qr-attendance
       └─ If NO token: Normal redirect to dashboard

3. SHOW CONFIRMATION MODAL (AttendanceController->qrAttendance)
   ├─ Get today's attendance record
   ├─ Determine action: TIME_IN or TIME_OUT
   ├─ Display mobile-friendly standalone HTML modal
   └─> Show employee name, time, date, action type

4. EMPLOYEE CONFIRMS (clicks "Confirm" button)
   └─> JavaScript AJAX request to:
       time_attendance/portal/api/qr-process.php
       
5. PROCESS ATTENDANCE (QRAttendanceController->processQRAttendance)
   ├─ Validate QR token
   ├─ Check today's record
   ├─ Auto-detect: TIME_IN or TIME_OUT
   ├─ Insert/Update database record
   └─> Return JSON response

6. SHOW SUCCESS & REDIRECT
   ├─ Show success message for 2 seconds
   └─> Redirect to dashboard
```

## Files Created/Modified

### New Files (time_attendance/portal/)

1. **time_attendance/portal/controllers/QRAttendanceController.php**
   - Smooth auto-detection logic (TIME_IN vs TIME_OUT)
   - Validates QR token
   - Inserts/updates attendance records
   - Returns JSON response
   - Key method: `processQRAttendance($employee_id, $qr_token)`

2. **time_attendance/portal/api/qr-process.php**
   - REST API endpoint
   - Receives POST request with employee_id and token
   - Calls QRAttendanceController
   - Returns JSON for AJAX processing
   - Handles errors and unauthorized access

3. **time_attendance/portal/config/db_config.php**
   - Database configuration
   - Starts session for API authentication
   - Sets timezone to Asia/Manila

### Modified Files

1. **employee_portal/app/views/employee-portal/qr-confirmation-modal.php**
   - Now uses AJAX instead of traditional POST
   - Mobile-optimized standalone HTML
   - Shows loading spinner during processing
   - Auto-redirects on success
   - Shows error on failure

2. **AttendanceController (no changes needed)**
   - Already calls qrAttendance() correctly
   - Modal now handles AJAX submission internally
   - processQRAttendance() method not used from modal anymore

## Data Flow

### Scenario 1: First QR Scan (TIME_IN)
```
Employee scans QR code at 08:00 AM
│
└─> Modal shows:
    - Employee Name: John Doe
    - Time: 08:00:00
    - Date: Monday, January 20, 2025
    - Action: TIME_IN
    
└─> Employee clicks "Confirm"
    │
    └─> API checks: No record for today
        │
        └─> INSERT attendance record (time_in = 08:00:00)
            │
            └─> Response: {"success": true, "message": "Time In recorded at 08:00:00", "action_type": "TIME_IN"}
```

### Scenario 2: Second QR Scan (TIME_OUT)
```
Employee scans QR code at 05:30 PM
│
└─> Modal shows:
    - Employee Name: John Doe
    - Time: 17:30:00
    - Date: Monday, January 20, 2025
    - Action: TIME_OUT
    
└─> Employee clicks "Confirm"
    │
    └─> API checks: Record exists with time_in but NO time_out
        │
        └─> UPDATE attendance record (time_out = 17:30:00)
            │
            └─> Response: {"success": true, "message": "Time Out recorded at 17:30:00", "action_type": "TIME_OUT"}
```

### Scenario 3: Duplicate Scan (Already Completed)
```
Employee scans QR code at 06:00 PM (after already timed out)
│
└─> API checks: Record exists with both time_in AND time_out
    │
    └─> Response: {"success": false, "message": "You have already timed out today at 17:30"}
```

## Smart Detection Logic

The `QRAttendanceController::processQRAttendance()` method handles smart TIME_IN/TIME_OUT detection:

```php
1. Check if record exists for today (same date)
   
   IF NO RECORD:
   └─> ACTION = TIME_IN
       └─> INSERT new record with time_in = NOW
       
   ELSE IF time_in IS EMPTY:
   └─> ACTION = TIME_IN
       └─> UPDATE record SET time_in = NOW
       
   ELSE IF time_out IS EMPTY:
   └─> ACTION = TIME_OUT
       └─> UPDATE record SET time_out = NOW
       └─> Calculate hours worked
       
   ELSE (both filled):
   └─> ERROR = Already completed
       └─> RETURN error message with time_out timestamp
```

## Mobile Optimization

The confirmation modal is fully mobile-optimized:
- Touch-friendly buttons (44px+ minimum height)
- Responsive font sizing
- Proper viewport meta tags
- No layout.php wrapper (standalone HTML)
- Landscape orientation support
- Works on iOS and Android

## API Response Format

### Success Response
```json
{
    "success": true,
    "message": "Time In recorded successfully at 08:00:00",
    "action": "TIME_IN",
    "action_type": "TIME_IN"
}
```

### Error Response
```json
{
    "success": false,
    "message": "You have already timed out today at 17:30"
}
```

## Error Handling

1. **Missing QR Token**: Redirects to dashboard with error
2. **Invalid/Expired Token**: Shows error message in modal
3. **Unauthorized User**: API returns 401
4. **Database Error**: API returns error message
5. **Already Completed**: Shows helpful message with previous time_out

## Testing Checklist

- [ ] QR code scan redirects to login if not logged in
- [ ] Login with QR token shows confirmation modal
- [ ] Modal displays correct employee info
- [ ] Modal shows TIME_IN on first scan
- [ ] First scan inserts attendance record
- [ ] Modal shows TIME_OUT on second scan
- [ ] Second scan updates time_out
- [ ] Third scan shows "already completed" error
- [ ] Mobile devices show responsive layout
- [ ] AJAX submission works on mobile
- [ ] Success message shows and redirects
- [ ] Error handling works correctly
- [ ] Session persists through API call
- [ ] Database records created/updated correctly

## Database Tables Used

- `ta_attendance` - Main attendance records
  - `attendance_id` (Primary Key)
  - `employee_id`
  - `attendance_date`
  - `time_in`
  - `time_out`
  - `status`
  - `recorded_by` = 'QR'
  - `shift_id` (for payroll/shifts)

- `ta_employee_shifts` - Employee shift assignments
  - Used to populate shift_id when creating attendance record

- `employees` - Employee information
  - Used to get employee name and details

## Future Enhancements

1. Add QR kiosk dashboard showing last 10 scans
2. Add manager notifications for anomalies
3. Add offline mode with sync when online
4. Add geolocation tracking
5. Add photo capture with QR scan
6. Add push notifications for confirmations
7. Add batch QR scans for shift entry/exit
8. Add QR token expiration settings

## Troubleshooting

### Modal not appearing on mobile
- Clear browser cache
- Check viewport meta tags in HTML
- Test in device's native browser

### AJAX request failing
- Check browser console for errors (F12)
- Verify time_attendance/portal/api/qr-process.php exists
- Check session is being passed via credentials: 'same-origin'
- Verify database connection in db_config.php

### Database record not created
- Check db_config.php credentials
- Verify ta_attendance table exists
- Check file permissions on upload/debug folders
- Look at qr_debug.log for error messages

### Session lost in API
- Verify db_config.php calls session_start()
- Check credentials: 'same-origin' in fetch request
- Verify cookies are enabled in browser
- Check CORS headers if cross-origin

## File Locations

```
capstone_hr_management_system/
├── time_attendance/
│   └── portal/
│       ├── controllers/
│       │   └── QRAttendanceController.php (NEW)
│       ├── api/
│       │   └── qr-process.php (NEW)
│       └── config/
│           └── db_config.php (NEW)
├── employee_portal/
│   ├── index.php (routing already set up)
│   ├── app/
│   │   ├── controllers/
│   │   │   └── AttendanceController.php (modified - qrAttendance method)
│   │   │   └── AuthController.php (handles QR token in login)
│   │   └── views/
│   │       └── employee-portal/
│   │           └── qr-confirmation-modal.php (UPDATED - now uses AJAX)
├── qr_handler.php (routes QR scans to employee_portal)
├── login.php (handles initial QR flow)
└── qr_debug.log (debug output from API)
```

## Summary

This smooth implementation provides:
✅ Clean separation of concerns (UI vs API vs Operations)
✅ Mobile-optimized user experience
✅ Auto-detection of TIME_IN/TIME_OUT
✅ AJAX-based submission for better UX
✅ Comprehensive error handling
✅ Centralized API in time_attendance folder
✅ Session management through API
✅ Debug logging for troubleshooting
✅ Single codebase - no duplication
