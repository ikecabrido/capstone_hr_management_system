-- ============================================================================
-- HR LEARNING & DEVELOPMENT SYSTEM - DATABASE CREATION SCRIPT
-- ============================================================================
-- This script creates all necessary tables for the HR Learning & Development
-- system including user management, training programs, performance management,
-- career development, LMS, compliance, and analytics.
-- 
-- Database: hr_learning_dev
-- Charset: utf8mb4
-- Collation: utf8mb4_unicode_ci
-- ============================================================================

-- Create Database
CREATE DATABASE IF NOT EXISTS hr_learning_dev;
USE hr_learning_dev;

-- ============================================================================
-- 1. USERS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    role ENUM('admin', 'manager', 'employee', 'trainer') DEFAULT 'employee',
    department VARCHAR(100),
    position VARCHAR(100),
    manager_id INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_role (role),
    INDEX idx_status (status),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. TRAINING PROGRAMS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS training_programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description LONGTEXT,
    category VARCHAR(50) NOT NULL,
    type VARCHAR(50),
    duration INT COMMENT 'Duration in hours',
    created_by INT NOT NULL,
    status ENUM('draft', 'active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. TRAINING ENROLLMENTS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS training_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    program_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    start_date DATE,
    end_date DATE,
    completion_date TIMESTAMP NULL,
    status ENUM('pending', 'in_progress', 'completed', 'dropped') DEFAULT 'pending',
    progress_percentage INT DEFAULT 0,
    score DECIMAL(5,2),
    certificate_issued BOOLEAN DEFAULT FALSE,
    UNIQUE KEY unique_enrollment (user_id, program_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (program_id) REFERENCES training_programs(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 4. CAREER PATHS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS career_paths (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description LONGTEXT,
    target_position VARCHAR(100),
    prerequisites VARCHAR(255),
    skills_required JSON,
    duration_months INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ensure 'prerequisites' column exists for legacy installations
ALTER TABLE career_paths
    ADD COLUMN IF NOT EXISTS prerequisites VARCHAR(255);

-- ensure 'skills_required' column exists too (added later)
ALTER TABLE career_paths
    ADD COLUMN IF NOT EXISTS skills_required JSON;

-- ensure 'created_by' exists on tables that were added later
ALTER TABLE career_paths
    ADD COLUMN IF NOT EXISTS created_by INT;
ALTER TABLE training_programs
    ADD COLUMN IF NOT EXISTS created_by INT;
ALTER TABLE leadership_programs
    ADD COLUMN IF NOT EXISTS created_by INT;
ALTER TABLE individual_development_plans
    ADD COLUMN IF NOT EXISTS created_by INT;
ALTER TABLE compliance_trainings
    ADD COLUMN IF NOT EXISTS created_by INT;

-- ============================================================================
-- 5. INDIVIDUAL DEVELOPMENT PLANS (IDPs) TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS individual_development_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    career_path_id INT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    objectives LONGTEXT,
    milestones JSON,
    status ENUM('draft', 'active', 'completed') DEFAULT 'active',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (career_path_id) REFERENCES career_paths(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ensure 'milestones' column exists for legacy installations
ALTER TABLE individual_development_plans
    ADD COLUMN IF NOT EXISTS milestones JSON;

-- ============================================================================
-- 6. COMPETENCIES TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS competencies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description LONGTEXT,
    category VARCHAR(50),
    proficiency_levels JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 7. USER COMPETENCIES TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS user_competencies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    competency_id INT NOT NULL,
    current_level VARCHAR(50),
    target_level VARCHAR(50),
    assessed_date TIMESTAMP,
    assessed_by INT,
    UNIQUE KEY unique_user_competency (user_id, competency_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (competency_id) REFERENCES competencies(id) ON DELETE CASCADE,
    FOREIGN KEY (assessed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 8. LEADERSHIP PROGRAMS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS leadership_programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description LONGTEXT,
    level VARCHAR(50),
    focus_area VARCHAR(100),
    duration_weeks INT,
    target_audience VARCHAR(255),
    outcomes JSON,
    created_by INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 8b. LEADERSHIP ENROLLMENTS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS leadership_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    program_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    start_date DATE,
    end_date DATE,
    completion_date TIMESTAMP NULL,
    status ENUM('pending', 'in_progress', 'completed', 'dropped') DEFAULT 'pending',
    feedback LONGTEXT,
    UNIQUE KEY unique_leadership_enrollment (user_id, program_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (program_id) REFERENCES leadership_programs(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 10. PERFORMANCE REVIEWS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS performance_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    review_period_start DATE NOT NULL,
    review_period_end DATE NOT NULL,
    rating DECIMAL(3,2),
    comments LONGTEXT,
    reviewed_date TIMESTAMP,
    status ENUM('draft', 'submitted', 'completed') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES users(id),
    FOREIGN KEY (reviewer_id) REFERENCES users(id),
    INDEX idx_employee_id (employee_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 11. 360 DEGREE FEEDBACK TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS feedback_360 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    reviewer_type ENUM('manager', 'peer', 'subordinate', 'external'),
    rating DECIMAL(3,2),
    comments LONGTEXT,
    feedback_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES users(id),
    FOREIGN KEY (reviewer_id) REFERENCES users(id),
    INDEX idx_employee_id (employee_id),
    INDEX idx_reviewer_type (reviewer_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 12. LMS COURSES TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS lms_courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description LONGTEXT,
    category VARCHAR(50),
    instructor_id INT,
    course_content LONGTEXT,
    duration_hours INT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 13. LMS COURSE ENROLLMENTS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS lms_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completion_date TIMESTAMP NULL,
    progress_percentage INT DEFAULT 0,
    score DECIMAL(5,2),
    status ENUM('enrolled', 'in_progress', 'completed', 'dropped') DEFAULT 'enrolled',
    UNIQUE KEY unique_lms_enrollment (user_id, course_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES lms_courses(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 14. COMPLIANCE TRAININGS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS compliance_trainings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description LONGTEXT,
    compliance_type VARCHAR(100),
    due_date DATE,
    frequency VARCHAR(50),
    mandatory BOOLEAN DEFAULT TRUE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_compliance_type (compliance_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 15. COMPLIANCE TRAINING ASSIGNMENTS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS compliance_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    compliance_training_id INT NOT NULL,
    assigned_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    due_date DATE NOT NULL,
    completion_date TIMESTAMP NULL,
    status ENUM('assigned', 'in_progress', 'completed', 'overdue') DEFAULT 'assigned',
    acknowledgment_date TIMESTAMP NULL,
    UNIQUE KEY unique_compliance_assignment (user_id, compliance_training_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (compliance_training_id) REFERENCES compliance_trainings(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_user_id (user_id),
    INDEX idx_due_date (due_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 16. TRAINING ANALYTICS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS training_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    program_id INT,
    total_enrolled INT DEFAULT 0,
    completed INT DEFAULT 0,
    in_progress INT DEFAULT 0,
    dropped INT DEFAULT 0,
    average_score DECIMAL(5,2),
    completion_rate DECIMAL(5,2),
    roi_value DECIMAL(10,2),
    created_date DATE DEFAULT CURRENT_DATE,
    FOREIGN KEY (program_id) REFERENCES training_programs(id),
    INDEX idx_program_id (program_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 17. SUCCESSION PLANNING TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS succession_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    position_id INT,
    position_name VARCHAR(150) NOT NULL,
    current_holder_id INT,
    successor_id INT,
    readiness_level VARCHAR(50),
    planned_transition_date DATE,
    status ENUM('draft', 'active', 'completed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (current_holder_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (successor_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 18. TEAM BUILDING ACTIVITIES TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS team_activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description LONGTEXT,
    activity_date DATE NOT NULL,
    department VARCHAR(100),
    organizer_id INT,
    budget DECIMAL(10,2),
    participant_count INT,
    status ENUM('planned', 'ongoing', 'completed', 'cancelled') DEFAULT 'planned',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_activity_date (activity_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 19. TRAINING LOGS / AUDIT TRAIL TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS training_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    action_type VARCHAR(50),
    details LONGTEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- DATABASE CREATION COMPLETE
-- ============================================================================
-- All tables have been created successfully with proper relationships,
-- indexes, and constraints. The database is now ready for use.
-- ============================================================================
