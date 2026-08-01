-- Learning & Development Database Population Script
-- This script populates the LD module with comprehensive dummy data

USE hr_management;

-- Insert additional training programs
INSERT INTO ld_training_programs (title, description, trainer, start_date, end_date, max_participants, status, created_by_user_id) VALUES
('Advanced Leadership Development', 'Comprehensive leadership training for senior managers covering strategic thinking, team management, and organizational change.', 'Dr. Sarah Johnson', '2026-05-01', '2026-05-15', 25, 'active', 7),
('Digital Marketing Mastery', 'Complete digital marketing course covering SEO, social media marketing, content strategy, and analytics.', 'Mike Chen', '2026-04-15', '2026-04-30', 30, 'active', 7),
('Cybersecurity Fundamentals', 'Essential cybersecurity training for all employees covering data protection, phishing awareness, and security best practices.', 'Lisa Rodriguez', '2026-06-01', '2026-06-10', 50, 'active', 7),
('Project Management Professional', 'PMP certification preparation course covering project lifecycle, risk management, and stakeholder communication.', 'David Thompson', '2026-07-01', '2026-07-20', 20, 'active', 7),
('Data Analytics with Python', 'Hands-on data analytics training using Python, pandas, and visualization tools.', 'Dr. Emily Wang', '2026-05-20', '2026-06-05', 35, 'active', 7),
('Customer Service Excellence', 'Advanced customer service training focusing on communication skills, conflict resolution, and customer satisfaction.', 'Jennifer Martinez', '2026-04-20', '2026-04-25', 40, 'active', 7),
('Financial Planning for Managers', 'Financial literacy training for managers covering budgeting, forecasting, and financial decision making.', 'Robert Kim', '2026-08-01', '2026-08-10', 25, 'active', 7),
('Agile Methodology Workshop', 'Practical agile training covering Scrum, Kanban, and agile project management techniques.', 'Tom Anderson', '2026-06-15', '2026-06-20', 30, 'active', 7),
('Communication Skills Masterclass', 'Advanced communication training covering public speaking, presentation skills, and effective writing.', 'Maria Garcia', '2026-05-10', '2026-05-17', 45, 'active', 7),
('Quality Management Systems', 'ISO 9001 and quality management training for process improvement and quality assurance.', 'James Wilson', '2026-09-01', '2026-09-15', 28, 'active', 7);

-- Insert additional courses for the new programs
INSERT INTO ld_courses (title, description, instructor, duration_hours, ld_training_programs_id, content_type, status, created_by_user_id) VALUES
-- Advanced Leadership Development courses
('Strategic Leadership', 'Learn strategic thinking and long-term planning skills', 'Dr. Sarah Johnson', 8, 4, 'in-person', 'active', 7),
('Team Dynamics & Management', 'Understanding team behavior and effective management techniques', 'Dr. Sarah Johnson', 6, 4, 'online', 'active', 7),
('Change Management', 'Leading organizational change and transformation', 'Dr. Sarah Johnson', 8, 4, 'hybrid', 'active', 7),

-- Digital Marketing Mastery courses
('SEO Fundamentals', 'Search engine optimization basics and advanced techniques', 'Mike Chen', 6, 5, 'online', 'active', 7),
('Social Media Marketing', 'Building brand presence on social platforms', 'Mike Chen', 8, 5, 'online', 'active', 7),
('Content Marketing Strategy', 'Creating and distributing valuable content', 'Mike Chen', 6, 5, 'hybrid', 'active', 7),

-- Cybersecurity Fundamentals courses
('Data Protection Basics', 'Understanding data privacy and protection laws', 'Lisa Rodriguez', 4, 6, 'online', 'active', 7),
('Phishing Awareness', 'Recognizing and preventing phishing attacks', 'Lisa Rodriguez', 3, 6, 'online', 'active', 7),
('Security Best Practices', 'Daily security habits and procedures', 'Lisa Rodriguez', 5, 6, 'in-person', 'active', 7),

-- PMP Certification courses
('Project Initiation', 'Project charter and stakeholder analysis', 'David Thompson', 6, 7, 'hybrid', 'active', 7),
('Project Planning', 'Creating comprehensive project plans', 'David Thompson', 8, 7, 'in-person', 'active', 7),
('Risk Management', 'Identifying and managing project risks', 'David Thompson', 6, 7, 'online', 'active', 7),

