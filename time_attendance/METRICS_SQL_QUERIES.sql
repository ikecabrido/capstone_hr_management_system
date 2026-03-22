-- ============================================================================
-- ATTENDANCE METRICS REPORTING QUERIES
-- ============================================================================
-- These SQL queries can be used by HR to generate reports on attendance metrics
-- Use these to monitor employee performance, identify issues, and make decisions
-- ============================================================================

-- ============================================================================
-- 1. MONTHLY METRICS FOR ALL EMPLOYEES
-- ============================================================================
SELECT 
    am.employee_id,
    e.full_name,
    e.department,
    am.month_year,
    CONCAT(am.attendance_rate, '%') as attendance_rate,
    CONCAT(am.absence_rate, '%') as absence_rate,
    CONCAT(am.punctuality_score, '/100') as punctuality_score,
    am.overtime_frequency_rating,
    CONCAT(am.overall_performance_score, '/100') as overall_performance,
    am.total_present_days,
    am.total_absent_days,
    am.total_late_incidents,
    CONCAT(am.total_overtime_hours, 'h') as total_overtime
FROM ta_attendance_metrics am
JOIN employees e ON am.employee_id = e.employee_id
WHERE am.month_year = DATE_FORMAT(CURDATE(), '%Y-%m')
ORDER BY am.overall_performance_score DESC;

-- ============================================================================
-- 2. TOP PERFORMERS THIS MONTH
-- ============================================================================
SELECT 
    e.full_name,
    e.employee_id,
    e.department,
    am.overall_performance_score,
    ps.punctuality_grade,
    am.attendance_rate,
    am.total_late_incidents
FROM ta_attendance_metrics am
JOIN ta_punctuality_scores ps ON am.employee_id = ps.employee_id 
    AND am.month_year = ps.month_year
JOIN employees e ON am.employee_id = e.employee_id
WHERE am.month_year = DATE_FORMAT(CURDATE(), '%Y-%m')
    AND am.overall_performance_score >= 90
ORDER BY am.overall_performance_score DESC
LIMIT 10;

-- ============================================================================
-- 3. EMPLOYEES WITH ATTENDANCE ISSUES
-- ============================================================================
SELECT 
    e.full_name,
    e.employee_id,
    e.department,
    am.attendance_rate,
    am.absence_rate,
    am.total_absent_days,
    am.total_late_incidents
FROM ta_attendance_metrics am
JOIN employees e ON am.employee_id = e.employee_id
WHERE am.month_year = DATE_FORMAT(CURDATE(), '%Y-%m')
    AND (am.attendance_rate < 85 OR am.absence_rate > 15)
ORDER BY am.overall_performance_score ASC;

-- ============================================================================
-- 4. CRITICAL PUNCTUALITY ISSUES
-- ============================================================================
SELECT 
    e.full_name,
    e.employee_id,
    e.department,
    ps.punctuality_grade,
    ps.punctuality_score,
    ps.total_late_incidents,
    ps.total_late_minutes,
    ROUND(ps.average_late_minutes, 2) as avg_late_minutes
FROM ta_punctuality_scores ps
JOIN employees e ON ps.employee_id = e.employee_id
WHERE ps.month_year = DATE_FORMAT(CURDATE(), '%Y-%m')
    AND ps.punctuality_grade IN ('D', 'F')
ORDER BY ps.punctuality_score ASC;

-- ============================================================================
-- 5. EXCESSIVE OVERTIME FLAGGING
-- ============================================================================
SELECT 
    e.full_name,
    e.employee_id,
    e.department,
    of.month_year,
    of.overtime_instances,
    CONCAT(of.total_overtime_hours, 'h') as total_overtime,
    ROUND(of.average_overtime_per_instance, 2) as avg_per_instance,
    of.overtime_frequency_rating,
    CONCAT(of.approved_overtime_hours, 'h') as approved,
    CONCAT(of.unapproved_overtime_hours, 'h') as unapproved
FROM ta_overtime_frequency of
JOIN employees e ON of.employee_id = e.employee_id
WHERE of.month_year = DATE_FORMAT(CURDATE(), '%Y-%m')
    AND of.overtime_frequency_rating IN ('HIGH', 'CRITICAL')
ORDER BY of.total_overtime_hours DESC;

-- ============================================================================
-- 6. DETAILED LATE INCIDENTS
-- ============================================================================
SELECT 
    e.full_name,
    e.employee_id,
    e.department,
    a.attendance_date,
    TIME(a.time_in) as actual_time_in,
    s.start_time as scheduled_time,
    a.late_minutes,
    CASE 
        WHEN a.late_minutes > 30 THEN 'SEVERE'
        WHEN a.late_minutes > 15 THEN 'MODERATE'
        ELSE 'MINOR'
    END as late_severity,
    CASE 
        WHEN alr.is_excused = 1 THEN 'EXCUSED'
        ELSE 'UNEXCUSED'
    END as excuse_status
