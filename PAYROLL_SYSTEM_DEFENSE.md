# PAYROLL MANAGEMENT SYSTEM - COMPREHENSIVE DEFENSE DOCUMENT

**Project**: BCP Capstone HR Management System - Payroll Module  
**Date**: April 5, 2026  
**Author**: Payroll System Development Team

---

## TABLE OF CONTENTS
1. System Overview
2. Architecture & Data Flow
3. Database Schema
4. Modules & Data Sources
5. Salary Calculation Logic (By Position Category)
6. Deduction & Allowance Processing
7. Payroll Period Management
8. Data Integrity & Validation
9. Q&A for Defense

---

## 1. SYSTEM OVERVIEW

### Purpose
The Payroll Management System automates salary calculation, deduction processing, and payslip generation for a school organization with multiple position categories (Teachers, Administrative, Support, Professional).

### Key Features
- **Position-based Payroll**: Different calculation rules for Teachers (unit-based), Admin, Support, Professional (daily-rate)
- **Attendance Integration**: Automatic deduction for unexcused absences and late minutes
- **Multi-phase Processing**: Period creation → Payroll calculation → Payslip generation → Finalization → Clearance approval
- **Manual Adjustments**: Support for manual allowances and deductions via pr_employee_adjustments table
- **Real-time Reporting**: Dashboard showing payroll status, deductions trend, processing metrics

---

## 2. SYSTEM ARCHITECTURE & DATA FLOW

### High-Level Transaction Flow

```
PAYROLL CYCLE:
├── CREATE PERIOD
│   ├── Admin creates payroll period (start/end dates)
│   └── Period status: 'open'
│
├── PROCESS PAYROLL (Preview)
│   ├── Admin selects period and employee
│   ├── System calculates salary for every active employee
│   ├── Calculation retrieves:
│   │   ├── Base salary (from rao_offer_salary or employees.base_salary)
│   │   ├── Teaching units (for teachers from pr_teacher_loads)
│   │   ├── Attendance data (from time_attendance tables)
│   │   ├── Manual adjustments (from pr_employee_adjustments)
│   │   └── Contributions status (from employee_contributions)
│   ├── System calculates gross pay and applies deductions
│   └── Payroll shown in preview (not yet saved)
│
├── FINALIZE PAYROLL
│   ├── Admin clicks "Finalize Payroll" button
│   ├── System creates pr_runs record with status='draft'
│   ├── For each employee, system:
│   │   ├── Calculates payroll again
│   │   ├── Inserts pr_payslip record
│   │   ├── Inserts payslip item breakdowns (pr_payslip_items)
│   │   └── Updates pr_runs status='finalized'
│   └── Period status changed to 'closed'
│
├── PAYSLIP GENERATION
│   ├── Payslips displayed per employee
│   ├── Shows gross pay, deductions itemized, net pay
│   └── Can print or export as PDF
│
├── PAYROLL CLEARANCE
│   ├── Creates clearance record for each active employee
│   ├── HR verifies amounts are correct
│   ├── Clearance status changes from 'pending' to 'approved'
│   └── Finance can process payment after clearance
│
└── DASHBOARD REPORTING
    ├── Shows total employees, last period, total payroll
    ├── Shows total gross pay, deductions, net payroll
    ├── Shows processing status (# processed / # pending)
    ├── Shows pending clearances count
    └── Monthly trend charts (gross vs net, deductions)
```

### System Modules & Data Sources

| Module | Purpose | Data Source | Output |
|--------|---------|------------|--------|
| **payrollController.php** | Orchestrates calculations | Calls payrollModel | preview data, payslip objects |
| **payrollModel.php** | Core calculation engine | employees, rao_offer_salary, pr_teacher_loads, time_attendance, pr_employee_adjustments | payroll array (gross, net, deductions breakdown) |
| **payrollPeriodModel.php** | Period management | pr_periods table | period list, status |
| **payslipModel.php** | Payslip storage & retrieval | pr_payslips, pr_payslip_items | payslip details with breakdown |
| **dashboardModel.php** | Analytics & reporting | pr_payslips, pr_runs, pr_periods, payroll_clearances | monthly totals, processing status, clearance counts |
| **allowanceDeductionModel.php** | Manual adjustment mgmt | pr_employee_adjustments | adjustment list, add/update/delete |
| **payrollProcess.php** (View) | UI for payroll calculations | payrollController | preview table, employee search, finalize button |
| **payroll.php** (Dashboard) | Dashboard view | dashboardController | KPI cards, charts |

---

## 3. DATABASE SCHEMA

### Core Payroll Tables

#### `pr_periods`
```sql
CREATE TABLE pr_periods (
  period_id INT PRIMARY KEY AUTO_INCREMENT,
  period_name VARCHAR(50),          -- "Jan 1-15, 2026"
  start_date DATE,                   -- Period start
  end_date DATE,                     -- Period end
  pay_date DATE,                     -- Payment date
  status ENUM('open','processing','closed'),
  created_at TIMESTAMP DEFAULT NOW()
);
```
**Purpose**: Defines payroll periods; marks when calculations are allowed/closed

#### `pr_runs`
```sql
CREATE TABLE pr_runs (
  run_id INT PRIMARY KEY AUTO_INCREMENT,
  payroll_period_id INT,             -- FK to pr_periods
  processed_at TIMESTAMP,
  status ENUM('draft','finalized'),  -- draft=calculating, finalized=complete
  finalized_by INT,                  -- User ID who finalized
  FOREIGN KEY (payroll_period_id) REFERENCES pr_periods(period_id)
);
```
**Purpose**: Tracks payroll run instances; one run per period

