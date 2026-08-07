<?php
require_once "../auth/database.php";

$db = Database::getInstance()->getConnection();

// Check if tables already exist
$tables = ['exit_resignations', 'exit_terminations', 'exit_interviews', 'exit_knowledge_transfer_plans', 'exit_knowledge_transfer_items', 'exit_employee_settlements', 'exit_documents', 'exit_surveys', 'exit_survey_questions', 'exit_survey_responses', 'exit_survey_answers'];
$existingTables = [];

foreach ($tables as $table) {
    $stmt = $db->prepare("SHOW TABLES LIKE ?");
    $stmt->execute([$table]);
    if ($stmt->fetch()) {
        $existingTables[] = $table;
    }
}

if (!empty($existingTables)) {
    echo "The following tables already exist: " . implode(', ', $existingTables) . "\n";
    echo "Skipping table creation.\n";
    exit(0);
}

$exitTablesSQL = "
-- Table structure for table `exit_resignations`
CREATE TABLE `exit_resignations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(50) NOT NULL,
  `resignation_type` enum('voluntary','involuntary') NOT NULL,
  `reason` text NOT NULL,
  `notice_date` date NOT NULL,
  `last_working_date` date NOT NULL,
  `comments` text,
  `submitted_by` int(11) DEFAULT NULL,
  `preclearance_desk_person` int(11) DEFAULT NULL,
  `status` enum('pending_review','pending_legal_review','approved','rejected','rejected_by_legal','withdrawn') DEFAULT 'pending_review',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `hr_approved_by` int(11) DEFAULT NULL,
  `hr_approved_at` datetime DEFAULT NULL,
  `hr_approval_comments` text DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `review_remarks` text DEFAULT NULL,
  `legal_approved_by` int(11) DEFAULT NULL,
  `legal_approved_at` datetime DEFAULT NULL,
  `legal_approval_comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_resignation_employee` (`employee_id`),
  KEY `fk_resignation_submitted_by` (`submitted_by`),
  KEY `fk_resignation_preclearance_desk` (`preclearance_desk_person`),
  KEY `fk_resignation_approved_by` (`approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `exit_terminations`
CREATE TABLE `exit_terminations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(50) NOT NULL,
  `termination_reason` text NOT NULL,
  `effective_date` date NOT NULL,
  `comments` text,
  `submitted_by` int(11) DEFAULT NULL,
  `status` enum('pending_review','pending_legal_review','approved','rejected','rejected_by_legal','withdrawn') DEFAULT 'pending_review',
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `review_remarks` text DEFAULT NULL,
  `legal_approved_by` int(11) DEFAULT NULL,
  `legal_approved_at` datetime DEFAULT NULL,
  `legal_approval_comments` text DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_termination_employee` (`employee_id`),
  KEY `fk_termination_submitted_by` (`submitted_by`),
  KEY `fk_termination_approved_by` (`approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `exit_interviews`
CREATE TABLE `exit_interviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(50) NOT NULL,
  `exit_case_type` enum('resignation','termination') NOT NULL,
  `exit_case_id` int(11) NOT NULL,
  `interviewer_id` int(11) DEFAULT NULL,
  `scheduled_date` date NOT NULL,
  `scheduled_time` time NOT NULL,
  `location` varchar(255) DEFAULT 'Virtual',
  `notes` text,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `completed_at` timestamp NULL DEFAULT NULL,
  `feedback` text,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_interview_employee` (`employee_id`),
  KEY `fk_interview_exit_case` (`exit_case_id`),
  KEY `fk_interview_interviewer` (`interviewer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `exit_interview_feedback`
CREATE TABLE `exit_interview_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interview_id` int(11) NOT NULL,
  `overall_satisfaction` tinyint(1) NOT NULL,
  `work_environment_rating` tinyint(1) NOT NULL,
  `management_rating` tinyint(1) NOT NULL,
  `compensation_rating` tinyint(1) NOT NULL,
  `work_life_balance_rating` tinyint(1) NOT NULL,
  `reason_for_leaving` text NOT NULL,
  `suggestions` text,
  `would_recommend` enum('yes','no') NOT NULL,
  `additional_comments` text,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_feedback_interview` (`interview_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `exit_knowledge_transfer_plans`
CREATE TABLE `exit_knowledge_transfer_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(50) NOT NULL,
  `successor_id` varchar(50) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','completed','cancelled') DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_transfer_employee` (`employee_id`),
  KEY `fk_transfer_successor` (`successor_id`),
  KEY `fk_transfer_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `exit_knowledge_transfer_items`
CREATE TABLE `exit_knowledge_transfer_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) NOT NULL,
  `item_type` enum('document','process','contact','system','other') NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `notes` text,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_item_plan` (`plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `exit_employee_settlements`
