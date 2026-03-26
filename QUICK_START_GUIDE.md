# 🚀 Quick Start Guide - Employee Dashboard Features

## 📍 Where to Find Each Feature

### **Employee Dashboard** (`employee_dashboard.php`)

#### 1. **Shift Schedule Card**
- **Location**: Below the Quick Stats (Present, Late, Hours, Overtime)
- **Look For**: Purple gradient card with "📅 Today's Shift"
- **Shows**: Shift name, Start time, End time, Break duration

#### 2. **Leave Balance Section**
- **Location**: Middle of page
- **Changes**: New "➕ Request Leave" button at the top
- **Button**: Click to open Leave Request Form

#### 3. **Leave Request History**
- **Location**: Bottom of page (after Recent Attendance)
- **Title**: "📋 My Leave Requests"
- **Shows**: List of all leave requests with status badges
- **Status Colors**:
  - 🟡 Yellow = Pending
  - 🟢 Green = Approved/Final-Approved
  - 🔴 Red = Rejected

#### 4. **Time In/Out Buttons**
- **Location**: In "Time In/Out" section at top
- **Changes**: Now shows confirmation modal when clicked
- **Modal Shows**: Employee name, current date, current time

#### 5. **Leave Request Modal**
- **How to Open**: Click "➕ Request Leave" button
- **Form Fields**:
  - Leave Type (dropdown)
  - Start Date (date picker)
  - End Date (date picker)
  - Reason (text area)
- **Validation**: All fields required, dates must be future
- **Submit**: Click "Submit Request" button

#### 6. **Time In/Out Confirmation Modals**
- **Time In Modal**: Shows after clicking Time In button
  - Title: "Time In Confirmation" (green)
  - Shows: Employee name, date, time
  - Action: Click "OK" to close
  
- **Time Out Modal**: Shows after clicking Time Out button
  - Title: "Time Out Confirmation" (orange)
  - Shows: Employee name, date, time
  - Action: Click "OK" to close

---

## 📱 QR Scanner Page (`qr_scanner.php`)

### How to Access
1. Go to `/time_attendance/public/qr_scanner.php`
2. If logged in → See camera scanner
3. If not logged in → See login form

### For Employees
1. Open QR Scanner page
2. Allow camera access (browser will ask)
3. Point phone camera at displayed QR code
4. Scanner auto-detects code
5. See confirmation modal immediately
6. Click "OK" to return to scanning

### QR Scanning Actions
- **First Scan of Day** → Times In
- **Second Scan of Day** → Times Out
- **Invalid Code** → Shows error message
- **Expired Code** → Shows "Invalid or expired QR token"

---

## 🎯 QR Generator Page (`qr_generator.php`)

### How to Access
- `/time_attendance/public/qr_generator.php`

### How to Use
1. Click "Generate QR Code" button
2. QR code appears on screen
3. Choose action:
   - 🖨️ **Print** → Print to paper
   - ⬇️ **Download** → Save as PNG image
   - 📤 **Share** → Share via device

### Important Features
- ⏱️ **Countdown Timer** shows time remaining (always 1 minute)
- 🔄 After 1 minute expired → Generate new code
- 🔐 Each code is single-use only
- 📊 Perfect for displaying to employees for scanning

---

## 🔧 How Features Work Together

```
EMPLOYEE DASHBOARD
    │
    ├─ Shift Schedule Card
    │  └─ Shows today's assigned shift
    │
    ├─ Leave Balance + Request Form
    │  ├─ Shows remaining leave days
    │  └─ Request Leave button opens modal
    │
    ├─ Leave Request History
    │  └─ Shows all requests with status
    │
    ├─ Time In/Out Buttons
    │  ├─ Time In → Shows green confirmation
    │  └─ Time Out → Shows orange confirmation
    │
    └─ QR Scanner Link (add to menu)
       └─ Opens camera for scanning

QR GENERATOR PAGE (Admin)
    └─ Generates daily QR codes
       ├─ Print for office
       ├─ Display on screen
       └─ Share with employees

QR SCANNER PAGE (Employee)
    └─ Scans QR code with phone
       ├─ First scan → Time In
       ├─ Second scan → Time Out
       └─ Shows confirmation modal
```

---

## 📊 Data Relationships

