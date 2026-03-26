# Attendance Metrics - Quick Reference

## What's Tracked

### 1. Late Minutes ⏱️
**Definition:** Exact number of minutes employee arrived after their scheduled shift start time

**Where it's stored:** `ta_attendance.late_minutes`

**When it's calculated:** During time-in processing, automatically compared against shift start time

**Visibility:** Employee Dashboard

---

### 2. Punctuality Score 📊
**Definition:** Monthly performance score (0-100) based on late incidents and severity

**Formula:**
```
Score = 100 - (late_incidents × 5) - (severe_late × 10) - (avg_late_minutes ÷ 5)
```

**Grades:**
| Score | Grade | Description |
|-------|-------|-------------|
| 90-100 | A | Excellent |
| 80-89 | B | Good |
| 70-79 | C | Acceptable |
| 60-69 | D | Poor |
| <60 | F | Serious Issues |

**Where it's stored:** `ta_punctuality_scores`

**When it's calculated:** End of month or on-demand

---

### 3. Attendance Rate 📈
**Definition:** Percentage of working days employee was present

**Formula:**
```
Attendance Rate = (Present Days / Total Working Days) × 100
```

**Range:** 0-100%

**Where it's stored:** `ta_attendance_metrics.attendance_rate`

---

### 4. Absence Rate 📉
**Definition:** Percentage of working days employee was absent

**Formula:**
```
Absence Rate = (Absent Days / Total Working Days) × 100
```

**Range:** 0-100%

**Where it's stored:** `ta_attendance_metrics.absence_rate`

---

### 5. Absence Days 📅
**Definition:** Total count of days employee was absent during the period

**Where it's stored:** 
- `ta_absence_late_records` (individual records)
- `ta_attendance_metrics.total_absent_days` (monthly summary)

---

### 6. Overtime Minutes/Hours ⚡
**Definition:** Time worked beyond scheduled shift hours

**Calculation:** `overtime_hours = total_hours_worked - shift_hours`

**Where it's stored:** 
- `ta_attendance.overtime_hours` (per day)
- `ta_overtime_tracking` (detailed events)
- `ta_overtime_frequency.total_overtime_hours` (monthly)

---

### 7. Overtime Frequency 🔄
**Definition:** How often overtime occurs in a month

**Rating Scale:**
| Rating | Instances | Meaning |
|--------|-----------|---------|
| LOW | 0-2 | Minimal overtime |
| MODERATE | 3-5 | Occasional overtime |
| HIGH | 6-9 | Frequent overtime |
| CRITICAL | 10+ | Excessive (needs review) |

**Where it's stored:** `ta_overtime_frequency.overtime_frequency_rating`

---

### 8. Overall Performance Score 🎯
**Definition:** Weighted composite score combining all metrics

**Weights:**
- Attendance Rate: 40%
- Punctuality Score: 35%
- Absence Prevention (100 - absence_rate): 25%

**Formula:**
```
Overall = (Attendance × 0.40) + (Punctuality × 0.35) + ((100 - Absence) × 0.25)
```

**Range:** 0-100

**Where it's stored:** `ta_attendance_metrics.overall_performance_score`

---

## Database Tables

| Table | Purpose | Key Columns |
|-------|---------|------------|
| `ta_punctuality_scores` | Monthly punctuality metrics | employee_id, month_year, punctuality_score, punctuality_grade |
| `ta_overtime_tracking` | Individual overtime events | employee_id, overtime_date, overtime_hours, reason_category, approved |
| `ta_overtime_frequency` | Monthly overtime summary | employee_id, month_year, overtime_instances, overtime_frequency_rating |
| `ta_attendance_metrics` | Comprehensive monthly metrics | employee_id, month_year, attendance_rate, absence_rate, overall_performance_score |
| `ta_attendance` | Daily attendance records | late_minutes, overtime_hours, status |

---

## API Endpoints

### Base URL
`/time_attendance/app/api/metrics.php`

