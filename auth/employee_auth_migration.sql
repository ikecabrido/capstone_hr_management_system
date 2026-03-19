-- Role-Based Authentication Migration Script
-- This script adds department and position data to employees for role-based routing
-- Run this in phpMyAdmin or MySQL command line

-- Add department column if it doesn't exist
-- Note: Check if column exists first, then add
-- This uses a workaround since MySQL doesn't support ADD COLUMN IF NOT EXISTS

-- Method: Use ALTER TABLE with IGNORE to prevent errors (works in most cases)
-- Or run these individual queries separately:

-- 1. Add department column
-- ALTER TABLE employees ADD COLUMN department VARCHAR(100) DEFAULT NULL;

-- 2. Add position column  
-- ALTER TABLE employees ADD COLUMN position VARCHAR(100) DEFAULT NULL;

-- Since ALTER TABLE ADD COLUMN IF NOT EXISTS is not supported in MySQL,
-- we will use a workaround - try to add and ignore error if it exists
-- This is a simplified approach that works for most cases

-- Actually, let's just run the UPDATE statements first
-- If columns don't exist, they will just add null values

-- Update existing employees with department and position data based on their email
UPDATE employees SET department = 'Human Resources', position = 'HR Manager' WHERE email = 'ana.cruz@school.edu';
UPDATE employees SET department = 'Information Technology', position = 'IT Specialist' WHERE email = 'mark.lee@school.edu';
UPDATE employees SET department = 'Finance', position = 'Accountant' WHERE email = 'john.rey@school.edu';
UPDATE employees SET department = 'Finance', position = 'Payroll Officer' WHERE email = 'liza.torres@school.edu';
UPDATE employees SET department = 'Human Resources', position = 'Recruitment Specialist' WHERE email = 'pedro.santos@school.edu';
UPDATE employees SET department = 'Legal', position = 'Compliance Officer' WHERE email = 'karen.villanueva@school.edu';
UPDATE employees SET department = 'Human Resources', position = 'L&D Coordinator' WHERE email = 'maria.santos@school.edu';
UPDATE employees SET department = 'Human Resources', position = 'Performance Analyst' WHERE email = 'james.wilson@school.edu';
UPDATE employees SET department = 'Human Resources', position = 'Employee Relations Specialist' WHERE email = 'emily.davis@school.edu';
UPDATE employees SET department = 'Human Resources', position = 'Workforce Planner' WHERE email = 'robert.brown@school.edu';
UPDATE employees SET department = 'Human Resources', position = 'Exit Interview Coordinator' WHERE email = 'sarah.miller@school.edu';
UPDATE employees SET department = 'Clinic', position = 'Nurse' WHERE email = 'michael.garcia@school.edu';
UPDATE employees SET department = 'Human Resources', position = 'Timekeeping Specialist' WHERE email = 'jennifer.lopez@school.edu';
UPDATE employees SET department = 'Academic', position = 'Faculty' WHERE email = 'david.martinez@school.edu';

-- Update employees without matching emails using first_name and last_name
UPDATE employees SET department = 'Human Resources', position = 'HR Manager' WHERE first_name = 'Ana' AND last_name = 'Cruz' AND (department IS NULL OR department = '');
UPDATE employees SET department = 'Information Technology', position = 'IT Manager' WHERE first_name = 'Mark' AND last_name = 'Lee' AND (department IS NULL OR department = '');
UPDATE employees SET department = 'Finance', position = 'Accountant' WHERE first_name = 'John' AND last_name = 'Rey' AND (department IS NULL OR department = '');
UPDATE employees SET department = 'Finance', position = 'Payroll Officer' WHERE first_name = 'Liza' AND last_name = 'Torres' AND (department IS NULL OR department = '');
UPDATE employees SET department = 'Human Resources', position = 'Recruitment Specialist' WHERE first_name = 'Pedro' AND last_name = 'Santos' AND (department IS NULL OR department = '');

-- Create department to role mapping for authentication
CREATE TABLE IF NOT EXISTS department_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL UNIQUE,
    system_role VARCHAR(50) NOT NULL,
    redirect_page VARCHAR(255) NOT NULL,
    description VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert department role mappings (use INSERT IGNORE to avoid errors)
INSERT IGNORE INTO department_roles (department_name, system_role, redirect_page, description) VALUES
('Human Resources', 'hr_admin', 'legal_compliance/legal_compliance.php', 'HR Admin - Full HR access'),
('Information Technology', 'it_admin', 'time_attendance/time_attendance.php', 'IT Staff - System administration'),
('Finance', 'payroll', 'payroll/payroll.php', 'Finance - Payroll and accounting'),
('Legal', 'compliance', 'legal_compliance/legal_compliance.php', 'Legal - Compliance and legal'),
('Clinic', 'clinic', 'clinic/clinic.php', 'Health Services'),
('Academic', 'employee', 'employee_portal/employee_portal.php', 'Faculty/Staff Portal'),
('Administration', 'admin', 'legal_compliance/legal_compliance.php', 'Administrative access');

-- To add the columns, run these commands separately in phpMyAdmin:
-- ALTER TABLE employees ADD COLUMN department VARCHAR(100) DEFAULT NULL;
-- ALTER TABLE employees ADD COLUMN position VARCHAR(100) DEFAULT NULL;
