-- Clinic Management System Database Schema
-- Complete OOP-based clinic management system
-- Created for BCP Bulacan Clinic System

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS hr_management;
USE hr_management;

-- Employees Table - Using the main employees table from HR Management
-- The table structure is based on the provided employee management SQL dump
CREATE TABLE IF NOT EXISTS employees (
    employee_id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) DEFAULT NULL,
    full_name VARCHAR(255) NOT NULL,
    address TEXT DEFAULT NULL,
    contact_number VARCHAR(20) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    department VARCHAR(100) DEFAULT NULL,
    position VARCHAR(100) DEFAULT NULL,
    date_hired DATE DEFAULT NULL,
    employment_status VARCHAR(50) DEFAULT 'Active',
    -- Clinic Specific Fields
    medical_conditions TEXT,
    current_medications TEXT,
    blood_type VARCHAR(10),
    allergies TEXT,
    emergency_contact_name VARCHAR(255),
    emergency_contact_phone VARCHAR(20),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (employee_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Patients Table - Employees who become patients
CREATE TABLE IF NOT EXISTS cm_patients (
    patient_id VARCHAR(50) PRIMARY KEY,
    employee_id INT(11),
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    email VARCHAR(150),
    phone VARCHAR(20),
    address TEXT,
    birth_date DATE,
    gender ENUM('Male', 'Female', 'Other'),
    blood_type VARCHAR(10),
    allergies TEXT,
    medical_conditions TEXT,
    current_medications TEXT,
    patient_type ENUM('Student', 'Staff', 'Faculty', 'Visitor'),
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE SET NULL,
    INDEX idx_patient_type (patient_type),
    INDEX idx_status (status),
    INDEX idx_employee_id (employee_id)
);

-- Medical Records Table - Store health records
CREATE TABLE IF NOT EXISTS cm_medical_records (
    record_id VARCHAR(20) PRIMARY KEY,
    patient_id VARCHAR(20) NOT NULL,
    visit_date DATETIME NOT NULL,
    chief_complaint TEXT NOT NULL,
    diagnosis TEXT,
    treatment TEXT,
    consultation_type ENUM('Walk-in', 'Appointment', 'Emergency', 'Follow-up'),
    status ENUM('Completed', 'Pending', 'Follow-up') DEFAULT 'Pending',
    attending_physician VARCHAR(150),
    vital_signs JSON,
    medications_prescribed TEXT,
    notes TEXT,
    follow_up_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by VARCHAR(100),
    FOREIGN KEY (patient_id) REFERENCES cm_patients(patient_id) ON DELETE CASCADE,
    INDEX idx_visit_date (visit_date),
    INDEX idx_patient_id (patient_id),
    INDEX idx_status (status),
    INDEX idx_consultation_type (consultation_type)
);

-- Medicine Inventory Table - Manage clinic medicines
CREATE TABLE IF NOT EXISTS cm_medicine_inventory (
    medicine_id VARCHAR(20) PRIMARY KEY,
    medicine_name VARCHAR(200) NOT NULL,
    generic_name VARCHAR(200),
    category VARCHAR(100),
    dosage_form ENUM('Tablet', 'Capsule', 'Liquid', 'Injection', 'Ointment', 'Other'),
    strength VARCHAR(50),
    current_stock INT DEFAULT 0,
    reorder_level INT DEFAULT 10,
    unit_cost DECIMAL(8,2),
    selling_price DECIMAL(8,2),
    expiry_date DATE,
    supplier VARCHAR(200),
    manufacturer VARCHAR(200),
    storage_requirements TEXT,
    status ENUM('Available', 'Low Stock', 'Out of Stock', 'Expired') DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by VARCHAR(100),
    INDEX idx_medicine_name (medicine_name),
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_expiry_date (expiry_date)
);

-- Medicine Usage Logs - Track medicine consumption
CREATE TABLE IF NOT EXISTS cm_medicine_usage_logs (
    log_id VARCHAR(20) PRIMARY KEY,
    medicine_id VARCHAR(20) NOT NULL,
    record_id VARCHAR(20),
    usage_date DATETIME NOT NULL,
    quantity_used INT NOT NULL,
    remaining_stock INT NOT NULL,
    purpose VARCHAR(200),
    used_by VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (medicine_id) REFERENCES cm_medicine_inventory(medicine_id) ON DELETE CASCADE,
    FOREIGN KEY (record_id) REFERENCES cm_medical_records(record_id) ON DELETE SET NULL,
    INDEX idx_medicine_id (medicine_id),
    INDEX idx_usage_date (usage_date)
);

-- Emergency Cases Table - Handle urgent cases
CREATE TABLE IF NOT EXISTS cm_emergency_cases (
    case_id VARCHAR(20) PRIMARY KEY,
    patient_id VARCHAR(20) NOT NULL,
    incident_date DATETIME NOT NULL,
    incident_type ENUM('Accident', 'Medical Emergency', 'Injury', 'Other'),
    severity_level ENUM('Low', 'Medium', 'High', 'Critical'),
    chief_complaint TEXT NOT NULL,
    initial_assessment TEXT,
    treatment_provided TEXT,
    attending_staff VARCHAR(20),
    case_status ENUM('Active', 'Resolved', 'Transferred', 'Closed') DEFAULT 'Active',
    ambulance_called BOOLEAN DEFAULT FALSE,
    ambulance_arrival_time DATETIME,
    transfer_hospital VARCHAR(200),
    follow_up_required BOOLEAN DEFAULT FALSE,
    follow_up_date DATE,
    contact_person VARCHAR(150),
    contact_phone VARCHAR(20),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by VARCHAR(100),
    FOREIGN KEY (patient_id) REFERENCES cm_patients(patient_id) ON DELETE CASCADE,
    FOREIGN KEY (attending_staff) REFERENCES cm_employees(employee_id) ON DELETE SET NULL,
    INDEX idx_incident_date (incident_date),
    INDEX idx_patient_id (patient_id),
    INDEX idx_case_status (case_status),
    INDEX idx_severity_level (severity_level)
);

-- Clinic Reports Table - Store generated reports
CREATE TABLE IF NOT EXISTS cm_clinic_reports (
    report_id VARCHAR(20) PRIMARY KEY,
    report_type ENUM('Daily', 'Weekly', 'Monthly', 'Custom', 'Annual'),
    report_date DATE NOT NULL,
    start_date DATE,
    end_date DATE,
    report_data JSON,
    generated_by VARCHAR(100),
    status ENUM('Generated', 'Processing', 'Error') DEFAULT 'Generated',
    file_path VARCHAR(500),
    file_format ENUM('PDF', 'Excel', 'HTML', 'JSON'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_report_type (report_type),
    INDEX idx_report_date (report_date),
    INDEX idx_status (status)
);

-- Vital Signs Table - Detailed vital signs tracking
CREATE TABLE IF NOT EXISTS cm_vital_signs (
    vital_sign_id VARCHAR(20) PRIMARY KEY,
    record_id VARCHAR(20) NOT NULL,
    blood_pressure_systolic INT,
    blood_pressure_diastolic INT,
    heart_rate INT,
    respiratory_rate INT,
    temperature DECIMAL(4,1),
    weight DECIMAL(5,2),
    height DECIMAL(5,2),
    oxygen_saturation DECIMAL(3,1),
    blood_sugar DECIMAL(5,1),
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    recorded_by VARCHAR(100),
    FOREIGN KEY (record_id) REFERENCES cm_medical_records(record_id) ON DELETE CASCADE,
    INDEX idx_record_id (record_id),
    INDEX idx_recorded_at (recorded_at)
);

-- Document Attachments Table - Store medical documents
CREATE TABLE IF NOT EXISTS cm_document_attachments (
    attachment_id VARCHAR(20) PRIMARY KEY,
    record_id VARCHAR(20),
    patient_id VARCHAR(20),
    document_type ENUM('Lab Result', 'X-Ray', 'Prescription', 'Medical Certificate', 'Other'),
    document_name VARCHAR(200) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT,
    mime_type VARCHAR(100),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    uploaded_by VARCHAR(100),
    FOREIGN KEY (record_id) REFERENCES cm_medical_records(record_id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES cm_patients(patient_id) ON DELETE CASCADE,
    INDEX idx_record_id (record_id),
    INDEX idx_patient_id (patient_id),
    INDEX idx_document_type (document_type)
);

-- Department Table - Manage departments
CREATE TABLE IF NOT EXISTS cm_departments (
    department_id VARCHAR(10) PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL,
    department_head VARCHAR(20),
    location VARCHAR(200),
    contact_phone VARCHAR(20),
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_head) REFERENCES cm_employees(employee_id) ON DELETE SET NULL,
    INDEX idx_department_name (department_name)
);

-- Supplier Table - Manage medicine suppliers
CREATE TABLE IF NOT EXISTS cm_suppliers (
    supplier_id VARCHAR(20) PRIMARY KEY,
    supplier_name VARCHAR(200) NOT NULL,
    contact_person VARCHAR(150),
    phone VARCHAR(20),
    email VARCHAR(150),
    address TEXT,
    payment_terms VARCHAR(100),
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_supplier_name (supplier_name)
);

-- Insert sample data for testing
INSERT INTO cm_departments (department_id, department_name, location) VALUES
('ACAD', 'Academic Affairs', 'Main Building'),
('ADMIN', 'Administration', 'Admin Building'),
('HR', 'Human Resources', 'Admin Building'),
('FINANCE', 'Finance', 'Admin Building'),
('IT', 'Information Technology', 'Tech Building'),
('LIB', 'Library', 'Library Building'),
('MED', 'Medical Services', 'Clinic Building'),
('MAINT', 'Maintenance', 'Utility Building'),
('SEC', 'Security', 'Gate House');

-- Insert sample medicines
INSERT INTO cm_medicine_inventory (medicine_id, medicine_name, generic_name, category, dosage_form, strength, current_stock, reorder_level, unit_cost, expiry_date, supplier) VALUES
('1', 'Paracetamol', 'Acetaminophen', 'Analgesic', 'Tablet', '500mg', 500, 50, 2.50, '2025-12-31', 'MEDSUP001'),
('2', 'Ibuprofen', 'Ibuprofen', 'Analgesic', 'Tablet', '400mg', 300, 30, 3.75, '2025-11-30', 'MEDSUP001'),
('3', 'Amoxicillin', 'Amoxicillin', 'Antibiotic', 'Capsule', '500mg', 200, 25, 8.50, '2025-10-31', 'MEDSUP002'),
('4', 'Omeprazole', 'Omeprazole', 'Antacid', 'Capsule', '20mg', 150, 20, 6.25, '2026-01-31', 'MEDSUP002'),
('5', 'Loratadine', 'Loratadine', 'Antihistamine', 'Tablet', '10mg', 400, 40, 4.00, '2025-09-30', 'MEDSUP001');

-- Insert sample suppliers
INSERT INTO cm_suppliers (supplier_id, supplier_name, contact_person, phone, email) VALUES
('MEDSUP001', 'MediCare Pharmaceuticals', 'John Smith', '123-456-7890', 'john@medicare.com'),
('MEDSUP002', 'HealthPlus Supplies', 'Maria Santos', '098-765-4321', 'maria@healthplus.com');

-- Create views for common queries
CREATE VIEW IF NOT EXISTS v_employee_summary AS
SELECT 
    e.employee_id,
    CONCAT(e.first_name, ' ', e.last_name) as full_name,
    e.department,
    e.position,
    e.employee_type,
    e.employment_status,
    COUNT(p.patient_id) as medical_records_count,
    MAX(mr.visit_date) as last_visit_date
FROM cm_employees e
LEFT JOIN cm_patients p ON e.employee_id = p.employee_id
LEFT JOIN cm_medical_records mr ON p.patient_id = mr.patient_id
GROUP BY e.employee_id;

CREATE VIEW IF NOT EXISTS v_patient_summary AS
SELECT 
    p.patient_id,
    CONCAT(p.first_name, ' ', p.last_name) as full_name,
    p.patient_type,
    p.status,
    COUNT(mr.record_id) as total_visits,
    COUNT(CASE WHEN mr.visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 END) as visits_last_30_days,
    MAX(mr.visit_date) as last_visit_date
FROM cm_patients p
LEFT JOIN cm_medical_records mr ON p.patient_id = mr.patient_id
GROUP BY p.patient_id;

CREATE VIEW IF NOT EXISTS v_medicine_status AS
SELECT 
    medicine_id,
    medicine_name,
    category,
    current_stock,
    reorder_level,
    unit_cost,
    expiry_date,
    CASE 
        WHEN expiry_date < CURDATE() THEN 'Expired'
        WHEN current_stock <= reorder_level THEN 'Low Stock'
        WHEN current_stock = 0 THEN 'Out of Stock'
        ELSE 'Available'
    END as stock_status,
    CASE 
        WHEN expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'Expiring Soon'
        ELSE 'OK'
    END as expiry_status
FROM cm_medicine_inventory;

-- Create stored procedures for common operations (simplified version)
DELIMITER //

CREATE PROCEDURE sp_add_employee(
    IN p_employee_id VARCHAR(20),
    IN p_first_name VARCHAR(100),
    IN p_last_name VARCHAR(100),
    IN p_email VARCHAR(150),
    IN p_phone VARCHAR(20),
    IN p_department VARCHAR(100),
    IN p_position VARCHAR(100),
    IN p_employee_type VARCHAR(20),
    IN p_created_by VARCHAR(100)
)
BEGIN
    INSERT INTO cm_employees (
        employee_id, first_name, last_name, email, phone, 
        department, position, employee_type, created_by
    ) VALUES (
        p_employee_id, p_first_name, p_last_name, p_email, p_phone,
        p_department, p_position, p_employee_type, p_created_by
    );
    
    -- Also create patient record for the employee
    INSERT INTO cm_patients (
        patient_id, employee_id, first_name, last_name, email, phone,
        patient_type, status
    ) VALUES (
        CONCAT('PAT', p_employee_id), p_employee_id, p_first_name, p_last_name, 
        p_email, p_phone, 'Staff', 'Active'
    );
END//

CREATE PROCEDURE sp_add_medical_record(
    IN p_record_id VARCHAR(20),
    IN p_patient_id VARCHAR(20),
    IN p_chief_complaint TEXT,
    IN p_diagnosis TEXT,
    IN p_treatment TEXT,
    IN p_consultation_type VARCHAR(20),
    IN p_attending_physician VARCHAR(150),
    IN p_created_by VARCHAR(100)
)
BEGIN
    -- Insert medical record
    INSERT INTO cm_medical_records (
        record_id, patient_id, chief_complaint, diagnosis, treatment,
        consultation_type, attending_physician, created_by
    ) VALUES (
        p_record_id, p_patient_id, p_chief_complaint, p_diagnosis, p_treatment,
        p_consultation_type, p_attending_physician, p_created_by
    );
END//

DELIMITER ;

-- Create triggers for automatic status updates (simplified version)
DELIMITER //

CREATE TRIGGER tr_medicine_stock_update 
BEFORE UPDATE ON cm_medicine_inventory
FOR EACH ROW
BEGIN
    IF NEW.current_stock <= 0 THEN
        SET NEW.status = 'Out of Stock';
    ELSEIF NEW.current_stock <= NEW.reorder_level THEN
        SET NEW.status = 'Low Stock';
    ELSEIF NEW.expiry_date < CURDATE() THEN
        SET NEW.status = 'Expired';
    ELSE
        SET NEW.status = 'Available';
    END IF;
END//

CREATE TRIGGER tr_emergency_case_close
BEFORE UPDATE ON cm_emergency_cases
FOR EACH ROW
BEGIN
    IF NEW.case_status = 'Closed' AND OLD.case_status != 'Closed' THEN
        SET NEW.updated_at = NOW();
    END IF;
END//

DELIMITER ;

-- Grant permissions (adjust as needed for your setup)
-- GRANT SELECT, INSERT, UPDATE, DELETE ON bcp_clinic_system.* TO 'clinic_user'@'localhost';

-- Database setup complete
SELECT 'Clinic Management System Database Setup Complete!' as status;
