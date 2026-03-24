-- =====================================================
-- INCIDENT DISCIPLINARY WORKFLOW SYSTEM - SAMPLE DATA
-- Add to sample_hr database
-- =====================================================

-- First run the migration to create tables and add columns
-- Then run this sample data file

-- =====================================================
-- 1. UPDATE EXISTING INCIDENTS WITH WORKFLOW COLUMNS
-- =====================================================

-- Update existing incidents with workflow data
UPDATE incidents SET 
    respondent_id = 7,
    reporter_name = 'Ana Cruz',
    current_workflow_step = 'closed',
    nte_deadline = '2026-03-20',
    explanation_deadline = '2026-03-22',
    final_decision = 'verbal_warning',
    final_decision_date = '2026-03-23',
    closed_at = '2026-03-23 16:36:38',
    closure_reason = 'First offense - verbal warning issued',
    created_by = 2
WHERE id = 60;

UPDATE incidents SET 
    respondent_id = 17,
    reporter_name = 'Juan Dela Cruz',
    current_workflow_step = 'closed',
    nte_deadline = '2026-03-21',
    explanation_deadline = '2026-03-23',
    final_decision = 'verbal_warning',
    final_decision_date = '2026-03-23',
    closed_at = '2026-03-23 16:55:40',
    closure_reason = 'First tardiness offense - verbal warning issued',
    created_by = 2
WHERE id = 61;

UPDATE incidents SET 
    respondent_id = 3,
    reporter_name = 'Ana Reyes',
    current_workflow_step = 'under_review',
    created_by = 2
WHERE id = 62;

UPDATE incidents SET 
    respondent_id = 4,
    reporter_name = 'HR Department',
    current_workflow_step = 'hr_evaluation',
    nte_deadline = '2026-03-17',
    explanation_deadline = '2026-03-19',
    created_by = 2
WHERE id = 63;

UPDATE incidents SET 
    respondent_id = 8,
    reporter_name = 'Lisa Gomez',
    current_workflow_step = 'closed',
    nte_deadline = '2026-03-12',
    explanation_deadline = '2026-03-14',
    final_decision = 'suspension',
    final_decision_date = '2026-03-23',
    closed_at = '2026-03-23 16:42:16',
    closure_reason = 'Employee suspended for 2 weeks without pay',
    created_by = 2
WHERE id = 64;

-- =====================================================
-- 2. INSERT NEW INCIDENTS WITH FULL WORKFLOW DATA
-- =====================================================

-- Incident 65: Case at NTE Issued stage
INSERT INTO incidents (`incident_id`, `reporter_id`, `incident_type`, `severity`, `category`, `violation_type`, `title`, `description`, `incident_date`, `incident_time`, `location`, `complainant_name`, `respondent_name`, `witnesses`, `reported_by`, `is_anonymous`, `is_confidential`, `status`, `assigned_to`, `assigned_hr_id`, `respondent_id`, `reporter_name`, `current_workflow_step`, `nte_deadline`, `created_by`, `created_at`, `updated_at`) VALUES
('INC-2026-005', 5, 'misconduct', 'medium', 'Insubordination', 'major', 'Refusal to Follow Direct Instructions', 'Employee repeatedly refused to follow direct instructions from supervisor regarding task completion.', '2026-03-20', '09:30:00', 'IT Department', 'Maria Lopez', 'Mark Lee', 'Sarah Kim, David Chen', 'Employee', 0, 0, 'in_progress', 3, 2, 3, 'Maria Lopez', 'nte_issued', '2026-03-24', 2, '2026-03-21 10:00:00', '2026-03-21 14:30:00');

-- Incident 66: Case at Explanation Received stage
INSERT INTO incidents (`incident_id`, `reporter_id`, `incident_type`, `severity`, `category`, `violation_type`, `title`, `description`, `incident_date`, `incident_time`, `location`, `complainant_name`, `respondent_name`, `witnesses`, `reported_by`, `is_anonymous`, `is_confidential`, `status`, `assigned_to`, `assigned_hr_id`, `respondent_id`, `reporter_name`, `current_workflow_step`, `nte_deadline`, `explanation_deadline`, `created_by`, `created_at`, `updated_at`) VALUES
('INC-2026-006', 6, 'attendance', 'high', 'Absence Without Leave', 'major', 'Unauthorized Absence', 'Employee was absent for 3 consecutive days without prior notice or approval.', '2026-03-18', NULL, 'Finance Department', 'John Rey', 'Brian Flores', 'Angela Torres', 'Employee', 0, 0, 'in_progress', 4, 2, 14, 'John Rey', 'explanation_received', '2026-03-21', '2026-03-23', 2, '2026-03-20 09:00:00', '2026-03-22 16:00:00');

