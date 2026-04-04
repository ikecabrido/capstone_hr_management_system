# WFA (Workforce Analytics) Tables - Quick Reference

## 📊 All 17 Tables Overview

| # | Table Name | Purpose | Records | Refresh |
|---|------------|---------|---------|---------|
| 1 | `wfa_employee_metrics` | Daily KPIs snapshot | 1 per day | Daily 11:59 PM |
| 2 | `wfa_department_analytics` | Department-level stats | 1 per dept/day | Monthly |
| 3 | `wfa_attrition_tracking` | Individual separations | As they occur | Real-time |
| 4 | `wfa_monthly_attrition` | Monthly turnover trends | 1 per month | Monthly |
| 5 | `wfa_diversity_metrics` | Gender/age/dept diversity | Multiple/day | Daily |
| 6 | `wfa_risk_assessment` | At-risk employee scoring | 1 per employee | Daily |
| 7 | `wfa_performance_distribution` | Performance level distribution | 7 records/day | Daily |
| 8 | `wfa_salary_statistics` | Compensation by department | 1 per dept/day | Monthly |
| 9 | `wfa_tenure_analysis` | Tenure bracket distribution | 5 records/day | Monthly |
| 10 | `wfa_age_distribution` | Age group demographics | 5 records/day | Monthly |
| 11 | `wfa_gender_distribution` | Gender demographics | 3 records/day | Monthly |
| 12 | `wfa_reports` | Report audit trail | As generated | Real-time |
| 13 | `wfa_custom_filters` | User-saved filters | Per user | Real-time |
| 14 | `wfa_audit_log` | System activity log | Per action | Real-time |
| 15 | `wfa_headcount_planning` | Headcount budget plan | 1 per dept/year | Annual |
| 16 | `wfa_skill_gap_analysis` | Skill deficiencies | 1 per skill/dept | Quarterly |
| 17 | `wfa_compensation_analysis` | Salary competitiveness | 1 per position/dept | Annual |

---

## 🔑 Key Fields by Table

### `wfa_employee_metrics`
```
metric_date (DATE) - KEY
total_employees, total_teachers, total_staff (INT)
new_hires_this_year (INT)
average_salary, average_performance_score (DECIMAL)
```

### `wfa_risk_assessment`
```
employee_id (VARCHAR) - FK
risk_level (ENUM: high/medium/low) - KEY
risk_score (0-100) - KEY
performance_score, absence_days, tenure_months
```

### `wfa_attrition_tracking`
```
employee_id (VARCHAR) - FK
separation_date, separation_type - KEY
department, tenure_years
reason_for_leaving (TEXT)
```

### `wfa_department_analytics`
```
department (VARCHAR) - KEY
metric_date (DATE) - KEY
employee_count, average_salary
average_performance_score, vacancy_count
```

### `wfa_diversity_metrics`
```
metric_date, diversity_category, category_value - UNIQUE
employee_count, percentage
average_salary, average_performance
```

---

## 📈 Data Population Strategy

### Daily Automated (11:59 PM)
```php
$helper = new WFADatabaseHelper($pdo);

// 1. Calculate and insert employee metrics
$metrics = calculateDailyMetrics();
$helper->insertEmployeeMetrics($metrics);

// 2. Update risk assessments for all employees
$employees = getActiveEmployees();
foreach ($employees as $emp) {
    $riskData = calculateRisk($emp);
    $helper->updateRiskAssessment($emp['id'], $riskData);
}

// 3. Update diversity metrics
updateDiversityMetrics($helper);
```

### Monthly Automated (1st of month)
```php
// 1. Aggregate department analytics
$departments = getDepartments();
foreach ($departments as $dept) {
    $analytics = calculateDeptAnalytics($dept);
    $helper->insertDepartmentAnalytics($dept, $analytics);
}

// 2. Calculate monthly attrition
$attrition = calculateMonthlyAttrition();
insertMonthlyAttrition($attrition);

// 3. Performance distribution
$perfDist = calculatePerformanceDistribution();
// Insert for each performance level
```

### On-Demand
```php
// When employee exits
$helper->recordAttrition($employee_id, $separationData);

// When report is generated
$helper->saveReportSnapshot($name, $type, $userId, $filters, $data);

// When user saves filter
$helper->saveCustomFilter($userId, $filterName, $config);

// Log all actions
$helper->logAuditAction($userId, 'action_name', 'resource_type');
```

---

## 🔍 Common Queries

### Get Dashboard KPIs
```php
$metrics = $helper->getDashboardMetrics(date('Y-m-d'));
echo "Total Employees: " . $metrics['total_employees'];
echo "Avg Performance: " . $metrics['average_performance_score'];
```

### Find At-Risk Employees
```php
$riskEmployees = $helper->getHighRiskEmployees(15);
foreach ($riskEmployees as $emp) {
    echo $emp['employee_id'] . " - Risk: " . $emp['risk_level'];
}
```

### Department Comparison
```php
$deptAnalytics = $helper->getDepartmentAnalytics();
// Returns array of all departments with counts, salaries, etc.
```

### Attrition Metrics
```php
$metrics = $helper->getAttritionMetrics('2026-01-01', '2026-03-21');
// Returns separations by type with counts and percentages
```

---

## 🛠️ Implementation Checklist

