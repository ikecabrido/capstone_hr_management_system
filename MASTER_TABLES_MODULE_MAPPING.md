# 🔍 Master Tables Source Analysis
## Which Tables Support Which Modules?

---

## 📊 TABLE-TO-MODULE MAPPING

### **1. DEPARTMENTS** 
**Comes from**: Foundational (Core HR)  
**Used by**:
- ✅ **Workforce Analytics (WFA)** - Department distribution, dept analytics
- ✅ **Time & Attendance** - Shift scheduling by department
- ✅ **Learning & Development** - Training by department
- ✅ **Performance** - Org hierarchy, performance by dept
- ✅ **Payroll** - Cost center allocation, budget tracking
- ✅ **Engagement Relations** - Dept-level engagement metrics
- ✅ **Recruitment** - Department staffing needs

**Current Database**: `wfa_department_analytics` references departments  
**Your Database Query**: Uses `e.department` as TEXT field (needs normalization)

---

### **2. POSITIONS**
**Comes from**: Foundational (Core HR)  
**Used by**:
- ✅ **Career Paths** (Learning & Development) - `career_paths` table has `target_position`
- ✅ **Succession Planning** (Learning & Development) - `succession_plans` has `position_name`
- ✅ **Recruitment** (Recruitment Module) - Job postings have positions
- ✅ **Performance** - Position-based performance expectations
- ✅ **Payroll** - Position-based salary grades
- ✅ **Workforce Analytics** - Position distribution analysis
- ✅ **Compliance** (Learning & Development) - Position-specific training requirements

**Current Database**: `positions` field in employees is TEXT (needs normalization)  
**Your Database Query**: `career_paths.target_position` is VARCHAR

---

### **3. EMPLOYMENT_TYPES**
**Comes from**: Time & Attendance + Payroll  
**Used by**:
- ✅ **Payroll** - Different salary calculations per type
- ✅ **Time & Attendance** - Leave entitlements vary by type
- ✅ **HR Compliance** - Benefits eligibility tracking
- ✅ **Exit Management** - Notice period varies by type
- ✅ **Workforce Analytics** - Employee type distribution

**Current Database**: 
- `ta_leave_types` handles leave specifically
- `ta_leave_balances` tracks balances by employee
- No standardized employment type tracking

**Your Database Gap**: 
- Part-time vs Full-time affects leave days
- Contract employees have different notice periods
- Not tracked in current schema

---

### **4. SALARY_GRADES**
**Comes from**: Payroll Module  
**Used by**:
- ✅ **Payroll** - Base salary calculation
- ✅ **Workforce Analytics** - Salary statistics, compensation analysis
- ✅ **Performance** - Performance-to-salary correlation
- ✅ **Recruitment** - Job posting salary bands
- ✅ **Exit Management** - Settlement calculations based on salary

**Current Database**: 
- `wfa_salary_statistics` table tracks salary data
- `wfa_compensation_analysis` tracks salary ranges
- No standardized grade structure

**Your Database Gap**: 
- No salary master table
- Can't normalize salary ranges
- Hard to track compensation benchmarking

---

### **5. QUALIFICATIONS**
**Comes from**: Learning & Development + Recruitment  
**Used by**:
- ✅ **Recruitment** - `applications` applicants have qualifications
- ✅ **Learning & Development** - `competencies` and `lms_courses`
- ✅ **Compliance** - Mandatory certifications tracking
- ✅ **Performance** - Qualification-based performance expectations
- ✅ **Career Paths** - `career_paths.skills_required` stores JSON skills

**Current Database**: 
- `education` table for applicant education
- `competencies` table for skill definitions
- `lms_courses` for training
- No centralized qualification registry

**Your Database Structure**:
```sql
-- Applicant education (recruitment)
CREATE TABLE `education` (
  `school`, `field_of_study`, `degree`, `start_date`, `end_date`
);

-- Competencies (learning)
CREATE TABLE `competencies` (
  `name`, `description`, `category`, `proficiency_levels`
);

-- Career paths (learning)
CREATE TABLE `career_paths` (
  `skills_required` JSON,  -- Stored as JSON array
  `prerequisites`
);
```

---

### **6. EMPLOYEE_QUALIFICATIONS**
**Comes from**: Learning & Development + Compliance  
**Used by**:
- ✅ **Compliance** - `compliance_assignments` tracks training completion
- ✅ **Learning & Development** - `lms_enrollments` tracks course completion
- ✅ **Performance** - Qualification requirements verification
- ✅ **Recruitment** - Candidate qualification verification

