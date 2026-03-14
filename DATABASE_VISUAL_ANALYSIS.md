# Time & Attendance Database - Visual Analysis

## Current Database Structure

```
┌─────────────────────────────────────────────────────────┐
│          TIME & ATTENDANCE DATABASE (14 TABLES)         │
└─────────────────────────────────────────────────────────┘

ACTIVE TABLES (10)
├── users ✅
├── employees ✅
├── shifts ✅
├── employee_shifts ✅
├── attendance ✅
├── attendance_tokens ✅
├── leave_requests ✅
├── leave_types ✅
├── audit_logs ✅
└── notifications ⚠️ (Partial)

UNUSED TABLES (3)
├── department_heads ❌ (Created but never used)
├── holidays ❌ (Created but never used)
└── leave_balances ❌ (Created but never used)

⚠️ PROBLEMATIC SCHEMA (1)
└── notifications (Schema doesn't match queries)
```

---

## Table Relationships - CURRENT STATE

```
                    users (user_id)
                       ↓    ↑
                       │    │
                   ┌───┴────┴───┐
                   │             │
              employees       audit_logs
             (emp_id)         (user/emp_id)
                   ↓
            ┌──────┴──────┐
            │             │
      employee_shifts    attendance
            ↓                ↓
            └─→ shifts ←─────┘
                    ↓
            attendance_tokens

ORPHANED TABLES (Not connected):
├── department_heads ─→ (FK to users, but never populated)
├── leave_requests ─→ (FK to employees, but dept_head_id = NULL)
├── leave_types ─→ (Used by leave_requests, but balance tracking missing)
├── leave_balances ─→ (Never initialized or updated)
└── holidays ─→ (Completely unused)
```

---

## Leave Approval Workflow - CURRENT vs FIXED

### ❌ CURRENT BROKEN WORKFLOW
```
┌──────────────┐
│   Employee   │
│   Submits    │
│    Leave     │
└──────┬───────┘
       │
       ↓
┌──────────────────────┐
│  HR Admin Reviews    │
│  (NO BALANCE CHECK)  │  ← PROBLEM 1: No validation
│  (IMMEDIATE APPROVAL)│
└──────┬───────────────┘
       │
       ↓
   ✓ APPROVED
   (No balance deducted)  ← PROBLEM 2: Balance never updated
```

**Issues:**
- ❌ Department head approval skipped
- ❌ No balance enforcement
- ❌ leave_balances table never touched
- ❌ No leave limit validation

---

### ✅ FIXED WORKFLOW
```
┌──────────────┐
│   Employee   │
│   Submits    │
│    Leave     │
└──────┬───────┘
       │
       ↓
┌──────────────────────────────┐
│ CHECK: Leave Balance Valid?  │
│ (remaining_days ≥ requested) │ ← FIX 1: Validate balance
└──────┬───────────────────────┘
       │
   YES │  NO
   ↓   │   ↓
   ↓   │  ✗ REJECTED
   ↓   │  (Show error: need X more days)
   ↓   │
   ↓   └──────────────────┐
   ↓                      │
┌──────────────────────────────────┐
│ Department Head Reviews          │  ← FIX 2: Two-tier approval
│ (Uses department_heads table)    │
│ Status → APPROVED_BY_HEAD        │
└──────┬───────────────────────────┘
       │
   APPROVE or REJECT
       │
       ↓
   ┌───────────────────┐
   │ IF APPROVED ONLY: │
   │   ↓               │
┌──────────────────────────────┐
│ HR Admin Final Review        │
│ Status → APPROVED_BY_HR      │
└──────┬───────────────────────┘
       │
   APPROVE or REJECT
       │
       ├─ APPROVED ────┐
       │                ↓
       │         ┌──────────────────┐
       │         │ DEDUCT BALANCE   │ ← FIX 3: Update leave_balances
       │         │ used_days +=days │
       │         │ remaining_days-= │
       │         └─────────┬────────┘
       │                   ↓
       │             ✓ COMPLETE
       │
       └─ REJECTED ──┐
                     ↓
               ✗ REJECTED
               (No balance change)
```

**Improvements:**
- ✅ Department head approval required
- ✅ Balance validation before submission
- ✅ Two-tier approval workflow
- ✅ Automatic balance deduction
- ✅ Audit trail maintained

---

## Table Usage Heat Map

```
┌─────────────────┬──────────────┬─────────────────────┐
│ TABLE           │ STATUS       │ USAGE PERCENTAGE    │
├─────────────────┼──────────────┼─────────────────────┤
│ users           │ ✅ Active    │ █████████████ 90%   │
│ employees       │ ✅ Active    │ █████████████ 85%   │
│ attendance      │ ✅ Active    │ ████████████ 80%    │
│ shifts          │ ✅ Active    │ ████████ 50%        │
│ leave_requests  │ ✅ Active    │ ███████ 40%         │
│ audit_logs      │ ✅ Active    │ ██████ 35%          │
│ employee_shifts │ ✅ Active    │ ████ 25%            │
│ leave_types     │ ✅ Active    │ ███ 20%             │
│ attendance_tokens│ ✅ Active    │ ███ 15%             │
│ notifications   │ ⚠️ Partial   │ ██ 10%              │
│ leave_balances  │ ❌ Unused    │ 0%                  │
│ holidays        │ ❌ Unused    │ 0%                  │
│ department_heads│ ❌ Unused    │ 0%                  │
└─────────────────┴──────────────┴─────────────────────┘
```