```
Employee
├─ Has Many: Attendance Records
├─ Has Many: Leave Requests
├─ Has Many: Employee Shifts
│  └─ Each shift has: Start Time, End Time, Break Duration
└─ Has Many: Leave Balances

Leave Request
├─ Status: Pending → Approved → Final-Approved (or Rejected)
├─ Requires: Leave Type, Dates, Reason
└─ Stores: Created Date, Submission Details

QR Token
├─ Generated: By Admin (1 per generation)
├─ Validity: 1 minute only
├─ Usage: Single-use (can't scan twice)
└─ Logged: In audit_log for tracking
```

---

## 🎯 Key Features at a Glance

### **Shift Scheduling**
- ✅ Shows today's assigned shift
- ✅ Shows shift times and break duration
- ✅ Updates automatically from database
- ✅ "No shift" message if unassigned

### **Leave Management**
- ✅ Request new leave with form
- ✅ See all past leave requests
- ✅ Status tracking (Pending/Approved/Rejected)
- ✅ Admin remarks displayed
- ✅ Leave balance in percentage progress bar

### **Time Tracking**
- ✅ Manual time in/out with buttons
- ✅ QR code scanning option
- ✅ Confirmation modals showing name/date/time
- ✅ Auto-detects time in vs time out

### **QR System**
- ✅ Admin generates codes (1-min validity)
- ✅ Employee scans with phone
- ✅ Single-use tokens (no reuse)
- ✅ Instant confirmation modal
- ✅ Print/Download/Share options

---

## 🔐 Security Features

✅ All user input validated
✅ SQL injection prevented (prepared statements)
✅ XSS prevented (HTML escaping)
✅ Authentication required for all actions
✅ QR tokens expire automatically
✅ Single-use QR tokens prevent cheating
✅ All actions logged in audit trail
✅ Role-based access control

---

## 📱 Mobile Compatibility

✅ **Shift Card**: Fully responsive
✅ **Leave Form**: Mobile-friendly inputs
✅ **Modals**: Touch-optimized buttons
✅ **QR Scanner**: Camera access on mobile
✅ **QR Generator**: Print-friendly design
✅ **All Pages**: Tested on phones/tablets

---

## 🐛 Troubleshooting

### Leave Request Not Submitting
- [ ] Check all form fields filled
- [ ] Verify dates are in future
- [ ] Check browser console for errors
- [ ] Ensure JavaScript enabled

### QR Scanner Not Working
- [ ] Check browser has camera access permission
- [ ] Try a different browser (Chrome recommended)
- [ ] Ensure good lighting on QR code
- [ ] Make sure phone camera is not blocked

### Shift Schedule Not Showing
- [ ] Check if shift is assigned in admin panel
- [ ] Verify shift effective dates include today
- [ ] Check shift is marked as active

### Confirmation Modal Not Showing
- [ ] Clear browser cache
- [ ] Refresh the page
- [ ] Check browser JavaScript enabled
- [ ] Try different browser

---

## 📞 Support Information

For issues or questions:
1. Check browser console for error messages
2. Verify all requirements met (auth, dates, etc.)
3. Try refreshing the page
4. Check admin panel for data

---

## 🎓 Usage Examples

### **Example 1: Employee Time In/Out via QR**
1. Employee opens Dashboard
2. Sees "Today's Shift: Morning 9:00 AM - 5:00 PM"
3. Clicks on QR Scanner link
4. Points phone camera at displayed QR code
5. Scanner detects code
6. Green "Time In Confirmation" modal appears
7. Shows name, date, time
8. Employee clicks "OK"
9. Dashboard reloads, Time In now shows in card

### **Example 2: Request Leave**
1. Employee on Dashboard
2. Scrolls to Leave Balance section
3. Clicks "➕ Request Leave" button
4. Modal appears with form
5. Selects "Sick Leave" from dropdown
6. Picks dates: Mar 25 - Mar 26, 2026
7. Enters reason: "Doctor appointment"
8. Clicks "Submit Request"
9. Success message appears
10. Modal closes and Dashboard reloads
11. New request appears in "My Leave Requests" with "Pending" status

### **Example 3: Admin Generate QR**
1. Admin goes to QR Generator page
2. Clicks "Generate QR Code"
3. Purple QR code displays with 1-minute countdown
4. Clicks "Print" to print for office
5. Displays QR code at entrance
6. Employees scan to time in/out
7. Timer shows "Expires in: 45 seconds"
8. After 1 minute expires, admin generates new code

---

**Version**: 1.0  
**Status**: Production Ready ✅  
**Last Updated**: March 19, 2026

