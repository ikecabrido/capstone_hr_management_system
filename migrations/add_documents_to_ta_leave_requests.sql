-- Add documents column to ta_leave_requests table if it doesn't exist
ALTER TABLE `ta_leave_requests` 
ADD COLUMN `documents` LONGTEXT COMMENT 'JSON array of uploaded document file paths' AFTER `supporting_document`,
ADD COLUMN `reason` TEXT COMMENT 'Reason for leave' AFTER `details`;

-- Add document upload date tracking
ALTER TABLE `ta_leave_requests` 
ADD COLUMN `document_uploaded_at` TIMESTAMP NULL DEFAULT NULL AFTER `documents`;
