# 🔄 Time & Attendance System - Complete Process Flow Analysis

**Date**: March 22, 2026  
**System**: HR Management System - Time & Attendance Module  
**Status**: Comprehensive Process Documentation

---

## 📋 Table of Contents

1. [System Overview](#system-overview)
2. [Core Components Architecture](#core-components-architecture)
3. [Main Process Flows](#main-process-flows)
4. [Database Schema & Models](#database-schema--models)
5. [API Endpoints & Data Flow](#api-endpoints--data-flow)
6. [User Interactions & Workflows](#user-interactions--workflows)
7. [Integration Points](#integration-points)
8. [Error Handling & Validation](#error-handling--validation)

---

## 🎯 System Overview

The Time & Attendance System is a comprehensive PHP-based application that manages:
- **Employee Attendance Tracking** (Time In/Out with QR code support)
- **Shift Management** (Predefined shifts and flexible scheduling)
- **Leave Management** (Request, approval workflow, balance tracking)
- **Holiday Management** (National/Regional holidays with API sync)
- **Absence & Late Management** (Auto-detection and excuse handling)
- **Schedule Calendar** (Visual monthly/weekly/daily views)
- **Dashboard & Metrics** (Real-time attendance insights)

### Technology Stack
```
Frontend:  PHP (Templating), JavaScript (Vanilla + AJAX), HTML5/CSS3
Framework: AdminLTE 3.2 (Dashboard template)
Backend:   PHP 7.4+ with PDO (Object-Oriented)
Database:  MySQL/MariaDB
Libraries: FullCalendar 6.1.10, Nager.Date API (Holidays)
```

---

## 🏗️ Core Components Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                    TIME & ATTENDANCE SYSTEM                     │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌─────────────────────┐      ┌──────────────────────────┐    │
│  │  PRESENTATION LAYER │      │   BUSINESS LOGIC LAYER   │    │
│  │  (Frontend/Views)   │      │   (Controllers)          │    │
│  ├─────────────────────┤      ├──────────────────────────┤    │
│  │                     │      │                          │    │
│  │ • time_attendance.  │      │ • AttendanceController   │    │
│  │   php (Main Page)   │      │ • LeaveController        │    │
│  │                     │      │ • ShiftController        │    │
│  │ • Components:       │      │ • HolidayController      │    │
│  │   - Dashboard       │      │ • NotificationController │    │
│  │   - Calendar        │      │                          │    │
│  │   - Sidebar         │      └──────────────────────────┘    │
│  │   - Holidays Widget │                                      │
│  │                     │      ┌──────────────────────────┐    │
│  │ • CSS Styling       │      │    DATA ACCESS LAYER     │    │
│  │   - calendar_       │      │   (Models)               │    │
│  │     schedule.css    │      ├──────────────────────────┤    │
│  │   - custom.css      │      │                          │    │
│  │                     │      │ • Attendance.php         │    │
│  │ • JavaScript Logic  │      │ • Leave.php              │    │
│  │   - calendar_       │      │ • Shift.php              │    │
│  │     schedule.js     │      │ • EmployeeShift.php      │    │
│  │   - Various APIs    │      │ • Holiday.php            │    │
│  │                     │      │ • Employee.php           │    │
│  └─────────────────────┘      │ • Users.php              │    │
│           │                   │ • Notification.php       │    │
│           │                   │ • AbsenceLateMgmt.php    │    │
│           │                   │                          │    │
│           └─────────────────────────────────────────────────┐  │
│                                                             │  │
│          ┌──────────────────────────────────────────┐      │  │
│          │     HELPER & UTILITY LAYER               │      │  │
│          ├──────────────────────────────────────────┤      │  │
│          │                                          │      │  │
│          │ • HolidayHelper         • AuditLog       │      │  │
│          │ • LeaveAbsenceHelper    • QRHelper       │      │  │
│          │ • Helper                • Session        │      │  │
│          │ • NagerDateService      • Database       │      │  │
│          │                                          │      │  │
│          └──────────────────────────────────────────┘      │  │
│           │                                                 │  │
│           ▼                                                 │  │
│  ┌────────────────────────────────────────────────────┐   │  │
│  │    API LAYER (JSON Endpoints)                      │   │  │
│  ├────────────────────────────────────────────────────┤   │  │
│  │                                                    │   │  │
│  │ • submit_leave.php          • metrics.php         │   │  │
│  │ • get_leave_balance.php      • holiday_api.php    │   │  │
│  │ • get_employee_schedule.php  • realtime_updates.  │   │  │
│  │ • save_employee_schedule.php   php                │   │  │
│  │ • get_day_schedule.php       • absence_late_      │   │  │
│  │ • get_pending_leaves.php       management.php     │   │  │
│  │ • approve_leave_head.php     • get_day_records.   │   │  │
│  │ • approve_leave_hr.php         php                │   │  │
│  │                                                    │   │  │
│  └────────────────────────────────────────────────────┘   │  │
│           │                                                │  │
│           ▼                                                │  │
│  ┌────────────────────────────────────────────────────┐   │  │
│  │    DATABASE LAYER (MySQL Tables)                   │   │  │
│  ├────────────────────────────────────────────────────┤   │  │
│  │                                                    │   │  │
│  │ • ta_attendance          • ta_shifts              │   │  │
│  │ • ta_absence_late_       • ta_employee_shifts     │   │  │
│  │   records                • ta_leave_requests      │   │  │
│  │ • ta_leave_types         • ta_leave_balance       │   │  │
│  │ • ta_holidays            • ta_holiday_sync_log    │   │  │
│  │ • employees              • users                  │   │  │
│  │ • department_heads       • audit_logs             │   │  │
│  │                                                    │   │  │
│  └────────────────────────────────────────────────────┘   │  │
│                                                            │  │
└────────────────────────────────────────────────────────────┘  │
           │                                                     │
           ▼                                                     │
  ┌─────────────────────┐       ┌─────────────────────┐        │
  │   External APIs     │       │  Session & Auth     │        │
  ├─────────────────────┤       ├─────────────────────┤        │
  │ • Nager.Date API    │       │ • auth_check.php    │        │
  │   (Holiday Sync)    │       │ • auth.php          │        │
  │                     │       │ • Session Class     │        │
  └─────────────────────┘       └─────────────────────┘        │
```

---

## 🔄 Main Process Flows

### **FLOW 1: Employee Time In/Out Process**

```
┌───────────────────────────────────────────────────────────────┐
│                   ATTENDANCE RECORDING FLOW                   │
└───────────────────────────────────────────────────────────────┘

STEP 1: EMPLOYEE INITIATES TIME IN
  ├─ Method: Manual input OR QR Code scan
  ├─ Location: Dashboard or Mobile
  └─ Trigger: GET/POST to AttendanceController::timeIn()

STEP 2: VALIDATION CHECKS
  ├─ Verify user is authenticated
  ├─ Check if today is a holiday
  │  └─ If YES: Reject with message "Today is holiday: [name]"
  ├─ Check if employee already timed in today
  │  └─ If YES: Reject with message "Already timed in at [time]"
  └─ Verify employee exists and is active

STEP 3: TIME IN RECORD CREATION
  ├─ Query: INSERT INTO ta_attendance
  │  ├─ employee_id: [from request]
  │  ├─ time_in: NOW() [Philippines timezone UTC+8]
  │  ├─ attendance_date: CURDATE()
  │  └─ recorded_by: [MANUAL or QR]
  ├─ Get newly created record with attendance_id
  └─ Determine initial status (Present or Late)

STEP 4: STATUS DETERMINATION
  ├─ Get employee's shift from ta_employee_shifts
  ├─ Compare time_in with shift.start_time
  ├─ Logic:
  │  ├─ If time_in <= shift.start_time: Status = "PRESENT"
  │  ├─ If time_in > shift.start_time: Status = "LATE"
  │  └─ If no shift found: Default to "PRESENT"
  └─ Status is stored in ta_attendance.status

STEP 5: AUDIT LOGGING
  ├─ Log action: TIME_IN_SUCCESS or TIME_IN_FAILED
  ├─ Store: user_id, employee_id, attendance_id
  ├─ Store: method, status, timestamp
  └─ Table: audit_logs

STEP 6: RESPONSE TO USER
  ├─ Success: {
  │    success: true,
  │    message: "Time In recorded at [HH:MM:SS]",
  │    employee_name: "[Full Name]",
  │    time_in: "HH:MM:SS",
  │    status: "PRESENT" or "LATE"
  │  }
  └─ Failure: { success: false, message: "[Error details]" }

STEP 7: TIME OUT PROCESS (Later in day)
  ├─ Trigger: Similar flow but endpoint updates existing record
  ├─ Query: UPDATE ta_attendance
  │  └─ SET time_out = NOW()
  │      WHERE attendance_id = [id]
  ├─ Calculate work_hours if both times present
  └─ Final status may be updated (e.g., Undertime)

DATABASE FLOW:
  ta_attendance table state progression:
  ┌────────────────────────────────────────────────────────┐
  │ BEFORE: (No record for today)                          │
  ├────────────────────────────────────────────────────────┤
  │ AFTER TIME IN:                                         │
  │ {                                                      │
  │   attendance_id: 1234,                                 │
  │   employee_id: 5,                                      │
  │   time_in: 08:15:30,                                   │
  │   time_out: NULL,                                      │
  │   attendance_date: 2026-03-22,                         │
  │   status: "LATE",                                      │
  │   recorded_by: "QR"                                    │
  │ }                                                      │
  │                                                        │
  │ AFTER TIME OUT:                                        │
  │ {                                                      │
  │   ...same...,                                          │
  │   time_out: 17:30:45,                                  │
  │   work_hours: 9.25                                     │
  │ }                                                      │
  └────────────────────────────────────────────────────────┘
```

---

### **FLOW 2: Leave Request & Approval Process**

```
┌───────────────────────────────────────────────────────────────┐
│                   LEAVE REQUEST WORKFLOW                      │
└───────────────────────────────────────────────────────────────┘

STEP 1: EMPLOYEE SUBMITS LEAVE REQUEST
  ├─ Endpoint: POST /api/submit_leave.php
  ├─ Required Data:
  │  ├─ employee_id
  │  ├─ leave_type_id (e.g., 1=Vacation, 2=Sick, 3=Bereavement)
  │  ├─ start_date, end_date
  │  ├─ reason
  │  └─ [optional] details
  ├─ Validation:
  │  ├─ All required fields present?
  │  ├─ Valid date format?
  │  ├─ start_date <= end_date?
  │  ├─ User permissions (own request or HR admin)?
  │  └─ Access control check
  └─ Preparation:
     ├─ Calculate total_days
     ├─ Account for weekends & holidays (optional)
     └─ Determine applicable days for deduction

STEP 2: LEAVE BALANCE VERIFICATION
  ├─ Call: LeaveController::submitRequest()
  ├─ Check: LeaveModel::checkLeaveBalance()
  ├─ Query: SELECT balance FROM ta_leave_balance
  │  WHERE employee_id = [id]
  │        AND leave_type_id = [type]
  ├─ Comparison: balance >= total_days?
  └─ Result:
     ├─ Sufficient balance: Continue to Step 3
     └─ Insufficient: Return error, STOP

STEP 3: CREATE LEAVE REQUEST RECORD
  ├─ Query: INSERT INTO ta_leave_requests
  │  ├─ employee_id: [from request]
  │  ├─ leave_type_id: [from request]
  │  ├─ start_date: [from request]
  │  ├─ end_date: [from request]
  │  ├─ details: [reason/details]
  │  ├─ status: "PENDING"
  │  ├─ date_submitted: NOW()
  │  ├─ department_head_approved_by: NULL
  │  ├─ hr_approved_by: NULL
  │  └─ created_at: NOW()
  └─ Get: leave_request_id (auto-increment)

STEP 4: AUDIT LOG
  ├─ Action: LEAVE_REQUEST_SUBMITTED
  ├─ Store: user_id, employee_id, leave_request_id
  ├─ Store: leave_type_id, total_days
  └─ Table: audit_logs

STEP 5: NOTIFICATION TO DEPARTMENT HEAD
  ├─ Create: Notification record in ta_notifications
  ├─ Send Email/SMS: Notification to department head
  ├─ Alert: "New leave request from [Employee Name]"
  └─ Action Required: Click to review and approve/reject

STEP 6: DEPARTMENT HEAD REVIEW (First Level)
  ├─ Access: Department head dashboard
  ├─ View: Pending leave requests for department
  ├─ Query: LeaveModel::getPendingByDepartmentHead()
  │  SELECT lr.* FROM ta_leave_requests lr
  │    JOIN employees e ON lr.employee_id = e.employee_id
  │    JOIN department_heads dh ON dh.department = e.department
  │    WHERE dh.user_id = [current_user_id]
  │      AND lr.status = 'PENDING'
  ├─ Actions:
  │  ├─ APPROVE: Status → "APPROVED_BY_HEAD"
  │  ├─ REJECT: Status → "REJECTED_BY_HEAD", remarks stored
  │  └─ Audit log created
  └─ Notification: Employee informed of decision

STEP 7: HR FINAL REVIEW (If Approved by Head)
  ├─ Query: LeaveModel::getForHRApproval()
  │  SELECT * FROM ta_leave_requests
  │    WHERE status IN ('PENDING', 'APPROVED_BY_HEAD')
  ├─ Access: HR Admin dashboard
  ├─ Actions:
  │  ├─ FINAL APPROVE: Status → "APPROVED_BY_HR"
  │  │  └─ Deduct balance in Step 8
  │  ├─ REJECT: Status → "REJECTED_BY_HR"
  │  └─ Audit log created
  └─ Notification: Employee & Department Head informed

STEP 8: DEDUCT LEAVE BALANCE (Only if APPROVED_BY_HR)
  ├─ Call: LeaveModel::deductLeaveBalance()
  ├─ Query: UPDATE ta_leave_balance
  │  SET balance = balance - [total_days]
  │      WHERE employee_id = [id]
  │            AND leave_type_id = [type]
  ├─ Verify: Result shows rows affected
  └─ Audit: Log balance deduction

STEP 9: ABSENCE-LEAVE INTEGRATION
  ├─ Call: LeaveAbsenceHelper::onLeaveApproved()
  ├─ For each day in leave period:
  │  ├─ Check: Is there an absence record?
  │  ├─ If YES (employee was marked absent):
  │  │  ├─ UPDATE ta_absence_late_records
  │  │  │  SET is_excused = 1,
  │  │  │      excuse_type = "APPROVED_LEAVE",
  │  │  │      leave_request_id = [leave_req_id]
  │  │  └─ Employee sees absence as excused ✓
  │  │
  │  └─ If NO (no absence record):
  │     ├─ INSERT into ta_absence_late_records
  │     │  (This ensures leave is properly documented)
  │     └─ Mark as excused due to approved leave
  └─ Result: Absence days linked to approved leave

STEP 10: FINAL CONFIRMATION
  ├─ Response: { success: true, message: "Leave approved" }
  ├─ Employee Dashboard: Shows approved leave in calendar
  ├─ Department Head: Request removed from pending list
  ├─ HR Dashboard: Request removed from pending list
  └─ Audit Trail: Complete record of all approvals

DATABASE STATE PROGRESSION:
  ┌────────────────────────────────────────────────────────┐
  │ INITIAL STATE:                                         │
  │ ta_leave_balance: balance = 15 days                   │
  │ ta_leave_requests: [Empty]                            │
  │                                                        │
  │ AFTER SUBMISSION:                                     │
  │ ta_leave_requests: {                                  │
  │   id: 123,                                            │
  │   employee_id: 5,                                     │
  │   leave_type_id: 1,                                   │
  │   start_date: 2026-03-10,                             │
  │   end_date: 2026-03-12,                               │
  │   status: "PENDING",                                  │
  │   date_submitted: NOW()                               │
  │ }                                                      │
  │                                                        │
  │ AFTER DEPARTMENT HEAD APPROVAL:                       │
  │ ta_leave_requests.status = "APPROVED_BY_HEAD"        │
  │ department_head_approved_by = [user_id]              │
  │ department_head_approved_date = NOW()                │
  │                                                        │
  │ AFTER HR APPROVAL:                                    │
  │ ta_leave_requests.status = "APPROVED_BY_HR"          │
  │ hr_approved_by = [user_id]                           │
  │ hr_approved_date = NOW()                             │
  │                                                        │
  │ BALANCE DEDUCTED:                                     │
  │ ta_leave_balance: balance = 12 days (15 - 3)        │
  │                                                        │
  │ ABSENCE RECORDS UPDATED:                              │
  │ For Mar 10,11,12:                                     │
  │ ta_absence_late_records: {                            │
  │   is_excused: 1,                                      │
  │   excuse_type: "APPROVED_LEAVE",                      │
  │   leave_request_id: 123,                              │
  │   status: "APPROVED"                                  │
  │ }                                                      │
  └────────────────────────────────────────────────────────┘
```

---

### **FLOW 3: Absence & Late Management Process**

```
┌───────────────────────────────────────────────────────────────┐
│              ABSENCE & LATE AUTO-DETECTION FLOW               │
└───────────────────────────────────────────────────────────────┘

STEP 1: END OF DAY BATCH PROCESS (Automated/Scheduled)
  ├─ Trigger: Scheduled task or End-of-day process
  ├─ Time: Typically 11:59 PM after shift end time
  └─ Scope: Check all employees for that day

STEP 2: CHECK FOR TIME IN RECORDS
  ├─ Query: SELECT * FROM ta_attendance
  │  WHERE attendance_date = [today]
  ├─ Process each employee in payroll
  └─ For each employee:
     ├─ Found time_in record? YES → PRESENT (Check for LATE)
     ├─ No time_in record? NO → ABSENT (Create record)
     └─ Continue

STEP 3: DETERMINE LATE STATUS
  ├─ If time_in EXISTS:
  │  ├─ Get employee's shift: ta_employee_shifts
  │  ├─ Compare: time_in vs shift.start_time
  │  ├─ Logic:
  │  │  ├─ time_in > shift.start_time: Status = LATE
  │  │  │  └─ Create absence_late_records with type = "LATE"
  │  │  │
  │  │  ├─ time_in <= shift.start_time: Status = PRESENT
  │  │  │  └─ No absence record needed
  │  │  │
  │  │  └─ No shift assigned: Status = PRESENT (Assume OK)
  │  │
  │  └─ Calculate late_minutes:
  │     └─ late_minutes = (time_in - shift.start_time) in minutes

STEP 4: DETERMINE ABSENT STATUS
  ├─ If time_in NOT found:
  │  ├─ Check: Is today a holiday?
  │  │  └─ Query: SELECT * FROM ta_holidays
  │  │           WHERE holiday_date = [today]
  │  │                  AND is_active = 1
  │  │
  │  ├─ If YES (Is Holiday):
  │  │  └─ SKIP (No absence record needed)
  │  │
  │  ├─ If NO (Normal working day):
  │  │  ├─ Check: Is employee on approved leave?
  │  │  │  └─ Query: SELECT * FROM ta_leave_requests
  │  │  │           WHERE employee_id = [id]
  │  │  │                  AND start_date <= [today]
  │  │  │                  AND end_date >= [today]
  │  │  │                  AND status = "APPROVED_BY_HR"
  │  │  │
  │  │  ├─ If YES (On approved leave):
  │  │  │  └─ Create record with:
  │  │  │     ├─ type: "ABSENT"
  │  │  │     ├─ is_excused: 1
  │  │  │     ├─ excuse_type: "APPROVED_LEAVE"
  │  │  │     └─ leave_request_id: [link to leave request]
  │  │  │
  │  │  └─ If NO (No approved leave):
  │  │     └─ CREATE ABSENCE RECORD
  │  │        ├─ type: "ABSENT"
  │  │        ├─ is_excused: 0 (Unexcused initially)
  │  │        └─ excuse_type: NULL
  │
  └─ End

STEP 5: INSERT ABSENCE/LATE RECORDS
  ├─ Query: INSERT INTO ta_absence_late_records
  │  ├─ employee_id: [from process]
  │  ├─ date: [today]
  │  ├─ type: "ABSENT" or "LATE"
  │  ├─ late_minutes: [calculated]
  │  ├─ is_excused: [0 or 1]
  │  ├─ excuse_type: [AUTO_DETECTED or NULL]
  │  ├─ reason: "Auto-detected by system"
  │  ├─ status: "PENDING" or "APPROVED"
  │  ├─ created_by: "SYSTEM"
  │  └─ created_at: NOW()
  │
  ├─ Also create audit log:
  │  └─ Action: ABSENCE_DETECTED or LATE_DETECTED
  │
  └─ Result: Records ready for review/appeal

STEP 6: EMPLOYEE CAN APPEAL
  ├─ Employee Dashboard shows: "Unexcused Absence/Late"
  ├─ Employee can submit appeal:
  │  ├─ Endpoint: POST /api/appeal_absence.php
  │  ├─ Provide reason/documentation
  │  └─ Status changes: "PENDING_APPEAL"
  │
  ├─ Department Head reviews appeal:
  │  ├─ View: Pending appeals in their department
  │  ├─ Approve: is_excused = 1, excuse_type = MANUAL_APPROVAL
  │  ├─ Reject: is_excused = 0, appeal_rejected = 1
  │  └─ Comment: Can add remarks
  │
  └─ Audit: All appeals logged

STEP 7: METRICS & REPORTING
  ├─ Dashboard shows:
  │  ├─ Total absences (excused vs unexcused)
  │  ├─ Total lates (by department/employee)
  │  ├─ Trends (increased absences?)
  │  └─ Pattern analysis (specific days?)
  │
  └─ Reports available:
     ├─ Daily absence report
     ├─ Employee absence history
     ├─ Department summary
     └─ Attendance metrics

DATABASE FLOW:
  ┌────────────────────────────────────────────────────────┐
  │ SCENARIO 1: Employee Times In Late                    │
  │                                                        │
  │ ta_attendance: {                                       │
  │   employee_id: 5,                                      │
  │   time_in: 08:30:00,    ← 30 mins after shift start    │
  │   time_out: 17:45:00,                                  │
  │   attendance_date: 2026-03-22,                         │
  │   status: "LATE"                                       │
  │ }                                                      │
  │                                                        │
  │ ta_absence_late_records: {                             │
  │   employee_id: 5,                                      │
  │   date: 2026-03-22,                                    │
  │   type: "LATE",                                        │
  │   late_minutes: 30,                                    │
  │   is_excused: 0,                                       │
  │   status: "PENDING"                                    │
  │ }                                                      │
  ├────────────────────────────────────────────────────────┤
  │ SCENARIO 2: Employee Doesn't Time In (Absent)         │
  │                                                        │
  │ ta_attendance: [No record for this day]                │
  │                                                        │
  │ ta_absence_late_records: {                             │
  │   employee_id: 5,                                      │
  │   date: 2026-03-22,                                    │
  │   type: "ABSENT",                                      │
  │   is_excused: 0,                                       │
  │   status: "PENDING",                                   │
  │   reason: "Auto-detected - No time in recorded"        │
  │ }                                                      │
  ├────────────────────────────────────────────────────────┤
  │ SCENARIO 3: Absent but Approved Leave                 │
  │                                                        │
  │ ta_leave_requests: {                                   │
  │   id: 123,                                             │
  │   employee_id: 5,                                      │
  │   status: "APPROVED_BY_HR",                            │
  │   start_date: 2026-03-22,                              │
  │   end_date: 2026-03-22                                 │
  │ }                                                      │
  │                                                        │
  │ ta_absence_late_records: {                             │
  │   employee_id: 5,                                      │
  │   date: 2026-03-22,                                    │
  │   type: "ABSENT",                                      │
  │   is_excused: 1,            ← Auto-excused             │
  │   excuse_type: "APPROVED_LEAVE",                       │
  │   leave_request_id: 123,                               │
  │   status: "APPROVED"                                   │
  │ }                                                      │
  └────────────────────────────────────────────────────────┘
```

---

### **FLOW 4: Shift Management & Schedule Calendar**

```
┌───────────────────────────────────────────────────────────────┐
│                  SHIFT MANAGEMENT WORKFLOW                    │
└───────────────────────────────────────────────────────────────┘

STEP 1: CREATE SHIFT (Admin Task)
  ├─ Endpoint: ShiftController::createShift()
  ├─ Input:
  │  ├─ shift_name: "Morning Shift", "Afternoon Shift", etc.
  │  ├─ start_time: HH:MM (e.g., 08:00)
  │  ├─ end_time: HH:MM (e.g., 17:00)
  │  ├─ break_duration: Minutes (e.g., 60)
  │  ├─ description: Shift details
  │  └─ is_active: 1 or 0
  │
  ├─ Validation:
  │  ├─ Required fields present?
  │  ├─ start_time < end_time?
  │  ├─ Valid time format?
  │  └─ Duplicate shift check?
  │
  ├─ Database: INSERT INTO ta_shifts
  │  └─ Get: shift_id (auto-increment)
  │
  └─ Audit: Log shift creation

STEP 2: ASSIGN SHIFT TO EMPLOYEE
  ├─ Endpoint: ShiftController::assignEmployeeShift()
  ├─ Input:
  │  ├─ employee_id: [Employee to assign]
  │  ├─ shift_id: [Shift to assign]
  │  ├─ effective_from: Start date
  │  ├─ effective_to: End date (null = indefinite)
  │  └─ is_active: 1 or 0
  │
  ├─ Pre-processing:
  │  ├─ Check: Is employee active?
  │  ├─ Check: Is shift active?
  │  └─ Auto-deactivate: Previous shifts for this employee
  │
  ├─ Database: INSERT INTO ta_employee_shifts
  │  ├─ Create new assignment
  │  └─ Get: employee_shift_id
  │
  ├─ Cascade Updates:
  │  ├─ ALL future attendance will use this shift
  │  ├─ ALL future late detection uses this shift
  │  └─ Schedule calendar reflects this shift
  │
  └─ Audit: Log shift assignment

STEP 3: VIEW EMPLOYEE SCHEDULE (Schedule Calendar)
  ├─ User: HR Staff / Admin
  ├─ Access: time_attendance.php → "Schedule Calendar" tab
  ├─ UI Component: calendar_schedule.php
  │
  ├─ STEP 3A: SEARCH FOR EMPLOYEE
  │  ├─ Search input: Accepts employee name or ID
  │  ├─ JavaScript: Debounced AJAX request
  │  ├─ API: GET /app/components/calendar_schedule.php?action=search_employees&q=[term]
  │  │
  │  ├─ Backend:
  │  │  └─ Query: SELECT employee_id, full_name FROM employees
  │  │           WHERE full_name LIKE '[term]%'
  │  │           AND employment_status = 'Active'
  │  │           LIMIT 20
  │  │
  │  ├─ Response: JSON array of matching employees
  │  │  [
  │  │    { "employee_id": 5, "full_name": "Maria Garcia" },
  │  │    { "employee_id": 8, "full_name": "Maria Reyes" }
  │  │  ]
  │  │
  │  └─ UI: Display autocomplete dropdown

STEP 3B: SELECT EMPLOYEE
  │  ├─ User clicks: Select from dropdown
  │  ├─ Function: selectEmployee(employee_id)
  │  ├─ JavaScript trigger: Fetch schedule data

STEP 3C: FETCH SCHEDULE DATA
  │  ├─ API Call: GET /app/api/get_employee_schedule.php
  │  │  Parameters:
  │  │  ├─ employee_id: [selected]
  │  │  ├─ start_date: First day of current month
  │  │  └─ end_date: Last day of current month
  │  │
  │  ├─ Backend Processing:
  │  │  ├─ Query 1: Get employee info
  │  │  │  SELECT * FROM employees WHERE employee_id = [id]
  │  │  │
  │  │  ├─ Query 2: Get current shift
  │  │  │  SELECT s.* FROM ta_employee_shifts es
  │  │  │  JOIN ta_shifts s ON es.shift_id = s.shift_id
  │  │  │  WHERE es.employee_id = [id]
  │  │  │        AND es.is_active = 1
  │  │  │
  │  │  ├─ Query 3: Get attendance records for month
  │  │  │  SELECT * FROM ta_attendance
  │  │  │  WHERE employee_id = [id]
  │  │  │        AND attendance_date BETWEEN [start] AND [end]
  │  │  │
  │  │  ├─ Query 4: Get holidays in period
  │  │  │  SELECT * FROM ta_holidays
  │  │  │  WHERE holiday_date BETWEEN [start] AND [end]
  │  │  │        AND is_active = 1
  │  │  │
  │  │  └─ Query 5: Get leave requests for month
  │  │     SELECT * FROM ta_leave_requests
  │  │     WHERE employee_id = [id]
  │  │           AND (start_date <= [end] AND end_date >= [start])
  │  │           AND status IN ('APPROVED_BY_HR', 'APPROVED_BY_HEAD')
  │  │
  │  ├─ Data Compilation:
  │  │  └─ Combine all data into structured JSON
  │  │
  │  └─ Response: JSON with all schedule data

STEP 3D: RENDER CALENDAR
  │  ├─ JavaScript: Process response data
  │  ├─ Initialize: FullCalendar library (month view)
  │  │
  │  ├─ For each date in month:
  │  │  ├─ IF has attendance: Show blue event "Present"
  │  │  ├─ IF has approved leave: Show green event "Leave"
  │  │  ├─ IF is holiday: Show red event "Holiday"
  │  │  ├─ IF is absent: Show red X "Absent"
  │  │  └─ IF is late: Show yellow alert "Late"
  │  │
  │  ├─ Color Legend:
  │  │  ├─ 🟢 Green: Shift scheduled
  │  │  ├─ 🔵 Blue: Present/checked in
  │  │  ├─ 🟡 Yellow: Late
  │  │  ├─ 🔴 Red: Absent or Holiday
  │  │  └─ ⚫ Black: No data
  │  │
  │  └─ UI: Display calendar with color-coded events

STEP 3E: VIEW DAILY TIMELINE
  │  ├─ User clicks: Click on day in calendar
  │  ├─ Modal opens: Daily Timeline View
  │  │
  │  ├─ Canvas draws: 24-hour timeline
  │  │  ├─ X-axis: Hours (00:00 - 23:59)
  │  │  ├─ Y-axis: Time scale
  │  │  ├─ Green block: Shift hours (if assigned)
  │  │  ├─ Blue block: Actual attendance time
  │  │  └─ Red block: Absence or late period
  │  │
  │  ├─ Display Info:
  │  │  ├─ Expected start time (from shift)
  │  │  ├─ Actual time in (if present)
  │  │  ├─ Actual time out (if present)
  │  │  ├─ Status (PRESENT, LATE, ABSENT)
  │  │  └─ Work hours (if complete)
  │  │
  │  └─ Optional: Edit or add custom times

STEP 4: CUSTOM SHIFT ASSIGNMENT (Flexible Schedule)
  ├─ User: HR Admin can assign custom times for specific day
  ├─ Input modal: Allow override of standard shift
  ├─ Data:
  │  ├─ employee_id: [Already selected]
  │  ├─ date: [Day being edited]
  │  ├─ custom_start_time: HH:MM (optional)
  │  ├─ custom_end_time: HH:MM (optional)
  │  └─ reason: Why custom shift?
  │
  ├─ API: POST /app/api/save_employee_schedule.php
  │  ├─ Create record: ta_custom_shifts
  │  │  ├─ employee_id
  │  │  ├─ date
  │  │  ├─ start_time (or use default from shift)
  │  │  ├─ end_time (or use default)
  │  │  ├─ reason
  │  │  └─ created_by: HR user_id
  │  │
  │  └─ OR update existing record if already exists
  │
  ├─ Impact:
  │  ├─ Next time in/out uses this custom time
  │  ├─ Late detection based on custom time
  │  └─ Audit log records the change
  │
  └─ Confirmation: Calendar updates to show new times

DATABASE FLOW:
  ┌────────────────────────────────────────────────────────┐
  │ SHIFT CREATION:                                        │
  │                                                        │
  │ ta_shifts table:                                       │
  │ {                                                      │
  │   shift_id: 1,                                         │
  │   shift_name: "Morning Shift",                         │
  │   start_time: 08:00:00,                                │
  │   end_time: 17:00:00,                                  │
  │   break_duration: 60,                                  │
  │   is_active: 1                                         │
  │ }                                                      │
  ├────────────────────────────────────────────────────────┤
  │ EMPLOYEE SHIFT ASSIGNMENT:                             │
  │                                                        │
  │ ta_employee_shifts table:                              │
  │ {                                                      │
  │   employee_shift_id: 1,                                │
  │   employee_id: 5,                                      │
  │   shift_id: 1,                                         │
  │   effective_from: 2026-01-01,                          │
  │   effective_to: NULL (indefinite),                     │
  │   is_active: 1,                                        │
  │   created_at: NOW()                                    │
  │ }                                                      │
  ├────────────────────────────────────────────────────────┤
  │ CUSTOM SHIFT OVERRIDE:                                 │
  │                                                        │
  │ ta_custom_shifts table:                                │
  │ {                                                      │
  │   custom_shift_id: 100,                                │
  │   employee_id: 5,                                      │
  │   date: 2026-03-22,                                    │
  │   start_time: 10:00:00,  ← Different from shift       │
  │   end_time: 18:00:00,    ← Different from shift       │
  │   reason: "Training session day",                      │
  │   created_by: 1,                                       │
  │   created_at: NOW()                                    │
  │ }                                                      │
  └────────────────────────────────────────────────────────┘
```

---

### **FLOW 5: Holiday Management & Sync**

```
┌───────────────────────────────────────────────────────────────┐
│              HOLIDAY MANAGEMENT & SYNC FLOW                   │
└───────────────────────────────────────────────────────────────┘

STEP 1: SYSTEM INITIALIZATION
  ├─ On first use: HolidayHelper::init($database)
  ├─ Check: Are holidays in database?
  ├─ If NO: Trigger initial sync with Nager.Date API
  └─ If YES: Load from cache

STEP 2: MANUAL SYNC TRIGGER
  ├─ User: HR Admin clicks "Sync Holidays" button
  ├─ Location: Dashboard → Upcoming Holidays Widget
  ├─ Frontend: Click handler calls syncHolidays()
  │
  ├─ JavaScript:
  │  ├─ Ajax POST to: /app/api/holiday_api.php
  │  ├─ Action: 'sync'
  │  └─ Country: 'PH' (Philippines)
  │
  └─ Backend: holiday_api.php (Router)

STEP 3: HOLIDAY API ROUTE HANDLER
  ├─ Endpoint: POST /app/api/holiday_api.php
  ├─ Parameters:
  │  ├─ action: 'sync' or 'get'
  │  ├─ country_code: 'PH'
  │  ├─ year: [optional]
  │  └─ month: [optional]
  │
  ├─ Route to: HolidayController::sync()
  │  OR HolidayController::getHolidays()
  │
  └─ Pass to backend service

STEP 4: FETCH FROM NAGER.DATE API
  ├─ Service: NagerDateService::getHolidaysByCountry()
  ├─ API Call:
  │  GET https://date.nager.at/api/v3/PublicHolidays/[year]/[country_code]
  │  Example: /2026/PH
  │
  ├─ External API Response:
  │  [
  │    {
  │      "date": "2026-01-01",
  │      "localName": "New Year's Day",
  │      "name": "New Year's Day",
  │      "countryCode": "PH",
  │      "fixed": true,
  │      "global": true,
  │      "counties": null,
  │      "launchYear": 1970,
  │      "type": "Public"
  │    },
  │    ...more holidays...
  │  ]
  │
  └─ Store raw response

STEP 5: PROCESS & CATEGORIZE HOLIDAYS
  ├─ For each API holiday:
  │  ├─ Extract:
  │  │  ├─ holiday_date
  │  │  ├─ holiday_name
  │  │  ├─ description
  │  │  ├─ is_recurring
  │  │  ├─ country_code
  │  │  └─ category: Determine type
  │  │
  │  ├─ Categorization Logic:
  │  │  ├─ If "national" in name: category = "NATIONAL"
  │  │  ├─ If "regional" or city-specific: category = "REGIONAL"
  │  │  ├─ If "special" or "observance": category = "SPECIAL"
  │  │  └─ Default: category = "NATIONAL"
  │  │
  │  └─ Prepare data for insert/update

STEP 6: SYNC TO DATABASE
  ├─ Query 1: Check existing holidays
  │  SELECT COUNT(*) FROM ta_holidays
  │  WHERE YEAR(holiday_date) = [year]
  │        AND country_code = 'PH'
  │
  ├─ Query 2: For each API holiday
  │  a) Check if exists:
  │     SELECT * FROM ta_holidays
  │     WHERE holiday_date = [date]
  │           AND country_code = 'PH'
  │
  │  b) If exists: UPDATE ta_holidays
  │     SET holiday_name = [name],
  │         description = [desc],
  │         category = [category],
  │         is_active = 1,
  │         updated_at = NOW()
  │     WHERE holiday_id = [id]
  │
  │  c) If not exists: INSERT INTO ta_holidays
  │     (holiday_date, holiday_name, description, category,
  │      country_code, is_active, is_recurring)
  │     VALUES ([date], [name], [desc], [category], 'PH', 1, 1)
  │
  └─ Result: ta_holidays table updated

STEP 7: CREATE SYNC LOG
  ├─ Query: INSERT INTO ta_holiday_sync_log
  │  ├─ country_code: 'PH'
  │  ├─ year: [year synced]
  │  ├─ total_holidays: [count]
  │  ├─ sync_timestamp: NOW()
  │  ├─ synced_by: [user_id]
  │  ├─ status: 'SUCCESS'
  │  └─ api_response_time: [ms]
  │
  └─ Track syncs for audit

STEP 8: RESPONSE TO USER
  ├─ Success Response:
  │  {
  │    success: true,
  │    message: "Synced [count] holidays for 2026",
  │    last_sync: "2026-03-22 14:30:00",
  │    holidays_added: [count],
  │    holidays_updated: [count]
  │  }
  │
  └─ UI Updates:
     ├─ Last sync timestamp displayed
     ├─ Dashboard widget refreshes
     └─ Calendar updates with new holidays

STEP 9: USE HOLIDAYS IN SYSTEM
  ├─ During Attendance Time In:
  │  ├─ Check: Is today a holiday?
  │  │  Query: SELECT * FROM ta_holidays
  │  │         WHERE holiday_date = CURDATE()
  │  │               AND is_active = 1
  │  │
  │  ├─ If YES: Reject time in
  │  │  Message: "Today is [holiday_name]. No attendance required."
  │  │
  │  └─ If NO: Proceed with time in
  │
  ├─ During Absence Detection:
  │  ├─ If employee absent on holiday: SKIP (no absence record)
  │  └─ If employee present on holiday: Record as present
  │
  ├─ During Leave Calculation:
  │  ├─ When calculating leave days:
  │  ├─ For each day in leave period:
  │  │  ├─ If holiday: Don't count toward leave balance
  │  │  ├─ If weekend: Optionally don't count
  │  │  └─ If working day: Count as 1 day
  │  │
  │  └─ Result: Accurate leave day calculation
  │
  └─ In Calendar View:
     ├─ Holidays marked in red
     ├─ Holiday info displayed
     └─ Events cannot be scheduled on holidays

STEP 10: DISPLAY HOLIDAYS WIDGET
  ├─ Component: UpcomingHolidaysWidget.php
  ├─ Data:
  │  ├─ Next holiday: Next upcoming holiday
  │  ├─ Days until: Count to next holiday
  │  ├─ List: Top 5 upcoming holidays
  │  └─ Last sync: When data was last synced
  │
  ├─ HTML Elements:
  │  ├─ Countdown badge showing days
  │  ├─ Holiday name & date
  │  ├─ Holiday category badge (National/Regional)
  │  ├─ List of upcoming holidays
  │  └─ Manual sync button
  │
  └─ UI Location: Dashboard (right sidebar)

DATABASE FLOW:
  ┌────────────────────────────────────────────────────────┐
  │ BEFORE SYNC:                                           │
  │ ta_holidays: [Empty]                                   │
  │                                                        │
  │ AFTER API FETCH & SYNC:                                │
  │ ta_holidays table:                                     │
  │ {                                                      │
  │   holiday_id: 1,                                       │
  │   holiday_date: 2026-01-01,                            │
  │   holiday_name: "New Year's Day",                      │
  │   description: "National holiday",                     │
  │   category: "NATIONAL",                                │
  │   country_code: "PH",                                  │
  │   is_active: 1,                                        │
  │   is_recurring: 1,                                     │
  │   created_at: [sync date],                             │
  │   updated_at: [sync date]                              │
  │ },                                                     │
  │ { ... more holidays ... }                              │
  │                                                        │
  │ ta_holiday_sync_log table:                             │
  │ {                                                      │
  │   sync_id: 1,                                          │
  │   country_code: "PH",                                  │
  │   year: 2026,                                          │
  │   total_holidays: 25,                                  │
  │   sync_timestamp: 2026-03-22 14:30:00,                 │
  │   synced_by: 1,                                        │
  │   status: "SUCCESS",                                   │
  │   api_response_time: 245                               │
  │ }                                                      │
  └────────────────────────────────────────────────────────┘
```

---

## 📊 Database Schema & Models

### Core Tables Structure

```sql
-- EMPLOYEES TABLE
CREATE TABLE employees (
  employee_id INT PRIMARY KEY AUTO_INCREMENT,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100),
  department VARCHAR(50),
  position VARCHAR(50),
  employment_status ENUM('Active','Inactive','Resigned') DEFAULT 'Active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- SHIFTS TABLE
CREATE TABLE ta_shifts (
  shift_id INT PRIMARY KEY AUTO_INCREMENT,
  shift_name VARCHAR(50) NOT NULL UNIQUE,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  break_duration INT DEFAULT 60,
  description TEXT,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- EMPLOYEE SHIFTS TABLE
CREATE TABLE ta_employee_shifts (
  employee_shift_id INT PRIMARY KEY AUTO_INCREMENT,
  employee_id INT NOT NULL,
  shift_id INT NOT NULL,
  effective_from DATE NOT NULL,
  effective_to DATE,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (employee_id) REFERENCES employees(employee_id),
  FOREIGN KEY (shift_id) REFERENCES ta_shifts(shift_id)
);

-- ATTENDANCE TABLE
CREATE TABLE ta_attendance (
  attendance_id INT PRIMARY KEY AUTO_INCREMENT,
  employee_id INT NOT NULL,
  attendance_date DATE NOT NULL,
  time_in TIME,
  time_out TIME,
  work_hours DECIMAL(5,2),
  status ENUM('PRESENT','LATE','UNDERTIME','ABSENT','HOLIDAY') DEFAULT 'PRESENT',
  recorded_by ENUM('MANUAL','QR','SYSTEM') DEFAULT 'MANUAL',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (employee_id) REFERENCES employees(employee_id),
  INDEX idx_employee_date (employee_id, attendance_date)
);

-- LEAVE TYPES TABLE
CREATE TABLE ta_leave_types (
  leave_type_id INT PRIMARY KEY AUTO_INCREMENT,
  leave_type_name VARCHAR(50) NOT NULL,
  description TEXT,
  annual_allocation INT DEFAULT 15,
  is_active TINYINT(1) DEFAULT 1
);

-- LEAVE BALANCE TABLE
CREATE TABLE ta_leave_balance (
  balance_id INT PRIMARY KEY AUTO_INCREMENT,
  employee_id INT NOT NULL,
  leave_type_id INT NOT NULL,
  balance INT DEFAULT 15,
  last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (employee_id) REFERENCES employees(employee_id),
  FOREIGN KEY (leave_type_id) REFERENCES ta_leave_types(leave_type_id),
  UNIQUE KEY (employee_id, leave_type_id)
);

-- LEAVE REQUESTS TABLE
CREATE TABLE ta_leave_requests (
  id INT PRIMARY KEY AUTO_INCREMENT,
  employee_id INT NOT NULL,
  leave_type_id INT NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  details TEXT,
  status ENUM('PENDING','APPROVED_BY_HEAD','APPROVED_BY_HR','REJECTED_BY_HEAD','REJECTED_BY_HR') DEFAULT 'PENDING',
  date_submitted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  department_head_approved_by INT,
  department_head_approved_date DATETIME,
  hr_approved_by INT,
  hr_approved_date DATETIME,
  reject_reason TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (employee_id) REFERENCES employees(employee_id),
  FOREIGN KEY (leave_type_id) REFERENCES ta_leave_types(leave_type_id),
  INDEX idx_emp_status (employee_id, status),
  INDEX idx_date_range (start_date, end_date)
);

-- HOLIDAYS TABLE
CREATE TABLE ta_holidays (
  holiday_id INT PRIMARY KEY AUTO_INCREMENT,
  holiday_date DATE NOT NULL,
  holiday_name VARCHAR(100) NOT NULL,
  description TEXT,
  category ENUM('NATIONAL','REGIONAL','SPECIAL') DEFAULT 'NATIONAL',
  country_code VARCHAR(2) DEFAULT 'PH',
  is_active TINYINT(1) DEFAULT 1,
  is_recurring TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY (holiday_date, country_code),
  INDEX idx_active_date (is_active, holiday_date)
);

-- ABSENCE/LATE RECORDS TABLE
CREATE TABLE ta_absence_late_records (
  record_id INT PRIMARY KEY AUTO_INCREMENT,
  employee_id INT NOT NULL,
  date DATE NOT NULL,
  type ENUM('ABSENT','LATE') NOT NULL,
  late_minutes INT,
  is_excused TINYINT(1) DEFAULT 0,
  excuse_type VARCHAR(50),
  leave_request_id INT,
  reason TEXT,
  status ENUM('PENDING','APPROVED','REJECTED','PENDING_APPEAL') DEFAULT 'PENDING',
  created_by VARCHAR(50) DEFAULT 'SYSTEM',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (employee_id) REFERENCES employees(employee_id),
  FOREIGN KEY (leave_request_id) REFERENCES ta_leave_requests(id),
  INDEX idx_emp_date (employee_id, date),
  INDEX idx_status (status)
);

-- CUSTOM SHIFTS TABLE
CREATE TABLE ta_custom_shifts (
  custom_shift_id INT PRIMARY KEY AUTO_INCREMENT,
  employee_id INT NOT NULL,
  date DATE NOT NULL,
  start_time TIME,
  end_time TIME,
  reason TEXT,
  created_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (employee_id) REFERENCES employees(employee_id),
  UNIQUE KEY (employee_id, date)
);

-- NOTIFICATIONS TABLE
CREATE TABLE ta_notifications (
  notification_id INT PRIMARY KEY AUTO_INCREMENT,
  recipient_id INT,
  type VARCHAR(50),
  reference_id INT,
  message TEXT,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- AUDIT LOGS TABLE
CREATE TABLE audit_logs (
  log_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  action VARCHAR(50),
  table_name VARCHAR(50),
  record_id INT,
  old_values JSON,
  new_values JSON,
  status ENUM('SUCCESS','FAILED') DEFAULT 'SUCCESS',
  details TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_user_action (user_id, action)
);
```

---

## 🔌 API Endpoints & Data Flow

### Key API Endpoints

| Endpoint | Method | Purpose | Parameters |
|----------|--------|---------|------------|
| `submit_leave.php` | POST | Submit leave request | employee_id, leave_type_id, start_date, end_date, reason |
| `get_leave_balance.php` | GET | Check employee leave balance | employee_id, leave_type_id |
| `get_employee_schedule.php` | GET | Get employee schedule for date range | employee_id, start_date, end_date |
| `save_employee_schedule.php` | POST | Save custom shift times | employee_id, date, start_time, end_time |
| `get_day_schedule.php` | GET | Get specific day schedule | employee_id, date |
| `get_pending_leaves.php` | GET | Get pending leave requests | role (head/hr), department (if head) |
| `approve_leave_head.php` | POST | Approve leave (department head) | leave_request_id, approver_id, remarks |
| `approve_leave_hr.php` | POST | Approve leave (HR final) | leave_request_id, approver_id, remarks |
| `holiday_api.php` | POST | Sync holidays or get holidays | action (sync/get), country_code, year, month |
| `metrics.php` | GET | Get attendance metrics | date_from, date_to, department, employee_id |
| `realtime_updates.php` | WebSocket/Poll | Real-time attendance updates | employee_id |
| `absence_late_management.php` | POST/GET | Manage absence/late records | action, record_id, is_excused, excuse_type |
| `get_day_records.php` | GET | Get daily records | date |

---

## 👥 User Interactions & Workflows

### Roles & Permissions

```
┌─────────────────────────────────────────────────────────────┐
│ ROLE: EMPLOYEE                                              │
├─────────────────────────────────────────────────────────────┤
│ ✓ Time In / Time Out (Manual or QR)                         │
│ ✓ View own attendance record                                │
│ ✓ View own schedule                                         │
│ ✓ Submit leave request                                      │
│ ✓ View leave balance                                        │
│ ✓ Appeal absence/late                                       │
│ ✗ Cannot approve leave                                      │
│ ✗ Cannot view other employees' records                      │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│ ROLE: DEPARTMENT HEAD                                       │
├─────────────────────────────────────────────────────────────┤
│ ✓ View team attendance                                      │
│ ✓ Approve/Reject leave requests (first level)               │
│ ✓ View team schedule                                        │
│ ✓ Approve absence/late appeals                              │
│ ✓ View team metrics                                         │
│ ✗ Cannot change leave balance                               │
│ ✗ Cannot modify attendance records directly                 │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│ ROLE: HR ADMINISTRATOR                                      │
├─────────────────────────────────────────────────────────────┤
│ ✓ View all attendance records                               │
│ ✓ Final approval of leave requests                          │
│ ✓ Manage shifts & schedules                                 │
│ ✓ Modify leave balance                                      │
│ ✓ Create/Edit employees                                     │
│ ✓ Sync holidays from API                                    │
│ ✓ Generate attendance reports                               │
│ ✓ View all metrics & analytics                              │
│ ✓ Override attendance records (with audit)                  │
│ ✓ Manage absence/late appeals                               │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│ ROLE: SYSTEM ADMINISTRATOR                                  │
├─────────────────────────────────────────────────────────────┤
│ ✓ All HR Administrator permissions                          │
│ ✓ Manage system users & roles                               │
│ ✓ Manage database                                           │
│ ✓ System settings & configuration                           │
│ ✓ Access audit logs                                         │
│ ✓ Backup & restore operations                               │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔗 Integration Points

### System Integrations

```
TIME & ATTENDANCE SYSTEM
         │
         ├─→ Employee Management System
         │   └─ Get employee info (name, dept, position)
         │   └─ Sync active status
         │
         ├─→ Authentication/Authorization System
         │   └─ Session management
         │   └─ Role-based access control
         │
         ├─→ Notification System
         │   └─ Email/SMS notifications
         │   └─ Leave request alerts
         │
         ├─→ Payroll System
         │   └─ Export attendance for payroll
         │   └─ Calculate late deductions
         │   └─ Calculate overtime
         │
         ├─→ Holiday Management API
         │   └─ Nager.Date API (external)
         │   └─ Sync national/regional holidays
         │
         ├─→ QR Code System
         │   └─ QR code scanning & validation
         │   └─ Device registration
         │
         ├─→ Reporting & Analytics
         │   └─ Generate attendance reports
         │   └─ Metrics & dashboards
         │
         └─→ Audit & Compliance
             └─ Audit logs
             └─ Compliance reporting
             └─ Data retention
```

---

## ⚠️ Error Handling & Validation

### Validation Rules

```
ATTENDANCE TIME IN:
  ├─ Check: User logged in? → 401 Unauthorized
  ├─ Check: Employee exists? → 400 Employee not found
  ├─ Check: Is today holiday? → Reject with holiday info
  ├─ Check: Already timed in? → Reject (already timed in)
  ├─ Check: Valid timezone? → Convert to PH time (UTC+8)
  └─ Result: Record created or error returned

LEAVE REQUEST SUBMISSION:
  ├─ Check: All fields present? → 400 Missing fields
  ├─ Check: Valid date format? → 400 Invalid date
  ├─ Check: start_date <= end_date? → 400 Invalid range
  ├─ Check: Has balance? → 400 Insufficient balance
  ├─ Check: Access control? → 403 Forbidden
  ├─ Check: Past dates? → Warn but allow if approved
  └─ Result: Request created or error returned

SHIFT ASSIGNMENT:
  ├─ Check: Employee active? → 400 Employee inactive
  ├─ Check: Shift exists? → 400 Shift not found
  ├─ Check: Time range valid? → 400 Invalid times
  ├─ Check: Overlap with existing? → Deactivate old
  └─ Result: Assignment created

ABSENCE DETECTION:
  ├─ Check: Is holiday? → Skip processing
  ├─ Check: Is approved leave? → Mark excused
  ├─ Check: Shift exists? → Get expected time
  ├─ Check: Attended? → If not, create absence
  └─ Result: Absence record created (if applicable)
```

---

## 📈 Data Flow Diagram (High-Level)

```
┌─────────────────────────────────────────────────────────────┐
│              USER INTERFACE LAYER                           │
│  (time_attendance.php)                                      │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Dashboard Tab          Schedule Calendar Tab              │
│  ├─ Time In/Out         ├─ Employee Search                 │
│  ├─ Today's Status      ├─ Month/Week/Day View             │
│  ├─ Metrics             ├─ Timeline Editor                 │
│  ├─ Holidays Widget     └─ Custom Shift Save               │
│  ├─ Leave Balance       Leave Management Tab               │
│  └─ Pending Approvals   ├─ Submit Leave                    │
│                         ├─ My Requests Status              │
│                         ├─ Approve Leaves (if Head/HR)     │
│                         └─ Leave History                   │
│                                                             │
└──────────────┬──────────────────────────────────────────────┘
               │ AJAX/Form Submissions
               ▼
┌─────────────────────────────────────────────────────────────┐
│              API LAYER                                      │
│  (app/api/*.php)                                            │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ├─ submit_leave.php           ├─ holiday_api.php          │
│  ├─ approve_leave_*.php        ├─ metrics.php              │
│  ├─ get_employee_schedule.php  ├─ realtime_updates.php     │
│  ├─ save_employee_schedule.php ├─ absence_late_mgmt.php    │
│  ├─ get_pending_leaves.php     └─ get_day_records.php      │
│  └─ get_leave_balance.php                                  │
│                                                             │
└──────────────┬──────────────────────────────────────────────┘
               │ Controller & Model Calls
               ▼
┌─────────────────────────────────────────────────────────────┐
│           BUSINESS LOGIC LAYER                              │
│  (app/controllers/*.php)                                    │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ├─ AttendanceController       ├─ LeaveController          │
│  ├─ ShiftController            ├─ HolidayController        │
│  ├─ NotificationController     └─ AbsenceLateMgmt Logic    │
│                                                             │
│  Uses Helper Classes:                                       │
│  ├─ Helper.php                 ├─ HolidayHelper            │
│  ├─ LeaveAbsenceHelper         ├─ QRHelper                 │
│  ├─ AuditLog.php               └─ Session.php              │
│                                                             │
└──────────────┬──────────────────────────────────────────────┘
               │ Data Access Operations
               ▼
┌─────────────────────────────────────────────────────────────┐
│            DATA ACCESS LAYER                                │
│  (app/models/*.php)                                         │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ├─ Attendance.php       ├─ Holiday.php                    │
│  ├─ Leave.php            ├─ AbsenceLateMgmt.php            │
│  ├─ Shift.php            ├─ Employee.php                   │
│  ├─ EmployeeShift.php    ├─ Notification.php               │
│  └─ Users.php                                              │
│                                                             │
│  All models use Database.php (PDO connection)              │
│                                                             │
└──────────────┬──────────────────────────────────────────────┘
               │ SQL Queries (Parameterized)
               ▼
┌─────────────────────────────────────────────────────────────┐
│              DATABASE LAYER                                 │
│  (MySQL/MariaDB)                                            │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ├─ ta_attendance          ├─ ta_holidays                  │
│  ├─ ta_employee_shifts     ├─ ta_holiday_sync_log          │
│  ├─ ta_shifts              ├─ ta_absence_late_records      │
│  ├─ ta_leave_requests      ├─ ta_leave_types               │
│  ├─ ta_leave_balance       ├─ employees                    │
│  ├─ ta_notifications       ├─ department_heads             │
│  └─ audit_logs             └─ ta_custom_shifts             │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 🎯 Summary

The Time & Attendance System follows a well-structured MVC architecture with:

- **Clear separation of concerns**: Views, Controllers, Models, and Utilities
- **Robust validation** at every step
- **Comprehensive audit logging** for compliance
- **Role-based access control** for security
- **Multiple integration points** with external systems
- **Real-time processing** with fallback mechanisms
- **Holiday synchronization** with external API
- **Flexible scheduling** with custom shift support
- **Leave & absence integration** for accurate tracking
- **Metrics & reporting** capabilities

All processes follow standardized patterns for error handling, validation, and logging, ensuring system reliability and auditability.

---

**Last Updated**: March 22, 2026  
**Version**: 1.0