**Current Database**: 
- `lms_enrollments` tracks course completions
- `compliance_assignments` tracks mandatory training
- `leadership_enrollments` tracks program completions

**Your Database Structure**:
```sql
-- LMS Enrollments (learning)
CREATE TABLE `lms_enrollments` (
  `user_id`, `course_id`, `completion_date`, `progress_percentage`, `score`, `status`
);

-- Compliance Assignments (learning)
CREATE TABLE `compliance_assignments` (
  `user_id`, `compliance_training_id`, `completion_date`, `status`
);

-- Leadership Enrollments (learning)
CREATE TABLE `leadership_enrollments` (
  `user_id`, `program_id`, `completion_date`, `certificate_issued`
);
```

---

### **7. LOCATIONS**
**Comes from**: Time & Attendance + Payroll  
**Used by**:
- ✅ **Time & Attendance** - Shift scheduling varies by location
- ✅ **Attendance tracking** - Timezone-aware clock-in/out
- ✅ **Workforce Analytics** - Analytics by location
- ✅ **Payroll** - Location-based allowances
- ✅ **Holiday Calendar** - Different holidays per location

**Current Database**: 
- `ta_attendance` has no location field
- `ta_shifts` defines shifts globally (no location context)
- `ta_holidays` has `is_recurring` but no location-specific holidays

**Your Database Gap**: 
- No location master table
- Can't track multi-location operations
- Holiday sync doesn't account for locations
- Time zones not tracked for remote employees

**Your Database Structure**:
```sql
-- Shifts (time & attendance)
CREATE TABLE `ta_shifts` (
  `shift_name`, `start_time`, `end_time`, `break_duration`
  -- No location context
);

-- Holidays (time & attendance)
CREATE TABLE `ta_holidays` (
  `holiday_name`, `holiday_date`, `is_recurring`
  -- No location field
);

-- Holiday Sync Log
CREATE TABLE `ta_holiday_sync_log` (
  `sync_date`, `country_code`  -- Country-based but not location-based
);
```

---

### **8. UPDATED EMPLOYEES TABLE**
**Comes from**: Every module  
**Core enhancement for**:
- ✅ **All modules** - Better employee data
- ✅ **Workforce Analytics** - Demographics (gender, age_group, nationality)
- ✅ **Time & Attendance** - Manager hierarchy, location
- ✅ **Payroll** - Salary, emergency contact, bank details
- ✅ **Learning & Development** - Qualifications, career path tracking
- ✅ **Exit Management** - Probation dates, retirement eligibility
- ✅ **Performance** - Manager assignments for reviews
- ✅ **Compliance** - Manager verification workflows

**Current Database**: 
```sql
CREATE TABLE `employees` (
  `employee_id`, `full_name`, `address`, `contact_number`, `email`,
  `department`, `position`, `date_hired`, `employment_status`,
  `created_at`, `updated_at`, `user_id`
  -- Missing: salary, gender, DOB, manager_id, location, etc.
);
```

**New Fields to Add**:
```
department_id          → Link to DEPARTMENTS table
position_id            → Link to POSITIONS table
employment_type_id     → Link to EMPLOYMENT_TYPES table
location_id            → Link to LOCATIONS table
salary_grade_id        → Link to SALARY_GRADES table
manager_id             → Self-reference to employees (reporting hierarchy)
gender                 → For diversity analytics
date_of_birth          → For age calculations
age_group              → Generated column
marital_status         → For HR records
nationality            → For compliance
base_salary            → For payroll integration
emergency_contact_*    → For safety compliance
probation_end_date     → For HR workflow
confirmation_date      → For HR records
retirement_eligible_date → For HR planning
employee_status        → More granular than employment_status
```

---

## 🗺️ COMPLETE MODULE ECOSYSTEM MAP