-- Incident 67: Case at HR Evaluation stage
INSERT INTO incidents (`incident_id`, `reporter_id`, `incident_type`, `severity`, `category`, `violation_type`, `title`, `description`, `incident_date`, `incident_time`, `location`, `complainant_name`, `respondent_name`, `witnesses`, `reported_by`, `is_anonymous`, `is_confidential`, `status`, `assigned_to`, `assigned_hr_id`, `respondent_id`, `reporter_name`, `current_workflow_step`, `nte_deadline`, `explanation_deadline`, `created_by`, `created_at`, `updated_at`) VALUES
('INC-2026-007', 9, 'harassment', 'critical', 'Verbal Harassment', 'major', 'Hostile Work Environment', 'Employee created a hostile work environment through repeated verbal harassment of colleagues.', '2026-03-15', '15:00:00', 'Registrar Office', 'Diane Castro', 'Angela Torres', 'Pedro Lopez, Maria Santos', 'Employee', 0, 1, 'in_progress', 2, 2, 15, 'Diane Castro', 'hr_evaluation', '2026-03-18', '2026-03-20', 2, '2026-03-17 08:00:00', '2026-03-21 11:00:00');

-- Incident 68: Case at Decision Made stage
INSERT INTO incidents (`incident_id`, `reporter_id`, `incident_type`, `severity`, `category`, `violation_type`, `title`, `description`, `incident_date`, `incident_time`, `location`, `complainant_name`, `respondent_name`, `witnesses`, `reported_by`, `is_anonymous`, `is_confidential`, `status`, `assigned_to`, `assigned_hr_id`, `respondent_id`, `reporter_name`, `current_workflow_step`, `nte_deadline`, `explanation_deadline`, `final_decision`, `final_decision_date`, `created_by`, `created_at`, `updated_at`) VALUES
('INC-2026-008', 10, 'misconduct', 'medium', 'Policy Violation', 'minor', 'Improper Use of Company Resources', 'Employee used company equipment for personal business during work hours.', '2026-03-19', '10:00:00', 'Academic Affairs', 'Jose Garcia', 'Maria Santos', 'Lance Tan', 'Employee', 0, 0, 'pending_approval', 2, 2, 9, 'Jose Garcia', 'decision_made', '2026-03-22', '2026-03-24', 'written_warning', '2026-03-23', 2, '2026-03-20 14:00:00', '2026-03-23 15:00:00');

-- Incident 69: Case at Final Action stage
INSERT INTO incidents (`incident_id`, `reporter_id`, `incident_type`, `severity`, `category`, `violation_type`, `title`, `description`, `incident_date`, `incident_time`, `location`, `complainant_name`, `respondent_name`, `witnesses`, `reported_by`, `is_anonymous`, `is_confidential`, `status`, `assigned_to`, `assigned_hr_id`, `respondent_id`, `reporter_name`, `current_workflow_step`, `nte_deadline`, `explanation_deadline`, `final_decision`, `final_decision_date`, `created_by`, `created_at`, `updated_at`) VALUES
('INC-2026-009', 11, 'theft', 'critical', 'Property Misappropriation', 'major', 'Theft of Company Property', 'Employee was found to have stolen office supplies and equipment.', '2026-03-10', '16:00:00', 'Maintenance', 'System', 'Pedro Lopez', 'Security Cameras', 'System', 0, 1, 'pending_approval', 1, 2, 17, 'System', 'final_action', '2026-03-13', '2026-03-15', 'termination', '2026-03-22', 1, '2026-03-12 09:00:00', '2026-03-22 10:00:00');

-- Incident 70: New case just submitted
INSERT INTO incidents (`incident_id`, `reporter_id`, `incident_type`, `severity`, `category`, `violation_type`, `title`, `description`, `incident_date`, `incident_time`, `location`, `complainant_name`, `respondent_name`, `witnesses`, `reported_by`, `is_anonymous`, `is_confidential`, `status`, `assigned_to`, `assigned_hr_id`, `respondent_id`, `reporter_name`, `current_workflow_step`, `created_by`, `created_at`, `updated_at`) VALUES
('INC-2026-010', 12, 'misconduct', 'low', 'Dress Code', 'minor', 'Dress Code Violation', 'Employee came to work wearing inappropriate attire.', '2026-03-23', '08:30:00', 'Administration', 'Patricia Go', 'Lance Tan', 'Niki Zepanya', 'Employee', 0, 0, 'open', NULL, 2, 8, 'Patricia Go', 'submitted', 2, '2026-03-23 09:00:00', '2026-03-23 09:00:00');

