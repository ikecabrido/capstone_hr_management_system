# Admin Dashboard - Attendance Metrics Display

## Overview

The admin dashboard now displays comprehensive attendance metrics for the entire organization. These metrics provide real-time insights into employee attendance patterns, punctuality, and performance.

---

## Metrics Displayed

### Row 1: Core Performance Indicators

#### 1. **Average Attendance Rate** 📊
- **Icon:** Percentage symbol
- **Color:** Primary (Blue)
- **Meaning:** Percentage of working days employees are present across the organization
- **Range:** 0-100%
- **Target:** 95%+
- **Calculation:** Average of all employees' attendance rates for the month

#### 2. **Average Punctuality Score** ✅
- **Icon:** Thumbs up
- **Color:** Success (Green)
- **Meaning:** Average punctuality performance score for all employees
- **Range:** 0-100
- **Grades:** A (90+), B (80-89), C (70-79), D (60-69), F (<60)
- **Calculation:** Average of all employees' punctuality scores
- **Action:** Scores below 70 indicate potential issue

#### 3. **Average Absence Rate** ❌
- **Icon:** Ban symbol
- **Color:** Danger (Red)
- **Meaning:** Percentage of working days employees are absent
- **Range:** 0-100%
- **Target:** <10%
- **Alert:** >20% indicates serious issue
- **Calculation:** Average of all employees' absence rates

#### 4. **Average Performance Score** ⭐
- **Icon:** Star
- **Color:** Warning (Yellow)
- **Meaning:** Overall composite performance score (weighted average of all metrics)
- **Range:** 0-100
- **Weights:** Attendance (40%), Punctuality (35%), Absence Prevention (25%)
- **Target:** 80+
- **Use:** Employee evaluation and recognition

### Row 2: Operational Metrics

#### 5. **Total Late Incidents** ⏰
- **Icon:** Clock
- **Color:** Info (Light Blue)
- **Meaning:** Total number of times employees were late in the month
- **Trend:** Lower is better
- **Track:** Month-over-month comparison
- **Use:** Identify systemic tardiness issues

#### 6. **Total Overtime Hours** ⚡
- **Icon:** Lightning bolt
- **Color:** Purple
- **Meaning:** Total cumulative overtime hours worked by all employees
- **Range:** Varies by organization
- **Monitor:** High values indicate workload pressure
- **Alert:** Watch for patterns indicating burnout
- **Use:** Workload management and resource planning

#### 7. **Excellent Performers** 👤
- **Icon:** Check mark
- **Color:** Teal
- **Meaning:** Number of employees with performance score ≥90 (Grade A)
- **Target:** Maximize this number
- **Recognition:** Consider for rewards/promotions
- **Benchmark:** Track percentage of excellent performers

#### 8. **Critical Issues** ⚠️
- **Icon:** Exclamation triangle
- **Color:** Orange
- **Meaning:** Number of employees with critical issues
- **Criteria:** Performance score <60 OR absence rate >20%
- **Action Required:** Immediate review needed
- **Response:** HR intervention/counseling

---

## How to Interpret the Metrics

### Healthy Organization Profile
```
Attendance Rate:     95%+
Punctuality Score:   80+
Absence Rate:        <10%
Performance Score:   80+
Late Incidents:      <5% of workforce
Overtime Hours:      Consistent, manageable levels
Excellent:           70%+ of workforce
Critical Issues:     <5% of workforce
```

### Warning Signs
```
Attendance Rate:     <85%          → Escalating issue
Absence Rate:        >15%          → Investigation needed
Punctuality Score:   <70           → Training recommended
Late Incidents:      Increasing    → Pattern analysis needed
Overtime Hours:      Consistently high → Staffing review needed
Critical Issues:     >10%          → Urgent HR action
```

---

## Real-Time Updates

- **Auto-Refresh:** Metrics update every 5 minutes automatically
- **Manual Refresh:** Click the dashboard to force immediate refresh
- **Last Updated:** Shown in system status section
- **Data Source:** Current month's attendance data

---

## Access Control

**Who can see this:**
- Admin users
- HR managers with admin privileges
- Dashboard access role

**Dashboard Location:** `/admin_dashboard.php`

---

## Using Metrics for Decision Making

### 1. **Workload Management**
- Monitor overtime hours for burnout indicators
- Identify departments with excessive overtime
- Plan staffing adjustments

### 2. **Performance Recognition**
- Identify excellent performers (score ≥90)
- Target for recognition programs
- Consider for promotions/bonuses

### 3. **Corrective Action**
- Identify critical issues early
- Initiate counseling/training
- Track improvement over time

### 4. **Department Analysis**
- Compare metrics across departments
- Identify top/bottom performing units
- Share best practices

