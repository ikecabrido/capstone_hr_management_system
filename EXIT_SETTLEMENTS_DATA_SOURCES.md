# EXIT EMPLOYEE SETTLEMENTS - DATA SOURCES & FLOW

## Overview
`exit_employee_settlements` table is populated during the employee exit process. Data comes from multiple sources combined together.

---

## DATA SOURCE MAPPING

### Table: `exit_employee_settlements`

| Column | Data Source | How it's populated |
|--------|-------------|-------------------|
| `id` | AUTO_INCREMENT | Database auto-generates |
| `employee_id` | `exit_resignations.employee_id` | From resignation record |
| `resignation_id` | `exit_resignations.id` | Links resignation to settlement |
| `basic_salary` | `rao_offer_salary.salary` (primary) or `employees.base_salary` (fallback) | Query employee's salary offer |
| `hra` | HR input | Manually entered by HR |
| `conveyance` | HR input | Manually entered by HR |
| `lta` | HR input | Manually entered by HR |
| `medical_allowance` | HR input | Manually entered by HR |
| `other_allowances` | HR input | Manually entered by HR |
| `provident_fund` | **CALCULATED** | (basic_salary × 0.12) |
| `gratuity` | **CALCULATED** | (basic_salary × 15 × years_of_service) / 26 |
| `notice_pay` | **CALCULATED** | (basic_salary / 30) × notice_days |
| `outstanding_loans` | HR input | Manually found from employee records |
| `other_deductions` | HR input | Manually entered by HR |
| `net_payable` | **CALCULATED** | Total earnings - Total deductions |
| `settlement_date` | HR input | Date of settlement |
| `status` | Defaults to 'draft' | HR updates after approval |
| `approved_by` | HR input | User ID who approved |
| `approved_at` | System | Timestamp when approved |
| `created_by` | System from session | User ID who created |
| `created_at` | System | Current timestamp |
| `updated_at` | System | Updated timestamp |

---

## DATA FLOW DIAGRAM

```
EMPLOYEE EXIT PROCESS:
═════════════════════

STEP 1: Resignation Submitted
├─ Employee/HR submits resignation
├─ Data recorded in: exit_resignations
│  ├─ employee_id ← employees.employee_id
│  ├─ resignation_type ← HR selects (voluntary/involuntary)
│  ├─ reason ← HR enters reason
│  ├─ notice_date ← HR enters date
│  ├─ last_working_date ← HR enters date
│  └─ status ← 'pending'
│
STEP 2: Settlement Created
├─ HR clicks "Create Settlement"
├─ System fetches employee data:
│  ├─ FROM employees:
│  │  ├─ employee_id
│  │  ├─ date_hired (calculate years_of_service)
│  │  └─ base_salary (fallback)
│  │
│  ├─ FROM exit_resignations:
│  │  ├─ last_working_date (calculate notice_days)
│  │  ├─ resignation_id
│  │  └─ employee_id
│  │
│  └─ FROM rao_offer_salary (via rao_hired_applicants):
│     └─ salary (PRIMARY source for basic_salary)
│
├─ System CALCULATES:
│  ├─ years_of_service = TODAY() - date_hired
│  ├─ gratuity = (basic_salary × 15 × years_of_service) / 26
│  ├─ provident_fund = basic_salary × 0.12
│  ├─ notice_pay = (basic_salary / 30) × notice_days
│  └─ net_payable = earnings - deductions
│
├─ HR ENTERS MANUALLY:
│  ├─ hra
│  ├─ conveyance
│  ├─ lta
│  ├─ medical_allowance
│  ├─ other_allowances
│  ├─ outstanding_loans
│  ├─ other_deductions
│  └─ settlement_date
│
└─ Data INSERTED into exit_employee_settlements
```

---

## CALCULATED FIELDS FORMULAS

### 1. Gratuity
```
gratuity = (basic_salary × 15 × years_of_service) / 26

Example:
- Basic Salary: ₱40,000/month
- Years of Service: 7 years
- Gratuity = (40,000 × 15 × 7) / 26 = ₱161,538.46
```

### 2. Provident Fund
```
provident_fund = (basic_salary + da) × 0.12

Example:
- Basic Salary: ₱40,000
- DA: ₱0
- PF = (40,000 + 0) × 0.12 = ₱4,800.00
```

### 3. Notice Pay
```
notice_pay = (basic_salary / 30) × notice_days

Example:
- Basic Salary: ₱40,000
- Notice Days: 30 (standard)
- Notice Pay = (40,000 / 30) × 30 = ₱40,000.00
```

### 4. Net Payable
```
Total Earnings:
= basic_salary 
  + hra 
  + conveyance 
  + lta 
  + medical_allowance 
  + other_allowances 
  + gratuity 
  + notice_pay

Total Deductions:
= provident_fund 
  + outstanding_loans 
  + other_deductions

net_payable = Total Earnings - Total Deductions

Example:
Earnings:  ₱241,538.46 (40,000 + 161,538 + 40,000)
Deductions: ₱4,800.00 (PF only)
Net Pay:   ₱236,738.46
```