-- =====================================================
-- 3. INCIDENT WORKFLOW TABLE - Step-by-step tracking
-- =====================================================

-- Workflow for incident 62 (under_review)
INSERT INTO incident_workflow (`incident_id`, `step`, `step_status`, `started_at`, `completed_at`, `deadline`, `notes`, `performed_by`, `created_at`, `updated_at`) VALUES
(62, 'submitted', 'completed', '2026-03-23 16:41:29', '2026-03-23 16:41:29', NULL, 'Incident reported by employee', 2, '2026-03-23 16:41:29', '2026-03-23 16:41:29'),
(62, 'under_review', 'in_progress', '2026-03-23 16:41:29', NULL, '2026-03-25 16:41:29', 'HR reviewing the incident details', 2, '2026-03-23 16:41:29', '2026-03-23 16:41:29');

-- Workflow for incident 63 (hr_evaluation)
INSERT INTO incident_workflow (`incident_id`, `step`, `step_status`, `started_at`, `completed_at`, `deadline`, `notes`, `performed_by`, `created_at`, `updated_at`) VALUES
(63, 'submitted', 'completed', '2026-03-23 16:41:29', '2026-03-23 16:41:29', NULL, 'Incident reported by HR', 2, '2026-03-23 16:41:29', '2026-03-23 16:41:29'),
(63, 'under_review', 'completed', '2026-03-23 16:41:29', '2026-03-24 10:00:00', '2026-03-25 16:41:29', 'Approved to issue NTE', 2, '2026-03-23 16:41:29', '2026-03-24 10:00:00'),
(63, 'nte_issued', 'completed', '2026-03-24 10:00:00', '2026-03-24 14:00:00', '2026-03-26 10:00:00', 'NTE-2026-003 issued', 2, '2026-03-24 10:00:00', '2026-03-24 14:00:00'),
(63, 'explanation_received', 'completed', '2026-03-24 14:00:00', '2026-03-25 09:00:00', '2026-03-27 14:00:00', 'Explanation received from employee', 4, '2026-03-24 14:00:00', '2026-03-25 09:00:00'),
(63, 'hr_evaluation', 'in_progress', '2026-03-25 09:00:00', NULL, '2026-03-27 09:00:00', 'Evaluating explanation and evidence', 2, '2026-03-25 09:00:00', '2026-03-25 09:00:00');

-- Workflow for incident 65 (nte_issued)
INSERT INTO incident_workflow (`incident_id`, `step`, `step_status`, `started_at`, `completed_at`, `deadline`, `notes`, `performed_by`, `created_at`, `updated_at`) VALUES
(65, 'submitted', 'completed', '2026-03-21 10:00:00', '2026-03-21 10:00:00', NULL, 'Incident reported', 2, '2026-03-21 10:00:00', '2026-03-21 10:00:00'),
(65, 'under_review', 'completed', '2026-03-21 10:00:00', '2026-03-21 14:00:00', '2026-03-23 10:00:00', 'Approved to issue NTE', 2, '2026-03-21 10:00:00', '2026-03-21 14:00:00'),
(65, 'nte_issued', 'in_progress', '2026-03-21 14:00:00', NULL, '2026-03-24 14:00:00', 'NTE issued - awaiting employee response', 2, '2026-03-21 14:00:00', '2026-03-21 14:00:00');

-- Workflow for incident 66 (explanation_received)
INSERT INTO incident_workflow (`incident_id`, `step`, `step_status`, `started_at`, `completed_at`, `deadline`, `notes`, `performed_by`, `created_at`, `updated_at`) VALUES
(66, 'submitted', 'completed', '2026-03-20 09:00:00', '2026-03-20 09:00:00', NULL, 'Incident reported', 2, '2026-03-20 09:00:00', '2026-03-20 09:00:00'),
(66, 'under_review', 'completed', '2026-03-20 09:00:00', '2026-03-20 11:00:00', '2026-03-22 09:00:00', 'Approved to issue NTE', 2, '2026-03-20 09:00:00', '2026-03-20 11:00:00'),
(66, 'nte_issued', 'completed', '2026-03-20 11:00:00', '2026-03-20 15:00:00', '2026-03-22 11:00:00', 'NTE-2026-006 issued', 2, '2026-03-20 11:00:00', '2026-03-20 15:00:00'),
(66, 'explanation_received', 'in_progress', '2026-03-22 16:00:00', NULL, '2026-03-25 16:00:00', 'Explanation received - under review', 14, '2026-03-20 11:00:00', '2026-03-22 16:00:00');