---

## Data Flow - Employee Leave Request

### CURRENT (Broken)
```
Employee (DB)
     ↓
     └─→ Submit Leave Request
           ↓
           └─→ leave_requests table (✓ Stored)
                 ↓
                 └─→ FK: leave_type_id (✓ Works)
                 └─→ FK: employee_id (✓ Works)
                 └─→ FK: department_head_id (❌ NULL - table unused)
                 └─→ FK: hr_admin_id (✓ Works)
                 ↓
                 └─→ HR Admin approves
                      ↓
                      └─→ leave_balances checked? (❌ NO)
                      └─→ leave_balances updated? (❌ NO)
                      └─→ Status = APPROVED_BY_HR

RESULT: Leave approved but balance never tracked
```

### FIXED
```
Employee (DB)
     ↓
     └─→ Submit Leave Request
           ↓
           ├─→ CHECK: leave_balances.remaining_days >= total_days (✓ NEW)
           │   ├─ YES → Continue
           │   └─ NO → REJECT with error message
           │
           └─→ leave_requests table (✓ Stored)
                 ↓
                 ├─→ FK: leave_type_id (✓ Works)
                 ├─→ FK: employee_id (✓ Works)
                 ├─→ FK: department_head_id (✓ NOW POPULATED)
                 └─→ FK: hr_admin_id (✓ Works)
                 ↓
                 └─→ Department Head reviews (✓ NEW)
                      └─→ Status = APPROVED_BY_HEAD
                      └─→ HR Admin approves
                           ├─→ leave_balances.used_days += total_days (✓ NEW)
                           ├─→ leave_balances.remaining_days -= total_days (✓ NEW)
                           └─→ Status = APPROVED_BY_HR

RESULT: Leave approved AND balance properly maintained
```

---

## Database Schema - What Changed

### BEFORE
```sql
├── department_heads (14 fields)
│   ├── dept_head_id (PK)
│   ├── user_id (FK) ─┐
│   ├── department    │ ← UNUSED
│   └── ...           │   (never populated)
│                     │   (never queried)
│
├── leave_balances (8 fields)
│   ├── balance_id (PK)
│   ├── employee_id (FK)
│   ├── leave_type_id (FK)
│   ├── remaining_days ← NEVER UPDATED
│   └── ...
│
└── holidays (6 fields)
    ├── holiday_id (PK)
    ├── holiday_date
    ├── holiday_name ← NEVER QUERIED
    └── ...
```

### AFTER
```sql
├── department_heads (14 fields)
│   ├── dept_head_id (PK)
│   ├── user_id (FK) ─┐
│   ├── department    │ ✓ POPULATED
│   └── ...           │ ✓ QUERIED
│                     │ ✓ USED IN APPROVALS
│
├── leave_balances (8 fields)
│   ├── balance_id (PK)
│   ├── employee_id (FK)
│   ├── leave_type_id (FK)
│   ├── remaining_days ✓ AUTOMATICALLY UPDATED
│   ├── UNIQUE CONSTRAINT (emp_id, type_id, year)
│   └── ...
│
└── holidays (6 fields)
    ├── holiday_id (PK)
    ├── holiday_date
    ├── holiday_name ✓ POPULATED WITH HOLIDAYS
    └── ✓ INDEXED FOR FAST QUERIES
    └── ✓ CHECKED DURING ATTENDANCE
```

---

## Process Flow - Leave Submission to Approval

