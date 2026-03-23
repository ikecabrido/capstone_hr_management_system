# Quick Reference: Dashboard Integration SQL Queries

## Employee Portal Dashboard Queries

### 1. Get Employee Profile Data
```sql
SELECT e.*, u.username, u.email, u.role 
FROM employees e
LEFT JOIN users u ON e.user_id = u.id
WHERE e.user_id = ? AND e.employment_status = 'Active'
LIMIT 1
```

### 2. Get Employee Attendance Today
```sql
SELECT a.clock_in, a.clock_out, a.status, a.date
FROM attendance a
WHERE a.employee_id = ? AND DATE(a.date) = CURDATE()
LIMIT 1
```

### 3. Get Employee Leave Balance
```sql
SELECT 
    l.leave_type,
    COUNT(CASE WHEN l.status = 'Approved' THEN 1 END) as used,
    (SELECT COUNT(*) FROM leaves WHERE employee_id = ?) - 
    COUNT(CASE WHEN l.status = 'Approved' THEN 1 END) as remaining
FROM leaves l
WHERE l.employee_id = ?
GROUP BY l.leave_type
```

### 4. Get Upcoming Employee Schedule
```sql
SELECT es.shift_id, s.shift_name, s.start_time, s.end_time, es.assigned_date
FROM employee_shifts es
JOIN shifts s ON es.shift_id = s.shift_id
WHERE es.employee_id = ? 
AND es.assigned_date >= CURDATE()
ORDER BY es.assigned_date
LIMIT 7
```

### 5. Get Pending Leave Requests for Employee
```sql
SELECT l.leave_id, l.leave_type, l.start_date, l.end_date, l.status, l.reason
FROM leaves l
WHERE l.employee_id = ? 
AND l.status IN ('Pending', 'Approved')
ORDER BY l.start_date
```

### 6. Get Employee Performance Data
```sql
SELECT pr.review_id, pr.rating, pr.feedback, pr.review_date, pr.status
FROM performance_reviews pr
WHERE pr.employee_id = ?
ORDER BY pr.review_date DESC
LIMIT 5
```

---

## Time Attendance Dashboard Queries

### 1. Get Today's Attendance Summary
```sql
SELECT 
    e.employee_id,
    e.full_name,
    e.department,
    e.position,
    a.clock_in,
    a.clock_out,
    a.status,
    CASE 
        WHEN a.clock_in IS NULL THEN 'Absent'
        WHEN TIME(a.clock_in) > '09:00:00' THEN 'Late'
        ELSE 'On-time'
    END as attendance_status
FROM employees e
LEFT JOIN attendance a ON e.employee_id = a.employee_id 
WHERE DATE(a.date) = CURDATE()
AND e.employment_status = 'Active'
ORDER BY e.full_name
```

### 2. Get Attendance Statistics
```sql
SELECT 
    COUNT(*) as total_employees,
    SUM(CASE WHEN a.clock_in IS NOT NULL THEN 1 ELSE 0 END) as present,
    SUM(CASE WHEN a.clock_in IS NULL THEN 1 ELSE 0 END) as absent,
    SUM(CASE WHEN TIME(a.clock_in) > '09:00:00' THEN 1 ELSE 0 END) as late
FROM employees e
LEFT JOIN attendance a ON e.employee_id = a.employee_id 
WHERE DATE(a.date) = CURDATE()
AND e.employment_status = 'Active'
```

### 3. Get Pending Leave Requests (Today)
```sql
SELECT 
    l.leave_id,
    e.full_name,
    e.department,
    l.leave_type,
    l.start_date,
    l.end_date,
    l.reason,
    l.status
FROM leaves l
JOIN employees e ON l.employee_id = e.employee_id
WHERE l.status = 'Pending'
AND (l.start_date >= CURDATE() OR l.end_date >= CURDATE())
ORDER BY l.start_date
```