CREATE TABLE `exit_employee_settlements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(50) NOT NULL,
  `resignation_id` int(11) DEFAULT NULL,
  `exit_case_type` enum('resignation','termination') DEFAULT NULL,
  `exit_case_id` int(11) DEFAULT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `remaining_salary` decimal(10,2) DEFAULT 0.00,
  `overtime_pay` decimal(10,2) DEFAULT 0.00,
  `holiday_pay` decimal(10,2) DEFAULT 0.00,
  `bonuses` decimal(10,2) DEFAULT 0.00,
  `commission` decimal(10,2) DEFAULT 0.00,
  `hra` decimal(10,2) DEFAULT 0.00,
  `conveyance` decimal(10,2) DEFAULT 0.00,
  `lta` decimal(10,2) DEFAULT 0.00,
  `medical_allowance` decimal(10,2) DEFAULT 0.00,
  `other_allowances` decimal(10,2) DEFAULT 0.00,
  `separation_pay` decimal(10,2) DEFAULT 0.00,
  `tax` decimal(10,2) DEFAULT 0.00,
  `sss` decimal(10,2) DEFAULT 0.00,
  `philhealth` decimal(10,2) DEFAULT 0.00,
  `pagibig` decimal(10,2) DEFAULT 0.00,
  `cash_advance` decimal(10,2) DEFAULT 0.00,
  `company_loan` decimal(10,2) DEFAULT 0.00,
  `equipment_damage` decimal(10,2) DEFAULT 0.00,
  `missing_assets` decimal(10,2) DEFAULT 0.00,
  `late_deductions` decimal(10,2) DEFAULT 0.00,
  `absence_deductions` decimal(10,2) DEFAULT 0.00,
  `provident_fund` decimal(10,2) DEFAULT 0.00,
  `gratuity` decimal(10,2) DEFAULT 0.00,
  `notice_pay` decimal(10,2) DEFAULT 0.00,
  `outstanding_loans` decimal(10,2) DEFAULT 0.00,
  `other_deductions` decimal(10,2) DEFAULT 0.00,
  `net_payable` decimal(10,2) NOT NULL,
  `settlement_date` date NOT NULL,
  `payment_date` date DEFAULT NULL,
  `status` enum('draft','pending_approval','approved','paid','rejected') DEFAULT 'draft',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_settlement_employee` (`employee_id`),
  KEY `fk_settlement_resignation` (`resignation_id`),
  KEY `idx_settlement_exit_case` (`exit_case_type`, `exit_case_id`),
  KEY `fk_settlement_approved_by` (`approved_by`),
  KEY `fk_settlement_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `payroll_clearances`
CREATE TABLE IF NOT EXISTS `payroll_clearances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `settlement_id` int(11) NOT NULL,
  `requested_by` int(11) DEFAULT NULL,
  `requested_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `last_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_settlement_id` (`settlement_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Payroll clearance requests linked to exit settlements';