```
┌─────────────────────────────────────────────────────────────┐
│                      MASTER TABLES                          │
│  (Departments, Positions, Employment Types, Locations,     │
│   Qualifications, Salary Grades)                           │
└─────────────────┬───────────────────────────────────────────┘
                  │
        ┌─────────┴─────────────────────────┐
        │                                   │
        v                                   v
┌──────────────────────┐          ┌──────────────────────┐
│  CORE EMPLOYEES      │          │   TIME & ATTENDANCE  │
│  (Enhanced with      │          │                      │
│   demographics,      │◄────────►│  - ta_attendance     │
│   salary, manager)   │          │  - ta_shifts         │
└──────────┬───────────┘          │  - ta_holidays       │
           │                      │  - ta_leave_*        │
           │                      │  - ta_*_metrics      │
           │                      └──────────────────────┘
           │
    ┌──────┴──────────────────────────────┐
    │                                     │
    v                                     v
┌──────────────────────┐         ┌──────────────────────┐
│  PAYROLL MODULE      │         │  LEARNING & DEVEL.   │
│                      │         │                      │
│  - Salary calc       │◄───────►│  - lms_courses       │
│  - Payroll reports   │         │  - lms_enrollments   │
│  - Allowances/       │         │  - career_paths      │
│    Deductions        │         │  - competencies      │
└──────────────────────┘         │  - compliance_*      │
           │                     │  - leadership_*      │
           │                     │  - individual_devel* │
           │                     └──────────────────────┘
           │                              │
    ┌──────┴──────────────┬───────────────┘
    │                     │
    v                     v
┌──────────────────────┐  ┌──────────────────────┐
│  WORKFORCE           │  │  PERFORMANCE         │
│  ANALYTICS (WFA)     │  │                      │
│                      │  │  - performance_*     │
│  - Department        │  │  - feedback_360      │
│    Analytics         │  │  - wfa_risk_*        │
│  - Diversity Metrics │  │  - succession_*      │
│  - Risk Assessment   │  │                      │
│  - Salary Analysis   │  │                      │
│  - Attrition Track   │  └──────────────────────┘
└──────────────────────┘

           │
           v
┌──────────────────────────────────────┐
│  EXIT MANAGEMENT                     │
│                                      │
│  - resignations                      │
│  - exit_interviews                   │
│  - knowledge_transfer_*              │
│  - employee_settlements              │
└──────────────────────────────────────┘
```

---

## 📋 DEPENDENCY SUMMARY

| Table | Purpose | Depends On | Feeds Into |
|-------|---------|-----------|-----------|
| DEPARTMENTS | Org structure | - | All modules |
| POSITIONS | Job hierarchy | DEPARTMENTS | All modules |
| EMPLOYMENT_TYPES | Leave/Benefits | - | Payroll, T&A |
| LOCATIONS | Multi-site ops | - | T&A, Payroll |
| QUALIFICATIONS | Skill registry | - | L&D, Recruitment |
| SALARY_GRADES | Comp bands | POSITIONS | Payroll, WFA |
| EMPLOYEE_QUAL. | Qual tracking | EMPLOYEES, QUALIFICATIONS | L&D, Compliance |
| EMPLOYEES (Updated) | Employee master | All above | All modules |

---

## 🎯 WHICH GAPS THIS SOLVES

### Current Issues in Your Database:

1. ❌ **No Department Normalization**
   - Currently: TEXT field in employees
   - Problem: Can't aggregate by department ID
   - Solution: DEPARTMENTS table + foreign key

2. ❌ **No Position Hierarchy**
   - Currently: TEXT field in employees
   - Problem: Can't track career progression
   - Solution: POSITIONS table with level hierarchy

3. ❌ **No Manager Tracking**
   - Currently: No manager_id in employees
   - Problem: Can't build org chart
   - Solution: manager_id self-reference in EMPLOYEES

4. ❌ **No Salary Master**
   - Currently: No standardized salary grades
   - Problem: Can't benchmark compensation
   - Solution: SALARY_GRADES table

5. ❌ **No Location Tracking**
   - Currently: No location table
   - Problem: Can't handle multi-site operations
   - Solution: LOCATIONS table

6. ❌ **Demographics Missing**
   - Currently: No gender, DOB, age_group
   - Problem: Diversity reports incomplete
   - Solution: New columns in EMPLOYEES + GENERATED column

7. ❌ **Employment Type Not Standardized**
   - Currently: Uses employment_status enum
   - Problem: Can't track leave entitlements per type
   - Solution: EMPLOYMENT_TYPES table

---

## ✨ RESULT AFTER IMPLEMENTATION

All these modules will have clean, normalized data:
- ✅ Workforce Analytics - Complete diversity data
- ✅ Time & Attendance - Multi-location, timezone-aware
- ✅ Payroll - Standardized salary grades, allowances
- ✅ Learning & Development - Qualification tracking
- ✅ Performance - Org hierarchy for reviews
- ✅ Exit Management - Manager workflow tracking
- ✅ Recruitment - Position-based candidate matching

