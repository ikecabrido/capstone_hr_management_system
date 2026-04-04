# Payroll Employee Configuration UI - Setup & Usage Guide

## Overview
This is a **demo/preview form** to manage payroll employee configuration data before implementing it into your actual payroll calculations. You can test the interface and data flow without affecting the live payroll system.

---

## Files Created

### 1. Database Tables
**File:** `payroll_employee_config.sql`

Creates 4 tables:
- `pr_employee_details` - Base salary, position type, teacher info
- `pr_employee_benefits` - SSS/PhilHealth/Pag-IBIG enrollment
- `pr_position_deduction_rates` - Deduction amounts by position
- `pr_teacher_qualification_rates` - Pay rates per unit by qualification

### 2. Backend
**Model:** `models/payrollEmployeeConfigModel.php`
- Database queries for CRUD operations
- Get employee configurations
- Save/Update configurations

**Controller:** `controllers/payrollEmployeeConfigController.php`
- Handles form submissions
- AJAX endpoints for edit/delete/save operations
- Data validation

### 3. Frontend UI
**View:** `views/payrollEmployeeConfig.php`
- Employee list with current configurations
- Edit modal with form
- Summary statistics dashboard
- Search functionality

---

## How to Use

### Step 1: Import Database Tables
```sql
-- Run the SQL file in phpMyAdmin or MySQL command line
mysql -u root -p your_database < payroll_employee_config.sql
```

OR

- Go to phpMyAdmin
- Select your HR database
- Click "Import" tab
- Upload `payroll_employee_config.sql`

### Step 2: Access the Form
Navigate to:
```
http://localhost/capstone_hr_management_system/payroll/views/payrollEmployeeConfig.php
```

### Step 3: Configure Employees

#### For Admin Staff:
1. Click **Edit** button on any employee
2. Fill in:
   - **Base Salary** (Monthly amount, e.g., 30,000)
   - **Position Type** = "Admin Staff"
   - **Trio Deductions** checkboxes (check if enrolled)
3. Click **Save Configuration**

#### For Teachers:
1. Click **Edit** button on teacher employee
2. Fill in:
   - **Base Salary** (will auto-calculate from units) - DISABLED for teachers
   - **Position Type** = "Teacher/Professor/Instructor"
   - **Teacher Qualification** = ProfEd/LPT/Masteral
   - **Teaching Units** (e.g., 30 units)
   - **Trio Deductions** checkboxes
3. Click **Save Configuration**

---

## Form Layout

### Summary Dashboard (Top)
Shows statistics:
- Admin Staff Count
- Teachers Count
- SSS Enrolled
- PhilHealth Enrolled
- Pag-IBIG Enrolled

### Employee List (Main)
Shows each employee with:
- Name & Position Type badge (Admin/Teacher)
- Employee ID, Position, Department
- Current salary
- Trio deduction status (✓/✗)
- Teacher info (if applicable)
- Edit & Delete buttons

### Edit Modal
**Employee Information Section (Read-only):**
- Employee ID
- Full Name
- Position
- Department

**Payroll Details Section:**
- Base Salary (with ₱ prefix)
- Position Type (dropdown)

**Teacher Section (shown only if Teacher selected):**
- Teacher Qualification (ProfEd/LPT/Masteral)
- Teaching Units (numeric input)

**Trio Contributions Section:**
- SSS checkbox
- PhilHealth checkbox
- Pag-IBIG checkbox

**Deduction Rates Reference:**
- Shows position-based rates (informational only)

---

## Sample Data

The SQL file includes sample data:

### Employee Details (3 employees)
```
EMP001: ₱30,000/month - Admin
EMP002: ₱35,000/month - Admin  
EMP003: ₱32,000/month - Admin
```

### Teacher Qualification Rates
- ProfEd: ₱128/unit
- LPT: ₱130/unit
- Masteral: ₱250/unit

### Deduction Rates
```
Admin:   ₱1,020/absence, ₱2/min late, ₱120/hour late
Teacher: ₱1,536/absence, ₱2/min late, ₱120/hour late
```

---

## Features

✅ **List all employees** with current payroll config  
✅ **Edit employee** payroll details via modal form  
✅ **Delete** employee configuration  
✅ **Search** employees by name/ID/department  
✅ **Admin vs Teacher** differentiation  
✅ **Dynamic form** - teacher fields appear only for teachers  
✅ **Trio deduction checkboxes** for benefit enrollment  
✅ **Summary statistics** dashboard  
✅ **Responsive design** with Bootstrap styling  
✅ **Toast notifications** for success/error feedback  

---

## Calculation Examples (for reference)

### Example 1: Admin Staff (30,000/month base)
```
Semi-monthly base = 30,000 ÷ 2 = 15,000
Daily rate = 15,000 ÷ 15 = 1,000
If worked 10 days = 10,000
Absence deduction = ₱1,020 per day
```

### Example 2: Teacher (30 units, ProfEd)
```
Base salary formula = (30 units × 128) ÷ 2 = 1,920 semi-monthly
If 15 days worked = 1,920
Absence deduction = ₱1,536 per day
```

---

## Integration with Payroll Calculations

Once you're satisfied with this form:

1. **Use these tables in PayrollModel.php:**
   ```php
   LEFT JOIN pr_employee_details pd ON e.employee_id = pd.employee_id
   LEFT JOIN pr_employee_benefits pb ON e.employee_id = pb.employee_id
   LEFT JOIN pr_position_deduction_rates pdr ON pd.position_type = pdr.position_type
   ```

2. **Implement calculations based on position_type:**
   - If `position_type = 'Teacher'`: Use unit-based calculation
   - If `position_type = 'Admin'`: Use daily-rate calculation

3. **Check trio deduction flags before calculating:**
   - `has_sss`, `has_philhealth`, `has_pagibig`

4. **Apply position-specific deduction rates:**
   - `absence_deduction_amount`, `late_per_minute_rate`

---

## Troubleshooting

**Q: Form won't load**
- Check if database tables were imported
- Verify database connection in `auth/database.php`
- Check browser console for JavaScript errors

**Q: Can't see Edit button**
- Ensure you're logged in
- Check authentication in `auth/auth_check.php`

**Q: Data not saving**
- Check browser console for AJAX errors
- Verify form validation (required fields marked with *)
- Check database connectivity

**Q: Teacher section not showing**
- Select "Teacher/Professor/Instructor" from Position Type dropdown

---

## Next Steps

1. ✅ Test this form with sample data
2. ✅ Verify all CRUD operations work
3. ✅ Get approval from department heads for data accuracy
4. ✅ Once approved, integrate into PayrollModel.php for calculations
5. ✅ Create sync script for Legal & Compliance data when ready

---

## Files Reference

```
payroll/
├── payroll_employee_config.sql          ← SQL to import
├── models/
│   └── payrollEmployeeConfigModel.php   ← Database operations
├── controllers/
│   └── payrollEmployeeConfigController.php ← Form logic
└── views/
    └── payrollEmployeeConfig.php        ← UI Form (MAIN FILE)
```

---

## Questions or Issues?

- Check console logs (F12 → Console)
- Review error messages in browser
- Verify all files are in correct directories
- Ensure database tables exist

