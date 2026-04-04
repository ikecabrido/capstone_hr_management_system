# Employee Dashboard Package

## Overview
This package contains all the necessary files for your collaborator to run the `employee_dashboard.php` file in their employee portal or any location.

## Contents

### Folder Structure
```
employee_dashboard_package/
├── app/
│   ├── config/
│   │   └── Database.php
│   ├── core/
│   │   └── Session.php
│   ├── models/
│   │   ├── Users.php
│   │   ├── Employee.php
│   │   ├── Attendance.php
│   │   ├── Leave.php
│   │   ├── EmployeeShift.php
│   │   └── Shift.php
│   ├── controllers/
│   │   ├── AuthController.php
│   │   └── AttendanceController.php
│   ├── helpers/
│   │   ├── Helper.php
│   │   ├── AuditLog.php
│   │   └── QRHelper.php
│   └── components/
│       └── Sidebar.php
└── assets/
```

## Installation Instructions

1. **Copy the `employee_dashboard_package` folder** to your root directory or merge with existing folder structure.

2. **Directory Placement:**
   - If using in `employee_portal/`: Copy the `app` folder into `employee_portal/`
   - If using standalone: Keep the folder structure as is

3. **Copy the main file:**
   - Copy your `employee_dashboard.php` file into the same location as the `app` folder
   - Make sure relative paths work correctly (adjust if needed)

4. **Required Files NOT Included:**
   - **CSS/JavaScript Files**: You'll need to copy these from your `time_attendance/assets/` folder:
     - `style.css`
     - `employeeDashboard.css`
     - `mobile-responsive.js`
     - `Chart.js library` (referenced in employee_dashboard.php)

   - **Image Files**: Copy the BCP logo image if referenced:
     - `bcp-logo2.png`
     - `Bestlink College of the Philippines.jpeg`

5. **Database Configuration:**
   - Update `app/config/Database.php` with your database credentials if different from the original:
     ```php
     private $db_name = "hr_management";
     private $username = "root";
     private $password = "";
     ```

## File Descriptions

### Core Configuration
- **Database.php** - PDO database connection handler with timezone support
- **Session.php** - Session management utility

### Models (Database Layer)
- **Users.php** - User authentication and login
- **Employee.php** - Employee data retrieval and management
- **Attendance.php** - Attendance records and status
- **Leave.php** - Leave requests and balances
- **EmployeeShift.php** - Employee shift assignments
- **Shift.php** - Shift definitions and operations

### Controllers (Business Logic)
- **AuthController.php** - Authentication and authorization
- **AttendanceController.php** - Time in/out and QR processing

### Helpers (Utility Functions)
- **Helper.php** - Common utility functions (formatting, calculations, validation)
- **AuditLog.php** - Audit trail logging
- **QRHelper.php** - QR code token generation and validation

### Components (UI)
- **Sidebar.php** - Navigation sidebar component

## Usage

1. Place the `employee_dashboard.php` file in the root of this package (same level as `app` folder)
2. Access via browser: `http://localhost/path/to/employee_dashboard.php`
3. The file will automatically include all required dependencies from the `app` folder

## Important Notes

- ⚠️ **Relative Paths**: All files use relative paths (`../app/...`). Keep the folder structure intact.
- 🔐 **Security**: Ensure proper authentication before using (AuthController checks sessions)
- 🗄️ **Database**: The application requires the `hr_management` database with proper tables
- 🎨 **Assets**: CSS, JavaScript, and images must be placed in the `assets/` folder or adjusted paths
- 📱 **Responsive**: Includes mobile-responsive design with sidebar toggle

## Database Tables Required

The following tables must exist in the `hr_management` database:
- `users` - User accounts
- `employees` - Employee information
- `attendance` - Attendance records
- `leave_requests` - Leave request records
- `leave_balances` - Leave balance tracking
- `leave_types` - Leave type definitions
- `shifts` - Shift definitions
- `employee_shifts` - Employee-shift assignments
- `holidays` - Holiday calendar
- `attendance_tokens` - QR token management
- `audit_logs` - Audit trail

## Troubleshooting

**Connection Issues:**
- Check database credentials in `Database.php`
- Ensure MySQL server is running
- Verify table names match your schema

**Path Issues:**
- Verify folder structure is maintained
- Check relative paths in require statements
- Adjust paths if folder is renamed

**Missing Assets:**
- Copy CSS/JS files from your original `time_attendance/assets/`
- Copy image files (logo, background)
- Update paths in HTML if needed

## Support

If issues arise, check:
1. Database connectivity
2. File permissions
3. Folder structure integrity
4. Session configuration
5. PHP version compatibility (requires PHP 7.0+)

---

**Created:** March 19, 2026
**Package Version:** 1.0
