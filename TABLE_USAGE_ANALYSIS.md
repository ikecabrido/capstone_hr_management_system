# Database Tables Usage Analysis

## Overview
This document maps all 13 tables in the hr_management database to their usage in PHP functions and modules across the system.

---

## Table-by-Table Analysis

### 1. **USERS** ✓ IN USE
**Status:** ACTIVELY USED  
**Location:** `hr_management` database

**Purpose:** Core authentication and user management table

**Columns:**
- `id` (PK), `username`, `email`, `password_hash`, `role`, `full_name`, `department`, `is_active`, `created_at`, `updated_at`

**Direct Usage:**
```
✓ auth/user.php:23      - findByUsername($username)
✓ auth/user.php:32      - findById($id)
✓ auth/user.php:41      - updateProfile($id, $full_name)
✓ auth/auth.php         - Login/authentication queries
✓ time_attendance/app/api/realtime_updates.php:143 - LEFT JOIN users u
✓ time_attendance/app/helpers/NotificationService.php:286 - FROM users
```

**Modules Using:**
- Authentication system (login/logout)
- Activity logging (login events)
- Real-time updates API
- Notifications system

**Status:** ✅ **FULLY UTILIZED**

---

### 2. **EMPLOYEES** ✓ IN USE
**Status:** ACTIVELY USED  
**Location:** `hr_management` database

**Purpose:** Employee master data and employment information

**Columns:**
- `employee_id` (PK), `user_id` (FK), `first_name`, `last_name`, `employee_number`, `department`, `position`, `hire_date`, `status`, `email`, etc.

**Direct Usage:**
```
✓ time_attendance/public/shifts.php:1016          - SELECT employee_id, first_name, last_name FROM employees
✓ time_attendance/public/qr_scan.php:70           - SELECT employee_id, first_name, last_name FROM employees WHERE user_id
✓ time_attendance/public/Login.php:32             - SELECT employee_id, first_name, last_name FROM employees WHERE user_id
✓ time_attendance/public/calendar.php:89          - SELECT first_name, last_name FROM employees WHERE id
✓ time_attendance/public/calendar.php:150         - FROM employees (department query)
✓ time_attendance/public/analytics.php:45         - SELECT DISTINCT department FROM employees
✓ time_attendance/app/api/realtime_updates.php    - Multiple JOINs with employees
✓ time_attendance/app/api/approve_leave_head.php:78 - FROM employees e
```

**Modules Using:**
- Shift management (shifts.php)
- QR code scanning (qr_scan.php)
- Login dashboard (Login.php)
- Attendance calendar (calendar.php)
- Analytics/reporting (analytics.php)
- Real-time updates API
- Leave approval workflow

**Relationships:**
- FK to `users.id` on `user_id`
- Referenced by: `attendance`, `leave_requests`, `leave_balances`, `employee_shifts`, `department_heads`

**Status:** ✅ **FULLY UTILIZED - CORE TABLE**

---

### 3. **ATTENDANCE** ✓ IN USE
**Status:** ACTIVELY USED  
**Location:** `hr_management` database

**Purpose:** Track daily attendance records (time in/out)

**Columns:**
- `attendance_id` (PK), `employee_id` (FK), `shift_id`, `attendance_date`, `time_in`, `time_out`, `recorded_by`, `status`, `is_approved`, `total_hours_worked`, etc.

**Direct Usage:**
```
✓ time_attendance/app/api/realtime_updates.php:76  - FROM attendance a
✓ time_attendance/public/qr_scan.php:91            - SELECT attendance_id, time_in, time_out FROM attendance
✓ time_attendance/public/Login.php:42              - SELECT attendance_id, time_in, time_out FROM attendance
✓ time_attendance/public/export_dashboard.php:37   - SELECT * FROM attendance
✓ time_attendance/public/employee_dashboard.php:89 - SELECT * FROM attendance
✓ time_attendance/public/calendar.php:59           - FROM attendance (with date grouping)
✓ time_attendance/public/analytics.php:66,95,113,128 - Multiple attendance queries
✓ time_attendance/app/models/Attendance.php        - Full CRUD operations
```

**Modules Using:**
- QR scanning/attendance registration (qr_scan.php)
- Employee dashboard (employee_dashboard.php)
- Admin dashboard (dashboard.php)
- Attendance calendar (calendar.php)
- Analytics/reports (analytics.php, reports.php)
- Real-time updates API
- Export functionality (export_dashboard.php)
- Attendance model class (Attendance.php)

**Related Tables:**
- FK to `employees.employee_id`
- FK to `shifts.shift_id`
- Referenced by: `audit_logs`

**Status:** ✅ **FULLY UTILIZED - CRITICAL TABLE**

---

### 4. **ATTENDANCE_TOKENS** ✓ IN USE
**Status:** ACTIVELY USED  
**Location:** `hr_management` database

