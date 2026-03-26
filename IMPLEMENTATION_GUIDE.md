# Employee Dashboard - Quick Implementation Guide

## Overview
This guide provides step-by-step instructions to update both dashboards to use live data from the `hr_management` database.

---

## 🚀 Step 1: Update Employee Portal Dashboard

### File: `employee_portal/controllers/EmployeePortalController.php`

**Current Code:**
```php
<?php
require_once __DIR__ . '/../Models/Employee.php';

class EmployeePortalController {
    public function index() {
        $title = "Employee Portal";
        $model = new RequestType();
        $requestTypes = $model->all();
        
        $content = __DIR__ . '/../views/employee-portal/main-content.php';
        require __DIR__ . '/../views/employee-portal/index.php';
    }
}
```

**Updated Code:**
```php
<?php
require_once __DIR__ . '/../Models/Employee.php';
require_once __DIR__ . '/../Models/Attendance.php';
require_once __DIR__ . '/../Models/Leave.php';

class EmployeePortalController {
    public function index() {
        $title = "Employee Portal";
        
        // Get current logged-in user ID
        $userId = $_SESSION['user']['id'] ?? null;
        
        if (!$userId) {
            redirect('/');
            return;
        }
        
        // Get employee data
        $employee = (new Employee())->findByUserId($userId);
        
        if (!$employee) {
            // Employee record not found
            $employee = [
                'name' => 'Employee',
                'position' => 'Not assigned',
                'employment_status' => 'Pending',
                'employee_id' => 'N/A'
            ];
        }
        
        // Get request types
        $requestTypes = (new RequestType())->all();
        
        // Get today's attendance
        $attendance = null;
        if ($employee['employee_id'] !== 'N/A') {
            $attendance = (new Attendance())->getTodayAttendance($employee['employee_id']);
        }
        
        // Get leave balance
        $leaves = null;
        if ($employee['employee_id'] !== 'N/A') {
            $leaves = (new Leave())->getBalance($employee['employee_id']);
        }
        
        // Pass to view
        $content = __DIR__ . '/../views/employee-portal/main-content.php';
        
        require __DIR__ . '/../views/employee-portal/index.php';
    }
}
```

### File: `employee_portal/views/employee-portal/main-content.php`

**Replace This:**
```php
<?php require_once __DIR__ . '/sampleData.php'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4 p-3 bg-white shadow-sm rounded">
        <div class="mb-3 mb-md-0">
            <h2 class="fw-bold mb-1 text-5xl">Employee Portal</h2>
            <p class="text-muted mb-0">
                Welcome back,
                <span class="fw-semibold">
                    <?= htmlspecialchars($employee['name'] ?? 'Employee'); ?>
                </span>
            </p>
        </div>
```

**With This:**
```php
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4 p-3 bg-white shadow-sm rounded">
        <div class="mb-3 mb-md-0">
            <h2 class="fw-bold mb-1 text-5xl">Employee Portal</h2>
            <p class="text-muted mb-0">
                Welcome back,
                <span class="fw-semibold">
                    <?= htmlspecialchars($employee['full_name'] ?? $employee['name'] ?? 'Employee'); ?>
                </span>
            </p>
        </div>
```

---

## 🚀 Step 2: Update Time Attendance Dashboard

### File: `time_attendance/time_attendance.php`

**Find This Section (Around Line 210):**
```php
<!-- Dashboard Tab -->
<div class="tab-pane fade show active" id="dashboard" role="tabpanel">
  <!-- Info boxes -->
  <div class="row">
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cog"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">CPU Traffic</span>
          <span class="info-box-number">10<small>%</small></span>
        </div>
      </div>
    </div>
```

**Replace With:**
```php
<!-- Dashboard Tab -->
<div class="tab-pane fade show active" id="dashboard" role="tabpanel">
  <!-- Attendance Summary Cards -->
  <div class="row mb-4">
    <?php
    // Get today's attendance summary
    $db = new Database();
    $conn = $db->getConnection();
    
    $query = "SELECT 
                COUNT(*) as total_employees,
                SUM(CASE WHEN clock_in IS NOT NULL THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN clock_in IS NULL THEN 1 ELSE 0 END) as absent
              FROM employees e
              LEFT JOIN attendance a ON e.employee_id = a.employee_id 
              WHERE DATE(a.date) = CURDATE() OR a.date IS NULL
              AND e.employment_status = 'Active'";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $summary = $result->fetch_assoc();
    ?>
    
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total Employees</span>
          <span class="info-box-number"><?= $summary['total_employees'] ?? 0 ?></span>
        </div>
      </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Present Today</span>
          <span class="info-box-number"><?= $summary['present'] ?? 0 ?></span>
        </div>
      </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-times"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Absent Today</span>
          <span class="info-box-number"><?= $summary['absent'] ?? 0 ?></span>
        </div>
      </div>
    </div>
  </div>
```

---

## 🚀 Step 3: Create API Endpoints

### File: `employee_portal/api/dashboard_data.php`