#### `pr_payslips`
```sql
CREATE TABLE pr_payslips (
  payslip_id INT PRIMARY KEY AUTO_INCREMENT,
  payroll_run_id INT,                -- FK to pr_runs
  employee_id VARCHAR(20),           -- FK to employees
  gross_pay DECIMAL(10,2),           -- Total earnings
  total_deductions DECIMAL(10,2),    -- Sum of all deductions
  net_pay DECIMAL(10,2),             -- gross_pay - total_deductions
  generated_at TIMESTAMP,
  FOREIGN KEY (payroll_run_id) REFERENCES pr_runs(run_id),
  FOREIGN KEY (employee_id) REFERENCES employees(employee_id)
);
```
**Purpose**: Main payslip record; stores calculated totals

#### `pr_payslip_items`
```sql
CREATE TABLE pr_payslip_items (
  payslip_item_id INT PRIMARY KEY AUTO_INCREMENT,
  payslip_id INT,                    -- FK to pr_payslips
  item_type ENUM('earning','deduction'),
  description VARCHAR(100),         -- "Basic Salary", "SSS", "Absence", etc.
  amount DECIMAL(10,2),
  FOREIGN KEY (payslip_id) REFERENCES pr_payslips(payslip_id)
);
```
**Purpose**: Itemized breakdown of earnings and deductions

#### `pr_employee_adjustments`
```sql
CREATE TABLE pr_employee_adjustments (
  adjustment_id INT PRIMARY KEY AUTO_INCREMENT,
  employee_id VARCHAR(20),           -- FK to employees
  payroll_period_id INT,             -- FK to pr_periods
  type ENUM('allowance','deduction'),
  description VARCHAR(100),         -- "Bonus", "Loan", "Fine", etc.
  amount DECIMAL(10,2),
  FOREIGN KEY (employee_id) REFERENCES employees(employee_id),
  FOREIGN KEY (payroll_period_id) REFERENCES pr_periods(period_id)
);
```
**Purpose**: Manual adjustments added via allowance.php

#### `payroll_clearances`
```sql
CREATE TABLE payroll_clearances (
  clearance_id INT PRIMARY KEY AUTO_INCREMENT,
  employee_id VARCHAR(20),
  payslip_id INT,
  period_id INT,
  status ENUM('pending','approved','rejected'),
  created_at TIMESTAMP,
  approved_at TIMESTAMP NULL,
  FOREIGN KEY (employee_id) REFERENCES employees(employee_id),
  FOREIGN KEY (payslip_id) REFERENCES pr_payslips(payslip_id),
  FOREIGN KEY (period_id) REFERENCES pr_periods(period_id)
);
```
**Purpose**: HR approval workflow for payslips

#### `pr_position_deduction_rates`
```sql
CREATE TABLE pr_position_deduction_rates (
  id INT PRIMARY KEY AUTO_INCREMENT,
  position_type ENUM('teacher','admin','support','professional'),
  absence_deduction_amount DECIMAL(10,2),
  late_per_minute_rate DECIMAL(5,2),
  late_per_hour_rate DECIMAL(10,2),
  is_active TINYINT DEFAULT 1
);
```
**Purpose**: Configurable deduction amounts by position

### Supporting Tables (Joined for Calculation)

| Table | Purpose | Usage in Payroll |
|-------|---------|-----------------|
| `employees` | Employee master data | Position, teacher_qualification, base_salary |
| `rao_offer_salary` | Accepted salary offers | Primary source for base_salary |
| `rao_hired_applicants` | Link applicant to employee | Joins rao_offer_salary to employee_id |
| `pr_teacher_loads` | Teaching unit assignments | Units for teachers (unit_based pay) |
| `time_attendance` | Attendance records | Hours worked, absences, lates |
| `employee_contributions` | SSS/PhilHealth/Pag-IBIG status | Marks which deductions to apply |

---

## 4. SALARY CALCULATION LOGIC (DETAILED BY POSITION CATEGORY)

### Position Categories and Configuration

The system recognizes 4 position categories with different pay types:

```php
POSITION_CONFIG = [
    'teacher' => [
        'pay_type' => 'unit_based',      // Pay per teaching unit
        'has_overtime' => false,
        'absence_deduction' => 1536      // ₱1,536 per unexcused day
    ],
    'admin' => [
        'pay_type' => 'daily_rate',      // Pay per day worked
        'has_overtime' => true,          // Earns 1.25x overtime
        'absence_deduction' => 1020      // ₱1,020 per unexcused day
    ],
    'support' => [
        'pay_type' => 'daily_rate',
        'has_overtime' => false,
        'absence_deduction' => 800       // ₱800 per unexcused day
    ],
    'professional' => [
        'pay_type' => 'daily_rate',
        'has_overtime' => true,          // Earns 1.25x overtime
        'absence_deduction' => 1020      // ₱1,020 per unexcused day
    ]
];
```

Positions mapping:
- **Teacher**: Teacher, Assistant Teacher, Instructor, Professor, Associate Professor
- **Admin**: Principal, VP, HR Manager, Accountant, Finance Officer, Registrar, etc.
- **Support**: Janitor, Maintenance, Security, Driver, Canteen, Gardener, Groundskeeper
- **Professional**: Software Engineer, Junior Dev, IT Support, Librarian, Counselor, Nurse, etc.

