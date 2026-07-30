--  /*
-- |--------------------------------------------------------------------------
-- | CREATE TABLES
-- |--------------------------------------------------------------------------
-- |
-- */
CREATE TABLE
    ep_employee_documents (
        approval_id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        remarks TEXT DEFAULT NULL,
        submitted_on DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        submit_by INT UNSIGNED DEFAULT NULL,
        department INT UNSIGNED DEFAULT NULL,
        approver_id INT UNSIGNED DEFAULT NULL,
        approved_at DATETIME DEFAULT NULL,
        decision ENUM ('Approved', 'Rejected', 'Pending') DEFAULT 'Pending',
        file_path VARCHAR(255) DEFAULT NULL,
        KEY idx_submit_by (submit_by),
        KEY idx_department (department),
        KEY idx_approver (approver_id)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE
    ep_online_meetings (
        meetings_id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255),
        meeting_link TEXT,
        created_by INT,
        employee_id INT,
        scheduled_at DATETIME,
        status ENUM ('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled'
    );

CREATE TABLE
    ep_online_meetings (
        meetings_id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255),
        meeting_link TEXT,
        created_by INT,
        employee_id INT,
        scheduled_at DATETIME,
        status ENUM ('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled'
    );

CREATE TABLE
    ep_notifications (
        notification_id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        type ENUM (
            'announcement',
            'payroll',
            'leave',
            'training',
            'performance',
            'document',
            'meeting',
            'compliance',
            'general'
        ) DEFAULT 'general',
        priority ENUM ('normal', 'important', 'urgent') DEFAULT 'normal',
        target_url VARCHAR(255),
        created_by_user_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

CREATE TABLE
    ep_notification_recipients (
        recipient_id INT AUTO_INCREMENT PRIMARY KEY,
        notification_id INT NOT NULL,
        employee_id INT NOT NULL,
        is_read TINYINT (1) DEFAULT 0,
        read_at TIMESTAMP NULL,
        UNIQUE KEY uq_notification_employee (notification_id, employee_id),
        INDEX idx_employee (employee_id),
        INDEX idx_notification (notification_id),
        INDEX idx_is_read (is_read)
    );

CREATE TABLE
    ep_employee_benefits (
        benefit_id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        record_type ENUM (
            'SSS',
            'PhilHealth',
            'Pag-IBIG',
            'Withholding Tax',
            'BIR Form 2316'
        ) NOT NULL,
        period VARCHAR(20) NOT NULL,
        description TEXT,
        file_name VARCHAR(255) NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        uploaded_by INT NOT NULL,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

CREATE TABLE
    ep_payroll_request (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        request_type VARCHAR(100) NOT NULL,
        purpose VARCHAR(255),
        remarks TEXT,
        payroll_period_start DATE,
        payroll_period_end DATE,
        status ENUM (
            'Pending',
            'Processing',
            'Approved',
            'Rejected',
            'Completed',
            'Cancelled'
        ) DEFAULT 'Pending',
        requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        processed_at TIMESTAMP NULL,
        processed_by INT,
        rejection_reason TEXT,
        document_path VARCHAR(500),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_employee (employee_id),
        INDEX idx_status (status),
        INDEX idx_requested (requested_at)
    );

--  /*
-- |--------------------------------------------------------------------------
-- | ALTER TABLES
-- |--------------------------------------------------------------------------
-- |
-- */
ALTER TABLE users
ADD COLUMN password_reset_token VARCHAR(255),
ADD COLUMN password_reset_expires DATETIME;

ALTER TABLE ep_employee_benefits ADD CONSTRAINT fk_employee_benefits_employee FOREIGN KEY (employee_id) REFERENCES employees (id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE ep_notification_recipients ADD CONSTRAINT fk_notification FOREIGN KEY (notification_id) REFERENCES ep_notifications (notification_id) ON DELETE CASCADE;
    