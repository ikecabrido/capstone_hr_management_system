# Enhanced Attendance Metrics System - Implementation Guide

## Overview

This document outlines the newly implemented attendance metrics tracking system that includes:

1. **Late Minutes Tracking** - Exact minutes an employee was late
2. **Punctuality Score** - Calculated performance score (0-100)
3. **Attendance Rate** - Percentage of days present
4. **Absence Rate** - Percentage of days absent
5. **Overtime Frequency** - How often overtime occurs (LOW/MODERATE/HIGH/CRITICAL)
6. **Overall Performance Score** - Weighted composite score

## Database Schema

### New Tables Created

#### 1. `ta_punctuality_scores`
Stores monthly punctuality scores for each employee.

```sql
- score_id: INT PRIMARY KEY
- employee_id: VARCHAR(50)
- month_year: VARCHAR(7) - Format: YYYY-MM
- total_late_incidents: INT
- total_late_minutes: INT
- average_late_minutes: DECIMAL(5,2)
- punctuality_score: DECIMAL(5,2) - 0-100
- punctuality_grade: ENUM(A,B,C,D,F)
- score_breakdown: JSON - Detailed calculation breakdown
```

**Grading System:**
- **A (90+)**: Excellent punctuality
- **B (80-89)**: Good punctuality
- **C (70-79)**: Acceptable punctuality
- **D (60-69)**: Poor punctuality
- **F (<60)**: Serious punctuality issues

#### 2. `ta_overtime_tracking`
Records individual overtime events with approval status.

```sql
- tracking_id: INT PRIMARY KEY
- employee_id: VARCHAR(50)
- attendance_id: INT
- overtime_date: DATE
- overtime_hours: DECIMAL(5,2)
- overtime_minutes: INT
- reason_category: ENUM(PROJECT_DEADLINE, STAFFING_SHORTAGE, WORKLOAD_HEAVY, SHIFT_CHANGE, VOLUNTARY, OTHER)
- reason_notes: TEXT
- approved: BOOLEAN
- approved_by: INT
- approval_date: DATETIME
```

#### 3. `ta_overtime_frequency`
Monthly summary of overtime frequency and patterns.

```sql
- frequency_id: INT PRIMARY KEY
- employee_id: VARCHAR(50)
- month_year: VARCHAR(7)
- overtime_instances: INT - How many times overtime occurred
- total_overtime_hours: DECIMAL(8,2)
- average_overtime_per_instance: DECIMAL(5,2)
- max_overtime_in_single_day: DECIMAL(5,2)
- overtime_frequency_rating: ENUM(LOW, MODERATE, HIGH, CRITICAL)
- approved_overtime_hours: DECIMAL(8,2)
- unapproved_overtime_hours: DECIMAL(8,2)
```

**Frequency Ratings:**
- **LOW**: 0-2 instances per month
- **MODERATE**: 3-5 instances per month
- **HIGH**: 6-9 instances per month
- **CRITICAL**: 10+ instances per month (requires review)

#### 4. `ta_attendance_metrics`
Comprehensive monthly metrics for attendance performance.

```sql
- metric_id: INT PRIMARY KEY
- employee_id: VARCHAR(50)
- month_year: VARCHAR(7)
- attendance_rate: DECIMAL(5,2) - Percentage
- absence_rate: DECIMAL(5,2) - Percentage
- punctuality_score: DECIMAL(5,2)
- overtime_frequency_rating: VARCHAR(20)
- overall_performance_score: DECIMAL(5,2) - Weighted score
- total_present_days: INT
- total_absent_days: INT
- total_late_incidents: INT
- total_overtime_hours: DECIMAL(8,2)
- status_summary: JSON
```

### Updated Tables

#### `ta_attendance`
New columns added:
```sql
- late_minutes: INT - Number of minutes employee was late (0 if on time)
- early_out_minutes: INT - Number of minutes employee left early
- shift_minutes: INT - Expected shift duration in minutes
```

#### `ta_absence_late_records`
New column added:
```sql
- late_minutes: INT - Minutes late for this specific incident
```

## Calculation Formulas

### 1. Punctuality Score (0-100)

```
Base Score: 100
Deductions:
  - Per late incident: -5 points
  - Per severe late (>30 min): -10 points (additional)
  - Per 5 minutes average: -1 point

Formula: Score = MAX(0, 100 - (late_incidents * 5) - (severe_late_count * 10) - floor(avg_late_minutes / 5))
```

