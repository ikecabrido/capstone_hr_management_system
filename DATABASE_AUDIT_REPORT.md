# Database Audit & Optimization Report
**Generated:** March 14, 2026  
**Database:** time_and_attendance  
**Status:** Contains unused tables and incomplete process flows

---

## Executive Summary

The Time & Attendance database has **14 tables** of which **3 are completely unused** and **1 is partially used**. Process flows for leave requests and holiday calculations are incomplete, with orphaned table structures that are never populated or queried.

---

## Table Usage Analysis

| Table | Status | Usage | Issues |
|-------|--------|-------|--------|
| `attendance` | ✅ Active | Core attendance records | Good - actively used |
| `attendance_tokens` | ✅ Active | QR token generation | Good - actively used |
| `audit_logs` | ✅ Active | Login/action tracking | Good - actively used |
| `department_heads` | ❌ **UNUSED** | Leave approval (intended) | **Never populated, no queries** |
| `employees` | ✅ Active | Employee master data | Good - actively used |
| `employee_shifts` | ✅ Active | Shift assignments | Good - actively used |
| `holidays` | ❌ **UNUSED** | Holiday management (intended) | **Never populated, never queried** |
| `leave_balances` | ❌ **UNUSED** | Leave balance tracking | **Never updated, never queried** |
| `leave_requests` | ✅ Active | Leave request management | Good - actively used |
| `leave_types` | ✅ Active | Leave type reference | Good - actively used |
| `notifications` | ⚠️ Partial | Notifications | **Schema doesn't match queries** |
| `shifts` | ✅ Active | Shift management | Good - actively used |
| `users` | ✅ Active | User authentication | Good - actively used |

---

## Critical Issues

### 1. **BROKEN LEAVE APPROVAL WORKFLOW**

**Problem:** Leave requests reference `department_head_id` but the `department_heads` table is never populated.

**Current Flow (Broken):**
```sql
-- Table references non-existent data
ALTER TABLE leave_requests 
  ADD CONSTRAINT fk_department_head 
  FOREIGN KEY (department_head_id) 
  REFERENCES department_heads(dept_head_id);
```

**Issue:** No mechanism to assign department heads, so leave approval chain is broken.

**Impact:** 
- Leave requests cannot route to department heads for first-level approval
- Two-tier approval system (Department Head → HR) cannot function

---

### 2. **LEAVE BALANCE TRACKING NOT IMPLEMENTED**

**Problem:** `leave_balances` table is defined but never:
- Initialized when leave types are assigned
- Updated when leaves are approved
- Queried for remaining balance validation

**Current Data:**
- Table is empty
- No INSERT queries in codebase
- No balance checking before approval

**Impact:**
- Employees can request unlimited leave
- No enforcement of "Days Per Year" limits
- No tracking of used vs remaining days

---

### 3. **HOLIDAY CALCULATIONS MISSING**

**Problem:** `holidays` table is created but:
- Never populated with actual holidays
- Never queried during attendance processing
- No impact on attendance status calculations

**Current Issue:**
- Weekend logic not checking `holidays` table
- Employees marked absent on company holidays

---

### 4. **NOTIFICATIONS TABLE SCHEMA MISMATCH**

**Problem:** Database schema has multiple columns that are never used in queries:

**Unused Columns:**
```sql
-- These columns are never populated
user_phone  -- No phone number tracking
channel     -- Only email/SMS sent
sms_status  -- Never set
email_status -- Simplified in code
```

**Code Reality:** Simple notifications with email/SMS flags, not this complex structure.

---

### 5. **MISSING FOREIGN KEY CONSTRAINTS**

**Current Issues:**
```sql
-- No cascade delete - orphaned records possible
-- No unique constraints on combinations
-- Missing indexes on common filter columns
```

---

## Recommended Fixes

### **FIX 1: Populate Department Heads Table**

```sql
-- Setup department heads for leave approval
INSERT INTO department_heads (user_id, department, supervises_from, is_active)
SELECT DISTINCT 
    u.user_id, 
    e.department,
    CURDATE(),
    1
FROM users u
JOIN employees e ON u.user_id = e.user_id
WHERE u.role = 'HR_ADMIN'
AND e.department IS NOT NULL;

-- Link leave requests to department heads
UPDATE leave_requests lr
SET department_head_id = (
    SELECT dh.dept_head_id
    FROM department_heads dh
    JOIN employees e ON dh.department = e.department
    WHERE e.employee_id = lr.employee_id
    AND dh.is_active = 1
    LIMIT 1
)
WHERE department_head_id IS NULL;
```

### **FIX 2: Implement Leave Balance Tracking**

```sql
-- Initialize leave balances for all employees
INSERT INTO leave_balances (employee_id, leave_type_id, year, total_days, used_days, remaining_days)
SELECT 
    e.employee_id,
    lt.leave_type_id,
    YEAR(CURDATE()),
    lt.days_per_year,
    0,
    lt.days_per_year
FROM employees e
CROSS JOIN leave_types lt
WHERE e.status = 'ACTIVE'
AND NOT EXISTS (
    SELECT 1 FROM leave_balances lb
    WHERE lb.employee_id = e.employee_id
    AND lb.leave_type_id = lt.leave_type_id
    AND lb.year = YEAR(CURDATE())
);

-- Add check constraint before approving leave
SELECT 
    lr.leave_request_id,
    lb.remaining_days,
    lr.total_days,
    CASE WHEN lb.remaining_days >= lr.total_days THEN 'VALID' ELSE 'INSUFFICIENT' END as status
FROM leave_requests lr
JOIN leave_balances lb ON lr.employee_id = lb.employee_id 
    AND lr.leave_type_id = lb.leave_type_id
    AND lb.year = YEAR(lr.start_date)
WHERE lr.status = 'PENDING';
```