-- Workflow for incident 67 (hr_evaluation)
INSERT INTO incident_workflow (`incident_id`, `step`, `step_status`, `started_at`, `completed_at`, `deadline`, `notes`, `performed_by`, `created_at`, `updated_at`) VALUES
(67, 'submitted', 'completed', '2026-03-17 08:00:00', '2026-03-17 08:00:00', NULL, 'Incident reported', 2, '2026-03-17 08:00:00', '2026-03-17 08:00:00'),
(67, 'under_review', 'completed', '2026-03-17 08:00:00', '2026-03-17 14:00:00', '2026-03-19 08:00:00', 'Approved to issue NTE', 2, '2026-03-17 08:00:00', '2026-03-17 14:00:00'),
(67, 'nte_issued', 'completed', '2026-03-17 14:00:00', '2026-03-18 09:00:00', '2026-03-20 14:00:00', 'NTE-2026-007 issued', 2, '2026-03-17 14:00:00', '2026-03-18 09:00:00'),
(67, 'explanation_received', 'completed', '2026-03-18 09:00:00', '2026-03-19 10:00:00', '2026-03-21 09:00:00', 'Explanation received from employee', 15, '2026-03-18 09:00:00', '2026-03-19 10:00:00'),
(67, 'hr_evaluation', 'in_progress', '2026-03-19 10:00:00', NULL, '2026-03-21 10:00:00', 'Evaluating harassment case - reviewing witness statements', 2, '2026-03-19 10:00:00', '2026-03-19 10:00:00');

-- Workflow for incident 68 (decision_made)
INSERT INTO incident_workflow (`incident_id`, `step`, `step_status`, `started_at`, `completed_at`, `deadline`, `notes`, `performed_by`, `created_at`, `updated_at`) VALUES
(68, 'submitted', 'completed', '2026-03-20 14:00:00', '2026-03-20 14:00:00', NULL, 'Incident reported', 2, '2026-03-20 14:00:00', '2026-03-20 14:00:00'),
(68, 'under_review', 'completed', '2026-03-20 14:00:00', '2026-03-21 09:00:00', '2026-03-22 14:00:00', 'Approved to issue NTE', 2, '2026-03-20 14:00:00', '2026-03-21 09:00:00'),
(68, 'nte_issued', 'completed', '2026-03-21 09:00:00', '2026-03-21 11:00:00', '2026-03-23 09:00:00', 'NTE-2026-008 issued', 2, '2026-03-21 09:00:00', '2026-03-21 11:00:00'),
(68, 'explanation_received', 'completed', '2026-03-21 11:00:00', '2026-03-22 10:00:00', '2026-03-24 11:00:00', 'Explanation received', 9, '2026-03-21 11:00:00', '2026-03-22 10:00:00'),
(68, 'hr_evaluation', 'completed', '2026-03-22 10:00:00', '2026-03-23 11:00:00', '2026-03-24 10:00:00', 'Decision: Written Warning', 2, '2026-03-22 10:00:00', '2026-03-23 11:00:00'),
(68, 'decision_made', 'in_progress', '2026-03-23 11:00:00', NULL, NULL, 'Written warning approved - awaiting final action', 2, '2026-03-23 11:00:00', '2026-03-23 11:00:00');

