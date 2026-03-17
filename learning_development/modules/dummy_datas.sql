-- ============================================================================
-- DUMMY DATA FOR LEARNING & DEVELOPMENT MANAGEMENT SYSTEM
-- ============================================================================
-- This file contains comprehensive sample data referenced from hr_management database

-- ============================================================================
-- 1. TRAINING PROGRAMS & ENROLLMENTS
-- ============================================================================

-- Insert Training Programs
INSERT INTO training_programs (name, description, category, type, duration, status, created_at, cover_photo) VALUES
('Advanced Leadership Skills', 'Learn advanced leadership techniques and management strategies to lead teams effectively', 'Leadership', 'Workshop', 5, 'Active', NOW(), 'modules/img/gifholder/gifholder-1.gif'),
('Technical Project Management', 'Master the fundamentals of project management using industry-standard methodologies', 'Management', 'Course', 8, 'Active', NOW(), 'modules/img/gifholder/gifholder-2.gif'),
('Communication Excellence', 'Enhance your communication skills for better professional interactions', 'Soft Skills', 'Workshop', 3, 'Active', NOW(), 'modules/img/gifholder/gifholder-3.gif'),
('Data Analysis & Excel Mastery', 'Comprehensive training on data analysis tools and advanced Excel functions', 'Technical', 'Course', 6, 'Active', NOW(), 'modules/img/gifholder/gifholder-4.gif'),
('Customer Service Excellence', 'Develop exceptional customer service skills to improve client satisfaction', 'Soft Skills', 'Workshop', 2, 'Active', NOW(), 'modules/img/gifholder/gifholder-5.gif'),
('Digital Marketing Fundamentals', 'Learn the basics of digital marketing and social media strategies', 'Marketing', 'Course', 7, 'Active', NOW(), 'modules/img/gifholder/gifholder-6.gif'),
('Financial Analysis for Non-Finance', 'Understanding financial statements and business metrics for non-finance professionals', 'Finance', 'Course', 5, 'Active', NOW(), 'modules/img/gifholder/gifholder-7.gif'),
('Effective Negotiation Tactics', 'Master negotiation techniques for better business outcomes', 'Soft Skills', 'Workshop', 3, 'Active', NOW(), 'modules/img/gifholder/gifholder-8.gif'),
('Time Management & Productivity', 'Improve productivity and manage time effectively in a fast-paced environment', 'Soft Skills', 'Workshop', 2, 'Active', NOW(), 'modules/img/gifholder/gifholder-9.gif'),
('Creativity & Innovation in Business', 'Unlock your creative potential and drive innovation in your organization', 'Soft Skills', 'Workshop', 4, 'Active', NOW(), 'modules/img/gifholder/gifholder-10.gif'),
('Six Sigma Green Belt', 'Process improvement certification focusing on quality management', 'Operations', 'Course', 10, 'Active', NOW(), 'modules/img/gifholder/gifholder-11.gif'),
('Agile Methodology Bootcamp', 'Learn Agile principles and practices for software development teams', 'Technical', 'Bootcamp', 6, 'Active', NOW(), 'modules/img/gifholder/gifholder-12.gif'),
('Strategic Business Planning', 'Develop skills in strategic planning and competitive analysis', 'Management', 'Course', 7, 'Active', NOW(), 'modules/img/gifholder/gifholder-13.gif'),
('Supply Chain Management', 'Comprehensive overview of supply chain operations and optimization', 'Operations', 'Course', 8, 'Active', NOW(), 'modules/img/gifholder/gifholder-14.gif'),
('Quality Management Systems', 'ISO 9001 and quality management fundamentals', 'Operations', 'Workshop', 4, 'Active', NOW(), 'modules/img/gifholder/gifholder-15.gif'),
('Business Ethics & Compliance', 'Understanding ethical practices and regulatory compliance requirements', 'Compliance', 'Course', 3, 'Active', NOW(), 'modules/img/gifholder/gifholder-16.gif'),
('Advanced Public Speaking', 'Master the art of public speaking and presentation skills', 'Soft Skills', 'Workshop', 3, 'Active', NOW(), 'modules/img/gifholder/gifholder-17.gif'),
('Conflict Resolution Workshop', 'Learn techniques to resolve workplace conflicts effectively', 'Soft Skills', 'Workshop', 2, 'Active', NOW(), 'modules/img/gifholder/gifholder-18.gif'),
('Cloud Computing Essentials', 'Introduction to cloud platforms and cloud-based solutions', 'Technical', 'Course', 6, 'Active', NOW(), 'modules/img/gifholder/gifholder-19.gif'),
('Cybersecurity Fundamentals', 'Protect your organization from cyber threats and security breaches', 'Technical', 'Course', 5, 'Active', NOW(), 'modules/img/gifholder/gifholder-20.gif'),
('DevOps Best Practices', 'Learn containerization, CI/CD pipelines, and infrastructure as code', 'Technical', 'Bootcamp', 8, 'Active', NOW(), 'modules/img/gifholder/gifholder-21.gif'),
('Machine Learning Fundamentals', 'Introduction to machine learning algorithms and practical applications', 'Technical', 'Course', 9, 'Active', NOW(), 'modules/img/gifholder/gifholder-1.gif'),
('React & Modern JavaScript', 'Build dynamic web applications using React and ES6+ JavaScript', 'Technical', 'Course', 7, 'Active', NOW(), 'modules/img/gifholder/gifholder-2.gif'),
('User Experience Design', 'Create intuitive and engaging user interfaces with UX principles', 'Design', 'Course', 6, 'Active', NOW(), 'modules/img/gifholder/gifholder-3.gif'),
('Database Design & SQL', 'Master relational databases, queries, and optimization techniques', 'Technical', 'Course', 8, 'Active', NOW(), 'modules/img/gifholder/gifholder-4.gif'),
('API Development & Integration', 'Build and integrate RESTful APIs and microservices', 'Technical', 'Course', 6, 'Active', NOW(), 'modules/img/gifholder/gifholder-5.gif'),
('Advanced Excel for Analytics', 'Pivot tables, VLOOKUP, macros, and data visualization in Excel', 'Technical', 'Workshop', 4, 'Active', NOW(), 'modules/img/gifholder/gifholder-6.gif'),
('Power BI & Data Visualization', 'Create interactive dashboards and reports with Power BI', 'Technical', 'Course', 5, 'Active', NOW(), 'modules/img/gifholder/gifholder-7.gif'),
('Emotional Intelligence at Work', 'Develop emotional awareness and interpersonal effectiveness', 'Soft Skills', 'Workshop', 3, 'Active', NOW(), 'modules/img/gifholder/gifholder-8.gif'),
('Team Collaboration & Synergy', 'Build high-performing teams through effective collaboration', 'Management', 'Workshop', 3, 'Active', NOW(), 'modules/img/gifholder/gifholder-9.gif'),
('Presentation Skills Mastery', 'Create compelling presentations and deliver impactful messages', 'Soft Skills', 'Workshop', 4, 'Active', NOW(), 'modules/img/gifholder/gifholder-10.gif');

