SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS hrms;
USE hrms;

-- =========================
-- EMPLOYEES
-- =========================
CREATE TABLE employees (
  eer_employee_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  department VARCHAR(100),
  position VARCHAR(100),
  email VARCHAR(150) UNIQUE,
  phone VARCHAR(50),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO employees VALUES
(1,'Juan Dela Cruz','HR','HR Specialist','juan.delacruz@example.com','09171234567','2026-03-22 04:03:24'),
(2,'Maria Santos','IT','Software Engineer','maria.santos@example.com','09172345678','2026-03-22 04:03:24'),
(3,'Pedro Reyes','Finance','Accountant','pedro.reyes@example.com','09173456789','2026-03-22 04:03:24'),
(9,'Your Name','HR','Admin','your-email@example.com',NULL,'2026-03-22 10:35:18');

-- =========================
-- USERS
-- =========================
CREATE TABLE users (
  eer_user_id INT AUTO_INCREMENT PRIMARY KEY,
  employee_id INT NULL,
  username VARCHAR(50) NOT NULL,
  email VARCHAR(100),
  password VARCHAR(255) NOT NULL,
  full_name VARCHAR(100),
  role ENUM('recruitment','payroll','time','compliance','workforce','employee','learning','performance','engagement_relations','exit','clinic') NOT NULL,
  status ENUM('active','inactive') DEFAULT 'active',
  theme ENUM('light','dark') DEFAULT 'light',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (employee_id) REFERENCES employees(eer_employee_id) ON DELETE SET NULL
);

INSERT INTO users (eer_user_id, employee_id, username, email, password, full_name, role, status, theme, created_at) VALUES
(1,NULL,'hr_payroll','hr_payroll@company.com','$2y$10$lGdMJ...','Russell Ike','payroll','active','light','2026-03-06 13:13:06'),
(2,NULL,'hr_recruitment','hr_recruitment@company.com','$2y$10$Slnm...','Administrator','recruitment','active','light','2026-03-06 18:46:33'),
(3,NULL,'hr_time','hr_time@company.com','$2y$10$Slnm...','Admin','time','active','light','2026-03-06 18:47:07'),
(4,NULL,'hr_employee','hr_employee@company.com','$2y$10$Slnm...','someone','employee','active','light','2026-03-06 18:47:55'),
(5,NULL,'hr_compliance','hr_compliance@company.com','$2y$10$Slnm...','comply','compliance','active','light','2026-03-06 18:48:19'),
(6,NULL,'hr_workforce','hr_workforce@company.com','$2y$10$Slnm...','force','workforce','active','light','2026-03-06 18:48:43'),
(7,NULL,'hr_learning','hr_learning@company.com','$2y$10$Slnm...','learn','learning','active','light','2026-03-06 18:49:22'),
(8,NULL,'hr_performance','hr_performance@company.com','$2y$10$/Q0H...','Perform','performance','active','light','2026-03-06 18:49:46'),
(9,NULL,'hr_engagement','hr_engagement@company.com','$2y$10$Slnm...','engage','engagement_relations','active','light','2026-03-06 18:50:37'),
(10,NULL,'hr_exit','hr_exit@company.com','$2y$10$Slnm...','exit','exit','active','light','2026-03-06 18:51:04'),
(11,NULL,'hr_clinic','hr_clinic@company.com','$2y$10$Slnm...','clinic','clinic','active','light','2026-03-12 00:20:55'),
(12,NULL,'admin',NULL,'$2y$10$examplehash','Administrator','employee','active','light',NOW());

-- =========================
-- ANNOUNCEMENTS
-- =========================
CREATE TABLE eer_announcements (
  eer_announcements_id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  created_by INT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES employees(eer_employee_id) ON DELETE CASCADE
);

INSERT INTO eer_announcements VALUES
(1,'Company Town Hall','Join the monthly town hall tomorrow at 10 AM.',1,'2026-03-22 04:03:24'),
(2,'Office Clean-up Day','Office clean-up is scheduled this Friday.',2,'2026-03-22 04:03:24');

-- =========================
-- SOCIAL POSTS
-- =========================
CREATE TABLE eer_social_posts (
  eer_social_post_id INT AUTO_INCREMENT PRIMARY KEY,
  employee_id INT NOT NULL,
  content TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (employee_id) REFERENCES employees(eer_employee_id) ON DELETE CASCADE
);

INSERT INTO eer_social_posts VALUES
(1,2,'Excited to share our new project release!','2026-03-22 04:03:24'),
(2,3,'Looking for teammates for a weekend soccer match.','2026-03-22 04:03:24');

-- =========================
-- COMMENTS
-- =========================
CREATE TABLE eer_comments (
  eer_comment_id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT NOT NULL,
  employee_id INT NOT NULL,
  comment TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES eer_social_posts(eer_social_post_id) ON DELETE CASCADE,
  FOREIGN KEY (employee_id) REFERENCES employees(eer_employee_id) ON DELETE CASCADE
);

INSERT INTO eer_comments VALUES
(1,1,1,'Congrats team!','2026-03-22 04:03:24'),
(2,2,1,'Count me in.','2026-03-22 04:03:24');

-- =========================
-- GRIEVANCES
-- =========================
CREATE TABLE eer_grievances (
  eer_grievance_id INT AUTO_INCREMENT PRIMARY KEY,
  employee_id INT NOT NULL,
  subject VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  assigned_to INT,
  status ENUM('pending','in progress','resolved') DEFAULT 'pending',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  category ENUM('Workplace Conflict','Harassment / Bullying','Payroll Concern','Work Environment','Management Issue','Other') DEFAULT 'Workplace Conflict',
  anonymous TINYINT(1) DEFAULT 0,
  attachment_path VARCHAR(512),
  FOREIGN KEY (employee_id) REFERENCES employees(eer_employee_id) ON DELETE CASCADE,
  FOREIGN KEY (assigned_to) REFERENCES employees(eer_employee_id) ON DELETE SET NULL
);

INSERT INTO eer_grievances VALUES
(1,3,'Payroll not updated','My salary is missing the bonus for March.',NULL,'pending','2026-03-22 04:03:24','Workplace Conflict',0,NULL),
(2,2,'Laptop performance issue','System freezes frequently.',NULL,'in progress','2026-03-22 04:03:24','Workplace Conflict',0,NULL),
(3,9,'1','1',NULL,'pending','2026-03-22 11:45:19','Workplace Conflict',1,'uploads/grievances/sample.png');

-- =========================
-- GRIEVANCE UPDATES
-- =========================
CREATE TABLE eer_grievance_updates (
  eer_grievance_updates_id INT AUTO_INCREMENT PRIMARY KEY,
  grievance_id INT NOT NULL,
  update_text TEXT NOT NULL,
  updated_by INT NOT NULL,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (grievance_id) REFERENCES eer_grievances(eer_grievance_id) ON DELETE CASCADE,
  FOREIGN KEY (updated_by) REFERENCES employees(eer_employee_id) ON DELETE CASCADE
);

INSERT INTO eer_grievance_updates VALUES
(1,1,'HR escalated to payroll',1,'2026-03-22 04:03:24'),
(2,2,'IT opened ticket',1,'2026-03-22 04:03:24');

-- =========================
-- MESSAGES
-- =========================
CREATE TABLE eer_messages (
  eer_message_id INT AUTO_INCREMENT PRIMARY KEY,
  sender_id INT NOT NULL,
  receiver_id INT NOT NULL,
  message TEXT NOT NULL,
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (sender_id) REFERENCES employees(eer_employee_id) ON DELETE CASCADE,
  FOREIGN KEY (receiver_id) REFERENCES employees(eer_employee_id) ON DELETE CASCADE
);

INSERT INTO eer_messages VALUES
(1,1,2,'Please send report','2026-03-22 04:03:24'),
(2,2,1,'Report sent','2026-03-22 04:03:24');

-- =========================
-- RECOGNITIONS
-- =========================
CREATE TABLE eer_recognitions (
  eer_recognition_id INT AUTO_INCREMENT PRIMARY KEY,
  sender_id INT NOT NULL,
  receiver_id INT NOT NULL,
  message TEXT NOT NULL,
  points INT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (sender_id) REFERENCES employees(eer_employee_id),
  FOREIGN KEY (receiver_id) REFERENCES employees(eer_employee_id)
);

INSERT INTO eer_recognitions VALUES
(1,1,2,'Great job!',20,'2026-03-22 04:03:24'),
(2,2,3,'Thanks!',15,'2026-03-22 04:03:24');

-- =========================
-- REWARDS
-- =========================
CREATE TABLE eer_rewards (
  eer_reward_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255),
  points_required INT
);