```php
<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../models/Leave.php';

try {
    $userId = $_SESSION['user']['id'] ?? null;
    
    if (!$userId) {
        throw new Exception('User not authenticated');
    }
    
    // Get employee data
    $employee = (new Employee())->findByUserId($userId);
    
    if (!$employee) {
        throw new Exception('Employee record not found');
    }
    
    // Get today's attendance
    $db = (new Database())->getConnection();
    
    // Attendance
    $stmt = $db->prepare("SELECT * FROM attendance WHERE employee_id = ? AND DATE(date) = CURDATE()");
    $stmt->execute([$employee['employee_id']]);
    $attendance = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Leave balance
    $stmt = $db->prepare("SELECT leave_type, COUNT(*) as used FROM leaves WHERE employee_id = ? AND status = 'Approved' GROUP BY leave_type");
    $stmt->execute([$employee['employee_id']]);
    $leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Upcoming schedule
    $stmt = $db->prepare("SELECT s.*, es.assigned_date FROM employee_shifts es 
                         JOIN shifts s ON es.shift_id = s.shift_id 
                         WHERE es.employee_id = ? AND es.assigned_date >= CURDATE() 
                         ORDER BY es.assigned_date LIMIT 7");
    $stmt->execute([$employee['employee_id']]);
    $schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'employee' => $employee,
        'attendance' => $attendance,
        'leaves' => $leaves,
        'schedule' => $schedule
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
```

### File: `time_attendance/app/api/attendance_summary.php`

```php
<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/Database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $query = "SELECT 
                COUNT(DISTINCT e.employee_id) as total_employees,
                COUNT(DISTINCT CASE WHEN a.clock_in IS NOT NULL THEN a.employee_id END) as present,
                COUNT(DISTINCT CASE WHEN a.clock_in IS NULL THEN a.employee_id END) as absent
              FROM employees e
              LEFT JOIN attendance a ON e.employee_id = a.employee_id AND DATE(a.date) = CURDATE()
              WHERE e.employment_status = 'Active'";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $summary = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'data' => $summary
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
```

---

## 🚀 Step 4: Add Navigation Links

### File: `employee_portal/views/employee-portal/main-content.php`

**Add This in the Button Group:**
```html
<a href="/capstone_hr_management_system/time_attendance/time_attendance.php" 
   class="btn btn-outline-success bg-slate-50">
    <i class="fa-solid fa-calendar me-1"></i> View My Schedule
</a>
```

### File: `time_attendance/time_attendance.php`

**Add This in the Sidebar:**
```html
<li class="nav-item">
    <a href="/capstone_hr_management_system/employee_portal/index.php?url=profile" 
       class="nav-link">
        <i class="fas fa-user-circle"></i>
        <p>Employee Profile</p>
    </a>
</li>
```

---

## ✅ Testing Checklist

- [ ] Employee Portal shows YOUR employee data (not sample data)
- [ ] Time Attendance dashboard shows real attendance numbers
- [ ] Clicking schedule link takes you to time attendance
- [ ] Clicking profile link takes you to employee portal
- [ ] Data is consistent between both modules
- [ ] No JavaScript errors in browser console
- [ ] Page loads within 2 seconds

---

## 🔍 Verification Queries

### Verify Employee Portal Data
```sql
SELECT e.* FROM employees e 
JOIN users u ON e.user_id = u.id 
WHERE u.id = 1;
```

### Verify Time Attendance Data
```sql
SELECT COUNT(*) FROM attendance WHERE DATE(date) = CURDATE();
```

### Verify Employee-User Link
```sql
SELECT e.employee_id, e.full_name, u.username, u.id 
FROM employees e 
JOIN users u ON e.user_id = u.id;
```

---

## 🚨 Troubleshooting

**Issue**: Data not showing in Employee Portal
- **Solution 1**: Check if employee record exists for logged-in user
  ```sql
  SELECT * FROM employees WHERE user_id = [YOUR_USER_ID];
  ```
- **Solution 2**: Clear browser cache and refresh
- **Solution 3**: Check browser console for JavaScript errors (F12)

**Issue**: Time Attendance shows 0 attendance
- **Solution 1**: Check if attendance records exist
  ```sql
  SELECT * FROM attendance WHERE DATE(date) = CURDATE();
  ```
- **Solution 2**: Verify employee records exist and are marked 'Active'

**Issue**: Cross-module links don't work
- **Solution**: Verify URL paths are correct and files exist

---

## 📝 Summary of Changes

| Component | Change | Impact | Priority |
|-----------|--------|--------|----------|
| Employee Portal Controller | Add live employee data fetch | HIGH | 1 |
| Employee Portal View | Use live data instead of sampleData | HIGH | 1 |
| Time Attendance Dashboard | Show real attendance metrics | HIGH | 1 |
| API Endpoints | Create JSON endpoints | MEDIUM | 2 |
| Navigation | Add cross-module links | MEDIUM | 2 |
| Widgets | Add dashboard cards | LOW | 3 |

---

## 🎓 Learning Resources

- Employee Model: `employee_portal/models/Employee.php`
- Attendance Model: `time_attendance/app/models/Attendance.php`
- Database Class: `employee_portal/core/Database.php`
- API Examples: Check existing endpoints in `/api/` folders

---

**Implementation Time**: 2-3 hours  
**Difficulty Level**: Medium  
**Testing Time**: 30 minutes  

**Total Estimated Effort**: 3-4 hours including testing

---

**Let's do it!** 🚀