-- Insert Training Enrollments
INSERT INTO training_enrollments (user_id, program_id, status, enrollment_date, progress_percentage) VALUES
(1, 1, 'pending', DATE_SUB(NOW(), INTERVAL 15 DAY), 35),
(1, 2, 'pending', DATE_SUB(NOW(), INTERVAL 10 DAY), 50),
(1, 3, 'completed', DATE_SUB(NOW(), INTERVAL 30 DAY), 100),
(1, 11, 'approved', DATE_SUB(NOW(), INTERVAL 8 DAY), 20),
(1, 17, 'completed', DATE_SUB(NOW(), INTERVAL 35 DAY), 100),
(1, 22, 'pending', DATE_SUB(NOW(), INTERVAL 5 DAY), 15),
(2, 2, 'pending', DATE_SUB(NOW(), INTERVAL 20 DAY), 25),
(2, 4, 'approved', DATE_SUB(NOW(), INTERVAL 8 DAY), 40),
(2, 12, 'pending', DATE_SUB(NOW(), INTERVAL 14 DAY), 30),
(2, 18, 'approved', DATE_SUB(NOW(), INTERVAL 6 DAY), 15),
(2, 23, 'in_progress', DATE_SUB(NOW(), INTERVAL 3 DAY), 65),
(3, 6, 'pending', DATE_SUB(NOW(), INTERVAL 12 DAY), 45),
(3, 7, 'completed', DATE_SUB(NOW(), INTERVAL 45 DAY), 100),
(3, 13, 'pending', DATE_SUB(NOW(), INTERVAL 10 DAY), 60),
(3, 19, 'approved', DATE_SUB(NOW(), INTERVAL 9 DAY), 25),
(3, 24, 'in_progress', DATE_SUB(NOW(), INTERVAL 7 DAY), 55),
(4, 1, 'pending', DATE_SUB(NOW(), INTERVAL 5 DAY), 10),
(4, 8, 'pending', DATE_SUB(NOW(), INTERVAL 18 DAY), 55),
(4, 14, 'approved', DATE_SUB(NOW(), INTERVAL 12 DAY), 35),
(4, 20, 'pending', DATE_SUB(NOW(), INTERVAL 7 DAY), 20),
(4, 25, 'in_progress', DATE_SUB(NOW(), INTERVAL 2 DAY), 40),
(5, 2, 'completed', DATE_SUB(NOW(), INTERVAL 60 DAY), 100),
(5, 9, 'pending', DATE_SUB(NOW(), INTERVAL 7 DAY), 70),
(5, 15, 'pending', DATE_SUB(NOW(), INTERVAL 11 DAY), 40),
(5, 26, 'approved', DATE_SUB(NOW(), INTERVAL 4 DAY), 85),
(6, 3, 'pending', DATE_SUB(NOW(), INTERVAL 14 DAY), 30),
(6, 10, 'pending', DATE_SUB(NOW(), INTERVAL 20 DAY), 50),
(6, 16, 'completed', DATE_SUB(NOW(), INTERVAL 50 DAY), 100),
(6, 27, 'pending', DATE_SUB(NOW(), INTERVAL 6 DAY), 25),
(1, 5, 'pending', DATE_SUB(NOW(), INTERVAL 22 DAY), 20),
(2, 20, 'approved', DATE_SUB(NOW(), INTERVAL 4 DAY), 5),
(3, 21, 'in_progress', DATE_SUB(NOW(), INTERVAL 9 DAY), 35),
(4, 28, 'pending', DATE_SUB(NOW(), INTERVAL 11 DAY), 28),
(5, 29, 'approved', DATE_SUB(NOW(), INTERVAL 8 DAY), 50),
(6, 30, 'in_progress', DATE_SUB(NOW(), INTERVAL 6 DAY), 60),
(1, 4, 'in_progress', DATE_SUB(NOW(), INTERVAL 12 DAY), 45),
(2, 6, 'pending', DATE_SUB(NOW(), INTERVAL 18 DAY), 20),
(3, 8, 'approved', DATE_SUB(NOW(), INTERVAL 16 DAY), 75),
(4, 9, 'in_progress', DATE_SUB(NOW(), INTERVAL 14 DAY), 55),
(5, 11, 'pending', DATE_SUB(NOW(), INTERVAL 10 DAY), 30),
(6, 12, 'approved', DATE_SUB(NOW(), INTERVAL 13 DAY), 45),
(1, 13, 'in_progress', DATE_SUB(NOW(), INTERVAL 19 DAY), 65),
(2, 14, 'pending', DATE_SUB(NOW(), INTERVAL 7 DAY), 22),
(3, 15, 'approved', DATE_SUB(NOW(), INTERVAL 21 DAY), 80),
(4, 16, 'in_progress', DATE_SUB(NOW(), INTERVAL 17 DAY), 38),
(5, 17, 'completed', DATE_SUB(NOW(), INTERVAL 55 DAY), 100),
(6, 18, 'pending', DATE_SUB(NOW(), INTERVAL 15 DAY), 18);

-- ============================================================================
-- 2. CAREER PATHS & INDIVIDUAL DEVELOPMENT PLANS
-- ============================================================================

-- Insert Career Paths
INSERT INTO career_paths (name, description, target_position, prerequisites, skills_required, duration_months, status, created_at, cover_photo) VALUES
('Senior Manager Track', 'Path to becoming a senior manager with focus on leadership and strategic thinking', 'Senior Manager', '3+ years management experience', '[\"Strategic Planning\", \"Leadership\", \"Budget Management\"]', 18, 'active', NOW(), 'modules/img/gifholder/gifholder-1.gif'),
('Technical Expert Track', 'Career progression for technical professionals aiming for specialist roles', 'Technical Specialist', '5+ years technical experience', '[\"Deep Technical Knowledge\", \"Problem Solving\", \"Mentoring\"]', 24, 'active', NOW(), 'modules/img/gifholder/gifholder-2.gif'),
('Project Manager Track', 'Dedicated path for aspiring and growing project managers', 'Senior Project Manager', '2+ years project coordination', '[\"Project Management\", \"Team Leadership\", \"Stakeholder Management\"]', 12, 'active', NOW(), 'modules/img/gifholder/gifholder-3.gif'),
('Sales Executive Track', 'Career development for sales professionals targeting executive positions', 'Sales Director', '4+ years sales experience', '[\"Sales Strategy\", \"Leadership\", \"Client Relations\"]', 20, 'active', NOW(), 'modules/img/gifholder/gifholder-4.gif'),
('Human Resources Specialist', 'HR career development focusing on talent management and organizational development', 'Senior HR Manager', '2+ years HR experience', '[\"Talent Management\", \"Compensation\", \"Employee Relations\"]', 15, 'active', NOW(), 'modules/img/gifholder/gifholder-5.gif'),
('Financial Analyst Track', 'Career path for finance professionals aiming for senior analyst positions', 'Senior Financial Analyst', '3+ years accounting experience', '[\"Financial Analysis\", \"Reporting\", \"Risk Assessment\"]', 16, 'active', NOW(), 'modules/img/gifholder/gifholder-6.gif'),
('Data Scientist Career Path', 'Career progression in data science and advanced analytics', 'Lead Data Scientist', '2+ years data analysis experience', '[\"Machine Learning\", \"Data Visualization\", \"Programming\"]', 18, 'active', NOW(), 'modules/img/gifholder/gifholder-7.gif'),
('Cloud Architect Track', 'Path to becoming a cloud infrastructure architect', 'Cloud Solutions Architect', '4+ years cloud experience', '[\"Cloud Architecture\", \"System Design\", \"Security\"]', 20, 'active', NOW(), 'modules/img/gifholder/gifholder-8.gif'),
('Business Analyst Track', 'Career development for business analysis and consulting roles', 'Senior Business Analyst', '2+ years BA experience', '[\"Requirements Analysis\", \"Process Improvement\", \"Communication\"]', 14, 'active', NOW(), 'modules/img/gifholder/gifholder-9.gif'),
('Quality Assurance Lead', 'Leadership track for QA and quality management professionals', 'QA Manager', '3+ years QA experience', '[\"Quality Management\", \"Team Leadership\", \"Process Improvement\"]', 15, 'active', NOW(), 'modules/img/gifholder/gifholder-10.gif'),
('Operations Manager Track', 'Career development for operations professionals', 'Operations Director', '3+ years operations experience', '[\"Process Optimization\", \"Cost Control\", \"Team Management\"]', 16, 'active', NOW(), 'modules/img/gifholder/gifholder-11.gif'),
('Marketing Specialist Track', 'Career progression in marketing and brand management', 'Marketing Manager', '3+ years marketing experience', '[\"Campaign Management\", \"Brand Strategy\", \"Analytics\"]', 14, 'active', NOW(), 'modules/img/gifholder/gifholder-12.gif'),
('Compliance Officer Track', 'Specialized track for compliance and regulatory professionals', 'Senior Compliance Officer', '2+ years compliance experience', '[\"Regulatory Knowledge\", \"Risk Management\", \"Audit\"]', 12, 'active', NOW(), 'modules/img/gifholder/gifholder-13.gif'),
('Software Architect Track', 'Path for senior software engineers to become architects', 'Software Architect', '6+ years development experience', '[\"System Design\", \"Architecture Patterns\", \"Technical Leadership\"]', 18, 'active', NOW(), 'modules/img/gifholder/gifholder-14.gif'),
('Customer Success Manager', 'Career development for customer-facing professionals', 'Customer Success Director', '2+ years customer service experience', '[\"Account Management\", \"Customer Relations\", \"Problem Solving\"]', 13, 'active', NOW(), 'modules/img/gifholder/gifholder-15.gif');