-- Workflow for incident 69 (final_action)
INSERT INTO incident_workflow (`incident_id`, `step`, `step_status`, `started_at`, `completed_at`, `deadline`, `notes`, `performed_by`, `created_at`, `updated_at`) VALUES
(69, 'submitted', 'completed', '2026-03-12 09:00:00', '2026-03-12 09:00:00', NULL, 'Incident reported by system', 1, '2026-03-12 09:00:00', '2026-03-12 09:00:00'),
(69, 'under_review', 'completed', '2026-03-12 09:00:00', '2026-03-12 14:00:00', '2026-03-14 09:00:00', 'Critical case - immediate NTE issued', 1, '2026-03-12 09:00:00', '2026-03-12 14:00:00'),
(69, 'nte_issued', 'completed', '2026-03-12 14:00:00', '2026-03-13 10:00:00', '2026-03-15 14:00:00', 'NTE-2026-009 issued', 1, '2026-03-12 14:00:00', '2026-03-13 10:00:00'),
(69, 'explanation_received', 'completed', '2026-03-13 10:00:00', '2026-03-14 11:00:00', '2026-03-16 10:00:00', 'Explanation received - denied allegations', 17, '2026-03-13 10:00:00', '2026-03-14 11:00:00'),
(69, 'hr_evaluation', 'completed', '2026-03-14 11:00:00', '2026-03-21 10:00:00', '2026-03-16 11:00:00', 'Evidence reviewed - theft confirmed', 1, '2026-03-14 11:00:00', '2026-03-21 10:00:00'),
(69, 'decision_made', 'completed', '2026-03-21 10:00:00', '2026-03-22 09:00:00', NULL, 'Decision: Termination', 1, '2026-03-21 10:00:00', '2026-03-22 09:00:00'),
(69, 'final_action', 'in_progress', '2026-03-22 09:00:00', NULL, '2026-03-23 09:00:00', 'Preparing termination documents', 1, '2026-03-22 09:00:00', '2026-03-22 09:00:00');

-- Workflow for incident 70 (submitted)
INSERT INTO incident_workflow (`incident_id`, `step`, `step_status`, `started_at`, `completed_at`, `deadline`, `notes`, `performed_by`, `created_at`, `updated_at`) VALUES
(70, 'submitted', 'in_progress', '2026-03-23 09:00:00', NULL, NULL, 'New incident submitted - pending review', 2, '2026-03-23 09:00:00', '2026-03-23 09:00:00');

-- =====================================================
-- 4. NOTICE TO EXPLAIN (NTE) TABLE
-- =====================================================

INSERT INTO notice_to_explain (`incident_id`, `nte_number`, `issued_to`, `issued_by`, `issue_date`, `deadline_date`, `nte_content`, `delivery_method`, `delivered_at`, `is_received`, `received_signature`, `reminder_sent`, `reminder_sent_at`, `created_at`) VALUES
(63, 'NTE-2026-003', 4, 2, '2026-03-24', '2026-03-26', 'Dear Kevin Tan,\n\nYou are hereby issued a Notice to Explain regarding the incident dated March 15, 2026, concerning unauthorized access to confidential files.\n\nYou are required to submit your written explanation within 48 hours from receipt of this notice. Failure to respond may result in disciplinary action.\n\nPlease submit your explanation to the HR Department.', 'email', '2026-03-24 14:00:00', 1, 'Kevin Tan', 0, NULL, '2026-03-24 14:00:00'),
(65, 'NTE-2026-005', 3, 2, '2026-03-21', '2026-03-24', 'Dear Mark Lee,\n\nYou are hereby issued a Notice to Explain regarding the incident dated March 20, 2026, concerning refusal to follow direct instructions from your supervisor.\n\nYou are required to submit your written explanation within 48 hours from receipt of this notice. Failure to respond may result in disciplinary action.\n\nPlease submit your explanation to the HR Department.', 'email', '2026-03-21 14:30:00', 1, 'Mark Lee', 1, '2026-03-23 09:00:00', '2026-03-21 14:30:00'),
(66, 'NTE-2026-006', 14, 2, '2026-03-20', '2026-03-22', 'Dear Brian Flores,\n\nYou are hereby issued a Notice to Explain regarding the incident dated March 18, 2026, concerning unauthorized absence for 3 consecutive days.\n\nYou are required to submit your written explanation within 48 hours from receipt of this notice. Failure to respond may result in disciplinary action.\n\nPlease submit your explanation to the HR Department.', 'email', '2026-03-20 15:00:00', 1, 'Brian Flores', 0, NULL, '2026-03-20 15:00:00'),
(67, 'NTE-2026-007', 15, 2, '2026-03-17', '2026-03-20', 'Dear Angela Torres,\n\nYou are hereby issued a Notice to Explain regarding the incident dated March 15, 2026, concerning verbal harassment of colleagues.\n\nYou are required to submit your written explanation within 48 hours from receipt of this notice. Failure to respond may result in disciplinary action.\n\nPlease submit your explanation to the HR Department.', 'email', '2026-03-18 09:00:00', 1, 'Angela Torres', 0, NULL, '2026-03-18 09:00:00'),
(68, 'NTE-2026-008', 9, 2, '2026-03-21', '2026-03-23', 'Dear Maria Santos,\n\nYou are hereby issued a Notice to Explain regarding the incident dated March 19, 2026, concerning improper use of company resources.\n\nYou are required to submit your written explanation within 48 hours from receipt of this notice. Failure to respond may result in disciplinary action.\n\nPlease submit your explanation to the HR Department.', 'email', '2026-03-21 11:00:00', 1, 'Maria Santos', 0, NULL, '2026-03-21 11:00:00'),
(69, 'NTE-2026-009', 17, 1, '2026-03-12', '2026-03-15', 'Dear Pedro Lopez,\n\nYou are hereby issued a Notice to Explain regarding the incident dated March 10, 2026, concerning theft of company property.\n\nThis is a serious offense that may result in immediate termination. You are required to submit your written explanation within 48 hours from receipt of this notice.\n\nPlease submit your explanation to the HR Department immediately.', 'both', '2026-03-13 10:00:00', 1, 'Pedro Lopez', 0, NULL, '2026-03-13 10:00:00');

