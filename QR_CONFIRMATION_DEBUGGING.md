# QR Confirmation Modal Issue - Troubleshooting Guide

## Issue
After scanning QR code, login, and seeing the confirmation modal - clicking "Confirm" button doesn't respond.

## Diagnosis Steps

### Step 1: Check Browser Console
1. Open your browser's Developer Tools (F12)
2. Go to the **Console** tab
3. Click the "Confirm" button in the modal
4. Look for any error messages. Take note of:
   - JavaScript errors
   - Network request failures
   - CORS errors
   - HTTP status codes (404, 500, etc.)

### Step 2: Check Network Tab
1. In Developer Tools, go to the **Network** tab
2. Click the "Confirm" button
3. Look for the request to `attendance-confirm.php`
4. Check:
   - **Status code** - Should be 200 (success) or 400 (validation error), NOT 404 or 500
   - **Response preview** - Should show JSON with `success: true/false`
   - **Response size** - Should have content, not empty

### Step 3: Check Server Logs
Check your PHP error log (usually in xampp/php/logs/php_error.log):

```bash
# Windows (PowerShell)
Get-Content "C:\xampp\php\logs\php_error.log" -Tail 50

# or use a text editor to open:
C:\xampp\php\logs\php_error.log
```

Look for messages starting with: `[ATTENDANCE-CONFIRM]` or `[ATTENDANCE-CONFIRM-ERROR]`

## Common Issues & Solutions

### Issue A: "File not found" errors
**Error:** `Database file not found` or `Admin portal classes not found`

**Solution:** Check that these files exist:
```bash
# Database file
C:\xampp\htdocs\capstone_hr_management_system\auth\database.php

# Admin portal app folder  
C:\xampp\htdocs\capstone_hr_management_system\time_attendance\app\
C:\xampp\htdocs\capstone_hr_management_system\time_attendance\app\core\Session.php
C:\xampp\htdocs\capstone_hr_management_system\time_attendance\app\controllers\AttendanceController.php
C:\xampp\htdocs\capstone_hr_management_system\time_attendance\app\models\Attendance.php
C:\xampp\htdocs\capstone_hr_management_system\time_attendance\app\models\Employee.php
C:\xampp\htdocs\capstone_hr_management_system\time_attendance\app\helpers\Helper.php
C:\xampp\htdocs\capstone_hr_management_system\time_attendance\app\helpers\AuditLog.php
```

### Issue B: "User not authenticated" (401 error)
**Error:** `User not authenticated`

**Cause:** Session not properly passed from employee portal to attendance-confirm.php

**Solution:**
1. Check if you're still logged in (reload page and check if you're logged out)
2. Check if session cookies are being sent with AJAX request
3. Try adding credentials to the fetch request in employee_dashboard.php (line ~1670):

```javascript
fetch(fetchUrl, {
    method: 'POST',
    body: formData,
    credentials: 'same-origin',  // ADD THIS LINE
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    }
})
```

### Issue C: "Employee not found" (404 error)
**Error:** `Employee not found for user`

**Cause:** User ID exists but no corresponding employee_id in employees table

**Solution:**
1. Check if user has employee record:
```sql
SELECT user_id, employee_id FROM employees WHERE user_id = (SELECT user_id FROM your_login_session);
```

2. Make sure the user has been created as an employee in the system

### Issue D: Holiday check fails
**Error:** Message says "Today is a holiday"

**Solution:** This is actually correct behavior - employees can't time in on holidays. Check if today is marked as a holiday:
```sql
SELECT * FROM ta_holidays WHERE DATE(holiday_date) = CURDATE() AND is_active = 1;
```

### Issue E: Already timed in
**Error:** Message says "You have already timed in today"

**Solution:** This is correct - employee already has a time_in record for today. Check:
```sql
SELECT * FROM ta_attendance WHERE employee_id = 'YOUR_EMP_ID' AND DATE(attendance_date) = CURDATE();
```

## Real-Time Debugging

Add this to employee_dashboard.php around line 1670 to see actual response:

```javascript
.then(text => {
    console.log('[DEBUG] Raw response:', text);
    debugInfo.rawResponse = text.substring(0, 500);
    
    // ... rest of code
})
.catch(error => {
    console.error('[DEBUG] Fetch error:', error);
    debugInfo.fetchError = error.message;
    // ... rest of code
});
```

Then check the browser console to see exactly what the server is responding with.

## Log File Location
The API now logs all requests to your PHP error log. Look for:
- `[ATTENDANCE-CONFIRM]` - Info logs
- `[ATTENDANCE-CONFIRM-ERROR]` - Error logs

Log file location:
```
C:\xampp\php\logs\php_error.log
```

## Manual Testing
Test the endpoint directly without the modal:

### Using cURL (PowerShell):
```powershell
$sessionCookie = "PHPSESSID=your_session_id_here"
$url = "http://localhost/capstone_hr_management_system/employee_portal/api/attendance-confirm.php"
$body = @{
    action = "time_in"
    method = "QR"
} | ConvertTo-Json

Invoke-WebRequest -Uri $url `
    -Method POST `
    -Body $body `
    -ContentType "application/json" `
    -Headers @{"Cookie"=$sessionCookie}
```

### Using HTML form (test.html):
```html
<!DOCTYPE html>
<html>
<body>
<form action="/capstone_hr_management_system/employee_portal/api/attendance-confirm.php" method="POST">
    <input type="hidden" name="action" value="time_in">
    <input type="hidden" name="method" value="QR">
    <button type="submit">Test Time In</button>
</form>
</body>
</html>
```

## Step-by-Step Debugging

1. **Check endpoint responds at all:**
   - Open browser console (F12)
   - Click Confirm button
   - Check Network tab for attendance-confirm.php request
   - Does it appear? (If not, JavaScript error occurred)

2. **Check response status:**
   - Click on the attendance-confirm.php request in Network tab
   - Check "Status" column - is it 200, 400, 500, 404?
   - Each status means different thing:
     - **200** = Success (but check Response for actual result)
     - **400** = Bad request (validation error, check Response)
     - **401** = Not authenticated
     - **404** = File not found
     - **500** = Server error (check PHP error log)

3. **Check response content:**
   - Click "Response" tab in Network inspector
   - You should see JSON like:
     ```json
     {"success":true,"message":"Time in recorded at 14:30","action":"time_in","data":{...}}
     ```
   - If you see HTML instead, there's a fatal PHP error

4. **Check PHP error log:**
   - Open `C:\xampp\php\logs\php_error.log`
   - Search for `ATTENDANCE-CONFIRM` 
   - Look for any errors or exceptions

## If Still Stuck

Share with me:
1. **Browser console screenshot** (F12 → Console tab, after clicking Confirm)
2. **Network request details** (F12 → Network tab, click on attendance-confirm.php request, show Response)
3. **Last 20 lines of PHP error log** (C:\xampp\php\logs\php_error.log)
4. **The exact error message shown on screen** (if any)

This will help me identify the exact issue quickly.