INSERT INTO eer_rewards VALUES
(1,'Extra Day Off',100),
(2,'Gift Card',50);

-- =========================
-- SURVEYS
-- =========================
CREATE TABLE eer_surveys (
  eer_survey_id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  created_by INT,
  date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES employees(eer_employee_id)
);

INSERT INTO eer_surveys VALUES
(1,'Workplace Satisfaction Survey',1,'2026-03-22 04:03:24'),
(2,'Remote Work Check-In',2,'2026-03-22 04:03:24');

-- =========================
-- SURVEY QUESTIONS
-- =========================
CREATE TABLE eer_survey_questions (
  eer_survey_question_id INT AUTO_INCREMENT PRIMARY KEY,
  survey_id INT,
  question_text TEXT,
  type VARCHAR(50),
  FOREIGN KEY (survey_id) REFERENCES eer_surveys(eer_survey_id)
);

INSERT INTO eer_survey_questions VALUES
(1,1,'How satisfied are you?','rating'),
(2,1,'What can improve?','text'),
(3,2,'Do you have tools?','text');

-- =========================
-- SURVEY RESPONSES
-- =========================
CREATE TABLE eer_survey_responses (
  eer_survey_response_id INT AUTO_INCREMENT PRIMARY KEY,
  survey_id INT,
  employee_id INT,
  answers JSON,
  submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (survey_id) REFERENCES eer_surveys(eer_survey_id),
  FOREIGN KEY (employee_id) REFERENCES employees(eer_employee_id)
);

INSERT INTO eer_survey_responses VALUES
(1,1,2,'{\"1\":\"5\"}','2026-03-22 04:03:24'),
(2,1,3,'{\"1\":\"4\"}','2026-03-22 04:03:24');

-- =========================
-- SURVEY FEEDBACK
-- =========================
CREATE TABLE eer_survey_feedback (
  eer_survey_feedback_id INT AUTO_INCREMENT PRIMARY KEY,
  survey_id INT,
  employee_id INT,
  comment TEXT,
  rating TINYINT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (survey_id) REFERENCES eer_surveys(eer_survey_id),
  FOREIGN KEY (employee_id) REFERENCES employees(eer_employee_id)
);

INSERT INTO eer_survey_feedback VALUES
(1,1,1,'Very helpful',5,'2026-03-07'),
(2,1,2,'Great survey',5,'2026-03-07');

COMMIT;