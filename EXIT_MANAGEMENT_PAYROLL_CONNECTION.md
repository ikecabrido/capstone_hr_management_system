# EXIT MANAGEMENT & PAYROLL CONNECTION

## Overview

Exit Management and Payroll are connected through the **employee's employment status** (`employees.employment_status`). When an employee exits the organization (resignation, termination, or retirement), their employment status changes, which directly affects their inclusion in future payroll processing.

---

## CONNECTION POINTS

### 1. Employment Status Filter in Payroll

**Location**: `payroll/models/payrollModel.php`

The payroll system filters employees by employment status:

```php
// Get employees eligible for payroll
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

**Key Logic**: 
- Only employees with `employment_status = 'Active'` are included
- Additionally, they must not already have a payslip for the period (NOT EXISTS clause)

---

### 2. Exit Management Process

**Location**: `exit_management/models/ResignationModel.php`

When an employee resigns:

```
RESIGNATION WORKFLOW:
├── Employee/HR submits resignation
│   └── INSERT into `resignations` table
│       - status: 'pending'
│       - reason, notice_date, last_working_date recorded
│
├── HR approves/rejects resignation
│   └── UPDATE resignations table
│       - status: 'approved' or 'rejected'
│       - approved_by, approved_at recorded
│
└── Settlement processed (separate step)
    └── INSERT into `employee_settlements` table
        - withdrawal settlement calculations
        - gratuity, notice pay, provident fund calculated
        - outstanding loans deducted