-- Insert Individual Development Plans
INSERT INTO individual_development_plans (user_id, career_path_id, start_date, end_date, objectives, milestones, status, created_at, created_by) VALUES
(1, 1, DATE_SUB(NOW(), INTERVAL 45 DAY), DATE_ADD(NOW(), INTERVAL 315 DAY), 'Develop strategic leadership capabilities and team management skills', '[\"Complete leadership assessment\", \"Attend executive coaching\", \"Lead cross-functional project\"]', 'active', DATE_SUB(NOW(), INTERVAL 45 DAY), 1),
(2, 2, DATE_SUB(NOW(), INTERVAL 30 DAY), DATE_ADD(NOW(), INTERVAL 690 DAY), 'Become technical expert in cloud architecture', '[\"Obtain cloud certification\", \"Mentor junior developers\", \"Complete 3 architecture projects\"]', 'active', DATE_SUB(NOW(), INTERVAL 30 DAY), 1),
(3, 3, DATE_SUB(NOW(), INTERVAL 60 DAY), DATE_ADD(NOW(), INTERVAL 300 DAY), 'Master project management methodologies', '[\"PMP certification\", \"Manage 2 large projects\", \"Stakeholder training\"]', 'active', DATE_SUB(NOW(), INTERVAL 60 DAY), 1),
(4, 1, DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_ADD(NOW(), INTERVAL 520 DAY), 'Progress to senior management level', '[\"Leadership training\", \"Budget responsibility\", \"Team building initiatives\"]', 'active', DATE_SUB(NOW(), INTERVAL 20 DAY), 1),
(5, 4, DATE_SUB(NOW(), INTERVAL 50 DAY), DATE_ADD(NOW(), INTERVAL 550 DAY), 'Develop sales executive capabilities', '[\"Sales strategy course\", \"Client portfolio growth\", \"Team leadership\"]', 'active', DATE_SUB(NOW(), INTERVAL 50 DAY), 1),
(6, 5, DATE_SUB(NOW(), INTERVAL 90 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 'Complete HR specialist development', '[\"SHRM certification\", \"Compensation expertise\", \"Employee relations mastery\"]', 'completed', DATE_SUB(NOW(), INTERVAL 90 DAY), 1),
(1, 7, DATE_SUB(NOW(), INTERVAL 35 DAY), DATE_ADD(NOW(), INTERVAL 545 DAY), 'Transition to data science track', '[\"Machine learning courses\", \"Python mastery\", \"Data project completion\"]', 'active', DATE_SUB(NOW(), INTERVAL 35 DAY), 1),
(2, 8, DATE_SUB(NOW(), INTERVAL 55 DAY), DATE_ADD(NOW(), INTERVAL 545 DAY), 'Develop cloud architecture expertise', '[\"AWS certification\", \"Azure training\", \"Architecture patterns\"]', 'active', DATE_SUB(NOW(), INTERVAL 55 DAY), 1),
(3, 9, DATE_SUB(NOW(), INTERVAL 25 DAY), DATE_ADD(NOW(), INTERVAL 395 DAY), 'Master business analysis', '[\"Requirements training\", \"Process mapping\", \"Stakeholder analysis\"]', 'active', DATE_SUB(NOW(), INTERVAL 25 DAY), 1),
(4, 10, DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 445 DAY), 'Develop QA leadership', '[\"Quality frameworks\", \"Team management\", \"Process improvement\"]', 'active', DATE_SUB(NOW(), INTERVAL 5 DAY), 1),
(5, 11, DATE_SUB(NOW(), INTERVAL 40 DAY), DATE_ADD(NOW(), INTERVAL 440 DAY), 'Build operations management expertise', '[\"Lean methodology\", \"Process automation\", \"Cost reduction initiatives\"]', 'active', DATE_SUB(NOW(), INTERVAL 40 DAY), 1),
(6, 6, DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_ADD(NOW(), INTERVAL 465 DAY), 'Advance in financial analysis', '[\"Financial modeling\", \"Treasury management\", \"Risk assessment\"]', 'active', DATE_SUB(NOW(), INTERVAL 15 DAY), 1),
(1, 12, DATE_SUB(NOW(), INTERVAL 8 DAY), DATE_ADD(NOW(), INTERVAL 406 DAY), 'Develop marketing excellence', '[\"Digital marketing certification\", \"Campaign analytics\", \"Brand strategy\"]', 'pending', DATE_SUB(NOW(), INTERVAL 8 DAY), 1),
(2, 13, DATE_SUB(NOW(), INTERVAL 22 DAY), DATE_ADD(NOW(), INTERVAL 338 DAY), 'Become compliance expert', '[\"Advanced compliance training\", \"Audit certification\", \"Risk framework implementation\"]', 'active', DATE_SUB(NOW(), INTERVAL 22 DAY), 1),
(3, 14, DATE_SUB(NOW(), INTERVAL 18 DAY), DATE_ADD(NOW(), INTERVAL 522 DAY), 'Progress to software architect', '[\"System design patterns\", \"Architecture workshops\", \"Lead technical design\"]', 'active', DATE_SUB(NOW(), INTERVAL 18 DAY), 1),
(4, 15, DATE_SUB(NOW(), INTERVAL 12 DAY), DATE_ADD(NOW(), INTERVAL 378 DAY), 'Achieve customer success leadership', '[\"Executive communication\", \"Strategic account management\", \"Team leadership\"]', 'in_progress', DATE_SUB(NOW(), INTERVAL 12 DAY), 2);

-- ============================================================================
-- 3. LEADERSHIP PROGRAMS & ENROLLMENTS
-- ============================================================================