### 4. Get Employee Shifts for Today
```sql
SELECT 
    es.employee_shift_id,
    e.employee_id,
    e.full_name,
    e.department,
    s.shift_name,
    s.start_time,
    s.end_time,
    es.assigned_date
FROM employee_shifts es
JOIN employees e ON es.employee_id = e.employee_id
JOIN shifts s ON es.shift_id = s.shift_id
WHERE DATE(es.assigned_date) = CURDATE()
AND e.employment_status = 'Active'
ORDER BY s.start_time
```

### 5. Get Late Arrivals (Last 7 Days)
```sql
SELECT 
    e.employee_id,
    e.full_name,
    e.department,
    a.date,
    a.clock_in,
    TIMEDIFF(TIME(a.clock_in), '09:00:00') as minutes_late
FROM attendance a
JOIN employees e ON a.employee_id = e.employee_id
WHERE TIME(a.clock_in) > '09:00:00'
AND a.date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
AND e.employment_status = 'Active'
ORDER BY a.date DESC, a.clock_in DESC
```

### 6. Get Attendance Trend (Last 30 Days)
```sql
SELECT 
    DATE(a.date) as date,
    COUNT(*) as total,
    SUM(CASE WHEN a.clock_in IS NOT NULL THEN 1 ELSE 0 END) as present,
    SUM(CASE WHEN a.clock_in IS NULL THEN 1 ELSE 0 END) as absent
FROM attendance a
WHERE a.date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY DATE(a.date)
ORDER BY a.date DESC
```

---

## Common Dashboard Metrics

### 1. Employee Statistics
```sql
SELECT 
    COUNT(*) as total_employees,
    SUM(CASE WHEN employment_status = 'Active' THEN 1 ELSE 0 END) as active,
    SUM(CASE WHEN employment_status = 'Inactive' THEN 1 ELSE 0 END) as inactive,
    COUNT(DISTINCT department) as departments
FROM employees
```

### 2. Department Summary
```sql
SELECT 
    department,
    COUNT(*) as employee_count,
    SUM(CASE WHEN employment_status = 'Active' THEN 1 ELSE 0 END) as active_count,
    COUNT(DISTINCT position) as unique_positions
FROM employees
WHERE employment_status = 'Active'
GROUP BY department
ORDER BY employee_count DESC
```

### 3. Leave Summary (Current Month)
```sql
SELECT 
    leave_type,
    COUNT(*) as total_requests,
    SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected
FROM leaves
WHERE MONTH(start_date) = MONTH(CURDATE())
AND YEAR(start_date) = YEAR(CURDATE())
GROUP BY leave_type
```

### 4. Attendance Rate (Current Month)
```sql
SELECT 
    DATE(a.date) as date,
    COUNT(DISTINCT a.employee_id) as total_employees,
    COUNT(DISTINCT CASE WHEN a.clock_in IS NOT NULL THEN a.employee_id END) as present,
    ROUND(
        (COUNT(DISTINCT CASE WHEN a.clock_in IS NOT NULL THEN a.employee_id END) / 
         COUNT(DISTINCT a.employee_id)) * 100, 2
    ) as attendance_percentage
FROM attendance a
WHERE MONTH(a.date) = MONTH(CURDATE())
AND YEAR(a.date) = YEAR(CURDATE())
GROUP BY DATE(a.date)
ORDER BY a.date
```

---

## Integration Testing Queries

### Test 1: Verify Employee-User Relationship
```sql
SELECT 
    e.employee_id,
    e.full_name,
    e.user_id,
    u.username,
    u.email,
    CASE WHEN u.id IS NOT NULL THEN 'Linked' ELSE 'Not Linked' END as status
FROM employees e
LEFT JOIN users u ON e.user_id = u.id
ORDER BY e.employee_id
```

### Test 2: Verify Attendance Records Exist
```sql
SELECT 
    e.employee_id,
    e.full_name,
    COUNT(a.attendance_id) as attendance_records,
    MAX(a.date) as last_record,
    MIN(a.date) as first_record
FROM employees e
LEFT JOIN attendance a ON e.employee_id = a.employee_id
WHERE e.employment_status = 'Active'
GROUP BY e.employee_id, e.full_name
ORDER BY attendance_records DESC
```