---

### CALCULATION #1: TEACHER SALARY (Unit-Based)

**Positions**: Teacher, Assistant Teacher, Instructor

**Source Data**:
- Base Salary: Monthly salary from `rao_offer_salary` table (must be accepted offer)
- Teaching Units: From `pr_teacher_loads` table (assigned teaching load)
- Teacher Qualification: From `employees.teacher_qualification` (affects salary rate)
- Attendance: From time_attendance tables

**Formula**:

```
GROSS PAY CALCULATION:
─────────────────────

1. BASIC SALARY (Unit-Based)
   Semi-monthly salary = Base Salary / 2
   Daily rate = Semi-monthly / 15 days
   
   Teaching units define compensation:
   • If units >= qualifying load → Use base salary
   • If units < qualifying load → Pro-rata: (units / expected_units) × base
   
   Basic Salary = (Daily Rate × Days Worked) + Overtime
   
   Example:
   - Base Salary = ₱30,000/month
   - Semi-monthly = ₱15,000
   - Daily rate = ₱1,000/day
   - Days worked = 15 (full period)
   - Basic Salary = ₱15,000
   
   NOTE: Teachers do NOT get overtime pay (has_overtime = false)

2. ALLOWANCES & ADJUSTMENTS
   + Manual allowances from pr_employee_adjustments (type='allowance')
   
   Example: Bonus = ₱2,000
   
3. GROSS PAY = Basic Salary + Allowances
   Example: ₱15,000 + ₱2,000 = ₱17,000

DEDUCTIONS CALCULATION:
──────────────────────

Stop condition: If GROSS PAY <= 0, no deductions are applied
               (Cannot deduct if employee has no pay)

1. STATUTORY CONTRIBUTIONS (If submitted)
   - SSS: ₱200 (if employee_contributions.status = 'submitted')
   - PhilHealth: ₱200 (if submitted)
   - Pag-IBIG: ₱200 (if submitted)
   
   Example: All three = ₱600 total

2. ATTENDANCE DEDUCTIONS
   Format: days_absent × ₱1,536/day
   
   Note: ONLY unexcused absences are deducted
         Approved leaves are NOT deducted
   
   Example: 1 unexcused day = 1 × ₱1,536 = ₱1,536

3. LATE MINUTES DEDUCTION
   Format: minutes_late × ₱2.00/minute
   
   Example: 30 minutes late = 30 × ₱2.00 = ₱60

4. MANUAL DEDUCTIONS
   + From pr_employee_adjustments (type='deduction')
   
   Example: Loan deduction = ₱1,000

5. TOTAL DEDUCTIONS = Contributions + Absences + Lates + Manual
   Example: ₱600 + ₱1,536 + ₱60 + ₱1,000 = ₱3,196

NET PAY CALCULATION:
───────────────────
NET PAY = GROSS PAY - TOTAL DEDUCTIONS
NET PAY = ₱17,000 - ₱3,196 = ₱13,804

```

**Example Payslip (Teacher)**:
```
EARNINGS:
  Basic Salary - Teacher (15 days @ ₱1,000/day)    ₱15,000.00
  Bonus (Allowance)                                  ₱2,000.00
─────────────────────────────────────────────
  TOTAL EARNINGS (GROSS PAY)                        ₱17,000.00

DEDUCTIONS:
  SSS                                                  ₱200.00
  PhilHealth                                           ₱200.00
  Pag-IBIG                                             ₱200.00
  Unexcused Absence (1 day × ₱1,536)                ₱1,536.00
  Late (30 minutes × ₱2/min)                           ₱60.00
  Loan Deduction                                     ₱1,000.00
─────────────────────────────────────────────
  TOTAL DEDUCTIONS                                  ₱3,196.00

NET PAY = ₱17,000.00 - ₱3,196.00 = ₱13,804.00
```

---

### CALCULATION #2: ADMINISTRATIVE STAFF (Daily Rate with Overtime)

**Positions**: Principal, VP, HR Manager, Finance Officer, Registrar, Accountant, etc.

**Source Data**:
- Base Salary: From `rao_offer_salary` (monthly)
- Hourly employees: Overtime applied if hours > 160/month
- Overtime multiplier: 1.25x

**Formula**:

```
GROSS PAY CALCULATION:
─────────────────────

1. BASIC SALARY (Daily Rate)
   Semi-monthly salary = Base Salary / 2
   Daily rate = Semi-monthly / 15 days
   Hourly rate = Semi-monthly / 160 hours
   
   Basic Salary = Daily Rate × Days Worked
   
   Example:
   - Base Salary = ₱36,000/month
   - Semi-monthly = ₱18,000
   - Hourly rate = ₱18,000 / 160 = ₱112.50/hr
   - Days worked = 15
   - Basic Salary = (18,000 / 15) × 15 = ₱18,000

2. OVERTIME PAY (If overtime hours > 0)
   Overtime rate = Hourly rate × 1.25 multiplier
   Overtime Pay = Overtime hours × (Hourly rate × 1.25)
   
   Example:
   - Overtime hours = 8
   - Overtime rate = ₱112.50 × 1.25 = ₱140.625/hr
   - Overtime Pay = 8 × ₱140.625 = ₱1,125

3. ALLOWANCES
   + Manual allowances from pr_employee_adjustments
   
   Example: ₱1,000

4. GROSS PAY = Basic + Overtime + Allowances
   Example: ₱18,000 + ₱1,125 + ₱1,000 = ₱20,125

DEDUCTIONS:
──────────

Stop condition: If GROSS PAY <= 0, no deductions applied

1. STATUTORY CONTRIBUTIONS (If submitted)
   - SSS: ₱200
   - PhilHealth: ₱200
   - Pag-IBIG: ₱200
   
   Example: ₱600

2. ATTENDANCE DEDUCTIONS
   Absence deduction = Unexcused days × ₱1,020/day
   
   Example: 2 unexcused days = 2 × ₱1,020 = ₱2,040

3. LATE DEDUCTION
   Late deduction = Minutes late × ₱2/minute
   
   Example: 60 minutes = 60 × ₱2 = ₱120

4. MANUAL DEDUCTIONS
   + From pr_employee_adjustments
   
   Example: ₱500

5. TOTAL DEDUCTIONS = ₱600 + ₱2,040 + ₱120 + ₱500 = ₱3,260

NET PAY = ₱20,125 - ₱3,260 = ₱16,865
```