-- Data Analytics courses
('Python for Data Analysis', 'Python programming fundamentals for data work', 'Dr. Emily Wang', 10, 8, 'online', 'active', 7),
('Data Visualization', 'Creating effective data visualizations', 'Dr. Emily Wang', 6, 8, 'hybrid', 'active', 7),
('Statistical Analysis', 'Statistical methods and hypothesis testing', 'Dr. Emily Wang', 8, 8, 'in-person', 'active', 7),

-- Customer Service Excellence courses
('Communication Skills', 'Effective verbal and written communication', 'Jennifer Martinez', 4, 9, 'online', 'active', 7),
('Conflict Resolution', 'Handling difficult customer situations', 'Jennifer Martinez', 6, 9, 'in-person', 'active', 7),
('Customer Satisfaction', 'Measuring and improving customer experience', 'Jennifer Martinez', 4, 9, 'hybrid', 'active', 7),

-- Financial Planning courses
('Budgeting Fundamentals', 'Creating and managing budgets', 'Robert Kim', 6, 10, 'hybrid', 'active', 7),
('Financial Forecasting', 'Predicting future financial performance', 'Robert Kim', 8, 10, 'in-person', 'active', 7),
('Investment Analysis', 'Evaluating investment opportunities', 'Robert Kim', 6, 10, 'online', 'active', 7),

-- Agile Methodology courses
('Scrum Framework', 'Understanding Scrum principles and practices', 'Tom Anderson', 6, 11, 'online', 'active', 7),
('Kanban Method', 'Visualizing workflow and limiting work in progress', 'Tom Anderson', 4, 11, 'hybrid', 'active', 7),
('Agile Planning', 'Sprint planning and backlog management', 'Tom Anderson', 6, 11, 'in-person', 'active', 7),

-- Communication Skills courses
('Public Speaking', 'Overcoming fear and delivering effective presentations', 'Maria Garcia', 6, 12, 'in-person', 'active', 7),
('Presentation Design', 'Creating visually appealing presentations', 'Maria Garcia', 4, 12, 'online', 'active', 7),
('Business Writing', 'Writing clear and professional business documents', 'Maria Garcia', 5, 12, 'hybrid', 'active', 7),

-- Quality Management courses
('ISO 9001 Standards', 'Understanding quality management systems', 'James Wilson', 8, 13, 'hybrid', 'active', 7),
('Process Improvement', 'Identifying and implementing process improvements', 'James Wilson', 6, 13, 'in-person', 'active', 7),
('Quality Assurance', 'Quality control and assurance techniques', 'James Wilson', 7, 13, 'online', 'active', 7);

-- Create enrollments for employees in various courses
INSERT INTO ld_enrollments (employee_user_id, ld_courses_id, status, progress_percentage, enrolled_at) VALUES
-- Russell Ike (payroll) enrollments
(1, 1, 'completed', 100, '2026-03-01 09:00:00'),
(1, 6, 'in-progress', 75, '2026-03-15 10:30:00'),
(1, 12, 'enrolled', 0, '2026-04-01 14:00:00'),

-- Administrator (recruitment) enrollments
(2, 2, 'completed', 100, '2026-02-20 11:00:00'),
(2, 8, 'completed', 100, '2026-03-10 13:15:00'),
(2, 15, 'in-progress', 60, '2026-03-25 16:45:00'),

-- Admin (time) enrollments
(3, 3, 'completed', 100, '2026-02-15 08:30:00'),
(3, 10, 'in-progress', 45, '2026-03-20 12:00:00'),
(3, 18, 'enrolled', 0, '2026-04-02 09:15:00'),

-- someone (employee) enrollments - already has some, add more
(4, 4, 'completed', 100, '2026-03-05 14:20:00'),
(4, 11, 'in-progress', 80, '2026-03-12 11:30:00'),
(4, 22, 'enrolled', 0, '2026-04-03 10:00:00'),

-- comply (compliance) enrollments
(5, 5, 'completed', 100, '2026-02-28 15:45:00'),
(5, 13, 'completed', 100, '2026-03-08 09:30:00'),
(5, 19, 'in-progress', 30, '2026-03-28 13:20:00'),

-- force (workforce) enrollments
(6, 7, 'completed', 100, '2026-03-03 10:15:00'),
(6, 14, 'in-progress', 55, '2026-03-18 14:30:00'),
(6, 25, 'enrolled', 0, '2026-04-01 11:45:00'),