### 5. **Trend Analysis**
- Compare month-to-month changes
- Identify seasonal patterns
- Plan for predictable variations

---

## Related Reports

### Run Detailed Reports
Access comprehensive SQL queries in:
`/time_attendance/METRICS_SQL_QUERIES.sql`

**Available Reports:**
1. Top performers this month
2. Employees with attendance issues
3. Critical punctuality problems
4. Excessive overtime flagging
5. Department-level analysis
6. Trend comparisons
7. Detailed alert reports

---

## Troubleshooting

### Metrics Show 0 or No Data
**Solution:**
- Ensure attendance records exist for the month
- Check that metrics have been calculated
- Run: `time_attendance/app/api/metrics.php?action=calculate_all_metrics`

### Metrics Not Updating
**Solution:**
- Hard refresh browser (Ctrl+F5)
- Check browser console for errors
- Verify database connection
- Check permissions

### Individual Employee Metrics vs. Dashboard
**Note:**
- Dashboard shows **averages** across all employees
- For individual metrics, check employee dashboard
- Access: `time_attendance/public/employee_dashboard.php`

---

## Mobile View

The metrics display is fully responsive and works on:
- Desktop (full view with all details)
- Tablet (optimized grid layout)
- Mobile (stacked single-column view)

---

## Technical Details

### API Endpoint
```
GET /time_attendance/app/api/metrics.php?action=get_attendance_metrics_summary&month_year=YYYY-MM
```

### Response Format
```json
{
  "success": true,
  "month_year": "2026-03",
  "summary": {
    "total_employees": 150,
    "avg_attendance_rate": 94.5,
    "avg_absence_rate": 8.2,
    "avg_punctuality_score": 82,
    "avg_overall_performance": 85,
    "total_late_incidents": 23,
    "total_overtime_hours": 456.5,
    "excellent_performers": 105,
    "critical_issues": 8
  }
}
```

### Database Query
- Queries: `ta_attendance_metrics` table
- Filter: Month-year comparison
- Calculation: Aggregations and averages
- Performance: <200ms response time

---

## Customization Options

### Change Refresh Rate
Edit `admin_dashboard.php` line with:
```javascript
setInterval(loadAttendanceMetrics, X * 60 * 1000); // X = minutes
```

### Modify Thresholds
Update critical/excellent cutoffs in:
`time_attendance/app/api/metrics.php` function `handleGetAttendanceMetricsSummary()`

### Add Additional Metrics
Extend the summary query to include new fields from `ta_attendance_metrics` table

---

## Export & Reporting

### Generate Monthly Report
Use SQL queries from `METRICS_SQL_QUERIES.sql`:
```sql
-- Query 1: Monthly Metrics for All Employees
SELECT * FROM ta_attendance_metrics
WHERE month_year = '2026-03'
ORDER BY overall_performance_score DESC;
```

### Export to Excel
1. Run SQL query
2. Export results to CSV
3. Open in Excel
4. Create charts and pivot tables

---

## Key Performance Indicators (KPIs)

### Organization-Level KPIs
| KPI | Formula | Target | Current |
|-----|---------|--------|---------|
| Attendance Rate | Avg attendance_rate | 95% | - |
| Punctuality Score | Avg punctuality_score | 80+ | - |
| Absence Rate | Avg absence_rate | <10% | - |
| Performance Score | Avg overall_performance | 80+ | - |

### Employee Distribution KPIs
| Category | Threshold | Target % | Current |
|----------|-----------|----------|---------|
| Excellent | Score ≥90 | 70%+ | - |
| Good | Score 80-89 | 20%+ | - |
| At Risk | Score <60 | <5% | - |
| Critical | Score <60 OR Absence >20% | <2% | - |

---

## Best Practices

1. **Weekly Review** - Check dashboard every week for trends
2. **Monthly Analysis** - Generate detailed reports monthly
3. **Department Comparison** - Compare metrics across teams
4. **Trend Tracking** - Compare month-over-month changes
5. **Action Planning** - Create action plans for critical issues
6. **Recognition** - Recognize top performers regularly
7. **Forecasting** - Use trends for resource planning

---

## Support

For questions about:
- **Metrics Calculations** → See `METRICS_IMPLEMENTATION_GUIDE.md`
- **SQL Queries** → See `METRICS_SQL_QUERIES.sql`
- **Quick Reference** → See `METRICS_QUICK_REFERENCE.md`
- **Technical Issues** → Check `METRICS_STATUS_REPORT.md`

---

**Last Updated:** March 20, 2026  
**Dashboard Version:** 2.0 with Metrics  
**Status:** ✅ Ready for Use