---

## EXAMPLE: COMPLETE DATA FLOW

**Scenario**: Dr. Angela Ramos (Employee ID: 4) resigns

### Source Tables:

**Table: employees** (ID: 4)
```
employee_id: 4
full_name: Dr. Angela Ramos
date_hired: 2018-08-01
employment_status: Active
```
→ date_hired used to calculate years_of_service = 7 years

**Table: exit_resignations** (ID: X)
```
employee_id: 4
resignation_type: voluntary
reason: Career advancement
notice_date: 2026-04-05
last_working_date: 2026-05-05  ← Used to calculate notice_days = 30
status: approved
```
→ resignation_id = X is stored in settlement

**Table: rao_offer_salary** (accepted offer for employee 4)
```
salary: ₱40,000.00  ← PRIMARY SOURCE for basic_salary
offer_status: accepted
```

### Settlement Calculation:

```php
$years_of_service = 7
$basic_salary = 40000
$notice_days = 30

$gratuity = (40000 × 15 × 7) / 26 = ₱161,538.46
$provident_fund = 40000 × 0.12 = ₱4,800.00
$notice_pay = (40000 / 30) × 30 = ₱40,000.00
```

### HR Enters Manually:
```
hra: 0
conveyance: 0
lta: 0
medical_allowance: 0
other_allowances: 0
outstanding_loans: 0
other_deductions: 0
settlement_date: 2026-05-05
```

### Final Settlement Record (inserted into exit_employee_settlements):
```
employee_id: 4
resignation_id: X
basic_salary: 40000.00
hra: 0.00
conveyance: 0.00
lta: 0.00
medical_allowance: 0.00
other_allowances: 0.00
provident_fund: 4800.00          ← CALCULATED
gratuity: 161538.46              ← CALCULATED
notice_pay: 40000.00             ← CALCULATED
outstanding_loans: 0.00
other_deductions: 0.00
net_payable: 236738.46           ← CALCULATED (earnings - deductions)
settlement_date: 2026-05-05
status: draft
created_by: 1
created_at: 2026-04-05 12:00:00
```

---

## WHERE DATA COMES FROM (SUMMARY)

### External Sources (must already exist):
1. **employees table** - Employee master data (name, hire date, etc.)
2. **rao_offer_salary table** - Employee's accepted salary offer
3. **exit_resignations table** - Resignation details (notice period, reason, etc.)

### HR Input (manual entry):
- Allowances: HRA, conveyance, LTA, medical, other
- Deductions: outstanding loans, other deductions
- Settlement date

### System Calculations:
- **Years of Service** = TODAY() - employees.date_hired
- **Gratuity** = (salary × 15 × years) / 26
- **Provident Fund** = salary × 0.12
- **Notice Pay** = (salary / 30) × notice_days
- **Net Payable** = total earnings - total deductions

---

## DATA INTEGRITY NOTES

### Requirements for Settlement Creation:

✓ Employee must exist in `employees` table
✓ Employee must have a resignation in `exit_resignations`
✓ Employee must have accepted salary in `rao_offer_salary` (or fallback to base_salary)
✓ Resignation must have last_working_date (to calculate notice_days)

### Validation Rules:

- basic_salary must be > 0 (prevents invalid settlements)
- net_payable >= 0 (no negative payouts)
- settlement_date should be >= last_working_date
- years_of_service should be >= 0

---

## CODE FLOW (PHP)

**Location**: `exit_management/controllers/SettlementController.php`

```php
public function createSettlement(array $data): array
{
    // Get employee basic data
    $employee = fetchEmployee($data['employee_id']);
    
    // Get resignation details
    $resignation = fetchResignation($data['resignation_id']);
    
    // Get salary (from rao_offer_salary)
    $basicSalary = fetchAcceptedSalary($employee['employee_id']);
    
    // Calculate years of service
    $yearsOfService = calculateYearsSince($employee['date_hired']);
    
    // Calculate settlement components
    $gratuity = ($basicSalary * 15 * $yearsOfService) / 26;
    $pf = $basicSalary * 0.12;
    $noticeDays = calculateDaysBetween($resignation['notice_date'], $resignation['last_working_date']);
    $noticePay = ($basicSalary / 30) * $noticeDays;
    
    // HR provides optional items
    $hra = $data['hra'] ?? 0;
    $outstanding_loans = $data['outstanding_loans'] ?? 0;
    
    // Calculate totals
    $totalEarnings = $basicSalary + $hra + $gratuity + $noticePay + ...;
    $totalDeductions = $pf + $outstanding_loans + ...;
    $netPayable = $totalEarnings - $totalDeductions;
    
    // Insert into exit_employee_settlements
    return $settlementModel->createSettlement([
        'employee_id' => $employee['employee_id'],
        'resignation_id' => $resignation['id'],
        'basic_salary' => $basicSalary,
        'gratuity' => $gratuity,
        'provident_fund' => $pf,
        'notice_pay' => $noticePay,
        'net_payable' => $netPayable,
        ...
    ]);
}
```

---

## END OF DATA SOURCE ANALYSIS
