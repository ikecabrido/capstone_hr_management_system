-- Create database
CREATE DATABASE IF NOT EXISTS hrms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hrms;

-- employees
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    department VARCHAR(100) DEFAULT NULL,
    position VARCHAR(100) DEFAULT NULL,
    email VARCHAR(150) UNIQUE,
    phone VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- surveys
CREATE TABLE IF NOT EXISTS eer_surveys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    created_by INT NOT NULL,
    date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES employees(id) ON DELETE CASCADE
);

-- survey_questions
CREATE TABLE IF NOT EXISTS eer_survey_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    survey_id INT NOT NULL,
    question_text TEXT NOT NULL,
    type VARCHAR(50) NOT NULL DEFAULT 'text',
    FOREIGN KEY (survey_id) REFERENCES eer_surveys(id) ON DELETE CASCADE
);

-- survey_responses
CREATE TABLE IF NOT EXISTS eer_survey_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    survey_id INT NOT NULL,
    employee_id INT NOT NULL,
    answers JSON NOT NULL,
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (survey_id) REFERENCES eer_surveys(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- survey_feedback (for comments and optional ratings)
CREATE TABLE IF NOT EXISTS survey_feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    survey_id INT NOT NULL,
    employee_id INT NOT NULL,
    comment TEXT NOT NULL,
    rating TINYINT UNSIGNED NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (survey_id) REFERENCES eer_surveys(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- recognitions
CREATE TABLE IF NOT EXISTS eer_recognitions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    points INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- rewards
CREATE TABLE IF NOT EXISTS eer_rewards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    points_required INT NOT NULL
);

-- grievances
CREATE TABLE IF NOT EXISTS eer_grievances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    anonymous TINYINT(1) NOT NULL DEFAULT 0,
    category ENUM('Workplace Conflict', 'Harassment / Bullying', 'Payroll Concern', 'Work Environment', 'Management Issue', 'Other') NOT NULL DEFAULT 'Workplace Conflict',
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    assigned_to INT NULL,
    status ENUM('Submitted', 'Under Review', 'For Investigation', 'Resolved', 'Closed') NOT NULL DEFAULT 'Submitted',
    resolution_notes TEXT NULL,
    action_taken TEXT NULL,
    escalated_to INT NULL,
    attachment_path VARCHAR(512) NULL,
    satisfaction_rating TINYINT NULL,
    satisfaction_comment TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES employees(id) ON DELETE SET NULL,
    FOREIGN KEY (escalated_to) REFERENCES employees(id) ON DELETE SET NULL
);

-- grievance_updates
CREATE TABLE IF NOT EXISTS eer_grievance_updates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grievance_id INT NOT NULL,
    update_text TEXT NOT NULL,
    updated_by INT NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (grievance_id) REFERENCES eer_grievances(id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES employees(id) ON DELETE CASCADE
);

-- announcements
CREATE TABLE IF NOT EXISTS eer_announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES employees(id) ON DELETE CASCADE
);

-- messages
CREATE TABLE IF NOT EXISTS eer_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- social_posts
CREATE TABLE IF NOT EXISTS eer_social_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- comments
CREATE TABLE IF NOT EXISTS eer_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    employee_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES eer_social_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);
-- Fully populated dummy data

-- employees
INSERT INTO employees (name, department, position, email, phone, created_at) VALUES
('Juan Dela Cruz', 'HR', 'HR Specialist', 'juan.delacruz@example.com', '09171234567', '2026-03-01 09:00:00'),
('Maria Santos', 'IT', 'Software Engineer', 'maria.santos@example.com', '09172345678', '2026-03-02 10:30:00'),
('Pedro Reyes', 'Finance', 'Accountant', 'pedro.reyes@example.com', '09173456789', '2026-03-03 11:15:00');

-- eer_surveys
INSERT INTO eer_surveys (title, created_by, date_created) VALUES
('Workplace Satisfaction Survey', 1, '2026-03-05 08:00:00'),
('Remote Work Check-In', 2, '2026-03-06 09:15:00');

-- eer_survey_questions
INSERT INTO eer_survey_questions (survey_id, question_text, type) VALUES
(1, 'How satisfied are you with your current role?', 'rating'),
(1, 'What can we improve in the office environment?', 'text'),
(2, 'Do you have the tools needed for remote work?', 'text'),
(2, 'How would you rate your work-life balance while working remotely?', 'rating');

