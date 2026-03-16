-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 07, 2026 at 10:39 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hr_management`
--

-- Create Database
CREATE DATABASE IF NOT EXISTS hr_management;
USE hr_management;

-- ============================================================================
-- HR MANAGEMENT & LEARNING & DEVELOPMENT SYSTEM - DATABASE CREATION SCRIPT
-- ============================================================================
-- This script creates all necessary tables for the combined HR Management
-- and Learning & Development system.
-- ============================================================================

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('recruitment','payroll','time','compliance','workforce','employee','learning','performance','engagement_relations','exit','admin','manager','trainer') NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `theme` enum('light','dark') DEFAULT 'light',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `theme`, `created_at`, `profile_pic`) VALUES
(1, 'hr_payroll', 'hr_payroll@example.com', '$2y$10$YSkTSwrSdqSBsF2e.pfyq.mNCCIF7ijV4h/s1pAc8Q7KlQHzbQTmq', 'Russell Ike', 'admin', 'light', '2026-03-06 21:13:06', NULL),
(2, 'hr_recruitment', 'hr_recruitment@example.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'Administrator', 'admin', 'light', '2026-03-07 02:46:33', NULL),
(3, 'hr_time', 'hr_time@example.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'Admin', 'admin', 'light', '2026-03-07 02:47:07', NULL),
(4, 'hr_employee', 'hr_employee@example.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'someone', 'admin', 'light', '2026-03-07 02:47:55', NULL),
(5, 'hr_compliance', 'hr_compliance@example.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'comply', 'admin', 'light', '2026-03-07 02:48:19', NULL),
(6, 'hr_workforce', 'hr_workforce@example.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'force', 'admin', 'light', '2026-03-07 02:48:43', NULL),
(7, 'hr_learning', 'hr_learning@example.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'learn', 'admin', 'light', '2026-03-07 02:49:22', NULL),
(8, 'hr_performance', 'hr_performance@example.com', '$2y$10$/aFKLVK.xloqiY31X4T.dOPKY2AnnkrpaME4f2z.l4LhQurY1/Zzy', 'Perform', 'performance', 'light', '2026-03-07 02:49:46', 'user_8.jpg'),
(9, 'hr_engagement', 'hr_engagement@example.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'engage', 'admin', 'light', '2026-03-07 02:50:37', NULL),
(10, 'hr_exit', 'hr_exit@example.com', '$2y$10$SlnmHAtElc/mb8xlerMDAOGb6n8KIk/3bGLs.z8Gjpk6r6eGjicOS', 'exit', 'admin', 'light', '2026-03-07 02:51:04', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_email` (`email`),
  ADD CONSTRAINT `fk_manager` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ============================================================================
-- INSERT SAMPLE DATA FOR LEARNING & DEVELOPMENT MODULES
-- ============================================================================

-- Insert training programs
INSERT INTO training_programs (name, description, category, type, duration, created_by, status) VALUES
('Leadership Foundations', 'Introduction to leadership skills for new managers.', 'Professional Development', 'John Smith', 1, 1, 'active'),
('Effective Communication', 'Build communication skills for remote teams.', 'Professional Development', 'Sarah Johnson', 1, 1, 'active'),
('Agile Project Management', 'Master agile methodologies and Scrum practices.', 'Professional Development', 'Michael Chen', 1, 1, 'active'),
('Python for Data Analysis', 'Learn Python fundamentals for data visualization and analysis.', 'Technical', 'Emily Davis', 1, 1, 'active'),
('Customer Service Excellence', 'Deliver exceptional customer experiences and handle difficult situations.', 'Professional Development', 'Lisa Anderson', 1, 1, 'active'),
('Digital Marketing Basics', 'Introduction to social media, SEO, and content marketing.', 'Marketing', 'Robert Wilson', 1, 1, 'active'),
('Time Management & Productivity', 'Optimize your workflow and achieve personal goals efficiently.', 'Professional Development', 'Jessica Martinez', 1, 1, 'active'),
('Sales Techniques', 'Proven strategies to close deals and build client relationships.', 'Sales', 'David Taylor', 1, 1, 'active'),
('Cybersecurity Awareness', 'Protect yourself and the company from security threats.', 'Security', 'Kevin Brown', 1, 1, 'active');

-- Insert career paths
INSERT INTO career_paths (name, description, target_position, prerequisites, skills_required, duration_months, status, created_by) VALUES
('Technical Lead', 'Progress from senior engineer to technical leadership and team management', 'Technical Lead / Engineering Manager', '5+ years professional experience, proven technical expertise', '["Leadership", "Communication", "System Design", "Mentoring", "Project Management"]', 18, 'active', 1),
('Project Manager', 'Develop skills to transition into project management and delivery leadership', 'Project Manager', '3+ years in any professional role', '["Planning", "Stakeholder Management", "Risk Management", "Agile Methodologies", "Communication"]', 12, 'active', 1),
('Product Manager', 'Transition into product management with focus on strategy and customer success', 'Product Manager', 'Experience with product or customer interaction', '["Product Strategy", "Data Analysis", "User Research", "Roadmap Planning", "Cross-functional Leadership"]', 15, 'active', 1),
('Subject Matter Expert', 'Develop deep expertise in a specific domain and become the go-to specialist', 'Senior Specialist / Subject Matter Expert', '2+ years experience in your domain', '["Deep Technical Knowledge", "Documentation", "Research", "Mentoring", "Innovation"]', 24, 'active', 1);