```
PHASE 1: SUBMISSION
┌─────────────────────────────────────────┐
│ Employee Initiates Leave Request        │
│  - Select: Leave Type, Start, End Date │
│  - Calculate: Total Days Required      │
└────────────────┬────────────────────────┘
                 │
                 ↓
         CHECK BALANCE (NEW)
         ┌──────────────────────────────┐
         │ Query: SELECT remaining_days │
         │ FROM leave_balances WHERE    │
         │   emp_id = ?                 │
         │   type_id = ?                │
         │   year = YEAR(NOW())         │
         │                              │
         │ IF remaining >= requested:   │
         │   → Continue                 │
         │ ELSE:                        │
         │   → Show error & EXIT        │
         └────────────┬─────────────────┘
                      │
                      ↓
PHASE 2: DEPARTMENT HEAD APPROVAL (NEW)
┌──────────────────────────────────────┐
│ Auto-Route to Department Head        │
│ (Via department_heads table)         │
│                                      │
│ Department Head sees:                │
│  - Employee name, ID                │
│  - Leave type & dates               │
│  - Current balance info (NEW)       │
│  - Reason                           │
│                                      │
│ Department Head Actions:             │
│  - ✓ Approve → APPROVED_BY_HEAD     │
│  - ✗ Reject → REJECTED              │
│  - ? Forward with remarks           │
└────────────────┬─────────────────────┘
                 │
                 ├─ REJECTED ────────────→ END (No approval)
                 │
                 └─ APPROVED_BY_HEAD
                      ↓
PHASE 3: HR ADMIN APPROVAL
┌──────────────────────────────────────┐
│ HR Admin Final Review                │
│                                      │
│ HR Admin sees:                       │
│  - All previous info                │
│  - Department Head approval ✓       │
│  - Department Head remarks (NEW)    │
│  - Final balance check (NEW)        │
│                                      │
│ HR Admin Actions:                    │
│  - ✓ Approve → APPROVED_BY_HR       │
│  - ✗ Reject → REJECTED              │
└────────────────┬─────────────────────┘
                 │
                 ├─ REJECTED ────────────→ END (No approval)
                 │
                 └─ APPROVED_BY_HR
                      ↓
PHASE 4: BALANCE DEDUCTION (NEW)
┌──────────────────────────────────────┐
│ Automatic Balance Update             │
│ UPDATE leave_balances SET:           │
│   used_days += total_days            │
│   remaining_days -= total_days       │
│ WHERE                                │
│   emp_id = ?                        │
│   type_id = ?                       │
│   year = YEAR(NOW())                │
│                                      │
│ ✓ Leave balance now shows true      │
│   remaining days                     │
└────────────────┬─────────────────────┘
                 │
                 ↓
PHASE 5: COMPLETION
┌──────────────────────────────────────┐
│ Leave Request: APPROVED_BY_HR        │
│ ✓ Stored in database                │
│ ✓ Balance deducted                  │
│ ✓ Logged in audit_logs              │
│ ✓ Notifications sent (optional)     │
└──────────────────────────────────────┘
```

---

## Data Consistency - Before vs After

### BEFORE (Inconsistent State)
```
Employee Table:
┌────┬────────┬───────────┐
│ ID │ Name   │ Status    │
├────┼────────┼───────────┤
│ 1  │ John   │ ACTIVE    │
│ 2  │ Maria  │ ACTIVE    │
└────┴────────┴───────────┘

Leave Requests:
┌──────────┬────────┬──────────────────┐
│ Request  │ Emp ID │ Days Requested   │
├──────────┼────────┼──────────────────┤
│ 101      │ 1      │ 15 days          │ ← No check
│ 102      │ 2      │ 20 days          │ ← No check
│ 103      │ 1      │ 25 days (TOTAL)  │ ← Could exceed 30!
└──────────┴────────┴──────────────────┘

Leave Balances:
┌────────┬────────┬──────────┐
│ Emp ID │ Type   │ Remaining│
├────────┼────────┼──────────┤
│ 1      │ Sick   │ 30 days  │ ← NEVER UPDATED!
│ 2      │ Sick   │ 30 days  │ ← NEVER UPDATED!
└────────┴────────┴──────────┘

PROBLEM: Employees could use more leave than allowed!
```

### AFTER (Consistent State)
```
Employee Table:
┌────┬────────┬───────────┐
│ ID │ Name   │ Status    │
├────┼────────┼───────────┤
│ 1  │ John   │ ACTIVE    │
│ 2  │ Maria  │ ACTIVE    │
└────┴────────┴───────────┘

Leave Requests:
┌──────────┬────────┬──────────────────┬──────────┐
│ Request  │ Emp ID │ Days Requested   │ Status   │
├──────────┼────────┼──────────────────┼──────────┤
│ 101      │ 1      │ 15 days          │ APPROVED │
│ 102      │ 2      │ 20 days          │ APPROVED │
│ 103      │ 1      │ 25 days (TOTAL)  │ REJECTED │ ← BLOCKED!
│ 104      │ 1      │ 15 days          │ APPROVED │
└──────────┴────────┴──────────────────┴──────────┘

Leave Balances:
┌────────┬────────┬───────────┬──────────────┐
│ Emp ID │ Type   │ Used Days │ Remaining    │
├────────┼────────┼───────────┼──────────────┤
│ 1      │ Sick   │ 30 days   │ 0 days       │ ✓ UPDATED!
│ 2      │ Sick   │ 20 days   │ 10 days      │ ✓ UPDATED!
└────────┴────────┴───────────┴──────────────┘

BENEFIT: Employees can only use allowed leave!
```

---

## Summary: What Gets Fixed

| Component | Before | After | Impact |
|-----------|--------|-------|--------|
| **Validation** | No checks | Balance check | Prevents overspending |
| **Approval** | Single-tier | Two-tier | Better control |
| **Balance Tracking** | Manual/None | Automatic | Real-time accuracy |
| **Holidays** | Ignored | Tracked | Correct attendance |
| **Data Integrity** | Weak | Strong | No orphaned data |
| **Performance** | No indexes | Optimized | Faster queries |

