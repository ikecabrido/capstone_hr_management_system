# Installation & Testing Guide

## Complete Setup Instructions

### Prerequisites Check
- ✅ XAMPP/WAMP/LAMP installed
- ✅ MySQL service running
- ✅ PHP 7.4+ installed
- ✅ Port 3306 (MySQL) available
- ✅ Port 80/8080 (Apache) available

---

## Step 1: Database Setup

### Method A: Using MySQL Command Line
```bash
# Connect to MySQL
mysql -u root -p

# Paste entire schema.sql content
CREATE DATABASE IF NOT EXISTS school_management;
USE school_management;

-- [Paste entire schema.sql file here]
```

### Method B: Using phpMyAdmin
1. Open `http://localhost/phpmyadmin`
2. Go to "Import" tab
3. Choose `database/schema.sql` file
4. Click "Go"

### Method C: Direct Import Command
```bash
mysql -u root -p < C:\xampp\htdocs\work_analytics\database\schema.sql
```

### Verify Installation
```sql
-- Login to MySQL
mysql -u root -p
USE school_management;

-- Check database
SHOW TABLES;

-- Verify employee data
SELECT COUNT(*) FROM employees;
-- Expected: 35+

-- Check departments
SELECT DISTINCT department FROM employees;
-- Expected: 6 departments
```

---

## Step 2: Configure Connection

### Edit config/config.php
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password_here');  // Change this!
define('DB_NAME', 'school_management');
```

### Test Connection (Optional)
Create a test file:
```php
<?php
require_once 'config/config.php';
require_once 'config/Database.php';