-- Insert Leadership Programs
INSERT INTO leadership_programs (name, description, level, focus_area, duration_weeks, target_audience, outcomes, status, created_at, cover_photo) VALUES
('Executive Leadership Program', 'Comprehensive program for developing executive-level leaders', 'Executive', 'Strategic Leadership', 8, 'C-level Executives', '[\"Strategic Vision\", \"Executive Decision Making\", \"Organizational Strategy\"]', 'active', NOW(), 'modules/img/gifholder/gifholder-1.gif'),
('Middle Management Excellence', 'Designed for middle managers to enhance leadership capabilities', 'Mid-Level', 'Team Management', 6, 'Middle Managers', '[\"Team Leadership\", \"Performance Management\", \"Delegation\"]', 'active', NOW(), 'modules/img/gifholder/gifholder-2.gif'),
('Emerging Leaders Program', 'Program for high-potential employees identified as future leaders', 'Foundation', 'Leadership Foundations', 5, 'High Potential Employees', '[\"Leadership Skills\", \"Self Awareness\", \"Communication\"]', 'active', NOW(), 'modules/img/gifholder/gifholder-3.gif'),
('Strategic Leadership Development', 'Focus on strategic thinking and long-term organizational planning', 'Executive', 'Strategic Thinking', 8, 'Senior Leaders', '[\"Strategic Planning\", \"Competitive Analysis\", \"Market Analysis\"]', 'active', NOW(), 'modules/img/gifholder/gifholder-4.gif'),
('Leadership Communication Workshop', 'Advanced communication skills for leaders across all levels', 'Mid-Level', 'Communication', 4, 'All Leaders', '[\"Executive Communication\", \"Presentation Skills\", \"Persuasion\"]', 'active', NOW(), 'modules/img/gifholder/gifholder-5.gif'),
('Change Management & Leadership', 'Guide organizational change as an effective leader', 'Mid-Level', 'Change Leadership', 6, 'Change Agents', '[\"Change Management\", \"Resistance Management\", \"Stakeholder Engagement\"]', 'active', NOW(), 'modules/img/gifholder/gifholder-6.gif'),
('Emotional Intelligence for Leaders', 'Develop emotional intelligence to improve team dynamics', 'Foundation', 'Self Development', 5, 'Emerging Leaders', '[\"Self Awareness\", \"Empathy\", \"Relationship Management\"]', 'active', NOW(), 'modules/img/gifholder/gifholder-7.gif'),
('Decision Making for Leaders', 'Learn frameworks for making strategic business decisions', 'Mid-Level', 'Critical Thinking', 5, 'Team Leads', '[\"Decision Frameworks\", \"Risk Assessment\", \"Problem Solving\"]', 'active', NOW(), 'modules/img/gifholder/gifholder-8.gif'),
('Servant Leadership Model', 'Leadership approach focused on serving and supporting team members', 'Foundation', 'Leadership Philosophy', 4, 'New Leaders', '[\"Service Leadership\", \"Empowerment\", \"Team Development\"]', 'active', NOW(), 'modules/img/gifholder/gifholder-9.gif'),
('Crisis Leadership & Resilience', 'Lead effectively during challenging times and organizational crises', 'Executive', 'Crisis Management', 6, 'Senior Leaders', '[\"Crisis Response\", \"Decision Making\", \"Team Stabilization\"]', 'active', NOW(), 'modules/img/gifholder/gifholder-10.gif'),
('Diversity & Inclusion Leadership', 'Building inclusive teams and fostering diversity in leadership', 'Mid-Level', 'Diversity', 5, 'All Leaders', '[\"Inclusive Leadership\", \"Bias Awareness\", \"Diversity Strategy\"]', 'active', NOW(), 'modules/img/gifholder/gifholder-11.gif'),
('Coaching & Mentoring Skills', 'Develop coaches and mentors for employee development', 'Mid-Level', 'Talent Development', 6, 'Managers', '[\"Coaching Skills\", \"Mentoring\", \"Employee Development\"]', 'active', NOW(), 'modules/img/gifholder/gifholder-12.gif'),
('Vision & Mission Leadership', 'Creating and communicating organizational vision and values', 'Executive', 'Strategic Leadership', 6, 'C-level & Directors', '[\"Vision Creation\", \"Mission Alignment\", \"Cultural Leadership\"]', 'active', NOW(), 'modules/img/gifholder/gifholder-13.gif'),
('Sustainable Leadership', 'Leadership practices for sustainable and responsible business growth', 'Executive', 'Sustainable Business', 7, 'Senior Leaders', '[\"Sustainability\", \"ESG Leadership\", \"Responsible Growth\"]', 'active', NOW(), 'modules/img/gifholder/gifholder-14.gif'),
('Digital Leadership Transformation', 'Leading organizations through digital transformation initiatives', 'Executive', 'Digital Strategy', 8, 'Technology Leaders', '[\"Digital Vision\", \"Tech Integration\", \"Change Leadership\"]', 'active', NOW(), 'modules/img/gifholder/gifholder-15.gif'),
('Influence Without Authority', 'Master the art of influencing without formal power', 'Mid-Level', 'Influence & Persuasion', 4, 'Project Leads', '[\"Persuasion\", \"Networking\", \"Stakeholder Management\"]', 'active', NOW(), 'modules/img/gifholder/gifholder-16.gif'),
('Adaptive Leadership', 'Learn to lead through ambiguity and rapid change', 'Foundation', 'Adaptability', 5, 'All Levels', '[\"Agility\", \"Problem Solving\", \"Resilience\"]', 'active', NOW(), 'modules/img/gifholder/gifholder-17.gif'),
('Global Leadership Excellence', 'Master cross-cultural leadership in global organizations', 'Executive', 'Global Strategy', 7, 'International Leaders', '[\"Cultural Awareness\", \"Global Strategy\", \"Cross-Cultural Communication\"]', 'active', NOW(), 'modules/img/gifholder/gifholder-18.gif');

-- Insert Leadership Enrollments
INSERT INTO leadership_enrollments (user_id, program_id, status, enrollment_date, progress_percentage) VALUES
(1, 1, 'in_progress', DATE_SUB(NOW(), INTERVAL 25 DAY), 45),
(1, 5, 'pending', DATE_SUB(NOW(), INTERVAL 10 DAY), 0),
(1, 15, 'approved', DATE_SUB(NOW(), INTERVAL 8 DAY), 30),
(2, 2, 'in_progress', DATE_SUB(NOW(), INTERVAL 35 DAY), 60),
(2, 4, 'pending', DATE_SUB(NOW(), INTERVAL 5 DAY), 0),
(2, 16, 'in_progress', DATE_SUB(NOW(), INTERVAL 12 DAY), 50),
(3, 3, 'pending', DATE_SUB(NOW(), INTERVAL 15 DAY), 0),
(3, 7, 'pending', DATE_SUB(NOW(), INTERVAL 12 DAY), 0),
(3, 17, 'in_progress', DATE_SUB(NOW(), INTERVAL 18 DAY), 40),
(4, 1, 'pending', DATE_SUB(NOW(), INTERVAL 8 DAY), 0),
(4, 6, 'pending', DATE_SUB(NOW(), INTERVAL 22 DAY), 0),
(4, 18, 'approved', DATE_SUB(NOW(), INTERVAL 6 DAY), 35),
(5, 2, 'in_progress', DATE_SUB(NOW(), INTERVAL 40 DAY), 50),
(5, 8, 'pending', DATE_SUB(NOW(), INTERVAL 16 DAY), 0),
(5, 11, 'in_progress', DATE_SUB(NOW(), INTERVAL 30 DAY), 55),
(6, 3, 'in_progress', DATE_SUB(NOW(), INTERVAL 20 DAY), 40),
(6, 10, 'in_progress', DATE_SUB(NOW(), INTERVAL 42 DAY), 75),
(6, 12, 'pending', DATE_SUB(NOW(), INTERVAL 10 DAY), 0),
(1, 9, 'pending', DATE_SUB(NOW(), INTERVAL 18 DAY), 0),
(2, 11, 'in_progress', DATE_SUB(NOW(), INTERVAL 30 DAY), 55),
(3, 13, 'pending', DATE_SUB(NOW(), INTERVAL 9 DAY), 0),
(4, 14, 'approved', DATE_SUB(NOW(), INTERVAL 14 DAY), 25),
(5, 9, 'in_progress', DATE_SUB(NOW(), INTERVAL 19 DAY), 65),
(6, 8, 'approved', DATE_SUB(NOW(), INTERVAL 28 DAY), 80),
(1, 12, 'pending', DATE_SUB(NOW(), INTERVAL 9 DAY), 0);

