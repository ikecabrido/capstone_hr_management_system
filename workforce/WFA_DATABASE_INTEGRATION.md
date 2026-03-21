# Workforce Analytics (WFA) - Database Integration Guide

## Overview
The Workforce Analytics system integrates seamlessly with the existing HR Management database using the `wfa_` prefix convention for all tables.

---

## Database Schema - 17 Tables

### **Core Analytics Tables**

#### 1. `wfa_employee_metrics` (Daily KPIs)
Tracks real-time employee counts and key performance indicators
```sql
- metric_date (DATE, UNIQUE)
- total_employees, total_teachers, total_staff
- new_hires_this_year
- average_salary, average_performance_score
```
**Purpose:** Dashboard main KPI cards, historical tracking
**Refresh:** Daily (automated via cron job)

---

#### 2. `wfa_department_analytics` (Department Statistics)
Aggregated metrics by department
```sql
- department (VARCHAR 100)
- employee_count, average_salary, average_performance_score
- headcount_target, vacancy_count
- average_tenure_years
- metric_date (DATE)
```
**Purpose:** Department-level comparisons, planning data
**Unique Key:** (department, metric_date)

---

#### 3. `wfa_attrition_tracking` (Separation Records)
Individual employee separation tracking
```sql
- employee_id (FK → employees.employee_id)
- separation_date, separation_type (resigned/retired/terminated)
- department, tenure_years
- reason_for_leaving (TEXT)
- exit_interview_completed, rehire_eligible
```
**Purpose:** Track individual departures, calculate turnover rates
**Indexes:** separation_date, separation_type, employee_id

---

#### 4. `wfa_monthly_attrition` (Turnover Trends)
Monthly aggregation of attrition data
```sql
- year_month (DATE, UNIQUE)
- total_separations, voluntary_separations, involuntary_separations
- attrition_rate_percent
- average_tenure_departing
```
**Purpose:** Trend analysis, visualization
**Refresh:** Monthly

---

#### 5. `wfa_diversity_metrics` (Diversity Analytics)
Gender, age, and department diversity tracking
```sql
- metric_date (DATE)
- diversity_category (gender, age_group, department)
- category_value (Male/Female/Other, age range, dept name)
- employee_count, percentage
- average_salary, average_performance
```
**Purpose:** D&I reporting, compliance tracking
**Unique Key:** (metric_date, diversity_category, category_value)

---

#### 6. `wfa_risk_assessment` (At-Risk Employees)
Predictive analytics for employee attrition risk
```sql
- employee_id (FK)
- risk_level (high/medium/low)
- risk_score (0-100)
- risk_factors (JSON array)
- low_performance_flag, high_absence_flag
- performance_score, absence_days, tenure_months
```
**Purpose:** Identify retention risks, intervention planning
**Indexes:** employee_id, risk_level, risk_score

---

#### 7. `wfa_performance_distribution` (Performance Levels)
Distribution of employees across performance ratings
```sql
- metric_date (DATE)
- performance_level (Excellent/Good/Average/Below Average/Poor)
- score_range_min, score_range_max
- employee_count, percentage
- department_breakdown (JSON)
```
**Purpose:** Performance analytics, bell curve analysis
**Unique Key:** (metric_date, performance_level)

---

#### 8. `wfa_salary_statistics` (Compensation Analysis)
Salary metrics by department
```sql
- metric_date (DATE)
- department (VARCHAR 100)
- employee_count
- min_salary, max_salary, average_salary, median_salary
- total_payroll, salary_variance
```
**Purpose:** Compensation planning, equity analysis
**Unique Key:** (metric_date, department)

---

#### 9. `wfa_tenure_analysis` (Employee Tenure)
Tenure distribution brackets
```sql
- metric_date (DATE)
- tenure_bracket (0-1yr/1-3yr/3-5yr/5-10yr/10+yr)
- employee_count, percentage
- average_salary, average_performance_score
- department_breakdown (JSON)
```
**Purpose:** Experience distribution, retention insights
**Unique Key:** (metric_date, tenure_bracket)