**Example Payslip (Admin)**:
```
EARNINGS:
  Basic Salary - Admin (18,000 / 15 × 15 days)     ₱18,000.00
  Overtime (8 hrs × ₱112.50 × 1.25)                ₱1,125.00
  Allowance                                        ₱1,000.00
─────────────────────────────────────────────
  TOTAL EARNINGS (GROSS PAY)                       ₱20,125.00

DEDUCTIONS:
  SSS                                                ₱200.00
  PhilHealth                                         ₱200.00
  Pag-IBIG                                           ₱200.00
  Unexcused Absences (2 days × ₱1,020)             ₱2,040.00
  Late (60 minutes × ₱2/min)                        ₱120.00
  Social Club Deduction                              ₱500.00
─────────────────────────────────────────────
  TOTAL DEDUCTIONS                                 ₱3,260.00

NET PAY = ₱20,125.00 - ₱3,260.00 = ₱16,865.00
```

---

### CALCULATION #3: SUPPORT STAFF (Daily Rate, No Overtime)

**Positions**: Janitor, Maintenance, Security, Driver, Canteen Staff, Gardener

**Source Data**:
- Base Salary: From `rao_offer_salary`
- NO overtime multiplier (has_overtime = false)

**Formula**:

```
GROSS PAY = (Base Salary / 2 / 15) × Days Worked + Allowances
          = Daily Rate × Days Worked + Allowances

Absence Deduction = Unexcused days × ₱800/day

Example:
- Base Salary = ₱20,000/month
- Daily rate = (₱20,000 / 2 / 15) = ₱666.67/day
- Days worked = 12
- Basic Salary = ₱666.67 × 12 = ₱8,000

DEDUCTIONS:
- SSS: ₱200
- Absences (1 day): 1 × ₱800 = ₱800
- Total: ₱1,000

NET PAY = ₱8,000 - ₱1,000 = ₱7,000
```

---

### CALCULATION #4: PROFESSIONAL STAFF (Daily Rate with Overtime)

**Positions**: Librarian, Counselor, School Nurse, IT Support, HR Specialist, etc.

**Formula**: Same as Admin (daily rate with 1.25x overtime)

```
GROSS PAY = (Base Salary / 2 / 15) × Days Worked + (Overtime hrs × Hourly rate × 1.25) + Allowances

Absence Deduction = Unexcused days × ₱1,020/day (same as Admin)
```

---

## 5. DEDUCTION & ALLOWANCE PROCESSING

### Deduction Sources and Rules

| Deduction Type | Source Table | Rule | Amount | Condition |
|---|---|---|---|---|
| **SSS** | `employee_contributions` | Fixed amount | ₱200 | If status='submitted' |
| **PhilHealth** | `employee_contributions` | Fixed amount | ₱200 | If status='submitted' |
| **Pag-IBIG** | `employee_contributions` | Fixed amount | ₱200 | If status='submitted' |
| **Absence** | `time_attendance` | Per day (unexcused only) | ₱1,536 (Teacher), ₱1,020 (Admin/Prof), ₱800 (Support) | Only unexcused; not applied if GROSS <= 0 |
| **Late Minutes** | `time_attendance` | Per minute | ₱2.00/min | Always applied if GROSS > 0 |
| **Manual Deduction** | `pr_employee_adjustments` (type='deduction') | Per period | Variable | If manually added by HR |

### Allowance Processing

| Allowance Type | Source Table | Condition |
|---|---|---|
| **Manual Allowance** | `pr_employee_adjustments` (type='allowance') | Added via allowance.php by HR |

### Important Notes on Deductions:

1. **Zero Gross Pay Protection**: If `GROSS PAY <= 0`, NO deductions are applied
   - Prevents sending employee negative net pay
   - Attendance deductions are not processed for employees with 0 days

2. **Unexcused Absences Only**: 
   - System fetches `unexcused_absent_days` from time_attendance
   - Uses `unexcused_absent_days × absence_deduction_rate`
   - Approved leaves (in time_attendance) are NOT deducted

3. **Late Deduction Always Applied** (if GROSS > 0):
   - Fetches `total_late_minutes` from attendance
   - Multiplies by ₱2.00/minute

4. **Contribution Deductions**:
   - Only applied if employee_contributions.status = 'submitted'
   - Fixed amount: ₱200 per contribution type

---

## 6. PAYROLL PERIOD MANAGEMENT

### Period States