-- HR Learning Admin (learning) enrollments
(7, 9, 'completed', 100, '2026-02-25 12:00:00'),
(7, 16, 'completed', 100, '2026-03-14 08:45:00'),
(7, 23, 'in-progress', 90, '2026-03-22 15:30:00'),

-- Perform (performance) enrollments
(8, 17, 'completed', 100, '2026-03-07 13:00:00'),
(8, 24, 'in-progress', 70, '2026-03-26 10:15:00'),
(8, 28, 'enrolled', 0, '2026-04-02 14:30:00'),

-- engage (engagement_relations) enrollments
(9, 20, 'completed', 100, '2026-03-11 09:20:00'),
(9, 26, 'in-progress', 40, '2026-03-29 11:00:00'),
(9, 30, 'enrolled', 0, '2026-04-03 08:30:00'),

-- exit (exit) enrollments
(10, 21, 'completed', 100, '2026-03-09 14:45:00'),
(10, 27, 'in-progress', 25, '2026-03-30 12:15:00'),
(10, 31, 'enrolled', 0, '2026-04-01 16:00:00');

-- Insert certifications for completed courses
INSERT INTO ld_certification (employee_id, ld_courses_id, certification_name, issued_date, expiry_date, issued_by_user_id, status) VALUES
-- Certifications for Russell Ike
(1, 1, 'Strategic Leadership Certificate', '2026-03-15', '2027-03-15', 7, 'active'),
(1, 6, 'Social Media Marketing Certificate', '2026-03-20', '2027-03-20', 7, 'active'),

-- Certifications for Administrator
(2, 2, 'Team Dynamics Certificate', '2026-03-01', '2027-03-01', 7, 'active'),
(2, 8, 'Project Planning Certificate', '2026-03-18', '2027-03-18', 7, 'active'),

-- Certifications for Admin
(3, 3, 'Change Management Certificate', '2026-02-28', '2027-02-28', 7, 'active'),
(3, 10, 'Risk Management Certificate', '2026-03-25', '2027-03-25', 7, 'active'),

-- Certifications for someone
(4, 4, 'SEO Fundamentals Certificate', '2026-03-12', '2027-03-12', 7, 'active'),
(4, 11, 'Python for Data Analysis Certificate', '2026-03-18', '2027-03-18', 7, 'active'),

-- Certifications for comply
(5, 5, 'Content Marketing Certificate', '2026-03-08', '2027-03-08', 7, 'active'),
(5, 13, 'Data Visualization Certificate', '2026-03-15', '2027-03-15', 7, 'active'),

-- Certifications for force
(6, 7, 'Security Best Practices Certificate', '2026-03-10', '2027-03-10', 7, 'active'),
(6, 14, 'Statistical Analysis Certificate', '2026-03-22', '2027-03-22', 7, 'active'),

-- Certifications for HR Learning Admin
(7, 9, 'Project Initiation Certificate', '2026-03-05', '2027-03-05', 7, 'active'),
(7, 16, 'Customer Satisfaction Certificate', '2026-03-20', '2027-03-20', 7, 'active'),

-- Certifications for Perform
(8, 17, 'Budgeting Fundamentals Certificate', '2026-03-14', '2027-03-14', 7, 'active'),
(8, 24, 'Business Writing Certificate', '2026-03-30', '2027-03-30', 7, 'active'),

-- Certifications for engage
(9, 20, 'Scrum Framework Certificate', '2026-03-18', '2027-03-18', 7, 'active'),
(9, 26, 'Public Speaking Certificate', '2026-04-01', '2027-04-01', 7, 'active'),

-- Certifications for exit
(10, 21, 'Kanban Method Certificate', '2026-03-16', '2027-03-16', 7, 'active'),
(10, 27, 'ISO 9001 Standards Certificate', '2026-04-02', '2027-04-02', 7, 'active'),

-- Some expired certifications for variety
(1, 6, 'Old Social Media Certificate', '2025-03-20', '2026-03-20', 7, 'expired'),
(2, 8, 'Old Project Planning Certificate', '2025-03-18', '2026-03-18', 7, 'expired'),

-- Some revoked certifications
(3, 10, 'Revoked Risk Management Certificate', '2026-02-15', '2027-02-15', 7, 'revoked'),
(4, 11, 'Revoked Python Certificate', '2026-02-20', '2027-02-20', 7, 'revoked');

-- Update some courses to inactive status for variety
UPDATE ld_courses SET status = 'inactive' WHERE ld_courses_id IN (3, 7, 12, 18, 25);