---

#### 10. `wfa_age_distribution` (Age Demographics)
Age group distribution
```sql
- metric_date (DATE)
- age_group (18-25/26-35/36-45/46-55/56+)
- employee_count, percentage
- average_salary, average_performance_score
- department_breakdown (JSON)
```
**Purpose:** Demographic analysis, succession planning
**Unique Key:** (metric_date, age_group)

---

#### 11. `wfa_gender_distribution` (Gender Demographics)
Gender diversity metrics
```sql
- metric_date (DATE)
- gender (Male/Female/Other)
- employee_count, percentage
- average_salary, average_performance_score
- department_breakdown (JSON)
- position_breakdown (JSON)
```
**Purpose:** Gender pay gap analysis, D&I metrics
**Unique Key:** (metric_date, gender)

---

#### 12. `wfa_reports` (Report Snapshots)
History of generated reports
```sql
- report_name (VARCHAR 255)
- report_type (dashboard/attrition/diversity/performance/salary/custom)
- report_date (DATE)
- generated_by (INT user_id)
- filters_applied (JSON)
- report_data (LONGTEXT JSON snapshot)
- file_path, export_format (CSV/PDF/Excel)
```
**Purpose:** Report audit trail, historical snapshots
**Indexes:** report_type, report_date, generated_by

---

#### 13. `wfa_custom_filters` (Saved Filters)
Saved filter configurations for reports
```sql
- filter_name (VARCHAR 255)
- user_id (INT)
- filter_config (JSON: department, employment_type, date_range)
- is_public (BOOLEAN)
```
**Purpose:** User-specific report configurations
**Indexes:** user_id, filter_name

---

#### 14. `wfa_audit_log` (System Audit Trail)
Track all analytics system activities
```sql
- user_id (INT)
- action (view_report/generate_report/export_data/update_filter)
- resource_type (report/filter/metric)
- resource_id (INT)
- details (TEXT)
- ip_address (VARCHAR 45)
```
**Purpose:** Compliance, security, usage tracking

---

#### 15. `wfa_headcount_planning` (Headcount Forecast)
Planned vs. actual headcount by department
```sql
- department (VARCHAR 100)
- fiscal_year (YEAR)
- planned_headcount, actual_headcount, variance
- planned_salary_budget, actual_salary_budget, budget_variance
```
**Purpose:** HR planning, budget forecasting
**Unique Key:** (department, fiscal_year)

---

#### 16. `wfa_skill_gap_analysis` (Competency Gaps)
Skill requirements vs. current proficiency
```sql
- department (VARCHAR 100)
- skill_name (VARCHAR 255)
- required_proficiency (Basic/Intermediate/Advanced/Expert)
- current_proficiency_avg
- employees_with_skill, employees_needing_training
- skill_gap_percentage, priority_level (critical/high/medium/low)
- training_recommendations (TEXT)
```
**Purpose:** L&D planning, capability assessment
**Unique Key:** (department, skill_name)

---

#### 17. `wfa_compensation_analysis` (Salary Competitiveness)
Market salary comparison
```sql
- department (VARCHAR 100)
- position (VARCHAR 100)
- current_avg_salary, market_median_salary
- salary_competitiveness_ratio (Current/Market %)
- employee_count
- salary_range_min, salary_range_max
- recommended_adjustment
- last_market_review (DATE)
```
**Purpose:** Compensation planning, market alignment
**Unique Key:** (department, position)

---

## Database Views (Ready-to-Use Queries)

### 1. `vw_current_employees_by_dept`
Current active employees and performance by department
```sql
SELECT department, employee_count, avg_performance_score
FROM vw_current_employees_by_dept;
```

### 2. `vw_at_risk_employees_summary`
Summary of at-risk employees by risk level
```sql
SELECT risk_level, count, avg_risk_score, percentage
FROM vw_at_risk_employees_summary;
```