**Example:**
- 2 late incidents, 20 minutes average late
- Score = 100 - (2 × 5) - (0 × 10) - floor(20/5)
- Score = 100 - 10 - 0 - 4 = **86 (Grade B)**

### 2. Attendance Rate

```
Attendance Rate = (Present Days / Total Working Days) × 100
```

### 3. Absence Rate

```
Absence Rate = (Absent Days / Total Working Days) × 100
```

### 4. Overall Performance Score (Weighted)

```
Overall Score = (Attendance Rate × 0.40) + (Punctuality Score × 0.35) + ((100 - Absence Rate) × 0.25)

Weights:
- Attendance: 40%
- Punctuality: 35%
- Absence Prevention: 25%
```

## API Endpoints

### Location
`/time_attendance/app/api/metrics.php`

### Available Endpoints

#### 1. Calculate Punctuality Score
```
GET/POST: ?action=calculate_punctuality&employee_id=EMP001&month_year=2026-03
```
Returns: Punctuality score, grade, breakdown

#### 2. Calculate Overtime Frequency
```
GET/POST: ?action=calculate_overtime_frequency&employee_id=EMP001&month_year=2026-03
```
Returns: Frequency rating, instances, hours

#### 3. Calculate All Metrics
```
GET/POST: ?action=calculate_all_metrics&employee_id=EMP001&month_year=2026-03
```
Returns: Comprehensive metrics (attendance, punctuality, overtime)

#### 4. Get Punctuality Score
```
GET: ?action=get_punctuality_score&employee_id=EMP001&month_year=2026-03
```
Returns: Stored punctuality score data

#### 5. Get Overtime Frequency
```
GET: ?action=get_overtime_frequency&employee_id=EMP001&month_year=2026-03
```
Returns: Stored overtime frequency data

#### 6. Get Attendance Metrics
```
GET: ?action=get_attendance_metrics&employee_id=EMP001&month_year=2026-03
```
Returns: Stored attendance metrics

#### 7. Record Late Minutes
```
POST: ?action=record_late_minutes
Body: attendance_id=123, late_minutes=15
```
Stores the number of minutes an employee was late

#### 8. Record Overtime Event
```
POST: ?action=record_overtime_event
Body: 
  - employee_id: EMP001
  - attendance_id: 123
  - overtime_hours: 2.5
  - reason_category: PROJECT_DEADLINE
  - reason_notes: "Project deadline extension"
```
Records an overtime event

#### 9. Get Employee Metrics Dashboard
```
GET: ?action=get_employee_metrics_dashboard&employee_id=EMP001&month_year=2026-03
```
Returns: Complete dashboard data with all metrics

## Helper Class: MetricsCalculator

Located: `/time_attendance/app/helpers/MetricsCalculator.php`

### Methods

```php
// Calculate punctuality score for a month
calculatePunctualityScore(string $employeeId, string $monthYear): array

// Calculate overtime frequency for a month
calculateOvertimeFrequency(string $employeeId, string $monthYear): array

// Calculate comprehensive attendance metrics
calculateAttendanceMetrics(string $employeeId, string $monthYear): array

// Record late minutes for an attendance record
recordLateMinutes(int $attendanceId, int $lateMinutes): array

// Record overtime event
recordOvertimeEvent(string $employeeId, int $attendanceId, float $overtimeHours, string $categoryKey, string $notes): array

// Retrieve stored punctuality score
getPunctualityScore(string $employeeId, string $monthYear): array

// Retrieve stored overtime frequency
getOvertimeFrequency(string $employeeId, string $monthYear): array

// Retrieve stored attendance metrics
getAttendanceMetrics(string $employeeId, string $monthYear): array
```

## Integration Points

### 1. Employee Dashboard
File: `/time_attendance/public/employee_dashboard.php`

**Display:**
- Attendance Rate (%)
- Absence Rate (%)
- Punctuality Score (/100)
- Overall Performance Score (/100)
- Late Incidents (count)
- Overtime Frequency (rating)

### 2. Data Export
File: `/time_attendance/public/export_dashboard.php`

**Includes:**
- New metrics in JSON response
- Metrics in Excel export

### 3. Attendance Controller
File: `/time_attendance/app/controllers/AttendanceController.php`

**Integration:**
- Late minutes calculation on timeIn
- Overtime minutes recording on timeOut