-- ============================================================================
-- 4. TEAM ACTIVITIES & PARTICIPATION
-- ============================================================================

-- Insert Team Activities
INSERT INTO team_activities (name, description, activity_date, department, budget, participant_count, status, created_at, cover_photo) VALUES
('Annual Team Building Retreat', 'Full-day team building and strategic planning session for all departments', DATE_ADD(NOW(), INTERVAL 30 DAY), 'All Departments', 5000.00, 12, 'planned', NOW(), 'modules/img/gifholder/gifholder-1.gif'),
('Cross-Functional Collaboration Workshop', 'Workshop to improve communication and collaboration across teams', DATE_ADD(NOW(), INTERVAL 25 DAY), 'HR & Operations', 2500.00, 8, 'planned', NOW(), 'modules/img/gifholder/gifholder-2.gif'),
('Innovation Hackathon 2026', 'Interactive hackathon to generate innovative ideas for organizational improvements', DATE_ADD(NOW(), INTERVAL 45 DAY), 'Technology', 3500.00, 15, 'planned', NOW(), 'modules/img/gifholder/gifholder-3.gif'),
('Diversity & Inclusion Initiative', 'Program promoting diversity and inclusive workplace culture', DATE_ADD(NOW(), INTERVAL 15 DAY), 'HR', 1500.00, 20, 'planned', NOW(), 'modules/img/gifholder/gifholder-4.gif'),
('Wellness & Work-Life Balance Program', 'Comprehensive wellness initiative focusing on employee well-being', DATE_ADD(NOW(), INTERVAL 20 DAY), 'HR', 3000.00, 35, 'planned', NOW(), 'modules/img/gifholder/gifholder-5.gif'),
('Mentoring Program Launch', 'Formal mentoring program connecting senior and junior staff', DATE_ADD(NOW(), INTERVAL 35 DAY), 'All Departments', 1000.00, 18, 'planned', NOW(), 'modules/img/gifholder/gifholder-6.gif'),
('Quarterly Knowledge Sharing Session', 'Monthly sessions where employees share expertise and best practices', DATE_ADD(NOW(), INTERVAL 10 DAY), 'Organization', 500.00, 25, 'planned', NOW(), 'modules/img/gifholder/gifholder-7.gif'),
('Community Outreach Initiative', 'Corporate social responsibility program for community involvement', DATE_ADD(NOW(), INTERVAL 50 DAY), 'CSR', 2000.00, 10, 'planned', NOW(), 'modules/img/gifholder/gifholder-8.gif'),
('Environmental Sustainability Project', 'Go-green initiative to promote environmental responsibility', DATE_ADD(NOW(), INTERVAL 40 DAY), 'Operations', 4000.00, 22, 'planned', NOW(), 'modules/img/gifholder/gifholder-9.gif'),
('Leadership Development Circle', 'Monthly discussion group for emerging leaders to develop professionally', DATE_ADD(NOW(), INTERVAL 5 DAY), 'Management', 800.00, 14, 'planned', NOW(), 'modules/img/gifholder/gifholder-10.gif'),
('Skills Development Workshop Series', 'Multi-week workshop covering essential professional skills', DATE_ADD(NOW(), INTERVAL 22 DAY), 'Training', 3500.00, 30, 'planned', NOW(), 'modules/img/gifholder/gifholder-11.gif'),
('Employee Recognition Program', 'Celebration and recognition of outstanding employee contributions', DATE_ADD(NOW(), INTERVAL 18 DAY), 'HR', 1200.00, 50, 'planned', NOW(), 'modules/img/gifholder/gifholder-12.gif'),
('Tech Innovation Lab', 'Collaborative space for exploring emerging technologies', DATE_ADD(NOW(), INTERVAL 60 DAY), 'Technology', 5500.00, 16, 'planned', NOW(), 'modules/img/gifholder/gifholder-13.gif'),
('Customer Experience Improvement', 'Initiative to enhance customer satisfaction and loyalty', DATE_ADD(NOW(), INTERVAL 28 DAY), 'Operations', 2200.00, 19, 'planned', NOW(), 'modules/img/gifholder/gifholder-14.gif'),
('Process Improvement Kaizen', 'Continuous improvement methodology implementation sessions', DATE_ADD(NOW(), INTERVAL 32 DAY), 'Operations', 1800.00, 24, 'planned', NOW(), 'modules/img/gifholder/gifholder-15.gif'),
('Health & Safety Awareness Campaign', 'Comprehensive health, safety, and wellness awareness program', DATE_ADD(NOW(), INTERVAL 12 DAY), 'Safety', 2500.00, 28, 'planned', NOW(), 'modules/img/gifholder/gifholder-16.gif'),
('Digital Transformation Roadshow', 'Series of sessions introducing digital tools and processes', DATE_ADD(NOW(), INTERVAL 38 DAY), 'Technology', 3200.00, 32, 'planned', NOW(), 'modules/img/gifholder/gifholder-17.gif'),
('Team Sports & Recreation', 'Organized sports activities and recreation for team bonding', DATE_ADD(NOW(), INTERVAL 8 DAY), 'HR', 2000.00, 40, 'planned', NOW(), 'modules/img/gifholder/gifholder-18.gif'),
('Performance Excellence Program', 'Program focusing on highest performance standards and metrics', DATE_ADD(NOW(), INTERVAL 42 DAY), 'Management', 2800.00, 21, 'planned', NOW(), 'modules/img/gifholder/gifholder-19.gif'),
('Social Responsibility Week', 'Week-long initiative for various charitable and social causes', DATE_ADD(NOW(), INTERVAL 55 DAY), 'CSR', 3300.00, 45, 'planned', NOW(), 'modules/img/gifholder/gifholder-20.gif'),
('Agile Transformation Initiative', 'Implementing Agile practices across organization', DATE_ADD(NOW(), INTERVAL 48 DAY), 'Technology', 4200.00, 26, 'planned', NOW(), 'modules/img/gifholder/gifholder-21.gif'),
('Customer Advisory Board Meeting', 'Strategic session with key customers for feedback', DATE_ADD(NOW(), INTERVAL 20 DAY), 'Sales', 1800.00, 12, 'planned', NOW(), 'modules/img/gifholder/gifholder-1.gif'),
('Code of Conduct Training', 'Mandatory ethics and compliance training for all employees', DATE_ADD(NOW(), INTERVAL 7 DAY), 'Compliance', 1200.00, 60, 'planned', NOW(), 'modules/img/gifholder/gifholder-2.gif'),
('Data Privacy & GDPR Workshop', 'Education on data protection and privacy regulations', DATE_ADD(NOW(), INTERVAL 19 DAY), 'IT & Compliance', 2100.00, 35, 'planned', NOW(), 'modules/img/gifholder/gifholder-3.gif'),
('Executive Strategy Summit', 'Annual gathering of leadership for strategic planning', DATE_ADD(NOW(), INTERVAL 65 DAY), 'Executive', 6500.00, 15, 'planned', NOW(), 'modules/img/gifholder/gifholder-4.gif');