### 3. `vw_department_diversity`
Gender and age diversity by department
```sql
SELECT department, male_count, female_count, age_groups_represented
FROM vw_department_diversity;
```

---

## Data Refresh Strategy

### **Daily Updates (Automated)**
- `wfa_employee_metrics` - Run daily at 11:59 PM
- `wfa_diversity_metrics` - Run daily
- `wfa_risk_assessment` - Run daily

### **Monthly Updates (Scheduled)**
- `wfa_monthly_attrition` - Run on 1st of each month
- `wfa_department_analytics` - Run on 1st of each month
- `wfa_performance_distribution` - Run on 1st of each month
- `wfa_salary_statistics` - Run on 1st of each month
- `wfa_tenure_analysis` - Run on 1st of each month
- `wfa_age_distribution` - Run on 1st of each month
- `wfa_gender_distribution` - Run on 1st of each month

### **On-Demand**
- `wfa_reports` - Generated when user exports/saves
- `wfa_skill_gap_analysis` - Quarterly review
- `wfa_compensation_analysis` - Annual review or ad-hoc
- `wfa_headcount_planning` - Fiscal year planning

---

## Integration with Existing Tables

### Foreign Key Relationships

```
wfa_attrition_tracking → employees(employee_id)
wfa_risk_assessment → employees(employee_id)
wfa_reports → users(id) [for generated_by]
wfa_custom_filters → users(id) [for user_id]
wfa_audit_log → users(id) [for user_id]
wfa_skill_gap_analysis → competencies(skill_name) [if available]
```

### Data Source Mapping

| WFA Table | Source Table(s) | Key Field |
|-----------|-----------------|-----------|
| wfa_employee_metrics | employees | employee_id |
| wfa_department_analytics | employees | department |
| wfa_attrition_tracking | exit_interviews, knowledge_transfer_plans | employee_id |
| wfa_risk_assessment | performance_reviews | employee_id |
| wfa_performance_distribution | performance_reviews | rating |
| wfa_salary_statistics | employees | department |
| wfa_tenure_analysis | employees | date_hired |
| wfa_age_distribution | employees | (calculated from DOB if available) |
| wfa_gender_distribution | employees | gender |
| wfa_skill_gap_analysis | competencies, performance_reviews | skill_name |
| wfa_compensation_analysis | employees | position |

---

## SQL Installation

1. **Import the schema file:**
```bash
mysql -u root -p hr_management < wfa_schema.sql
```

2. **Verify tables created:**
```sql
SHOW TABLES LIKE 'wfa_%';
```

3. **Check table counts:**
```sql
SELECT TABLE_NAME, TABLE_ROWS 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = 'hr_management' 
AND TABLE_NAME LIKE 'wfa_%'
ORDER BY TABLE_NAME;
```

---

## Data Population Scripts

### Sample: Initialize wfa_employee_metrics
```php
// PHP helper to calculate and insert daily metrics
$calculator = new MetricsCalculator();
$metrics = $calculator->calculateDailyMetrics();
$analytics->insertEmployeeMetrics($metrics);
```

### Sample: Calculate Risk Assessment
```php
$employees = $employeeModel->getAllEmployees();
foreach ($employees as $emp) {
    $riskScore = RiskCalculator::calculateRisk(
        $emp['performance_score'],
        $emp['absence_days'],
        $emp['tenure_months'],
        $emp['engagement_level']
    );
    $analytics->updateRiskAssessment($emp['employee_id'], $riskScore);
}
```

---

## Query Examples

### Find high-risk employees
```sql
SELECT employee_id, risk_score, risk_level, risk_factors
FROM wfa_risk_assessment
WHERE risk_level = 'high'
AND DATE(updated_at) = CURDATE()
ORDER BY risk_score DESC;
```

### Monthly attrition rate
```sql
SELECT year_month, attrition_rate_percent, total_separations
FROM wfa_monthly_attrition
ORDER BY year_month DESC
LIMIT 12;
```