-- =====================================================
-- 5. EMPLOYEE EXPLANATIONS TABLE
-- =====================================================

INSERT INTO explanations (`incident_id`, `employee_id`, `explanation_text`, `submission_method`, `submitted_at`, `is_late`, `late_reason`, `attachments`, `reviewed_by`, `reviewed_at`, `review_notes`) VALUES
(63, 4, 'I accessed the files to complete my assigned task. I was given authorization by my supervisor to review the confidential data for the quarterly report. I did not misuse or share any information with third parties.', 'online', '2026-03-25 08:30:00', 0, NULL, NULL, 2, '2026-03-25 09:00:00', 'Explanation received. Need to verify with supervisor about authorization.'),
(66, 14, 'I was experiencing severe family emergency. My mother was hospitalized and I had to take care of my younger siblings. I should have notified the company but was in a state of panic. I apologize for the inconvenience caused.', 'online', '2026-03-22 16:00:00', 0, NULL, 'medical_cert.pdf', 2, '2026-03-22 16:30:00', 'Medical certificate provided. Valid reason but proper notification required.'),
(67, 15, 'I deny the allegations. The complainant and I had a professional disagreement about work matters. I never used offensive language or created a hostile environment. The witnesses may have misinterpreted our conversation.', 'written', '2026-03-19 10:00:00', 0, NULL, NULL, 2, '2026-03-19 14:00:00', 'Explanation received. Need to interview witnesses for further evaluation.'),
(68, 9, 'I used the company printer for a few personal documents during lunch break. I was not aware this was a violation. I understand now that this is against company policy and I will not do it again.', 'online', '2026-03-22 10:00:00', 1, 'Submitted 1 hour after deadline due to internet issues', NULL, 2, '2026-03-22 10:30:00', 'Admission of guilt. First offense - written warning appropriate.'),
(69, 17, 'I did not steal any company property. The items in my bag were items I found in the trash and was planning to dispose of properly. This is a misunderstanding.', 'written', '2026-03-14 11:00:00', 0, NULL, NULL, 1, '2026-03-14 15:00:00', 'Explanation denied. Security footage confirms theft. Case to proceed to termination.');

-- =====================================================
-- 6. DISCIPLINARY ACTIONS TABLE
-- =====================================================

INSERT INTO disciplinary_actions (`incident_id`, `action_type`, `action_details`, `action_date`, `effective_date`, `duration_days`, `issued_by`, `approved_by`, `approved_at`, `is_final`, `document_path`, `created_at`) VALUES
(60, 'verbal_warning', 'First offense - verbal warning issued for insubordination. Employee advised to follow proper chain of command.', '2026-03-23', '2026-03-23', NULL, 2, 999, '2026-03-23 16:36:38', 1, NULL, '2026-03-23 16:36:38'),
(61, 'verbal_warning', 'First tardiness offense - verbal warning issued. Employee advised to arrive on time.', '2026-03-23', '2026-03-23', NULL, 2, 999, '2026-03-23 16:55:40', 1, NULL, '2026-03-23 16:55:40'),
(64, 'suspension', 'Employee suspended for 2 weeks without pay due to harassment complaint. Suspension effective immediately.', '2026-03-23', '2026-03-24', 14, 2, 1, '2026-03-23 16:41:29', 1, 'suspension_2026_004.pdf', '2026-03-23 16:41:29'),
(68, 'written_warning', 'Written warning for improper use of company resources. First offense - employee warned.', '2026-03-23', '2026-03-23', NULL, 2, 2, '2026-03-23 15:00:00', 1, 'written_warning_2026_008.pdf', '2026-03-23 15:00:00'),
(69, 'termination', 'Termination of employment due to theft of company property. Effective immediately.', '2026-03-22', '2026-03-22', NULL, 1, 1, '2026-03-22 10:00:00', 1, 'termination_2026_009.pdf', '2026-03-22 10:00:00');