### Endpoints
| Action | Method | Parameters | Returns |
|--------|--------|-----------|---------|
| `calculate_punctuality` | GET/POST | employee_id, month_year | Punctuality score & grade |
| `calculate_overtime_frequency` | GET/POST | employee_id, month_year | Frequency rating & hours |
| `calculate_all_metrics` | GET/POST | employee_id, month_year | All metrics combined |
| `get_employee_metrics_dashboard` | GET | employee_id, month_year | Dashboard data |
| `record_late_minutes` | POST | attendance_id, late_minutes | Success status |
| `record_overtime_event` | POST | employee_id, attendance_id, overtime_hours | Tracking ID |

### Example API Call
```javascript
// Get all metrics for dashboard
fetch('/time_attendance/app/api/metrics.php?action=get_employee_metrics_dashboard&employee_id=EMP001&month_year=2026-03')
    .then(r => r.json())
    .then(data => {
        console.log('Attendance Rate:', data.dashboard.attendance_rate);
        console.log('Punctuality Score:', data.dashboard.punctuality_score);
        console.log('Performance Score:', data.dashboard.overall_performance_score);
    });
```

---

## Employee Dashboard Display

The employee dashboard now shows:

```
📊 Performance Metrics
┌─────────────────────┬──────────────────────┐
│ Attendance Rate     │ Absence Rate         │
│ XX.XX%              │ XX.XX%               │
├─────────────────────┼──────────────────────┤
│ Punctuality Score   │ Overall Performance  │
│ XX/100              │ XX/100               │
├─────────────────────┼──────────────────────┤
│ Late Incidents      │ Overtime Frequency   │
│ X                   │ LOW/MODERATE/HIGH    │
└─────────────────────┴──────────────────────┘
```

---

## Thresholds & Alerts

### Punctuality Score Alerts
- **Below 70:** Poor punctuality - needs attention
- **Below 60:** Serious punctuality issues - escalate
- **Below 40:** Critical issues - disciplinary action may be needed

### Overtime Frequency Alerts
- **HIGH (6-9 instances):** Monitor for workload issues
- **CRITICAL (10+):** Review immediately, consider hiring or workload adjustment

### Absence Rate Alerts
- **Above 15%:** Monitor, may indicate health or engagement issues
- **Above 20%:** Escalate to HR

---

## How to Use Metrics

### For Employees
- Monitor personal punctuality and attendance trends
- Track overtime to manage work-life balance
- Understand performance score components

### For Managers
- Identify team members with attendance issues
- Plan workload to manage overtime frequency
- Recognize high-performing employees

### For HR
- Generate monthly reports by department
- Identify systemic issues (excessive overtime)
- Support disciplinary or recognition decisions
- Plan workforce adjustments

---

## Files Modified/Created

### New Files
- `migrations/003_add_metrics_tracking.sql` - Database schema
- `app/helpers/MetricsCalculator.php` - Calculation logic
- `app/api/metrics.php` - API endpoints

### Modified Files
- `public/employee_dashboard.php` - Added metrics display
- `public/export_dashboard.php` - Added metrics to export
- `public/qr_display_kiosk.php` - (referenced in your workspace)

---

## Testing Checklist

- [ ] Database migration runs without errors
- [ ] MetricsCalculator class loads correctly
- [ ] API endpoints return valid JSON
- [ ] Employee dashboard displays metrics
- [ ] Metrics calculations are accurate
- [ ] Export includes new metrics
- [ ] Late minutes recorded correctly
- [ ] Overtime frequency rates accurate
- [ ] Overall performance score weighted correctly

---

## Maintenance

### Monthly Tasks
- Review employee metrics
- Identify trends and issues
- Generate reports
- Update thresholds if needed

### Quarterly Review
- Analyze overtime patterns
- Check database size
- Optimize queries if needed
- Archive old data if necessary

---

**Last Updated:** March 20, 2026  
**System Version:** Enhanced Metrics v1.0