```
OPEN
  ↓ (Admin can run preview, modify employees)
  ↓
PROCESSING
  ↓ (Payroll is being calculated)
  ↓
CLOSED
  (No more calculations allowed for this period)
```

### Period Workflow

1. **Create Period** (payroll/views/periodManager.php)
   - Admin enters: period name, start date, end date, pay date
   - Inserts into `pr_periods` with status='open'

2. **Calculate/Preview** (payroll/views/payrollProcess.php)
   - Admin selects period (must be status='open')
   - System calculates all active employees for that period
   - Calculations shown in preview (not saved to DB yet)

3. **Finalize** (payroll/views/payrollProcess.php)
   - Admin clicks "Finalize Payroll" button
   - System:
     - Creates `pr_runs` record (status='draft')
     - Calculates payroll again for each employee
     - Inserts `pr_payslips` with gross_pay, total_deductions, net_pay
     - Inserts `pr_payslip_items` with itemized breakdown
     - Updates `pr_runs` status='finalized'
     - Updates `pr_periods` status='closed'
   - Now payslips are locked and searchable

4. **View Payslips** (payroll/views/payslip.php)
   - Shows all finalized payslips
   - Breakdowns come from `pr_payslip_items`

5. **Payroll Clearance** (payroll/views/payrollClearance.php)
   - HR reviews payslips
   - Creates/updates `payroll_clearances` record
   - HR can approve (status='approved') or reject (status='rejected')

---

## 7. DATA INTEGRITY & VALIDATION

### Validation Rules

| Check | Location | Impact |
|-------|----------|--------|
| Employee exists in `employees` table | payrollModel.calculateEmployeePayroll() | Returns empty array if not found |
| Period exists and has dates | payrollModel.calculateEmployeePayroll() | Returns empty array if no dates |
| Base salary > 0 | payrollModel.calculateEmployeePayroll() | Falls back to employees.base_salary |
| Period not closed | payrollController.isClosed() | Throws exception if already closed |
| No duplicate payslips | payrollModel.getEmployeesForPayroll() | Filters to only employees with no existing payslips for period |
| Teacher has teaching units | payrollModel.calculateEmployeePayroll() | Uses units=0 if not found (pro-rata to 0) |
| Contributions submitted | payrollModel.calculateEmployeePayroll() | Only applies deduction if status='submitted' |
| Unexcused absences only | payrollModel.getTimeAttendanceMetrics() | Fetches unexcused_absent_days specifically |

### Data Flow Validation

**Question**: How does the system ensure consistency between preview and final payslip?

**Answer**: 
- The calculation logic (`calculateEmployeePayroll()`) is called twice:
  1. First in preview (payroll/views/payrollProcess.php line 37 via previewPayroll())
  2. Again during finalize (payroll/models/payrollModel.php line 724)
- Both use identical logic, retrieving latest attendance data at time of call
- If attendance changes between preview and finalize, final payslip will differ (rare but possible)
- System does NOT prevent this; assumes finalize is the authoritative calculation

**Implication**: HR should finalize payroll immediately after preview to avoid attendance changes

---

## 8. Q&A FOR DEFENSE

### Q1: How does the system differentiate between Teachers and Administrative staff in salary calculation?

**A**: The system uses the `position` column in the `employees` table to categorize staff:
- **Teachers** (unit_based pay type):
  - Positions: Teacher, Assistant Teacher, Instructor
  - Get paid based on teaching units from `pr_teacher_loads` table
  - Pro-rata: if assigned units < expected units, salary is reduced proportionally
  - NO overtime pay (has_overtime = false)
  
- **Administrative** (daily_rate pay type):
  - Positions: Principal, VP, HR Manager, Finance Officer, etc.
  - Get paid per day worked (base_salary / 2 / 15 days)
  - Can earn overtime at 1.25x multiplier if hours > 160/month
  - Higher absence deduction: ₱1,020/day (vs Teacher: ₱1,536/day)

The category determines:
1. Pay calculation method (unit_based vs daily_rate)
2. Whether overtime is allowed (true/false)
3. Overtime multiplier (1.25x for admin/professional, null for teacher/support)
4. Absence deduction rate (₱1,536 Teacher, ₱1,020 Admin, ₱800 Support)

---

### Q2: Where does the base salary come from? Which table has the source of truth?

**A**: The priority flow is:

```
1. First Priority: rao_offer_salary table
   ├─ Query: SELECT salary FROM rao_offer_salary
   │   WHERE application_id IN (
   │     SELECT application_id FROM rao_hired_applicants 
   │     WHERE employee_id = ? AND offer_status = 'accepted'
   │   )
   └─ Reason: This is the accepted salary offer at hire time

2. Second Priority: employees.base_salary
   ├─ Query: SELECT base_salary FROM employees WHERE employee_id = ?
   │ (Only used if rao_offer_salary returns no record or ≤ 0)
   └─ Reason: Fallback for legacy employees or manual entries

3. If both = 0: No pay calculation (returns gross_pay = 0)
```

**Code Location**: payroll/models/payrollModel.php, lines 308-335

**Question**: What if an employee's salary is updated after hire?
**Answer**: The system uses the MOST RECENT accepted offer. If a new salary offer is created and accepted, that becomes the base. The system orders by `created_at DESC LIMIT 1`. However, updating `rao_offer_salary` after finalization does NOT retroactively change finalized payslips (those are locked in `pr_payslips` table).

---