FROM ta_attendance a
JOIN employees e ON a.employee_id = e.employee_id
JOIN ta_shifts s ON a.shift_id = s.shift_id
LEFT JOIN ta_absence_late_records alr ON a.attendance_id = alr.attendance_id 
    AND alr.type = 'LATE'
WHERE a.late_minutes > 0
    AND DATE(a.attendance_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
ORDER BY a.attendance_date DESC, a.late_minutes DESC;

-- ============================================================================
-- 7. OVERTIME TRACKING BY REASON
-- ============================================================================
SELECT 
    e.full_name,
    e.employee_id,
    e.department,
    ot.reason_category,
    COUNT(*) as instances,
    ROUND(SUM(ot.overtime_hours), 2) as total_hours,
    ROUND(AVG(ot.overtime_hours), 2) as avg_hours,
    SUM(CASE WHEN ot.approved = 1 THEN 1 ELSE 0 END) as approved_count,
    SUM(CASE WHEN ot.approved = 0 THEN 1 ELSE 0 END) as unapproved_count
FROM ta_overtime_tracking ot
JOIN employees e ON ot.employee_id = e.employee_id
WHERE MONTH(ot.overtime_date) = MONTH(CURDATE())
    AND YEAR(ot.overtime_date) = YEAR(CURDATE())
GROUP BY e.employee_id, ot.reason_category
ORDER BY total_hours DESC;

-- ============================================================================
-- 8. DEPARTMENT-LEVEL METRICS
-- ============================================================================
SELECT 
    e.department,
    COUNT(DISTINCT am.employee_id) as num_employees,
    ROUND(AVG(am.attendance_rate), 2) as avg_attendance_rate,
    ROUND(AVG(am.absence_rate), 2) as avg_absence_rate,
    ROUND(AVG(am.punctuality_score), 2) as avg_punctuality_score,
    ROUND(AVG(am.overall_performance_score), 2) as avg_performance_score,
    ROUND(SUM(am.total_overtime_hours), 2) as total_dept_overtime
FROM ta_attendance_metrics am
JOIN employees e ON am.employee_id = e.employee_id
WHERE am.month_year = DATE_FORMAT(CURDATE(), '%Y-%m')
GROUP BY e.department
ORDER BY avg_performance_score DESC;

-- ============================================================================
-- 9. ABSENT EMPLOYEES (WITH DETAILS)
-- ============================================================================
SELECT 
    e.full_name,
    e.employee_id,
    e.department,
    alr.absence_date,
    alr.is_excused,
    alr.excuse_status,
    alr.reason,
    alr.supporting_document_url
FROM ta_absence_late_records alr
JOIN employees e ON alr.employee_id = e.employee_id
WHERE alr.type = 'ABSENT'
    AND MONTH(alr.absence_date) = MONTH(CURDATE())
    AND YEAR(alr.absence_date) = YEAR(CURDATE())
ORDER BY alr.absence_date DESC;

-- ============================================================================
-- 10. MONTHLY PERFORMANCE TREND
-- ============================================================================
SELECT 
    e.full_name,
    e.employee_id,
    e.department,
    am.month_year,
    am.overall_performance_score,
    ps.punctuality_grade,
    am.attendance_rate,
    CONCAT(am.total_late_incidents, ' late') as late_incidents
FROM ta_attendance_metrics am
JOIN ta_punctuality_scores ps ON am.employee_id = ps.employee_id 
    AND am.month_year = ps.month_year
JOIN employees e ON am.employee_id = e.employee_id
WHERE am.employee_id = 'EMP001'  -- Change to specific employee
ORDER BY am.month_year DESC;

-- ============================================================================
-- 11. CRITICAL ALERTS
-- ============================================================================
SELECT 
    'Punctuality Issue' as alert_type,
    e.full_name,
    e.employee_id,
    e.department,
    CONCAT('Grade ', ps.punctuality_grade, ' - Score: ', ps.punctuality_score) as details
FROM ta_punctuality_scores ps
JOIN employees e ON ps.employee_id = e.employee_id
WHERE ps.month_year = DATE_FORMAT(CURDATE(), '%Y-%m')
    AND ps.punctuality_score < 60

UNION ALL

SELECT 
    'Excessive Absence' as alert_type,
    e.full_name,
    e.employee_id,
    e.department,
    CONCAT('Absence Rate: ', am.absence_rate, '%') as details
FROM ta_attendance_metrics am
JOIN employees e ON am.employee_id = e.employee_id
WHERE am.month_year = DATE_FORMAT(CURDATE(), '%Y-%m')
    AND am.absence_rate > 20

UNION ALL

SELECT 
    'Critical Overtime' as alert_type,
    e.full_name,
    e.employee_id,
    e.department,
    CONCAT('Rating: ', of.overtime_frequency_rating, ' (', of.overtime_instances, ' instances)') as details
FROM ta_overtime_frequency of
JOIN employees e ON of.employee_id = e.employee_id
WHERE of.month_year = DATE_FORMAT(CURDATE(), '%Y-%m')
    AND of.overtime_frequency_rating = 'CRITICAL'

ORDER BY alert_type, employee_id;

-- ============================================================================
-- 12. EMPLOYEE COMPARISON (vs. Department Average)
-- ============================================================================
SELECT 
    e.full_name,
    e.employee_id,
    am.overall_performance_score,
    ROUND(AVG(am2.overall_performance_score) 
        OVER (PARTITION BY e.department), 2) as dept_avg,
    ROUND(am.overall_performance_score - AVG(am2.overall_performance_score) 
        OVER (PARTITION BY e.department), 2) as difference,
    CASE 
        WHEN am.overall_performance_score > AVG(am2.overall_performance_score) 
            OVER (PARTITION BY e.department) THEN 'Above Average'
        WHEN am.overall_performance_score = AVG(am2.overall_performance_score) 
            OVER (PARTITION BY e.department) THEN 'Average'
        ELSE 'Below Average'
    END as performance_vs_dept
FROM ta_attendance_metrics am
JOIN employees e ON am.employee_id = e.employee_id
JOIN ta_attendance_metrics am2 ON e.department = 
    (SELECT department FROM employees WHERE employee_id = am2.employee_id)
WHERE am.month_year = DATE_FORMAT(CURDATE(), '%Y-%m')
    AND am2.month_year = DATE_FORMAT(CURDATE(), '%Y-%m')
ORDER BY e.department, am.overall_performance_score DESC;

-- ============================================================================
-- 13. TRENDS: GETTING WORSE OR BETTER
-- ============================================================================
SELECT 
    e.full_name,
    e.employee_id,
    (SELECT overall_performance_score FROM ta_attendance_metrics 
     WHERE employee_id = e.employee_id 
     ORDER BY month_year DESC LIMIT 1) as current_score,
    (SELECT overall_performance_score FROM ta_attendance_metrics 
     WHERE employee_id = e.employee_id 
     ORDER BY month_year DESC LIMIT 1 OFFSET 1) as previous_score,
    ROUND((SELECT overall_performance_score FROM ta_attendance_metrics 
     WHERE employee_id = e.employee_id 
     ORDER BY month_year DESC LIMIT 1) - 
     (SELECT overall_performance_score FROM ta_attendance_metrics 
     WHERE employee_id = e.employee_id 
     ORDER BY month_year DESC LIMIT 1 OFFSET 1), 2) as trend
FROM employees e
WHERE e.employment_status = 'Active'
ORDER BY trend DESC;

-- ============================================================================
-- 14. SUMMARY STATISTICS FOR CURRENT MONTH
-- ============================================================================
SELECT 
    'Overall Statistics' as metric,
    COUNT(DISTINCT am.employee_id) as value_int,
    NULL as value_decimal,
    NULL as value_text
FROM ta_attendance_metrics am
WHERE am.month_year = DATE_FORMAT(CURDATE(), '%Y-%m')

UNION ALL

SELECT 
    'Average Attendance Rate',
    NULL,
    ROUND(AVG(am.attendance_rate), 2),
    CONCAT(ROUND(AVG(am.attendance_rate), 2), '%')
FROM ta_attendance_metrics am
WHERE am.month_year = DATE_FORMAT(CURDATE(), '%Y-%m')

UNION ALL

SELECT 
    'Average Punctuality Score',
    NULL,
    ROUND(AVG(ps.punctuality_score), 2),
    CONCAT(ROUND(AVG(ps.punctuality_score), 2), '/100')
FROM ta_punctuality_scores ps
WHERE ps.month_year = DATE_FORMAT(CURDATE(), '%Y-%m')

UNION ALL

SELECT 
    'Average Performance Score',
    NULL,
    ROUND(AVG(am.overall_performance_score), 2),
    CONCAT(ROUND(AVG(am.overall_performance_score), 2), '/100')
FROM ta_attendance_metrics am
WHERE am.month_year = DATE_FORMAT(CURDATE(), '%Y-%m')

UNION ALL

SELECT 
    'Total Overtime Hours',
    NULL,
    ROUND(SUM(of.total_overtime_hours), 2),
    CONCAT(ROUND(SUM(of.total_overtime_hours), 2), ' hours')
FROM ta_overtime_frequency of
WHERE of.month_year = DATE_FORMAT(CURDATE(), '%Y-%m');

-- ============================================================================
-- NOTE: Replace DATE_FORMAT(CURDATE(), '%Y-%m') with specific date for past months
-- Example: '2026-02' for February 2026
-- ============================================================================