```

---

### 3. Settlement Model (Final Payoff)

**Location**: `exit_management/models/SettlementModel.php`

The Settlement model handles the final payout calculation when employee exits:

```php
public function createSettlement(array $data): int
{
    $stmt = $this->db->prepare("
        INSERT INTO employee_settlements (employee_id, resignation_id, 
            basic_salary, hra, conveyance, lta, medical_allowance,
            other_allowances, provident_fund, gratuity,
            notice_pay, outstanding_loans, other_deductions,
            net_payable, settlement_date, status, created_by, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'draft', ?, NOW())
    ");
    // ...
}
```

**Settlement Components**:
- **Gratuity**: `(Basic Salary × 15 × Years of Service) / 26`
- **Provident Fund**: `(Basic Salary + DA) × 0.12`
- **Notice Pay**: `(Basic Salary / 30) × Notice Days`
- **Outstanding Loans**: Deducted from settlement
- **Other Deductions**: Any additional deductions
- **Net Payable**: Total settlement amount

---

## DATA FLOW: FROM RESIGNATION TO PAYROLL EXCLUSION

```
STEP 1: Resignation Submitted
├── HR submits resignation via exit_management.php
├── Data inserted into `resignations` table
└── Employee still has employment_status = 'Active' at this point

STEP 2: Resignation Approved
├── HR approves resignation
├── `resignations` table status → 'approved'
└── ⚠️  IMPORTANT: Employee STILL has employment_status = 'Active'
    (System does NOT auto-update employment_status)

STEP 3: Settlement Created
├── HR creates settlement record via settlement form
├── Data inserted into `employee_settlements` table
├── Settlement amount calculated (gratuity, notice pay, etc.)
└── Employee STILL active in payroll calculations

STEP 4: UPDATE REQUIRED (Currently Manual)
├── HR should MANUALLY update employment_status in employees table
├── Status change: 'Active' → 'Resigned' (or 'Terminated')
└── SQL: UPDATE employees SET employment_status='Resigned' WHERE employee_id=?

STEP 5: Future Payroll Excluded
├── Next payroll cycle runs
├── getEmployeesForPayroll() queries WHERE employment_status='Active'
├── Resigned employee is NOT in results
└── No payslip generated for resigned employee
```

---

## CRITICAL GAP: Employment Status Not Auto-Updated

**Current Behavior**:
- Resignation approval updates `resignations.status` ✓
- Settlement calculation happens ✓
- **BUT**: `employees.employment_status` is NOT automatically updated ✗

**Implication**:
- Resigned employee could still be included in next payroll run
- HR must manually update `employees.employment_status` to '**Resigned**', '**Terminated**', '**Retired**', etc.
- Until status is changed, payroll system treats them as Active

**Recommended Fix** (if needed):
```php
// In ResignationController.php, processResignation() method:
public function processResignation(int $resignationId, string $action, int $approvedBy): array
{
    try {
        $status = ($action === 'approve') ? 'approved' : 'rejected';
        $success = $this->resignationModel->updateResignationStatus(
            $resignationId, 
            $status, 
            $approvedBy
        );

        if ($success && $action === 'approve') {
            // Get employee_id from resignation
            $resignation = $this->resignationModel->getResignationById($resignationId);
            
            // UPDATE employee status automatically
            $updateStmt = $this->db->prepare("
                UPDATE employees 
                SET employment_status = 'Resigned',
                    updated_at = NOW()
                WHERE employee_id = ?
            ");
            $updateStmt->execute([$resignation['employee_id']]);
        }
        
        return ['success' => true, 'message' => "Resignation $status successfully"];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
```

---

## PAYROLL EMPLOYEE FILTERING

The payroll system filters employees in multiple places:

### All Active Employees Query
```php
public function getAllActiveEmployeesForPeriod(int $periodId): array
{
    $stmt = $this->db->prepare("
        SELECT e.employee_id, e.full_name AS name, e.position, e.department
        FROM employees e
        WHERE e.employment_status='Active'  // ← FILTERS BY ACTIVE
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

### Teacher Employees Query
```php
public function getTeacherEmployees(): array
{
    $positions = ['Teacher', 'Assistant Teacher', 'Instructor', ...];
    $placeholders = implode(',', array_fill(0, count($positions), '?'));
    $stmt = $this->db->prepare(
        "SELECT e.employee_id as id, e.full_name AS name, e.position
         FROM employees e
         WHERE e.employment_status = 'Active'  // ← FILTERS BY ACTIVE
         AND e.position IN ($placeholders)
         ORDER BY e.full_name"
    );
    $stmt->execute($positions);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

---

## TABLES INVOLVED IN CONNECTION

### Exit Management Tables
| Table | Purpose | Relationship to Payroll |
|-------|---------|------------------------|
| `resignations` | Records resignation requests/approvals | Links to `employees.employee_id` |
| `employee_settlements` | Calculates final payout for exiting employee | Links to `employees.employee_id` and `resignations.id` |
| `exit_documents` | Documents required for exit process | HR compliance, not payroll |
| `exit_interviews` | Exit interview records | HR process, not payroll |

### Connection Table
| Table | Column | Purpose |
|-------|--------|---------|
| `employees` | `employment_status` | **KEY CONNECTION**: Filters payroll eligibility |
| `employees` | `employee_id` | Links resignations and settlements |

---

## FINAL PAYROLL (Settlement vs Regular Payslip)

Two separate payment streams for exiting employees:

### Regular Payroll Payslip
- **Generated by**: `payroll/models/payrollModel.php` → `calculateEmployeePayroll()`
- **Conditions**: Only if `employment_status = 'Active'`
- **Components**: Basic salary, overtime, deductions, contributions
- **Table**: `pr_payslips`, `pr_payslip_items`

### Settlement Payslip
- **Generated by**: `exit_management/models/SettlementModel.php` → `createSettlement()`
- **Conditions**: Always, when employee exits
- **Components**: Gratuity, notice pay, provident fund, outstanding loans, other deductions
- **Table**: `employee_settlements`

---

## EXAMPLE SCENARIO: Complete Exit-to-Payroll Flow

```
SCENARIO: Teacher "Juan Dela Cruz" resigns on April 1, 2026

TIMELINE:
─────────

January 31, 2026 - Regular Payroll Processing
├── Period: "Jan 16-31"
├── getEmployeesForPayroll() returns Juan (employment_status='Active')
├── Payslip generated: ₱15,000 gross, ₱1,200 deductions, ₱13,800 net
└── Juan included in regular payroll

February 15, 2026 - Resignation Submitted
├── HR enters resignation: last_working_date = "April 15, 2026"
├── Status: pending → approved
├── Data in resignations table
└── Juan STILL has employment_status='Active'

February 16, 2026 - Settlement Created
├── HR calculates settlement:
│   ├── Gratuity: (₱15,000 × 15 × 8 years) / 26 = ₱69,231
│   ├── Notice Pay: (₱15,000 / 30) × 15 days = ₱7,500
│   ├── Provident Fund: ₱15,000 × 0.12 = ₱1,800
│   └── Outstanding Loans: -₱5,000
├── Net Payable: ₱69,231 + ₱7,500 + ₱1,800 - ₱5,000 = ₱73,531
└── Status: draft → approved

April 1, 2026 - CRITICAL STEP (Manual)
├── HR MUST update employment status
├── SQL: UPDATE employees SET employment_status='Resigned' WHERE employee_id='Juan'
└── Juan now EXCLUDED from regular payroll

April 16-30, 2026 - April Payroll Processing
├── Period: "Apr 16-30"
├── getEmployeesForPayroll() queries WHERE employment_status='Active'
├── Juan NOT returned (employment_status='Resigned')
└── Final payslip NOT generated (last day was Apr 15)

Payment Processing:
├── Settlement amount ₱73,531 submitted for payment
├── Regular payroll for April 1-15 (if applicable): ₱[calculated amount]
├── Total payment to Juan: Settlement + Pro-rata April pay
└── Employee record closed
```

---

## SUMMARY FOR DEFENSE

### How Exit Management & Payroll Connect:

1. **Employment Status is the Bridge**
   - Payroll only includes employees where `employment_status = 'Active'`
   - Exit Management records resignation but does NOT change this status (gap)

2. **Resignation Process**
   - HR submits resignation with last_working_date
   - Resignation is approved (becomes status='approved')
   - Settlement calculations begin (gratuity, notice pay, etc.)

3. **Settlement Calculation**
   - Different from regular payslip
   - Includes terminal benefits (gratuity, notice pay, PF)
   - Outstanding loans deducted
   - One-time final payout

4. **Payroll Exclusion**
   - When `employment_status` changes from 'Active' to 'Resigned'/'Terminated'
   - Payroll system automatically excludes employee from future calculations
   - Prevents double-payment

5. **Known Gap**
   - Resignation approval does NOT automatically update `employees.employment_status`
   - Requires manual update or additional code
   - Should be automated to prevent errors

### Key Tables:
- **Payroll**: `pr_payslips`, `pr_payslip_items`, `pr_runs`
- **Exit**: `resignations`, `employee_settlements`
- **Link**: `employees.employment_status`, `employees.employee_id`

---

## END OF ANALYSIS