### Q3: How does the system handle Teachers' variable pay (teaching units)?

**A**: 
- **Data Source**: `pr_teacher_loads` table
- **Calculation**:
  ```
  For teachers (pay_type = 'unit_based'):
    Base salary / 2 (semi-monthly) = ₱15,000
    Teaching units from pr_teacher_loads.total_units
    
    If units >= expected_units (e.g., 20 units):
      Pay full semi-monthly salary
    
    If units < expected_units (e.g., 15 units when expecting 20):
      Pro-rata = (15 / 20) × ₱15,000 = ₱11,250
  ```

- **Pro-rata Example**:
  - Teacher contracted for 20 units/semester
  - Only assigned 15 units in pr_teacher_loads
  - Semi-monthly salary: ₱15,000
  - Actual gross (without deductions): (15/20) × ₱15,000 = ₱11,250
  - This ensures fairness for partial assignments

- **Query Location**: payroll/models/payrollModel.php lines 349-365
  ```php
  $stmtTeacherLoad = $this->db->prepare("
    SELECT total_units FROM pr_teacher_loads
    WHERE employee_id = :eid AND :pstart >= DATE_FORMAT(NOW(), '%Y-01-01')
    ORDER BY created_at DESC LIMIT 1
  ");
  ```

---

### Q4: How are absences and late deductions calculated? Why differentiate "unexcused" vs "excused"?

**A**:
- **Absence Deduction Logic**:
  ```
  System fetches: unexcused_absent_days from time_attendance
  NOT: total_absent_days (which includes excused absences)
  
  Deduction = unexcused_absent_days × absence_deduction_rate
  
  Example:
  - Employee absent 5 days: 2 unexcused (sick w/o approval), 3 excused (approved leave)
  - System only deducts: 2 × ₱1,536 = ₱3,072
  - Approved leaves are NOT deducted (no penalty for legitimate absences)
  ```

- **Why Differentiate**?
  - **Policy**: Unexcused absences are unauthorized; should be penalized
  - **Fairness**: Approved leave (vacation, medical, etc.) is legitimate; employee should not be doubly penalized
  - **Compliance**: Labor law typically protects approved absences from deduction

- **Late Deduction Logic**:
  ```
  System fetches: total_late_minutes from time_attendance
  Deduction = total_late_minutes × ₱2.00/minute
  
  Example: Employee 45 minutes late
  Deduction = 45 × ₱2 = ₱90
  (No differentiation between types of late)
  ```

- **Query Location**: payroll/models/payrollModel.php lines 430-460
  ```php
  $attendance = $this->getTimeAttendanceMetrics($employeeId, $start, $end);
  $unapprovedAbsentDays = (int)($attendance['unexcused_absent_days'] ?? 0);
  $lateMinutes = (int)($attendance['total_late_minutes'] ?? 0);
  ```

---

### Q5: What happens if Gross Pay is 0 or negative? Are deductions still applied?

**A**: **NO. All deductions are skipped if Gross Pay ≤ 0.**

**Code Location**: payroll/models/payrollModel.php lines 544-554
```php
if ($grossPay <= 0) {
    return [
        'gross_pay' => 0,
        'net_pay' => 0,
        'total_deductions' => 0,
        'earnings' => $earnings,
        'deductions' => []   // Empty deductions array
    ];
}
```

**Rationale**:
1. **Employee Protection**: Prevents sending an employee a negative net paycheck
2. **Business Logic**: If employee has 0 hours or was on unpaid leave the entire period, they should not be charged deductions
3. **System Behavior**: 
   - Employee with 0 days worked = Gross = 0
   - No deductions calculated (no payslip item breakdown)
   - Net Pay = 0
   - Payslip still created but with 0 amounts

**Example**:
```
Employee on unpaid medical leave entire period:
- Days worked = 0
- Gross Pay = ₱0
- Deductions = (SKIPPED - array is empty)
- Net Pay = ₱0
```

---

### Q6: How does manual adjustment (allowance.php) integrate with payroll processing?

**A**:
- **Data Source**: `pr_employee_adjustments` table
- **When Applied**: During `calculateEmployeePayroll()` in payrollModel

**Flow**:
```
1. HR navigates to allowance.php
2. HR selects employee, payroll period, adjustment type (allowance or deduction)
3. HR enters description and amount
4. System inserts into pr_employee_adjustments
   INSERT INTO pr_employee_adjustments 
   (employee_id, payroll_period_id, type, description, amount)
   VALUES (?, ?, ?, ?, ?)

5. Later, when payroll is calculated:
   System queries: 
   SELECT type, description, amount FROM pr_employee_adjustments
   WHERE employee_id = ? AND payroll_period_id = ?
   
6. For each adjustment:
   - If type='allowance' or type='benefit': Added to GROSS PAY
   - If type='deduction': Added to TOTAL DEDUCTIONS
   
7. Result: Allowance increases net pay; deduction decreases net pay
```

**Location**: payroll/models/payrollModel.php lines 316-346

**Example Integration**:
```
Without adjustment:
- Gross = ₱15,000, Deductions = ₱1,000, Net = ₱14,000

After HR adds Bonus (₱2,000 allowance) via allowance.php:
- Gross = ₱15,000 + ₱2,000 = ₱17,000
- Deductions = ₱1,000
- Net = ₱16,000

After HR adds Loan Repayment (₱500 deduction) via allowance.php:
- Gross = ₱17,000
- Deductions = ₱1,000 + ₱500 = ₱1,500
- Net = ₱15,500
```