### **FIX 3: Add Holiday Support**

```sql
-- Populate holidays
INSERT INTO holidays (holiday_date, holiday_name, description, is_working_day, year)
VALUES
('2026-01-01', 'New Year Day', 'Public Holiday', 0, 2026),
('2026-02-14', 'Foundation Day', 'Company Holiday', 0, 2026),
('2026-04-09', 'Araw ng Kagitingan', 'Public Holiday', 0, 2026),
('2026-06-12', 'Independence Day', 'Public Holiday', 0, 2026);

-- Update attendance status for holidays
UPDATE attendance a
SET a.status = 'ABSENT'
WHERE EXISTS (
    SELECT 1 FROM holidays h
    WHERE DATE(a.time_in) = h.holiday_date
    AND h.is_working_day = 0
)
AND a.is_approved = 0;
```

### **FIX 4: Add Missing Constraints**

```sql
-- Add foreign key constraints
ALTER TABLE leave_requests
ADD CONSTRAINT fk_leave_requests_department_heads
FOREIGN KEY (department_head_id) 
REFERENCES department_heads(dept_head_id)
ON DELETE SET NULL
ON UPDATE CASCADE;

-- Add unique constraint to prevent duplicate leave assignments
ALTER TABLE leave_balances
ADD CONSTRAINT unique_employee_leave_year
UNIQUE KEY (employee_id, leave_type_id, year);

-- Add indexes for common queries
ALTER TABLE attendance
ADD INDEX idx_approval_pending (is_approved, status);

ALTER TABLE leave_requests
ADD INDEX idx_approval_status (status, employee_id);

ALTER TABLE holidays
ADD INDEX idx_holiday_date (holiday_date, year);
```

---

## Incomplete Workflows to Address

### **Current Leave Request Flow:**
```
Employee submits → HR Admin approves → DONE
```

**Intended Flow (Not Working):**
```
Employee submits → Department Head approves → HR Admin approves → Leave balance updated → DONE
```

**What's Missing:**
1. ✗ Department head assignment
2. ✗ Two-tier approval workflow
3. ✗ Leave balance deduction on approval
4. ✗ Notification to department head

---

## Tables to Clean Up

### **Option A: Keep & Implement** (Recommended)
- ✅ Properly populate `department_heads` 
- ✅ Implement `leave_balances` updates
- ✅ Populate `holidays` table
- ✅ Fix `notifications` schema or simplify

### **Option B: Archive & Remove** (Not Recommended)
- ❌ Delete `department_heads` (breaks leave approval)
- ❌ Delete `leave_balances` (no leave limit enforcement)
- ❌ Delete `holidays` (no holiday management)

**Recommendation: Go with Option A** - These tables support important HR features.

---

## SQL Cleanup & Optimization Script

Run this to fix and optimize the database:

```sql
-- 1. Clean orphaned attendance tokens
DELETE FROM attendance_tokens 
WHERE expires_at < NOW() AND used = 0;

-- 2. Initialize department heads
INSERT IGNORE INTO department_heads (user_id, department, supervises_from, is_active)
SELECT DISTINCT u.user_id, e.department, CURDATE(), 1
FROM users u
JOIN employees e ON u.user_id = e.user_id
WHERE u.role = 'HR_ADMIN' AND e.department IS NOT NULL;

-- 3. Initialize leave balances for current year
INSERT IGNORE INTO leave_balances (employee_id, leave_type_id, year, total_days, used_days, remaining_days)
SELECT e.employee_id, lt.leave_type_id, YEAR(CURDATE()), lt.days_per_year, 0, lt.days_per_year
FROM employees e CROSS JOIN leave_types lt WHERE e.status = 'ACTIVE';

-- 4. Add missing foreign key constraints
ALTER TABLE leave_requests
ADD CONSTRAINT fk_leave_department_heads
FOREIGN KEY (department_head_id) REFERENCES department_heads(dept_head_id)
ON DELETE SET NULL ON UPDATE CASCADE;

-- 5. Add indexes for performance
ALTER TABLE attendance ADD INDEX idx_pending_approval (is_approved, status);
ALTER TABLE leave_requests ADD INDEX idx_approval_flow (status, employee_id);
ALTER TABLE holidays ADD INDEX idx_holiday_lookup (holiday_date, year);
```

---

## Action Items

| Priority | Task | Owner | Timeline |
|----------|------|-------|----------|
| 🔴 High | Fix department head assignment | Development | Immediate |
| 🔴 High | Implement leave balance deduction | Development | Immediate |
| 🟡 Medium | Populate holidays master | HR Admin | Before Month-End |
| 🟡 Medium | Add missing constraints | DBA | Next Sprint |
| 🟢 Low | Optimize notifications schema | Development | Q2 |

---

## Summary of Unused/Underutilized Resources

**Completely Unused (3):**
- `department_heads` - Zero queries, zero population mechanism
- `holidays` - Created but never used
- `leave_balances` - Orphaned table, no maintenance

**Partially Used (1):**
- `notifications` - Schema doesn't match queries

**Estimated Recovery:** By implementing these fixes:
- ✅ Enable proper leave approval workflow
- ✅ Enforce leave balances
- ✅ Support holiday management
- ✅ Improve data integrity with constraints
- ✅ Better query performance with proper indexes