### Test 3: Verify Leave Records
```sql
SELECT 
    e.employee_id,
    e.full_name,
    COUNT(l.leave_id) as leave_requests,
    SUM(CASE WHEN l.status = 'Approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN l.status = 'Pending' THEN 1 ELSE 0 END) as pending
FROM employees e
LEFT JOIN leaves l ON e.employee_id = l.employee_id
WHERE e.employment_status = 'Active'
GROUP BY e.employee_id, e.full_name
ORDER BY leave_requests DESC
```

### Test 4: Check Foreign Key Relationships
```sql
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'hr_management'
AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME
```

---

## PHP Implementation Examples

### Connect and Execute Query
```php
// MySQLi Example (Time Attendance)
$conn = new mysqli('localhost', 'root', '', 'hr_management');
$query = "SELECT * FROM employees WHERE employment_status = 'Active'";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    echo $row['full_name'];
}

// PDO Example (Employee Portal)
$pdo = new PDO("mysql:host=localhost;dbname=hr_management", "root", "");
$stmt = $pdo->prepare("SELECT * FROM employees WHERE user_id = ?");
$stmt->execute([$userId]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);
```

### Get Dashboard Metrics Function
```php
function getDashboardMetrics($conn) {
    $metrics = [];
    
    // Total employees
    $result = $conn->query("SELECT COUNT(*) as count FROM employees WHERE employment_status = 'Active'");
    $metrics['total_employees'] = $result->fetch_assoc()['count'];
    
    // Today's attendance
    $result = $conn->query("SELECT COUNT(*) as count FROM attendance WHERE DATE(date) = CURDATE()");
    $metrics['today_present'] = $result->fetch_assoc()['count'];
    
    // Pending leaves
    $result = $conn->query("SELECT COUNT(*) as count FROM leaves WHERE status = 'Pending'");
    $metrics['pending_leaves'] = $result->fetch_assoc()['count'];
    
    return $metrics;
}

$metrics = getDashboardMetrics($conn);
echo json_encode($metrics);
```

---

## Debugging Queries

### Check Database Connection
```sql
-- Verify database exists
SHOW DATABASES;

-- Use the database
USE hr_management;

-- Check tables
SHOW TABLES;

-- Check employees table structure
DESCRIBE employees;

-- Count records in each table
SELECT 'employees' as table_name, COUNT(*) as count FROM employees
UNION ALL
SELECT 'users', COUNT(*) FROM users
UNION ALL
SELECT 'attendance', COUNT(*) FROM attendance
UNION ALL
SELECT 'leaves', COUNT(*) FROM leaves
```

### Troubleshoot Foreign Keys
```sql
-- Check foreign key constraints
SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'hr_management'
AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Check if employee has no user linked
SELECT * FROM employees WHERE user_id IS NULL;

-- Check attendance without employee
SELECT * FROM attendance WHERE employee_id NOT IN (SELECT employee_id FROM employees);
```

---

## Performance Tips

1. **Add Indexes for Common Queries**
   ```sql
   ALTER TABLE employees ADD INDEX idx_user_id (user_id);
   ALTER TABLE attendance ADD INDEX idx_employee_id (employee_id);
   ALTER TABLE attendance ADD INDEX idx_date (date);
   ```

2. **Use EXPLAIN to Optimize Queries**
   ```sql
   EXPLAIN SELECT * FROM attendance 
   WHERE employee_id = 'EMP001' 
   AND DATE(date) = CURDATE();
   ```

3. **Limit Result Sets**
   ```sql
   SELECT * FROM attendance 
   LIMIT 1000;  -- Always limit large queries
   ```

---

**Last Updated**: 2026-03-19  
**Database**: hr_management  
**Modules**: Employee Portal, Time Attendance