-- Insert Team Activity Participants
INSERT INTO team_activity_participants (user_id, activity_id, status, created_at) VALUES
(1, 1, 'confirmed', DATE_SUB(NOW(), INTERVAL 20 DAY)),
(1, 5, 'confirmed', DATE_SUB(NOW(), INTERVAL 15 DAY)),
(1, 10, 'confirmed', DATE_SUB(NOW(), INTERVAL 16 DAY)),
(1, 19, 'confirmed', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(2, 2, 'confirmed', DATE_SUB(NOW(), INTERVAL 18 DAY)),
(2, 7, 'confirmed', DATE_SUB(NOW(), INTERVAL 12 DAY)),
(2, 14, 'confirmed', DATE_SUB(NOW(), INTERVAL 14 DAY)),
(2, 9, 'confirmed', DATE_SUB(NOW(), INTERVAL 17 DAY)),
(2, 23, 'confirmed', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(3, 3, 'confirmed', DATE_SUB(NOW(), INTERVAL 25 DAY)),
(3, 11, 'confirmed', DATE_SUB(NOW(), INTERVAL 10 DAY)),
(3, 6, 'confirmed', DATE_SUB(NOW(), INTERVAL 20 DAY)),
(3, 17, 'confirmed', DATE_SUB(NOW(), INTERVAL 13 DAY)),
(3, 24, 'confirmed', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(4, 4, 'confirmed', DATE_SUB(NOW(), INTERVAL 22 DAY)),
(4, 9, 'confirmed', DATE_SUB(NOW(), INTERVAL 8 DAY)),
(4, 12, 'confirmed', DATE_SUB(NOW(), INTERVAL 19 DAY)),
(4, 5, 'confirmed', DATE_SUB(NOW(), INTERVAL 24 DAY)),
(4, 20, 'confirmed', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(5, 6, 'confirmed', DATE_SUB(NOW(), INTERVAL 30 DAY)),
(5, 13, 'confirmed', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(5, 15, 'confirmed', DATE_SUB(NOW(), INTERVAL 11 DAY)),
(5, 18, 'confirmed', DATE_SUB(NOW(), INTERVAL 6 DAY)),
(5, 21, 'confirmed', DATE_SUB(NOW(), INTERVAL 8 DAY)),
(6, 8, 'confirmed', DATE_SUB(NOW(), INTERVAL 28 DAY)),
(6, 7, 'confirmed', DATE_SUB(NOW(), INTERVAL 23 DAY)),
(6, 3, 'confirmed', DATE_SUB(NOW(), INTERVAL 7 DAY)),
(6, 16, 'confirmed', DATE_SUB(NOW(), INTERVAL 9 DAY)),
(6, 22, 'confirmed', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(1, 2, 'confirmed', DATE_SUB(NOW(), INTERVAL 11 DAY)),
(2, 1, 'confirmed', DATE_SUB(NOW(), INTERVAL 19 DAY)),
(3, 5, 'confirmed', DATE_SUB(NOW(), INTERVAL 16 DAY)),
(4, 7, 'confirmed', DATE_SUB(NOW(), INTERVAL 21 DAY)),
(5, 4, 'confirmed', DATE_SUB(NOW(), INTERVAL 13 DAY)),
(6, 9, 'confirmed', DATE_SUB(NOW(), INTERVAL 10 DAY)),
(1, 11, 'confirmed', DATE_SUB(NOW(), INTERVAL 14 DAY)),
(2, 13, 'confirmed', DATE_SUB(NOW(), INTERVAL 22 DAY)),
(3, 8, 'confirmed', DATE_SUB(NOW(), INTERVAL 26 DAY)),
(4, 10, 'confirmed', DATE_SUB(NOW(), INTERVAL 18 DAY)),
(5, 2, 'confirmed', DATE_SUB(NOW(), INTERVAL 27 DAY)),
(6, 14, 'confirmed', DATE_SUB(NOW(), INTERVAL 23 DAY)),
(1, 15, 'confirmed', DATE_SUB(NOW(), INTERVAL 7 DAY)),
(2, 16, 'confirmed', DATE_SUB(NOW(), INTERVAL 6 DAY)),
(3, 18, 'confirmed', DATE_SUB(NOW(), INTERVAL 15 DAY)),
(4, 19, 'confirmed', DATE_SUB(NOW(), INTERVAL 20 DAY)),
(5, 20, 'confirmed', DATE_SUB(NOW(), INTERVAL 9 DAY)),
(6, 21, 'confirmed', DATE_SUB(NOW(), INTERVAL 12 DAY)),
(1, 12, 'confirmed', DATE_SUB(NOW(), INTERVAL 25 DAY)),
(2, 8, 'confirmed', DATE_SUB(NOW(), INTERVAL 19 DAY)),
(3, 4, 'confirmed', DATE_SUB(NOW(), INTERVAL 17 DAY)),
(4, 6, 'confirmed', DATE_SUB(NOW(), INTERVAL 12 DAY));

-- ============================================================================
-- 5. PERFORMANCE REVIEWS
-- ============================================================================

INSERT INTO performance_reviews (employee_id, reviewer_id, rating, review_period_start, review_period_end, comments, status, created_at) VALUES
(1, 2, 4.5, DATE_SUB(NOW(), INTERVAL 180 DAY), DATE_SUB(NOW(), INTERVAL 90 DAY), 'Exceptional performance in project delivery and team collaboration', 'completed', DATE_SUB(NOW(), INTERVAL 85 DAY)),
(2, 3, 4.2, DATE_SUB(NOW(), INTERVAL 180 DAY), DATE_SUB(NOW(), INTERVAL 90 DAY), 'Strong technical skills with room for improvement in communication', 'completed', DATE_SUB(NOW(), INTERVAL 83 DAY)),
(3, 1, 4.8, DATE_SUB(NOW(), INTERVAL 180 DAY), DATE_SUB(NOW(), INTERVAL 90 DAY), 'Outstanding leadership and mentorship qualities', 'completed', DATE_SUB(NOW(), INTERVAL 88 DAY)),
(4, 2, 3.9, DATE_SUB(NOW(), INTERVAL 180 DAY), DATE_SUB(NOW(), INTERVAL 90 DAY), 'Solid performance with focus needed on time management', 'completed', DATE_SUB(NOW(), INTERVAL 84 DAY)),
(5, 1, 4.6, DATE_SUB(NOW(), INTERVAL 180 DAY), DATE_SUB(NOW(), INTERVAL 90 DAY), 'Excellent strategic planning and execution capabilities', 'completed', DATE_SUB(NOW(), INTERVAL 86 DAY)),
(6, 3, 4.1, DATE_SUB(NOW(), INTERVAL 180 DAY), DATE_SUB(NOW(), INTERVAL 90 DAY), 'Good performance with potential for growth in advanced skills', 'completed', DATE_SUB(NOW(), INTERVAL 82 DAY)),
(1, 2, 4.3, DATE_SUB(NOW(), INTERVAL 90 DAY), NOW(), 'Continues to demonstrate leadership excellence and innovation', 'pending', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(2, 3, 4.0, DATE_SUB(NOW(), INTERVAL 90 DAY), NOW(), 'Improved communication skills, strong technical performance', 'pending', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(3, 1, 4.7, DATE_SUB(NOW(), INTERVAL 90 DAY), NOW(), 'Exceptional mentorship leading to team growth', 'in_progress', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(4, 2, 4.1, DATE_SUB(NOW(), INTERVAL 90 DAY), NOW(), 'Better time management and project coordination', 'pending', DATE_SUB(NOW(), INTERVAL 3 DAY));

-- ============================================================================
-- 6. 360-DEGREE FEEDBACK
-- ============================================================================

INSERT INTO feedback_360 (employee_id, reviewer_id, reviewer_type, rating, comments, feedback_date) VALUES
(1, 2, 'manager', 4.6, 'Excellent strategic thinking and team leadership', DATE_SUB(NOW(), INTERVAL 60 DAY)),
(1, 3, 'peer', 4.4, 'Great collaboration skills and always helpful', DATE_SUB(NOW(), INTERVAL 60 DAY)),
(1, 4, 'subordinate', 4.7, 'Inspires and motivates the team effectively', DATE_SUB(NOW(), INTERVAL 60 DAY)),
(2, 1, 'manager', 4.2, 'Strong technical abilities, needs to develop leadership', DATE_SUB(NOW(), INTERVAL 60 DAY)),
(2, 4, 'peer', 4.3, 'Reliable team member with good technical expertise', DATE_SUB(NOW(), INTERVAL 60 DAY)),
(2, 5, 'subordinate', 4.0, 'Provides good technical guidance', DATE_SUB(NOW(), INTERVAL 60 DAY)),
(3, 2, 'manager', 4.8, 'Exceptional performer and natural leader', DATE_SUB(NOW(), INTERVAL 60 DAY)),
(3, 1, 'peer', 4.7, 'Excellent mentor and supportive colleague', DATE_SUB(NOW(), INTERVAL 60 DAY)),
(3, 5, 'subordinate', 4.9, 'Outstanding mentor who helped my growth significantly', DATE_SUB(NOW(), INTERVAL 60 DAY)),
(4, 3, 'manager', 3.9, 'Competent but needs improvement in initiative', DATE_SUB(NOW(), INTERVAL 60 DAY)),
(4, 1, 'peer', 4.1, 'Cooperative team member', DATE_SUB(NOW(), INTERVAL 60 DAY)),
(4, 6, 'subordinate', 3.8, 'Adequate guidance provided', DATE_SUB(NOW(), INTERVAL 60 DAY)),
(5, 2, 'manager', 4.5, 'Strong strategic capabilities and initiative', DATE_SUB(NOW(), INTERVAL 60 DAY)),
(6, 1, 'manager', 4.2, 'Good performance with growth potential', DATE_SUB(NOW(), INTERVAL 60 DAY));

-- ============================================================================
-- 7. COMPETENCIES & USER COMPETENCIES
-- ============================================================================

INSERT INTO competencies (name, category, proficiency_levels, description, created_at) VALUES
('Leadership', 'Management', '[\"Beginner\", \"Intermediate\", \"Advanced\", \"Expert\"]', 'Ability to lead teams and make strategic decisions', NOW()),
('Communication', 'Soft Skills', '[\"Beginner\", \"Intermediate\", \"Advanced\", \"Fluent\"]', 'Effective written and verbal communication', NOW()),
('Technical Expertise', 'Technical', '[\"Beginner\", \"Intermediate\", \"Advanced\", \"Master\"]', 'Deep technical knowledge and problem-solving', NOW()),
('Project Management', 'Management', '[\"Beginner\", \"Intermediate\", \"Advanced\", \"Expert\"]', 'Planning, execution and delivery of projects', NOW()),
('Problem Solving', 'Core', '[\"Basic\", \"Intermediate\", \"Advanced\", \"Expert\"]', 'Ability to analyze and resolve complex issues', NOW()),
('Customer Relations', 'Soft Skills', '[\"Developing\", \"Proficient\", \"Excellent\", \"Exceptional\"]', 'Building and maintaining customer relationships', NOW()),
('Financial Analysis', 'Technical', '[\"Basic\", \"Intermediate\", \"Advanced\", \"Expert\"]', 'Analyzing financial data and metrics', NOW()),
('Strategic Planning', 'Management', '[\"Beginner\", \"Intermediate\", \"Advanced\", \"Expert\"]', 'Long-term planning and strategy development', NOW());

INSERT INTO user_competencies (user_id, competency_id, current_level, target_level, assessed_date) VALUES
(1, 1, 'Advanced', 'Expert', DATE_SUB(NOW(), INTERVAL 30 DAY)),
(1, 2, 'Advanced', 'Fluent', DATE_SUB(NOW(), INTERVAL 30 DAY)),
(1, 4, 'Intermediate', 'Advanced', DATE_SUB(NOW(), INTERVAL 30 DAY)),
(1, 8, 'Advanced', 'Expert', DATE_SUB(NOW(), INTERVAL 30 DAY)),
(2, 3, 'Advanced', 'Master', DATE_SUB(NOW(), INTERVAL 25 DAY)),
(2, 2, 'Intermediate', 'Advanced', DATE_SUB(NOW(), INTERVAL 25 DAY)),
(2, 5, 'Advanced', 'Expert', DATE_SUB(NOW(), INTERVAL 25 DAY)),
(3, 1, 'Expert', 'Expert', DATE_SUB(NOW(), INTERVAL 35 DAY)),
(3, 2, 'Advanced', 'Fluent', DATE_SUB(NOW(), INTERVAL 35 DAY)),
(3, 4, 'Advanced', 'Expert', DATE_SUB(NOW(), INTERVAL 35 DAY)),
(3, 6, 'Advanced', 'Exceptional', DATE_SUB(NOW(), INTERVAL 35 DAY)),
(4, 4, 'Intermediate', 'Advanced', DATE_SUB(NOW(), INTERVAL 28 DAY)),
(4, 5, 'Intermediate', 'Advanced', DATE_SUB(NOW(), INTERVAL 28 DAY)),
(5, 8, 'Advanced', 'Expert', DATE_SUB(NOW(), INTERVAL 22 DAY)),
(5, 1, 'Intermediate', 'Advanced', DATE_SUB(NOW(), INTERVAL 22 DAY)),
(6, 7, 'Intermediate', 'Advanced', DATE_SUB(NOW(), INTERVAL 18 DAY)),
(6, 2, 'Intermediate', 'Advanced', DATE_SUB(NOW(), INTERVAL 18 DAY));

-- ============================================================================
-- 8. COMPLIANCE TRAININGS & ASSIGNMENTS
-- ============================================================================

INSERT INTO compliance_trainings (title, description, compliance_type, frequency, mandatory, created_at) VALUES
('Annual Safety Training', 'Mandatory safety protocols and emergency procedures', 'Health & Safety', 'Yearly', true, NOW()),
('Data Privacy & GDPR Compliance', 'Understanding data protection and privacy regulations', 'Data Privacy', 'Yearly', true, NOW()),
('Anti-Harassment & Discrimination Policy', 'Workplace conduct and anti-harassment training', 'HR Compliance', 'Every 2 Years', true, NOW()),
('Information Security Awareness', 'Protecting company and customer information', 'Cybersecurity', 'Yearly', true, NOW()),
('Code of Conduct Training', 'Company policies and ethical standards', 'Business Ethics', 'Yearly', true, NOW()),
('Export Control & Trade Compliance', 'Regulations for international trade and exports', 'Legal Compliance', 'Yearly', false, NOW()),
('Environmental Compliance', 'Environmental laws and sustainability practices', 'Environmental', 'Every 2 Years', false, NOW()),
('Quality & ISO Standards', 'ISO standards and quality management', 'Quality', 'Yearly', false, NOW());

INSERT INTO compliance_assignments (user_id, compliance_training_id, due_date, completion_date, status) VALUES
(1, 1, DATE_ADD(NOW(), INTERVAL 90 DAY), NULL, 'assigned'),
(1, 2, DATE_ADD(NOW(), INTERVAL 90 DAY), NULL, 'assigned'),
(1, 3, DATE_ADD(NOW(), INTERVAL 180 DAY), NULL, 'assigned'),
(1, 5, DATE_ADD(NOW(), INTERVAL 90 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY), 'completed'),
(2, 1, DATE_ADD(NOW(), INTERVAL 90 DAY), DATE_SUB(NOW(), INTERVAL 50 DAY), 'completed'),
(2, 2, DATE_ADD(NOW(), INTERVAL 90 DAY), NULL, 'in_progress'),
(2, 4, DATE_ADD(NOW(), INTERVAL 90 DAY), NULL, 'assigned'),
(2, 5, DATE_ADD(NOW(), INTERVAL 90 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY), 'completed'),
(3, 1, DATE_ADD(NOW(), INTERVAL 90 DAY), DATE_SUB(NOW(), INTERVAL 45 DAY), 'completed'),
(3, 2, DATE_ADD(NOW(), INTERVAL 90 DAY), DATE_SUB(NOW(), INTERVAL 30 DAY), 'completed'),
(3, 5, DATE_ADD(NOW(), INTERVAL 90 DAY), NULL, 'assigned'),
(4, 1, DATE_ADD(NOW(), INTERVAL 90 DAY), NULL, 'assigned'),
(4, 2, DATE_ADD(NOW(), INTERVAL 90 DAY), NULL, 'assigned'),
(4, 5, DATE_ADD(NOW(), INTERVAL 90 DAY), NULL, 'assigned'),
(5, 1, DATE_ADD(NOW(), INTERVAL 90 DAY), NULL, 'in_progress'),
(5, 3, DATE_ADD(NOW(), INTERVAL 180 DAY), NULL, 'assigned'),
(5, 5, DATE_ADD(NOW(), INTERVAL 90 DAY), NULL, 'assigned'),
(6, 1, DATE_ADD(NOW(), INTERVAL 90 DAY), NULL, 'assigned'),
(6, 2, DATE_ADD(NOW(), INTERVAL 90 DAY), NULL, 'assigned'),
(6, 4, DATE_ADD(NOW(), INTERVAL 90 DAY), NULL, 'assigned');

-- ============================================================================
-- 9. LMS Courses
-- ============================================================================

INSERT INTO lms_courses (title, description, category, instructor_id, course_content, duration_hours, status, created_at) VALUES
('Advanced SQL Optimization', 'Learn advanced database query optimization techniques and performance tuning', 'Database', 2, 'Advanced techniques for database query optimization and performance tuning', 40, 'published', NOW()),
('Web Development with Python Flask', 'Build modern web applications using Python Flask framework', 'Web Development', 2, 'Comprehensive guide to building web applications with Python Flask', 36, 'published', NOW()),
('Mobile App Development Basics', 'Introduction to mobile app development for iOS and Android', 'Mobile Development', 3, 'Fundamental concepts and tools for developing mobile applications', 32, 'published', NOW()),
('Business Intelligence & Analytics', 'Master BI tools and analytics for data-driven decision making', 'Analytics', 5, 'BI tools and analytics methodologies for business intelligence', 30, 'published', NOW()),
('Graphic Design Essentials', 'Learn fundamental design principles and creative tools', 'Design', 6, 'Design principles and essential Adobe Creative Suite tools', 28, 'published', NOW()),
('Content Marketing Strategy', 'Develop effective content marketing campaigns and strategies', 'Marketing', 1, 'Strategies for developing and implementing content marketing campaigns', 24, 'published', NOW()),
('Advanced Excel for Business', 'Master advanced Excel functions for business analytics', 'Business Tools', 2, 'Advanced Excel functions and features for business analysis', 20, 'published', NOW()),
('Professional Writing Skills', 'Enhance business writing and documentation skills', 'Communication', 3, 'Business writing techniques and professional documentation skills', 16, 'published', NOW()),
('Project Risk Management', 'Identify, assess, and mitigate project risks', 'Project Management', 4, 'Risk identification, assessment, and mitigation strategies', 24, 'published', NOW()),
('Building Remote Teams', 'Strategies for managing and engaging remote teams effectively', 'Management', 1, 'Management strategies for remote and distributed teams', 20, 'published', NOW());

INSERT INTO lms_enrollments (user_id, course_id, status, progress_percentage, score, enrollment_date, completion_date) VALUES
(1, 1, 'completed', 100, 92, DATE_SUB(NOW(), INTERVAL 90 DAY), DATE_SUB(NOW(), INTERVAL 30 DAY)),
(1, 2, 'in_progress', 65, 0, DATE_SUB(NOW(), INTERVAL 45 DAY), NULL),
(2, 3, 'pending', 0, 0, DATE_SUB(NOW(), INTERVAL 10 DAY), NULL),
(2, 4, 'in_progress', 45, 0, DATE_SUB(NOW(), INTERVAL 25 DAY), NULL),
(2, 7, 'completed', 100, 88, DATE_SUB(NOW(), INTERVAL 60 DAY), DATE_SUB(NOW(), INTERVAL 15 DAY)),
(3, 1, 'in_progress', 75, 0, DATE_SUB(NOW(), INTERVAL 50 DAY), NULL),
(3, 4, 'pending', 0, 0, DATE_SUB(NOW(), INTERVAL 5 DAY), NULL),
(3, 8, 'completed', 100, 95, DATE_SUB(NOW(), INTERVAL 75 DAY), DATE_SUB(NOW(), INTERVAL 20 DAY)),
(4, 2, 'pending', 0, 0, DATE_SUB(NOW(), INTERVAL 8 DAY), NULL),
(4, 5, 'in_progress', 35, 0, DATE_SUB(NOW(), INTERVAL 30 DAY), NULL),
(4, 9, 'completed', 100, 85, DATE_SUB(NOW(), INTERVAL 85 DAY), DATE_SUB(NOW(), INTERVAL 25 DAY)),
(5, 4, 'in_progress', 55, 0, DATE_SUB(NOW(), INTERVAL 35 DAY), NULL),
(5, 6, 'completed', 100, 90, DATE_SUB(NOW(), INTERVAL 70 DAY), DATE_SUB(NOW(), INTERVAL 18 DAY)),
(5, 10, 'pending', 0, 0, DATE_SUB(NOW(), INTERVAL 3 DAY), NULL),
(6, 2, 'in_progress', 45, 0, DATE_SUB(NOW(), INTERVAL 40 DAY), NULL),
(6, 3, 'pending', 0, 0, DATE_SUB(NOW(), INTERVAL 6 DAY), NULL),
(6, 7, 'in_progress', 60, 0, DATE_SUB(NOW(), INTERVAL 20 DAY), NULL);

-- ============================================================================
-- DUMMY DATA IMPORT COMPLETE
-- ============================================================================