**Purpose:** One-time QR code tokens for secure attendance registration

**Columns:**
- `token_id` (PK), `token` (unique hash), `generated_by`, `generated_for_date`, `expires_at`, `used`, `used_by`, `used_at`, `ip_address`, `created_at`

**Direct Usage:**
```
✓ time_attendance/public/qr_display_kiosk.php:35        - SELECT ip_address FROM attendance_tokens
✓ time_attendance/app/helpers/QRHelper.php:119          - SELECT * FROM attendance_tokens
✓ time_attendance/app/helpers/QRHelper.php:135          - SELECT token_id, used, expires_at FROM attendance_tokens
✓ time_attendance/app/helpers/QRHelper.php:171          - SELECT * FROM attendance_tokens WHERE token
✓ time_attendance/app/helpers/QRHelper.php:184          - DELETE FROM attendance_tokens (cleanup)
✓ time_attendance/app/helpers/QRHelper.php:200          - SELECT COUNT(*) FROM attendance_tokens
```

**Modules Using:**
- QR code generation and validation (QRHelper.php)
- QR code display/kiosk (qr_display_kiosk.php)
- QR scanning (qr_scan.php via QRHelper)

**Related Tables:**
- FK to `users.id` on `generated_by`
- FK to `employees.employee_id` on `used_by`

**Status:** ✅ **FULLY UTILIZED - QR SECURITY**

---

### 5. **SHIFTS** ✓ IN USE
**Status:** ACTIVELY USED  
**Location:** `hr_management` database

**Purpose:** Define work shifts (morning, afternoon, night) with time windows

**Columns:**
- `shift_id` (PK), `shift_name`, `start_time`, `end_time`, `buffer_before_start`, `buffer_after_end`, `is_active`, `created_at`, `updated_at`

**Direct Usage:**
```
✓ time_attendance/public/shifts.php:102              - getAllShifts()
✓ time_attendance/public/shifts.php:103              - getShiftStatistics()
✓ time_attendance/app/controllers/ShiftController.php - Full CRUD operations
```

**Modules Using:**
- Shift management interface (shifts.php)
- Shift controller/model (ShiftController.php, Shift.php)
- Attendance registration (validates against shift hours)
- Analytics (shift-based reporting)

**Related Tables:**
- Referenced by: `attendance`, `employee_shifts`

**Status:** ✅ **FULLY UTILIZED**

---

### 6. **EMPLOYEE_SHIFTS** ✓ IN USE
**Status:** ACTIVELY USED  
**Location:** `hr_management` database

**Purpose:** Assign employees to shifts (many-to-many relationship)

**Columns:**
- `assignment_id` (PK), `employee_id` (FK), `shift_id` (FK), `assigned_date`, `effective_from`, `effective_to`, `is_active`, `created_at`, `updated_at`

**Direct Usage:**
```
✓ time_attendance/public/shifts.php:1066   - getEmployeesOnShift(null)
✓ time_attendance/app/controllers/ShiftController.php - Assignment CRUD
```

**Modules Using:**
- Shift assignment management (shifts.php)
- Attendance validation against assigned shifts
- Employee shift schedules

**Related Tables:**
- FK to `employees.employee_id`
- FK to `shifts.shift_id`

**Status:** ✅ **IN USE - SUPPORTING TABLE**

---

### 7. **HOLIDAYS** ✓ IN USE
**Status:** ACTIVELY USED  
**Location:** `hr_management` database

**Purpose:** Define company holidays and non-working days

**Columns:**
- `holiday_id` (PK), `holiday_date`, `holiday_name`, `is_working_day`, `description`, `created_at`, `updated_at`

**Direct Usage:**
```
✓ time_attendance/app/models/Attendance.php:239  - SELECT is_working_day FROM holidays WHERE holiday_date
✓ time_attendance/app/models/Attendance.php:257  - FROM holidays (range query)
✓ time_attendance/app/models/Attendance.php:276  - FROM holidays (exclusion check)
✓ time_attendance/verify_implementation.php:151  - SELECT COUNT(*) FROM holidays
```

**Modules Using:**
- Attendance model (holiday validation)
- Absence calculation
- Leave approval workflow
- Report generation

**Status:** ✅ **FULLY UTILIZED - BUSINESS RULE**

---

### 8. **LEAVE_TYPES** ✓ IN USE
**Status:** ACTIVELY USED  
**Location:** `hr_management` database

**Purpose:** Define types of leaves (Sick, Vacation, Personal, etc.)

**Columns:**
- `leave_type_id` (PK), `leave_type_name`, `max_days_per_year`, `is_deductible`, `description`, `created_at`, `updated_at`