-- Table structure for table `exit_documents`
CREATE TABLE `exit_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(50) NOT NULL,
  `document_type` enum('resignation_letter','clearance_form','handover_document','certificate','other') NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `status` enum('active','archived') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_document_employee` (`employee_id`),
  KEY `fk_document_uploaded_by` (`uploaded_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `exit_surveys`
CREATE TABLE `exit_surveys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `target_audience` enum('all','voluntary','involuntary') DEFAULT 'all',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','inactive','completed') DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_survey_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `exit_survey_questions`
CREATE TABLE `exit_survey_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('text','textarea','radio','checkbox','select','rating') NOT NULL,
  `options` json DEFAULT NULL,
  `required` tinyint(1) DEFAULT 0,
  `order_num` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_question_survey` (`survey_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `exit_survey_responses`
CREATE TABLE `exit_survey_responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `responses` json NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_response_survey` (`survey_id`),
  KEY `fk_response_employee` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `exit_survey_answers`
CREATE TABLE `exit_survey_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `response_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text,
  `answer_value` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_answer_response` (`response_id`),
  KEY `fk_answer_question` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Constraints
ALTER TABLE `exit_resignations` ADD CONSTRAINT `fk_resignation_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL, ADD CONSTRAINT `fk_resignation_hr_approved_by` FOREIGN KEY (`hr_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL, ADD CONSTRAINT `fk_resignation_legal_approved_by` FOREIGN KEY (`legal_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL, ADD CONSTRAINT `fk_resignation_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE, ADD CONSTRAINT `fk_resignation_submitted_by` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL, ADD CONSTRAINT `fk_resignation_preclearance_desk` FOREIGN KEY (`preclearance_desk_person`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `exit_terminations` ADD CONSTRAINT `fk_termination_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL, ADD CONSTRAINT `fk_termination_legal_approved_by` FOREIGN KEY (`legal_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL, ADD CONSTRAINT `fk_termination_reviewed_by` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL, ADD CONSTRAINT `fk_termination_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE, ADD CONSTRAINT `fk_termination_submitted_by` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `exit_interviews` ADD CONSTRAINT `fk_interview_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE, ADD CONSTRAINT `fk_interview_interviewer` FOREIGN KEY (`interviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `exit_interview_feedback` ADD CONSTRAINT `fk_feedback_interview` FOREIGN KEY (`interview_id`) REFERENCES `exit_interviews` (`id`) ON DELETE CASCADE;
ALTER TABLE `exit_knowledge_transfer_plans` ADD CONSTRAINT `fk_transfer_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL, ADD CONSTRAINT `fk_transfer_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE, ADD CONSTRAINT `fk_transfer_successor` FOREIGN KEY (`successor_id`) REFERENCES `employees` (`employee_id`) ON DELETE SET NULL;
ALTER TABLE `exit_knowledge_transfer_items` ADD CONSTRAINT `fk_item_plan` FOREIGN KEY (`plan_id`) REFERENCES `exit_knowledge_transfer_plans` (`id`) ON DELETE CASCADE;
ALTER TABLE `exit_employee_settlements` ADD CONSTRAINT `fk_settlement_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL, ADD CONSTRAINT `fk_settlement_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL, ADD CONSTRAINT `fk_settlement_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE, ADD CONSTRAINT `fk_settlement_resignation` FOREIGN KEY (`resignation_id`) REFERENCES `exit_resignations` (`id`) ON DELETE SET NULL;
ALTER TABLE `payroll_clearances` ADD CONSTRAINT `fk_clearance_settlement` FOREIGN KEY (`settlement_id`) REFERENCES `exit_employee_settlements` (`id`) ON DELETE CASCADE;
ALTER TABLE `exit_documents` ADD CONSTRAINT `fk_document_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE, ADD CONSTRAINT `fk_document_uploaded_by` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `exit_surveys` ADD CONSTRAINT `fk_survey_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `exit_survey_questions` ADD CONSTRAINT `fk_question_survey` FOREIGN KEY (`survey_id`) REFERENCES `exit_surveys` (`id`) ON DELETE CASCADE;
ALTER TABLE `exit_survey_responses` ADD CONSTRAINT `fk_response_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE, ADD CONSTRAINT `fk_response_survey` FOREIGN KEY (`survey_id`) REFERENCES `exit_surveys` (`id`) ON DELETE CASCADE;
ALTER TABLE `exit_survey_answers` ADD CONSTRAINT `fk_answer_question` FOREIGN KEY (`question_id`) REFERENCES `exit_survey_questions` (`id`) ON DELETE CASCADE, ADD CONSTRAINT `fk_answer_response` FOREIGN KEY (`response_id`) REFERENCES `exit_survey_responses` (`id`) ON DELETE CASCADE;
";

try {
    $db->exec($exitTablesSQL);
    echo "Exit management tables created successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>