- [ ] Run SQL schema file: `wfa_schema.sql`
- [ ] Verify 17 tables created: `SHOW TABLES LIKE 'wfa_%'`
- [ ] Copy `WFADatabaseHelper.php` to config folder
- [ ] Set up daily metrics cron job
- [ ] Set up monthly aggregation cron job
- [ ] Update existing pages to use WFA data
- [ ] Configure user permissions (SEE BELOW)
- [ ] Test API endpoints with sample data

---

## 🔐 User Permissions

```sql
-- HR Administrator (Full Access)
GRANT ALL PRIVILEGES ON hr_management.wfa_* 
TO 'hr_admin'@'localhost';

-- HR Manager (Read Only)
GRANT SELECT ON hr_management.wfa_* 
TO 'hr_manager'@'localhost';

-- Finance/Compensation Analyst
GRANT SELECT ON hr_management.wfa_salary_statistics 
TO 'finance'@'localhost';
GRANT SELECT ON hr_management.wfa_compensation_analysis 
TO 'finance'@'localhost';

-- L&D Coordinator
GRANT SELECT ON hr_management.wfa_skill_gap_analysis 
TO 'learning_dev'@'localhost';
```

---

## 📋 Usage Examples

### Initialize System for First Time
```php
<?php
require_once 'config/WFADatabaseHelper.php';

$pdo = new PDO('mysql:host=localhost;dbname=hr_management', 'root', '');
$helper = new WFADatabaseHelper($pdo);

// Check if tables exist
if ($helper->tablesExist()) {
    echo "✓ WFA tables ready!";
    
    // Get statistics
    $stats = $helper->getTableStatistics();
    print_r($stats);
} else {
    echo "✗ WFA tables not found. Run wfa_schema.sql first.";
}
?>
```

### Generate Daily Metrics Report
```php
<?php
require_once 'config/WFADatabaseHelper.php';

$pdo = new PDO('mysql:host=localhost;dbname=hr_management', 'root', '');
$helper = new WFADatabaseHelper($pdo);

$metrics = $helper->getDashboardMetrics();

$report = [
    'date' => $metrics['metric_date'],
    'total_employees' => $metrics['total_employees'],
    'avg_salary' => $metrics['average_salary'],
    'avg_performance' => $metrics['average_performance_score'],
    'new_hires_ytd' => $metrics['new_hires_this_year']
];

$helper->saveReportSnapshot(
    'Daily Metrics Report',
    'dashboard',
    $_SESSION['user_id'],
    [],
    $report
);

echo json_encode($report);
?>
```

### Track Employee Separation
```php
<?php
$helper->recordAttrition('EMP001', [
    'separation_date' => '2026-03-20',
    'separation_type' => 'resigned',
    'department' => 'IT',
    'tenure_years' => 3,
    'reason_for_leaving' => 'Pursuing higher education',
    'exit_interview_completed' => true,
    'rehire_eligible' => true
]);

$helper->logAuditAction(
    $_SESSION['user_id'],
    'record_separation',
    'attrition',
    1,
    'Recorded separation for EMP001'
);
?>
```

### Get Department Risk Summary
```php
<?php
$riskEmployees = $helper->getHighRiskEmployees(100);
$riskByDept = [];

foreach ($riskEmployees as $emp) {
    $dept = $emp['department'];
    if (!isset($riskByDept[$dept])) {
        $riskByDept[$dept] = ['high' => 0, 'medium' => 0];
    }
    $riskByDept[$dept][$emp['risk_level']]++;
}

echo json_encode($riskByDept);
?>
```

---

## 🔧 Maintenance Commands

### Backup WFA Tables
```bash
mysqldump -u root -p hr_management wfa_* > wfa_backup_$(date +%Y%m%d_%H%M%S).sql
```

### Check Table Health
```sql
-- Find stale data
SELECT TABLE_NAME, MAX(updated_at) as last_update
FROM information_schema.TABLES t
LEFT JOIN wfa_employee_metrics m ON 1=1
WHERE TABLE_SCHEMA = 'hr_management'
AND TABLE_NAME LIKE 'wfa_%'
GROUP BY TABLE_NAME;

-- Check for orphaned records
SELECT COUNT(*) as orphaned
FROM wfa_attrition_tracking
WHERE employee_id NOT IN (SELECT employee_id FROM employees);

-- Monitor growth
SELECT 
    TABLE_NAME,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) as size_mb,
    TABLE_ROWS as record_count
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = 'hr_management'
AND TABLE_NAME LIKE 'wfa_%'
ORDER BY (data_length + index_length) DESC;
```

---

## 🚀 Next Steps

1. **Install Schema**: Run `wfa_schema.sql` in MySQL
2. **Verify Tables**: Check all 17 tables created
3. **Copy Helper**: Move `WFADatabaseHelper.php` to config folder
4. **Test Connection**: Run initialization script above
5. **Set Permissions**: Configure database user roles
6. **Schedule Jobs**: Set up cron jobs for daily/monthly updates
7. **Create API**: Build endpoints that use `$helper` methods
8. **Update Pages**: Modify existing pages to display WFA data

---

**Last Updated:** March 21, 2026  
**Status:** Production Ready  
**Tables:** 17 | **Views:** 3 | **Indexes:** 15+