**Direct Usage:**
```
✓ time_attendance/public/leave_request.php:85     - SELECT * FROM leave_types WHERE is_deductible
✓ time_attendance/public/export_dashboard.php:47  - JOIN leave_types lt ON lr.leave_type_id
✓ time_attendance/public/export_dashboard.php:57  - JOIN leave_types lt ON lb.leave_type_id
```

**Modules Using:**
- Leave request interface (leave_request.php)
- Leave approval workflow
- Leave balance calculations
- Export/reporting functionality

**Related Tables:**
- Referenced by: `leave_requests`, `leave_balances`

**Status:** ✅ **FULLY UTILIZED - CONFIGURATION TABLE**

---

### 9. **LEAVE_REQUESTS** ✓ IN USE
**Status:** ACTIVELY USED  
**Location:** `hr_management` database

**Purpose:** Track employee leave request submissions and approval workflow

**Columns:**
- `leave_request_id` (PK), `employee_id` (FK), `leave_type_id` (FK), `start_date`, `end_date`, `days_requested`, `reason`, `status`, `approved_by`, `approved_date`, `created_at`, `updated_at`

**Direct Usage:**
```
✓ time_attendance/public/export_dashboard.php:46   - FROM leave_requests lr
✓ time_attendance/public/export_dashboard.php:214  - foreach ($leave_requests as $leave)
✓ time_attendance/app/api/submit_leave.php         - Submit new leave requests
✓ time_attendance/app/api/approve_leave_head.php   - Approve pending requests
✓ time_attendance/app/api/get_pending_leaves.php   - Retrieve pending leaves
```

**Modules Using:**
- Leave request submission (submit_leave.php)
- Leave request approval (approve_leave_head.php)
- Pending leave queries (get_pending_leaves.php)
- Export/reporting (export_dashboard.php)

**Related Tables:**
- FK to `employees.employee_id`
- FK to `leave_types.leave_type_id`
- Referenced by: `audit_logs`

**Status:** ✅ **FULLY UTILIZED - WORKFLOW TABLE**

---

### 10. **LEAVE_BALANCES** ✓ IN USE
**Status:** ACTIVELY USED  
**Location:** `hr_management` database

**Purpose:** Track remaining leave balance for each employee per leave type

**Columns:**
- `balance_id` (PK), `employee_id` (FK), `leave_type_id` (FK), `remaining_balance`, `consumed_days`, `year`, `created_at`, `updated_at`

**Direct Usage:**
```
✓ time_attendance/public/export_dashboard.php:56   - FROM leave_balances lb
✓ time_attendance/public/export_dashboard.php:248  - foreach ($leave_balances as $balance)
✓ time_attendance/app/api/get_leave_balance.php    - Retrieve employee leave balance
✓ time_attendance/verify_implementation.php:139    - SELECT COUNT(*) FROM leave_balances
```

**Modules Using:**
- Leave balance API (get_leave_balance.php)
- Leave request validation
- Export/reporting (export_dashboard.php)
- Leave deduction logic

**Related Tables:**
- FK to `employees.employee_id`
- FK to `leave_types.leave_type_id`

**Status:** ✅ **FULLY UTILIZED - BUSINESS LOGIC**

---

### 11. **DEPARTMENT_HEADS** ✓ IN USE
**Status:** IN USE (LIMITED)
**Location:** `hr_management` database

**Purpose:** Map department heads for leave approval chain

**Columns:**
- `head_id` (PK), `employee_id` (FK), `department`, `role`, `approved_by`, `created_at`, `updated_at`

**Direct Usage:**
```
✓ time_attendance/app/api/approve_leave_head.php:94 - FROM department_heads
✓ time_attendance/verify_implementation.php:145     - SELECT COUNT(*) FROM department_heads
```

**Modules Using:**
- Leave approval workflow (approve_leave_head.php)
- Department head identification

**Related Tables:**
- FK to `employees.employee_id`

**Status:** ✅ **IN USE - HIERARCHICAL MANAGEMENT**

---

### 12. **AUDIT_LOGS** ✓ IN USE
**Status:** ACTIVELY USED  
**Location:** `hr_management` database

**Purpose:** Track all system actions for compliance and debugging

**Columns:**
- `audit_id` (PK), `action_type`, `user_id` (FK), `employee_id` (FK), `attendance_id` (FK), `action_details` (JSON), `ip_address`, `user_agent`, `status`, `error_message`, `created_at`

**Direct Usage:**
```
✓ time_attendance/app/models/Attendance.php       - Log time in/out operations
✓ time_attendance/app/models/Leave.php            - Log leave operations
✓ time_attendance/public/qr_scan.php              - Log QR scans
✓ auth/auth.php                                   - Log login attempts
✓ Multiple controllers                             - CRUD operation logging
```

**Action Types Logged:**
- `LOGIN_SUCCESS`, `LOGIN_FAILED`
- `TIME_IN_SUCCESS`, `TIME_OUT_SUCCESS`
- `QR_SCAN_SUCCESS`, `QR_SCAN_FAILED`
- `ATTENDANCE_APPROVED`
- `LOGOUT`
- Various HR operations

