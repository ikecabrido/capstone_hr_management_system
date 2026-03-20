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
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('pending', 'in progress', 'resolved') NOT NULL DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
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

-- dummy data
INSERT INTO employees (name, department, position, email, phone) VALUES
('Juan Dela Cruz', 'HR', 'HR Specialist', 'juan.delacruz@example.com', '09171234567'),
('Maria Santos', 'IT', 'Software Engineer', 'maria.santos@example.com', '09172345678'),
('Pedro Reyes', 'Finance', 'Accountant', 'pedro.reyes@example.com', '09173456789');

INSERT INTO eer_surveys (title, created_by) VALUES
('Workplace Satisfaction Survey', 1),
('Remote Work Check-In', 2);

INSERT INTO eer_survey_questions (survey_id, question_text, type) VALUES
(1, 'How satisfied are you with your current role?', 'rating'),
(1, 'What can we improve in the office environment?', 'text'),
(2, 'Do you have the tools needed for remote work?', 'text');

INSERT INTO eer_survey_responses (survey_id, employee_id, answers) VALUES
(1, 2, JSON_OBJECT('1','5','2','More coffee options')),
(1, 3, JSON_OBJECT('1','4','2','Better chairs'));

INSERT INTO eer_recognitions (sender_id, receiver_id, message, points) VALUES
(1, 2, 'Great job on delivering the module ahead of schedule', 20),
(2, 3, 'Thanks for helping with the audit', 15);

INSERT INTO eer_rewards (name, points_required) VALUES
('Extra Day Off', 100),
('Gift Card', 50);

INSERT INTO eer_grievances (employee_id, subject, description, status) VALUES
(3, 'Payroll not updated', 'My salary is missing the bonus for March.', 'pending'),
(2, 'Laptop performance issue', 'System freezes frequently during development.', 'in progress');

INSERT INTO eer_grievance_updates (grievance_id, update_text, updated_by) VALUES
(1, 'HR has escalated to payroll team', 1),
(2, 'IT opened a ticket with support', 1);

INSERT INTO eer_announcements (title, content, created_by) VALUES
('Company Town Hall', 'Join the monthly town hall tomorrow at 10 AM.', 1),
('Office Clean-up Day', 'Office clean-up is scheduled this Friday.', 2);

INSERT INTO eer_messages (sender_id, receiver_id, message) VALUES
(1, 2, 'Please send the latest sprint report.'),
(2, 1, 'Report sent, kindly check your inbox.');

INSERT INTO eer_social_posts (employee_id, content) VALUES
(2, 'Excited to share our new project release!'),
(3, 'Looking for teammates for a weekend soccer match.');

INSERT INTO eer_comments (post_id, employee_id, comment) VALUES
(1, 1, 'Congrats team!'),
(2, 1, 'Count me in.');