**Timing Note**: Adjustments must be entered BEFORE payroll is finalized. After finalization, the payslip is locked and cannot be changed (pr_payslips table is the source of truth).

---

### Q7: What tables does the payroll system read from? Which does it write to?

**A**:

**READ ONLY**:
- `employees` (position, teacher_qualification, base_salary)
- `rao_offer_salary` (base salary offered)
- `rao_hired_applicants` (link offers to employees)
- `pr_teacher_loads` (teaching units for teachers)
- `time_attendance` (attendance, absences, late minutes)
- `employee_contributions` (SSS/PhilHealth/Pag-IBIG status)
- `pr_position_deduction_rates` (configurable deduction rates)
- `pr_periods` (read period dates to validate open/closed status)

**WRITE ONLY** (via payroll system):
- `pr_runs` (create new payroll run, finalize run)
- `pr_payslips` (insert payslip record)
- `pr_payslip_items` (insert earnings & deduction itemization)
- `payroll_clearances` (create clearance record)

**READ & WRITE**:
- `pr_employee_adjustments` (read during calculation; written by HR via allowance.php)
- `pr_periods` (write: update status to 'closed' after finalization)

**Never Written**:
- Employee master data, attendance records, offers, contributions (these are managed by other modules)

---

### Q8: How does the dashboard get its data? What calculations does it perform?

**A**: Dashboard queries finalized data; it does NOT recalculate salary.

**Data Source**: 
- Reads from already-finalized `pr_payslips` and `pr_runs` (which have gross_pay, total_deductions, net_pay already calculated)
- Does NOT call `calculateEmployeePayroll()` again

**Dashboard Queries**:

| Metric | SQL Query | Source |
|--------|-----------|--------|
| **Total Employees** | COUNT(*) FROM employees | employees table |
| **Last Period** | period_name WHERE status='open' or latest finalized | pr_periods |
| **Total Payroll** | SUM(net_pay) FROM pr_payslips | pr_payslips |
| **Average Salary** | AVG(net_pay) FROM pr_payslips WHERE status='finalized' | pr_payslips |
| **Total Deductions** | SUM(total_deductions) FROM pr_payslips | pr_payslips |
| **Total Gross** | SUM(gross_pay) FROM pr_payslips | pr_payslips |
| **Deductions Trend (Chart)** | SUM(total_deductions) GROUP BY month | pr_payslips + pr_runs + pr_periods |
| **Gross vs Net Chart** | SUM(gross_pay), SUM(net_pay) GROUP BY month | pr_payslips + pr_runs + pr_periods |
| **Processing Status** | # processed (net_pay > 0) / # pending (net_pay = 0) | pr_payslips |
| **Pending Clearances** | COUNT(*) WHERE status='pending' | payroll_clearances |

**Location**: payroll/models/dashboardModel.php

**Key Point**: Dashboard data is a SUMMARY of finalized records. It is NOT recalculating; it is aggregating and reporting.

---

### Q9: What is the "pro-rata" calculation for teachers with partial unit assignments?

**A**:
**Scenario**:
- Teacher hired for 20 teaching units/semester
- Base salary: ₱30,000/month (₱15,000 semi-monthly)
- Actual assigned units (from pr_teacher_loads): 12 units

**Pro-rata Calculation**:
```
Gross Pay = (actual_units / expected_units) × (Base Salary / 2 × Days Worked / 15)
          = (12 / 20) × ₱15,000
          = 0.6 × ₱15,000
          = ₱9,000
```

**Rationale**:
- Ensures fair compensation for partial loads
- Teacher working 12 units earns proportionally less than teacher working full 20 units
- Prevents overpayment for partial assignments

**Implementation Note**: The system does NOT hardcode the expected units. Instead, it uses:
- `actual_units` from `pr_teacher_loads.total_units`
- Default assumption: If teacher has units assigned, they are expected to receive pro-rata (no hardcoded cap)

**Code**: payroll/models/payrollModel.php, though specific pro-rata calculation might need review (system currently uses basic daily rate calculation, not explicitly pro-rata for teacher units)

---

### Q10: How does the system prevent double payroll processing for the same employee in the same period?

**A**:
- **Prevention Method**: SQL query uses `NOT EXISTS` clause