-- Insert sample training enrollments
INSERT INTO training_enrollments (user_id, program_id, status) VALUES
(4, 1, 'pending'),
(4, 2, 'in_progress'),
(7, 1, 'completed');

-- Insert sample LMS courses
INSERT INTO lms_courses (title, description, category, instructor_id, status) VALUES
('Introduction to Leadership', 'Basic leadership skills course', 'Leadership', 1, 'published'),
('Communication Skills', 'Effective communication techniques', 'Professional Development', 1, 'published');

-- Insert sample LMS enrollments
INSERT INTO lms_enrollments (user_id, course_id, status, progress_percentage, score) VALUES
(4, 1, 'in_progress', 50, NULL),
(7, 2, 'completed', 100, 95.0);

-- Insert sample compliance assignments
INSERT INTO compliance_assignments (user_id, compliance_training_id, status) VALUES
(4, 1, 'completed'),
(7, 2, 'in_progress');

-- Insert sample compliance trainings
INSERT INTO compliance_trainings (title, description, compliance_type, mandatory, created_by) VALUES
('Code of Conduct Training', 'Annual code of conduct review', 'Ethics', 1, 1),
('Data Privacy Compliance', 'GDPR and data protection training', 'Privacy', 1, 1);

-- Insert sample training analytics
INSERT INTO training_analytics (program_id, total_enrolled, completed, in_progress, dropped, average_score, completion_rate, roi_value, created_date) VALUES
(1, 25, 20, 3, 2, 85.5, 80.0, 15000.00, '2026-03-01'),
(2, 40, 30, 8, 2, 88.0, 75.0, 20000.00, '2026-03-01'),
(3, 30, 25, 4, 1, 90.0, 83.3, 18000.00, '2026-03-01');

-- Insert sample leadership programs
INSERT INTO leadership_programs (name, description, level, focus_area, duration_weeks, target_audience, outcomes, created_by, status) VALUES
('Executive Leadership', 'Advanced leadership for senior managers', 'Executive', 'Strategic Leadership', 8, 'Senior Managers', '["Strategic Thinking", "Change Management"]', 1, 'active'),
('Team Leadership Workshop', 'Building effective teams', 'Mid-Level', 'Team Management', 4, 'Team Leads', '["Communication", "Conflict Resolution"]', 1, 'active');

-- Insert sample leadership enrollments
INSERT INTO leadership_enrollments (user_id, program_id, status) VALUES
(4, 1, 'in_progress'),
(7, 2, 'completed');

-- Insert sample performance reviews
INSERT INTO performance_reviews (employee_id, reviewer_id, review_period_start, review_period_end, rating, comments, status) VALUES
(4, 1, '2026-01-01', '2026-03-01', 4.5, 'Excellent performance in Q1', 'completed'),
(7, 1, '2026-01-01', '2026-03-01', 4.0, 'Good progress, needs improvement in communication', 'completed');

-- Insert sample 360 feedback
INSERT INTO feedback_360 (employee_id, reviewer_id, reviewer_type, rating, comments) VALUES
(4, 1, 'manager', 4.5, 'Strong leadership skills'),
(4, 7, 'peer', 4.0, 'Collaborative team player');

-- Insert sample team activities
INSERT INTO team_activities (name, description, activity_date, department, organizer_id, budget, participant_count, status) VALUES
('Team Building Retreat', 'Annual team building event', '2026-04-15', 'All', 1, 5000.00, 50, 'planned'),
('Diversity Workshop', 'Promoting inclusivity', '2026-05-10', 'HR', 1, 2000.00, 20, 'completed');

-- Insert sample succession plans
INSERT INTO succession_plans (position_name, current_holder_id, successor_id, readiness_level, planned_transition_date, status) VALUES
('Senior Developer', 4, 7, 'High', '2026-06-01', 'active'),
('Project Manager', 7, 4, 'Medium', '2026-07-01', 'active');

-- Insert sample individual development plans
INSERT INTO individual_development_plans (user_id, career_path_id, start_date, end_date, objectives, milestones, status, created_by) VALUES
(4, 1, '2026-03-01', '2026-09-01', 'Develop technical leadership skills', '["Complete leadership course", "Mentor junior devs"]', 'active', 1),
(7, 2, '2026-03-01', '2026-09-01', 'Gain project management experience', '["Lead small project", "Get PMP certification"]', 'active', 1);

-- Insert sample competencies
INSERT INTO competencies (name, description, category, proficiency_levels) VALUES
('Communication', 'Effective verbal and written communication', 'Soft Skills', '["Basic", "Intermediate", "Advanced", "Expert"]'),
('Technical Skills', 'Programming and technical expertise', 'Technical', '["Beginner", "Intermediate", "Advanced", "Expert"]');

-- Insert sample user competencies
INSERT INTO user_competencies (user_id, competency_id, current_level, target_level, assessed_date, assessed_by) VALUES
(4, 1, 'Advanced', 'Expert', '2026-03-01', 1),
(7, 2, 'Intermediate', 'Advanced', '2026-03-01', 1);