## Data Collection Strategy

### Late Minutes
Calculated automatically when employee time-ins are recorded:
- Compares actual time-in against shift start time
- Stores difference in `late_minutes` column
- Updated whenever attendance status is processed

### Overtime Minutes
Recorded when:
- Employee's total_hours_worked > shift hours
- Overtime hours are calculated as: total_hours - shift_hours
- Converted to minutes and stored

### Metrics Aggregation
Happens monthly:
- Calculated from raw attendance data
- Stored in metrics tables for fast retrieval
- Updated whenever attendance record is modified

## Usage Examples

### Example 1: Get Employee Dashboard Metrics
```php
require_once 'app/helpers/MetricsCalculator.php';
$db = new Database();
$calculator = new MetricsCalculator($db->getConnection());

$metrics = $calculator->calculateAttendanceMetrics('EMP001', '2026-03');

echo "Attendance Rate: " . $metrics['attendance_rate'] . "%";
echo "Punctuality Score: " . $metrics['punctuality_score'] . "/100";
echo "Performance: " . $metrics['overall_performance_score'] . "/100";
```

### Example 2: Record Overtime Event
```php
$result = $calculator->recordOvertimeEvent(
    'EMP001',
    123,
    2.5,
    'PROJECT_DEADLINE',
    'Project XYZ deadline extension'
);
```

### Example 3: JavaScript Fetch in Dashboard
```javascript
async function loadMetrics(employeeId) {
    const response = await fetch(`../app/api/metrics.php?action=get_employee_metrics_dashboard&employee_id=${employeeId}`);
    const data = await response.json();
    
    if (data.success) {
        document.getElementById('attendance-rate').textContent = data.dashboard.attendance_rate + '%';
        document.getElementById('punctuality-score').textContent = data.dashboard.punctuality_score + '/100';
        document.getElementById('performance-score').textContent = data.dashboard.overall_performance_score + '/100';
    }
}
```

## Installation Steps

### 1. Database Migration
Run the migration file to create new tables:
```bash
mysql -u user -p database < migrations/003_add_metrics_tracking.sql
```

### 2. Update Attendance Processing
Ensure late_minutes are calculated during attendance recording:
- Modify timeIn/timeOut methods to call metric calculations
- Update shift comparison logic

### 3. Integration Testing
- Test punctuality score calculation
- Verify overtime frequency tracking
- Validate dashboard display
- Check API responses

## Performance Considerations

### Optimization Tips
1. Index frequently queried columns (employee_id, month_year)
2. Archive old metrics data annually
3. Use MATERIALIZED VIEWS for complex calculations
4. Cache metrics for current month in session

### Monitoring Metrics Tables
```sql
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) as size_mb
FROM information_schema.TABLES
WHERE table_name IN ('ta_punctuality_scores', 'ta_overtime_tracking', 'ta_overtime_frequency', 'ta_attendance_metrics');
```

## Troubleshooting

### Issue: Punctuality scores not calculating
- Ensure late_minutes column is populated in ta_attendance
- Check that attendance_date is set correctly
- Verify employee_id format matches

### Issue: Overtime frequency shows zero
- Ensure ta_overtime_tracking records are being created
- Check overtime_date format (should be YYYY-MM-DD)
- Verify overtime_hours are > 0

### Issue: Dashboard metrics not showing
- Clear browser cache
- Verify API endpoint is accessible
- Check database connection in MetricsCalculator
- Review browser console for errors

## Future Enhancements

1. **Predictive Analytics** - Forecast future punctuality trends
2. **Customizable Thresholds** - Allow HR to set custom scoring weights
3. **Team-Level Metrics** - Department and company-wide metrics
4. **Alerts & Notifications** - Auto-notify when employee exceeds thresholds
5. **Mobile API** - REST endpoints for mobile app integration
6. **Batch Calculations** - Calculate all employees' metrics in one run

## Related Files

- Database: `/time_attendance/migrations/003_add_metrics_tracking.sql`
- Helper: `/time_attendance/app/helpers/MetricsCalculator.php`
- API: `/time_attendance/app/api/metrics.php`
- Dashboard: `/time_attendance/public/employee_dashboard.php`
- Export: `/time_attendance/public/export_dashboard.php`

---

**Last Updated:** March 20, 2026
**Version:** 1.0
**Status:** Ready for Implementation