### Department salary comparison
```sql
SELECT department, 
       COUNT(*) as emp_count,
       ROUND(AVG(avg_salary), 2) as dept_avg,
       (SELECT AVG(avg_salary) FROM wfa_salary_statistics) as overall_avg
FROM wfa_salary_statistics
WHERE metric_date = CURDATE()
GROUP BY department
ORDER BY dept_avg DESC;
```

### Gender pay gap by department
```sql
SELECT department,
       SUM(CASE WHEN gender='Male' THEN employee_count ELSE 0 END) as male_count,
       SUM(CASE WHEN gender='Female' THEN employee_count ELSE 0 END) as female_count,
       ROUND(
           (SELECT AVG(average_salary) 
            FROM wfa_gender_distribution 
            WHERE gender='Male' AND metric_date=CURDATE()) / 
           (SELECT AVG(average_salary) 
            FROM wfa_gender_distribution 
            WHERE gender='Female' AND metric_date=CURDATE()) * 100, 2
       ) as male_to_female_ratio
FROM wfa_diversity_metrics
WHERE metric_date = CURDATE()
AND diversity_category = 'gender'
GROUP BY department;
```

---

## Performance Optimization

### Recommended Indexes (Already Included)
- `wfa_employee_metrics(metric_date)`
- `wfa_department_analytics(metric_date, department)`
- `wfa_risk_assessment(risk_level, updated_at)`
- `wfa_attrition_tracking(separation_date, separation_type)`
- All UNIQUE constraints enforce index creation

### Composite Indexes for Common Queries
```sql
CREATE INDEX idx_wfa_metrics_range 
ON wfa_employee_metrics(metric_date, total_employees, average_salary);

CREATE INDEX idx_wfa_diversity_analysis 
ON wfa_diversity_metrics(metric_date, diversity_category, category_value);
```

---

## Security & Compliance

### User Permissions
```sql
-- HR Administrator
GRANT ALL PRIVILEGES ON hr_management.wfa_* TO 'hr_admin'@'localhost';

-- HR Manager (Read-only)
GRANT SELECT ON hr_management.wfa_* TO 'hr_manager'@'localhost';

-- Department Head (View own department only)
GRANT SELECT ON hr_management.wfa_department_analytics TO 'dept_head'@'localhost';
```

### Audit Trail
All modifications to wfa_ tables are logged to `wfa_audit_log` with:
- User ID
- Action performed
- Timestamp
- IP Address
- Resource details

---

## Naming Convention Summary

| Prefix | Purpose | Example |
|--------|---------|---------|
| `wfa_` | Workforce Analytics core tables | wfa_employee_metrics |
| `vw_` | Database views (analytics) | vw_current_employees_by_dept |
| `idx_` | Index names | idx_wfa_metrics_date_dept |

---

## Backup & Recovery

### Backup WFA data only
```bash
mysqldump -u root -p hr_management wfa_* > wfa_backup_$(date +%Y%m%d).sql
```

### Restore from backup
```bash
mysql -u root -p hr_management < wfa_backup_20260321.sql
```

---

## Support & Troubleshooting

### Check data staleness
```sql
SELECT MAX(updated_at) as last_update, COUNT(*) as total_records
FROM wfa_employee_metrics;
```

### Verify foreign key integrity
```sql
SELECT * FROM wfa_attrition_tracking 
WHERE employee_id NOT IN (SELECT employee_id FROM employees);
```

### Monitor table sizes
```sql
SELECT TABLE_NAME, 
       ROUND(((data_length + index_length) / 1024 / 1024), 2) as 'Size in MB'
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = 'hr_management'
AND TABLE_NAME LIKE 'wfa_%'
ORDER BY (data_length + index_length) DESC;
```

---

**Last Updated:** March 21, 2026  
**Version:** 1.0.0  
**Status:** Production Ready