**Modules Using:**
- All authentication (auth/auth.php)
- All attendance (QR, manual registration)
- All leave management
- Debug dashboard (debug_dashboard.php)

**Status:** ✅ **FULLY UTILIZED - COMPLIANCE TABLE**

---

### 13. **NOTIFICATIONS** (?) UNCLEAR USAGE
**Status:** DEFINED BUT USAGE UNCERTAIN
**Location:** `hr_management` database

**Purpose:** Store system notifications (intended purpose)

**Columns:**
- `notification_id` (PK), `user_id` (FK), `type`, `title`, `message`, `link`, `is_read`, `created_at`, `updated_at`

**Direct Usage:**
```
❌ NO DIRECT QUERY FOUND in PHP files
⚠️ Referenced in NotificationService.php but not fully implemented
```

**Modules Mentioning:**
- `time_attendance/app/helpers/NotificationService.php` - Email-based notifications only
- No database INSERT/SELECT operations found

**Status:** ⚠️ **DEFINED BUT UNUSED - CANDIDATE FOR REMOVAL OR IMPLEMENTATION**

---

## Summary Table

| # | Table Name | Status | Priority | Usage Count | Critical? |
|---|------------|--------|----------|-------------|-----------|
| 1 | users | ✅ IN USE | HIGH | 6+ | YES |
| 2 | employees | ✅ IN USE | HIGH | 12+ | YES |
| 3 | attendance | ✅ IN USE | HIGH | 15+ | YES |
| 4 | attendance_tokens | ✅ IN USE | HIGH | 6+ | YES |
| 5 | shifts | ✅ IN USE | MEDIUM | 3+ | YES |
| 6 | employee_shifts | ✅ IN USE | MEDIUM | 2+ | YES |
| 7 | holidays | ✅ IN USE | MEDIUM | 3+ | YES |
| 8 | leave_types | ✅ IN USE | MEDIUM | 3+ | YES |
| 9 | leave_requests | ✅ IN USE | HIGH | 5+ | YES |
| 10 | leave_balances | ✅ IN USE | MEDIUM | 3+ | YES |
| 11 | department_heads | ✅ IN USE | MEDIUM | 2+ | PARTIAL |
| 12 | audit_logs | ✅ IN USE | HIGH | 10+ | YES |
| 13 | notifications | ⚠️ UNDEFINED | LOW | 0 | NO |

---

## Key Findings

### ✅ Fully Operational (12/13)
All core tables are actively used and integrated throughout the system:
- **Authentication & Security:** users, audit_logs, attendance_tokens
- **Employee Management:** employees, department_heads, employee_shifts, shifts
- **Time Tracking:** attendance, holidays
- **Leave Management:** leave_types, leave_requests, leave_balances

### ⚠️ Potential Issues
1. **notifications table** - Defined but not actively queried
   - NotificationService.php sends emails but doesn't store in DB
   - Could be removed or fully implemented in future
   - Consider removing if email-only is the strategy

### 🎯 Recommendations

**For Database Consolidation (if merging with time_and_attendance):**
1. ✅ All 12 active tables should be preserved
2. ⚠️ Evaluate notifications table - implement or remove
3. ✅ No conflicts expected with hr_management tables
4. ✅ No unused tables requiring removal

**For Code Optimization:**
1. Implement notifications database storage if needed
2. Consider indexing frequently queried columns (employee_id, user_id, created_at)
3. Add more comprehensive audit logging for leave operations

**For Module Consolidation:**
All time_attendance references can safely point to hr_management database tables:
- No schema conflicts
- All relationships intact
- All functions covered

---

## Code Location Reference

### Core Authentication
- `auth/auth.php` - Login/logout, audit logging
- `auth/user.php` - User queries
- `auth/database.php` - Connection management

### Time & Attendance Modules
- `time_attendance/public/qr_scan.php` - QR attendance
- `time_attendance/public/shifts.php` - Shift management
- `time_attendance/public/analytics.php` - Attendance analytics
- `time_attendance/public/calendar.php` - Attendance calendar
- `time_attendance/app/models/Attendance.php` - Attendance business logic
- `time_attendance/app/helpers/QRHelper.php` - QR token generation

### Leave Management
- `time_attendance/public/leave_request.php` - Leave submission
- `time_attendance/app/api/submit_leave.php` - Leave API
- `time_attendance/app/api/approve_leave_head.php` - Approval workflow
- `time_attendance/app/models/Leave.php` - Leave business logic

### Reporting
- `time_attendance/public/export_dashboard.php` - Data export
- `time_attendance/public/reports.php` - Report generation

### Debugging
- `debug_dashboard.php` - System status
- `time_attendance/verify_implementation.php` - Schema verification