-- eer_survey_responses
INSERT INTO eer_survey_responses (survey_id, employee_id, answers, submitted_at) VALUES
(1, 2, JSON_OBJECT('1','5','2','More coffee options'), '2026-03-07 14:00:00'),
(1, 3, JSON_OBJECT('1','4','2','Better chairs'), '2026-03-07 15:00:00'),
(2, 1, JSON_OBJECT('3','Yes','4','4'), '2026-03-08 09:30:00'),
(2, 3, JSON_OBJECT('3','No','4','3'), '2026-03-08 10:00:00');

-- survey_feedback
INSERT INTO survey_feedback (survey_id, employee_id, comment, rating, created_at) VALUES
(1, 2, 'Great survey! Learned a lot.', 5, '2026-03-07 14:05:00'),
(2, 3, 'Helpful questions, but missing options.', 4, '2026-03-08 10:05:00'),
(1, 3, 'Could include more about work-life balance.', 4, '2026-03-07 15:10:00');

-- eer_recognitions
INSERT INTO eer_recognitions (sender_id, receiver_id, message, points, created_at) VALUES
(1, 2, 'Great job on delivering the module ahead of schedule', 20, '2026-03-07 12:00:00'),
(2, 3, 'Thanks for helping with the audit', 15, '2026-03-07 13:30:00'),
(3, 1, 'Appreciate your mentoring in the last project', 10, '2026-03-07 14:20:00');

-- eer_rewards
INSERT INTO eer_rewards (name, points_required) VALUES
('Extra Day Off', 100),
('Gift Card', 50),
('Company Swag Pack', 75);

-- eer_grievances
INSERT INTO eer_grievances (employee_id, anonymous, category, subject, description, assigned_to, status, resolution_notes, action_taken, escalated_to, attachment_path, satisfaction_rating, satisfaction_comment, created_at) VALUES
(3, 0, 'Payroll Concern', 'Payroll not updated', 'My salary is missing the bonus for March.', 1, 'Under Review', 'Escalated to payroll team', 'Follow-up with finance', NULL, NULL, NULL, NULL, '2026-03-07 09:00:00'),
(2, 1, 'Work Environment', 'Laptop performance issue', 'System freezes frequently during development.', 2, 'For Investigation', 'IT ticket opened', 'Updated drivers and software', NULL, NULL, NULL, NULL, '2026-03-07 10:00:00');

-- eer_grievance_updates
INSERT INTO eer_grievance_updates (grievance_id, update_text, updated_by, updated_at) VALUES
(1, 'HR has escalated to payroll team', 1, '2026-03-07 10:30:00'),
(2, 'IT opened a ticket with support', 1, '2026-03-07 11:00:00');

-- eer_announcements
INSERT INTO eer_announcements (title, content, created_by, created_at) VALUES
('Company Town Hall', 'Join the monthly town hall tomorrow at 10 AM.', 1, '2026-03-06 08:00:00'),
('Office Clean-up Day', 'Office clean-up is scheduled this Friday.', 2, '2026-03-06 09:00:00');

-- eer_messages
INSERT INTO eer_messages (sender_id, receiver_id, message, timestamp) VALUES
(1, 2, 'Please send the latest sprint report.', '2026-03-06 15:00:00'),
(2, 1, 'Report sent, kindly check your inbox.', '2026-03-06 15:30:00'),
(3, 1, 'Can we meet regarding payroll?', '2026-03-07 09:15:00');

-- eer_social_posts
INSERT INTO eer_social_posts (employee_id, content, created_at) VALUES
(2, 'Excited to share our new project release!', '2026-03-06 10:00:00'),
(3, 'Looking for teammates for a weekend soccer match.', '2026-03-06 11:00:00');

-- eer_comments
INSERT INTO eer_comments (post_id, employee_id, comment, created_at) VALUES
(1, 1, 'Congrats team! Excellent work!', '2026-03-06 12:00:00'),
(2, 1, 'Count me in for the match!', '2026-03-06 12:30:00'),
(1, 3, 'Proud of the release!', '2026-03-06 13:00:00');

-- Fully populated survey_feedback
INSERT INTO survey_feedback (survey_id, employee_id, comment, rating, created_at) VALUES
(1, 1, 'Very helpful survey, clear questions.', 5, '2026-03-07 09:05:00'),
(1, 2, 'Great survey! Learned a lot.', 5, '2026-03-07 14:05:00'),
(1, 3, 'Could include more about work-life balance.', 4, '2026-03-07 15:10:00'),
(2, 1, 'Remote work survey is concise and useful.', 4, '2026-03-08 09:35:00'),
(2, 2, 'Helpful, but missing questions on team communication.', 4, '2026-03-08 09:50:00'),
(2, 3, 'Helpful questions, but missing options.', 4, '2026-03-08 10:05:00');