try {
    $db = Database::getInstance();
    echo "✅ Database connected successfully!";
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
?>
```

---

## Step 3: File Placement

### Correct Directory Structure
```
C:\xampp\htdocs\
├── work_analytics/
│   ├── public/
│   │   ├── index.html
│   ├── api/
│   │   ├── dashboard_metrics.php
│   │   ├── attrition_data.php
│   │   └── [other API files]
│   ├── assets/
│   │   ├── style.css
│   │   └── app.js
│   ├── config/
│   │   ├── config.php
│   │   └── Database.php
│   ├── models/
│   │   ├── Employee.php
│   │   └── Analytics.php
│   ├── database/
│   │   └── schema.sql
│   └── README.md
```

### Verify Installation
```bash
# Windows - Check if files exist
dir C:\xampp\htdocs\work_analytics\api\
dir C:\xampp\htdocs\work_analytics\config\
dir C:\xampp\htdocs\work_analytics\models\
```

---

## Step 4: Access Application

### Start Services
1. **XAMPP**: Click "Start" for Apache and MySQL
2. **Verify**: Visit `http://localhost` (see XAMPP dashboard)

### Access Dashboard
```
URL: http://localhost/work_analytics/public/index.html
OR
URL: http://localhost/work_analytics/
```

### Expected Result
- ✅ Dashboard loads with metrics
- ✅ Charts display with sample data
- ✅ All tabs are accessible
- ✅ No console errors (F12 to check)

---

## Testing Checklist

### 1. Dashboard Tab
- [ ] Metrics display (Total Employees, Teachers, Staff)
- [ ] Department chart shows data
- [ ] Gender distribution pie chart visible
- [ ] Age group line chart displayed
- [ ] Tenure radar chart shows data
- [ ] Numbers match database values

### 2. Attrition Tab
- [ ] Year selector works
- [ ] Attrition rate displays percentage
- [ ] Monthly attrition chart updates
- [ ] Performance distribution chart visible
- [ ] Separated employees table populated

### 3. Diversity Tab
- [ ] Gender diversity pie chart visible
- [ ] Age diversity bar chart shows data
- [ ] Department representation polar chart displays
- [ ] Salary statistics table populated
- [ ] All departments listed

### 4. Performance Tab
- [ ] Performance distribution doughnut chart visible
- [ ] Salary distribution bar chart shows data
- [ ] At-risk employees table populated
- [ ] Risk filter buttons work (High/Medium/Low)
- [ ] Correct counts per risk level

### 5. Custom Reports Tab
- [ ] Filter dropdowns have options
- [ ] Date pickers work correctly
- [ ] "Generate Report" button loads data
- [ ] CSV export works
- [ ] Report count displays correctly
- [ ] Clear filters resets form

### 6. API Testing
```bash
# Test each endpoint with curl or browser

# Dashboard metrics
curl http://localhost/work_analytics/api/dashboard_metrics.php

# Department distribution
curl http://localhost/work_analytics/api/department_distribution.php

# Attrition data
curl "http://localhost/work_analytics/api/attrition_data.php?year=2026"

# Custom report with filters
curl "http://localhost/work_analytics/api/custom_report.php?department=Academics"
```

### 7. Browser Compatibility
- [ ] Chrome
- [ ] Firefox
- [ ] Safari (if on Mac)
- [ ] Edge

### 8. Responsive Design
- [ ] Desktop view (1920x1080)
- [ ] Tablet view (768px width)
- [ ] Mobile view (375px width)

---

## Sample Data Verification

### Check Employee Distribution
```sql
SELECT department, COUNT(*) as count 
FROM employees 
WHERE employment_status NOT IN ('Resigned', 'Terminated')
GROUP BY department;
```

Expected output:
```
Administration     2
Academics         8
Finance           3
HR                3
IT                4
Support Services  4
```

### Check At-Risk Employees
```sql
SELECT name, department, performance_score, absence_days
FROM employees
WHERE (performance_score < 3 OR absence_days > 15)
AND employment_status NOT IN ('Resigned', 'Terminated');
```

Should return approximately 4 employees.

### Check Separated Employees
```sql
SELECT name, employment_status, separation_date
FROM employees
WHERE employment_status IN ('Resigned', 'Terminated', 'Retired')
ORDER BY separation_date DESC;
```

Should show 4 separated employees.

---

## Performance Check

### Database Query Performance
```sql
-- Check indexes
SHOW INDEX FROM employees;

-- Should see indexes on:
-- - department
-- - employment_status
-- - hire_date
-- - performance_score
```

### API Response Times
All API calls should respond in < 100ms:
- Dashboard metrics: ~20ms
- Distribution data: ~30ms
- At-risk employees: ~40ms
- Custom reports: ~50ms

---

## Troubleshooting

### Issue 1: "Page not found"
```
URL: http://localhost/work_analytics/public/index.html

Solution:
1. Verify files are in correct location
2. Check folder case sensitivity
3. Restart Apache service
4. Check htaccess permissions
```

### Issue 2: "Database connection error"
```
Solution:
1. Verify MySQL is running
   - XAMPP: Check MySQL status
   - Windows: Services → MySQL
   
2. Check credentials in config/config.php
   
3. Verify database exists:
   mysql -u root -p
   SHOW DATABASES;
   
4. Check database.php for errors
```

### Issue 3: "JSON parsing error"
```
Solution:
1. Check browser console (F12)
2. Test API directly: 
   http://localhost/work_analytics/api/dashboard_metrics.php
3. Should see pure JSON, not PHP errors
4. Check for PHP syntax errors
```

### Issue 4: "Charts not rendering"
```
Solution:
1. Check Chart.js CDN (F12 → Network tab)
2. Verify API returns valid data
3. Check console for JavaScript errors
4. Ensure canvas elements exist in HTML
```

### Issue 5: "No employee data"
```
Solution:
1. Verify schema.sql was imported
2. Count records: SELECT COUNT(*) FROM employees;
3. Re-import if needed: mysql < schema.sql
4. Check database name matches config.php
```

---

## Security Verification

### SQL Injection Test (Should be SAFE)
```
Try in custom report:
Department: '; DROP TABLE employees; --

Expected: No data modification, safe error handling
```

### CORS Headers Check
```bash
curl -i http://localhost/work_analytics/api/dashboard_metrics.php
```

Should see:
```
Access-Control-Allow-Origin: *
Content-Type: application/json
```

---

## Performance Optimization

### Verify Database Optimization
```sql
-- Check table size
SELECT 
  TABLE_NAME,
  ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
FROM information_schema.TABLES
WHERE table_schema = 'school_management';
```

### Check Query Performance
```sql
-- Enable query profiling
SET PROFILING = 1;

-- Run sample query
SELECT * FROM employees LIMIT 10;

-- Show profile
SHOW PROFILE;

-- Expected: < 10ms for full table scan
```

---

## Backup & Recovery

### Backup Database
```bash
# Create backup
mysqldump -u root -p school_management > backup.sql

# Restore from backup
mysql -u root -p school_management < backup.sql
```

### Backup Entire Application
```bash
# Windows
xcopy C:\xampp\htdocs\work_analytics backup_folder /E /I

# Linux/Mac
cp -r /var/www/html/work_analytics ./backup_folder
```

---

## Production Checklist

- [ ] Database password changed (not default)
- [ ] config/config.php credentials updated
- [ ] Error reporting disabled (only in production)
- [ ] HTTPS enabled (if public)
- [ ] Regular database backups scheduled
- [ ] Database user has limited permissions
- [ ] Application logs reviewed
- [ ] Performance monitored

---

## Support Resources

### Test API Endpoints
Visit in browser to test:
```
http://localhost/work_analytics/api/dashboard_metrics.php
http://localhost/work_analytics/api/department_distribution.php
http://localhost/work_analytics/api/attrition_data.php?year=2026
http://localhost/work_analytics/api/at_risk_employees.php
```

### Browser Developer Tools
1. Open: F12 or Ctrl+Shift+I
2. Check:
   - Console tab (JavaScript errors)
   - Network tab (API calls)
   - Application tab (local storage)

### Check Logs
```
Apache: C:\xampp\apache\logs\error.log
MySQL: C:\xampp\mysql\data\*.err
PHP: Check error_log in php.ini
```

---

## Final Verification

Run this checklist to confirm everything works:

```
✅ Database imported successfully
✅ MySQL service running
✅ Apache service running
✅ config/config.php has correct credentials
✅ http://localhost/work_analytics/ loads
✅ Dashboard tab shows metrics
✅ All charts render properly
✅ Attrition data loads
✅ Custom reports work
✅ No console errors (F12)
✅ API endpoints return JSON
✅ Print function works
✅ CSV export works
✅ Responsive on mobile view
```

If all items are ✅, your installation is complete and ready to use!

---

**Version**: 1.0.0  
**Last Updated**: 2026  
**Status**: Production Ready