-- =====================================================
-- 7. EMAIL NOTIFICATIONS LOG TABLE
-- =====================================================

INSERT INTO incident_email_log (`incident_id`, `recipient_id`, `recipient_email`, `email_type`, `subject`, `body`, `sent_by`, `sent_at`, `status`, `error_message`) VALUES
(62, 3, 'mark.lee@school.edu', 'incident_submitted', 'Incident Report Received - INC-2026-002', 'Your incident report has been submitted and is now under review by HR.', 2, '2026-03-23 16:41:29', 'sent', NULL),
(63, 4, 'john.fin@school.edu', 'nte_issued', 'Notice to Explain - INC-2026-003', 'You have been issued a Notice to Explain. Please respond within 48 hours.', 2, '2026-03-24 14:00:00', 'sent', NULL),
(63, 4, 'john.fin@school.edu', 'nte_reminder', 'Reminder: Notice to Explain Deadline - INC-2026-003', 'This is a reminder that your explanation is due soon.', 2, '2026-03-25 09:00:00', 'sent', NULL),
(65, 3, 'mark.lee@school.edu', 'nte_issued', 'Notice to Explain - INC-2026-005', 'You have been issued a Notice to Explain. Please respond within 48 hours.', 2, '2026-03-21 14:30:00', 'sent', NULL),
(65, 3, 'mark.lee@school.edu', 'nte_reminder', 'Reminder: Notice to Explain Deadline - INC-2026-005', 'This is a reminder that your explanation is due soon.', 2, '2026-03-23 09:00:00', 'sent', NULL),
(66, 14, 'brian.fin@school.edu', 'nte_issued', 'Notice to Explain - INC-2026-006', 'You have been issued a Notice to Explain. Please respond within 48 hours.', 2, '2026-03-20 15:00:00', 'sent', NULL),
(67, 15, 'angela.reg@school.edu', 'nte_issued', 'Notice to Explain - INC-2026-007', 'You have been issued a Notice to Explain. Please respond within 48 hours.', 2, '2026-03-18 09:00:00', 'sent', NULL),
(68, 9, 'maria.acad@school.edu', 'nte_issued', 'Notice to Explain - INC-2026-008', 'You have been issued a Notice to Explain. Please respond within 48 hours.', 2, '2026-03-21 11:00:00', 'sent', NULL),
(68, 9, 'maria.acad@school.edu', 'decision_notice', 'Decision Notice - INC-2026-008', 'A decision has been made regarding your case. Please contact HR for details.', 2, '2026-03-23 15:00:00', 'sent', NULL),
(69, 17, 'pedro.maint@school.edu', 'nte_issued', 'Notice to Explain - INC-2026-009', 'You have been issued a Notice to Explain for a serious offense. Immediate response required.', 1, '2026-03-13 10:00:00', 'sent', NULL),
(69, 17, 'pedro.maint@school.edu', 'decision_notice', 'Decision Notice - INC-2026-009', 'A decision has been made regarding your case. Please contact HR immediately.', 1, '2026-03-22 09:00:00', 'sent', NULL),
(70, 8, 'lance.admin@school.edu', 'incident_submitted', 'Incident Report Received - INC-2026-010', 'Your incident report has been submitted and is now under review by HR.', 2, '2026-03-23 09:00:00', 'sent', NULL);

-- =====================================================
-- 8. ACTIVITY LOG TABLE
-- =====================================================