**Code Location**: payroll/models/payrollModel.php line 247
```php
public function getEmployeesForPayroll(int $periodId): array
{
    $stmt = $this->db->prepare("
        SELECT e.employee_id, e.full_name AS name, e.position
        FROM employees e
        WHERE e.employment_status='Active'
        AND NOT EXISTS (
            SELECT 1
            FROM pr_payslips p
            JOIN pr_runs pr ON pr.run_id = p.payroll_run_id
            WHERE p.employee_id = e.employee_id
            AND pr.payroll_period_id = :periodId
        )
    ");
    $stmt->execute(['periodId' => $periodId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

**How It Works**:
1. Returns ONLY employees who do NOT have a payslip in the selected period
2. If employee already has pr_payslips record for period, they are excluded
3. Prevents showing duplicate employees in payroll processing form

**Enforcement**: 
- UI will not show employees with existing payslips
- If HR tries to manually insert duplicate payslip via API, database PRIMARY KEY constraint on (payroll_run_id, employee_id) would prevent it (though not explicitly enforced in current schema)

**Implication**: Period is immutable once finalized. HR must create a NEW period to run payroll again.

---

### Q11: What is the business logic behind the different absence deduction rates (₱1,536 vs ₱1,020 vs ₱800)?

**A**:
- **Teachers: ₱1,536/day**
  - Highest deduction (relative to typical daily rate)
  - Justification: Teachers' daily rate is higher; penalty should reflect earning potential
  - Teachers paid semi-monthly, so per-day absence value is significant

- **Admin/Professional: ₱1,020/day**
  - Middle tier
  - Standardized for office/professional staff
  - Reflects typical daily earnings

- **Support: ₱800/day**
  - Lowest deduction
  - Support staff typically have lower base salaries
  - Proportional to earning potential

**Configuration Location**: payroll/models/payrollModel.php, lines 7-36 (POSITION_CONFIG array)

**Overrideable**: Can be customized per position category in `pr_position_deduction_rates` table if needed for future flexibility

---

### Q12: If a teacher is assigned zero units, how is salary calculated?

**A**:
- **Current System Behavior**: If teacher has 0 units from pr_teacher_loads:
  ```
  teaching_units = 0
  The system does NOT have explicit pro-rata logic for zero units
  Gross Pay calculation would use: (Daily Rate) × (Days Worked)
  Not explicitly zero-ed out
  ```

- **Recommended Scenario for Defense**:
  ```
  Zero Units Scenario:
  - Teacher not assigned any teaching load for period
  - Payroll calculation proceeds with standard logic
  - If no base salary assigned to units, teacher gets ₱0 or minimal pay
  - HR should never finalize payroll for zero-unit teachers
  (They should remove teacher from period or assign units)
  ```

- **Best Practice**:
  - HR should ensure all teaching staff have teaching units assigned to pr_teacher_loads BEFORE payroll finalization
  - System should ideally validate and warn if teacher has no units

---

### Q13: Are there any edge cases or known limitations in the payroll system?

**A**:
1. **Timing of Attendance Data**: 
   - Attendance records must exist before payroll is finalized
   - If attendance is updated AFTER payroll is finalized, the payslip does NOT change (it is locked)

2. **Base Salary Requirement**:
   - If rao_offer_salary and employees.base_salary are both 0, gross pay = 0 (no error thrown)
   - HR should ensure valid salary is set at hire time

3. **Teacher Units Pro-rata**:
   - System does not have explicit zero-unit protection
   - If teacher has no units in pr_teacher_loads, behavior undefined

4. **Contribution Status**:
   - If employee_contributions record does not exist, deductions are NOT applied
   - HR must explicitly mark contributions as 'submitted' for deductions to work

5. **Late Minute Boundary**:
   - System includes partial late minutes (not rounded)
   - Example: 1 minute late = ₱2.00 deduction

6. **No Manual Payslip Edit Post-Finalization**:
   - Once pr_payslips is inserted, it cannot be edited by normal UI
   - Would require database correction or creating a new period/payslip

7. **Clearance Workflow**:
   - System allows multiple clearances per payslip (not enforced to be 1:1)
   - HR should manage manually

---

### Q14: How would you explain the dashboard's "Pending Clearances"  metric to finance team?

**A**: 
```
Definition:
  COUNT(*) WHERE status='pending' FROM payroll_clearances table

Meaning:
  Number of payslips awaiting HR approval before payment can be processed

Example Flow:
  1. Payroll is finalized → 50 payslips created
  2. System creates 50 payroll_clearances records (status='pending')
  3. HR reviews each payslip, approves 40 (status='approved')
  4. Dashboard shows: "Pending Clearances: 10"
  5. Finance waits for all 10 to be approved before processing payment

Action Needed:
  HR must approve all remaining clearances before payroll can be processed for payment
```

---

## SUMMARY FOR DEFENSE

### System Architecture Overview
The Payroll System is a 4-tier architecture:
1. **Data Layer**: Multiple tables (employees, salary offers, attendance, adjustments)
2. **Calculation Layer**: payrollModel.php with position-category-specific formulas
3. **Business Logic Layer**: payrollController.php orchestrating data flow
4. **Presentation Layer**: payroll.php (dashboard), payrollProcess.php (calculations), payslip.php (view)

### Key Differentiators
- **Position-Based Calculation**: Teachers (unit-based), Admin/Prof (daily + overtime), Support (daily)
- **Attendance Integration**: Automatic deduction for unexcused absences & late minutes
- **Multi-Source Data**: Salary from offers table, units from teacher loads, attendance integrated
- **Two-Stage Processing**: Preview (non-persistent) → Finalize (locks into database)
- **Clearance Workflow**: Payslips require HR approval before finance processes payment

### Strengths
✓ Clear position categorization with different rules
✓ Integrates multiple HR systems (attendance, contributions, adjustments)
✓ Prevents negative net pay (no deductions if gross = 0)
✓ Differentiates unexcused absences from approved leave
✓ Dashboard provides real-time visibility into payroll status
✓ Immutable payslip records (once finalized, cannot be altered)

### Design Rationale
- **Unexcused-only deduction**: Fairness; approved leave should not be penalized
- **Pro-rata for teachers**: Flexibility for partial unit assignments
- **Position-based absence rates**: Proportional to earning potential
- **Clearance workflow**: HR verification ensures accuracy before payment dispatch
- **No deductions if gross=0**: Employee protection; fairness

---

## END OF DEFENSE DOCUMENT

This document covers all aspects of the payroll system for comprehensive defense preparation. Study these calculations, Q&A, and system flows thoroughly for your presentation.