INSERT INTO incident_activity_log (`incident_id`, `activity_type`, `activity_description`, `performed_by`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES
(62, 'created', 'Incident created by employee', 2, '192.168.1.100', 'Mozilla/5.0', '{"reporter_id": 6}', '2026-03-23 16:41:29'),
(62, 'status_changed', 'Status changed from open to under_review', 2, '192.168.1.100', 'Mozilla/5.0', NULL, '2026-03-23 16:41:29'),
(63, 'created', 'Incident created by HR system', 2, '192.168.1.101', 'Mozilla/5.0', '{"reporter_id": 4}', '2026-03-23 16:41:29'),
(63, 'status_changed', 'Status changed from open to in_progress', 2, '192.168.1.101', 'Mozilla/5.0', NULL, '2026-03-23 16:41:29'),
(63, 'nte_issued', 'Notice to Explain issued', 2, '192.168.1.101', 'Mozilla/5.0', '{"nte_number": "NTE-2026-003"}', '2026-03-24 14:00:00'),
(63, 'explanation_received', 'Employee explanation received', 4, '192.168.1.105', 'Mozilla/5.0', NULL, '2026-03-25 08:30:00'),
(65, 'created', 'Incident created by employee', 2, '192.168.1.102', 'Mozilla/5.0', '{"reporter_id": 5}', '2026-03-21 10:00:00'),
(65, 'nte_issued', 'Notice to Explain issued', 2, '192.168.1.102', 'Mozilla/5.0', '{"nte_number": "NTE-2026-005"}', '2026-03-21 14:30:00'),
(65, 'reminder_sent', 'NTE reminder sent', 2, '192.168.1.102', 'Mozilla/5.0', NULL, '2026-03-23 09:00:00'),
(66, 'created', 'Incident created by employee', 2, '192.168.1.103', 'Mozilla/5.0', '{"reporter_id": 6}', '2026-03-20 09:00:00'),
(66, 'nte_issued', 'Notice to Explain issued', 2, '192.168.1.103', 'Mozilla/5.0', '{"nte_number": "NTE-2026-006"}', '2026-03-20 15:00:00'),
(66, 'explanation_received', 'Employee explanation received', 14, '192.168.1.110', 'Mozilla/5.0', NULL, '2026-03-22 16:00:00'),
(67, 'created', 'Incident created by employee', 2, '192.168.1.104', 'Mozilla/5.0', '{"reporter_id": 9}', '2026-03-17 08:00:00'),
(67, 'nte_issued', 'Notice to Explain issued', 2, '192.168.1.104', 'Mozilla/5.0', '{"nte_number": "NTE-2026-007"}', '2026-03-18 09:00:00'),
(67, 'explanation_received', 'Employee explanation received', 15, '192.168.1.115', 'Mozilla/5.0', NULL, '2026-03-19 10:00:00'),
(68, 'created', 'Incident created by employee', 2, '192.168.1.105', 'Mozilla/5.0', '{"reporter_id": 10}', '2026-03-20 14:00:00'),
(68, 'nte_issued', 'Notice to Explain issued', 2, '192.168.1.105', 'Mozilla/5.0', '{"nte_number": "NTE-2026-008"}', '2026-03-21 11:00:00'),
(68, 'explanation_received', 'Employee explanation received', 9, '192.168.1.109', 'Mozilla/5.0', NULL, '2026-03-22 10:00:00'),
(68, 'decision_made', 'Decision made: Written Warning', 2, '192.168.1.105', 'Mozilla/5.0', '{"decision": "written_warning"}', '2026-03-23 11:00:00'),
(69, 'created', 'Incident created by system', 1, '192.168.1.1', 'System', NULL, '2026-03-12 09:00:00'),
(69, 'nte_issued', 'Notice to Explain issued - Critical case', 1, '192.168.1.1', 'System', '{"nte_number": "NTE-2026-009"}', '2026-03-13 10:00:00'),
(69, 'explanation_received', 'Employee explanation received', 17, '192.168.1.117', 'Mozilla/5.0', NULL, '2026-03-14 11:00:00'),
(69, 'decision_made', 'Decision made: Termination', 1, '192.168.1.1', 'System', '{"decision": "termination"}', '2026-03-22 09:00:00'),
(70, 'created', 'Incident created by employee', 2, '192.168.1.106', 'Mozilla/5.0', '{"reporter_id": 12}', '2026-03-23 09:00:00');

-- =====================================================
-- 9. VERIFY DATA
-- =====================================================

-- Check incidents with workflow
SELECT id, incident_id, respondent_name, current_workflow_step, status, final_decision 
FROM incidents 
WHERE id >= 60 
ORDER BY id;

-- Check workflow steps
SELECT * FROM incident_workflow ORDER BY incident_id, started_at;

-- Check NTEs
SELECT id, nte_number, issued_to, issue_date, deadline_date, is_received 
FROM notice_to_explain 
ORDER BY id;

-- Check explanations
SELECT id, incident_id, employee_id, submitted_at, is_late, reviewed_by 
FROM explanations 
ORDER BY id;

-- Check disciplinary actions
SELECT id, incident_id, action_type, action_date, is_final 
FROM disciplinary_actions 
ORDER BY id;

-- Check email log
SELECT id, incident_id, email_type, recipient_email, sent_at, status 
FROM incident_email_log 
ORDER BY id;

-- Check activity log
SELECT id, incident_id, activity_type, activity_description, performed_by, created_at 
FROM incident_activity_log 
ORDER BY id;
