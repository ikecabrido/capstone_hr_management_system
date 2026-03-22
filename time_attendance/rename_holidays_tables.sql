-- Run this SQL in your database to create/rename the holiday tables with ta_ prefix

-- Drop old tables if they exist (without ta_ prefix)
DROP TABLE IF EXISTS holiday_sync_log;
DROP TABLE IF EXISTS holidays;

-- Create new holiday tables with ta_ prefix
CREATE TABLE IF NOT EXISTS ta_holidays (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    holiday_date DATE NOT NULL,
    is_recurring BOOLEAN DEFAULT 0,
    country_code VARCHAR(10) DEFAULT 'PH',
    description TEXT,
    category VARCHAR(50) DEFAULT 'national',
    is_active BOOLEAN DEFAULT 1,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_date (holiday_date),
    INDEX idx_recurring (is_recurring),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ta_holiday_sync_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sync_date DATE,
    total_holidays INT,
    country_code VARCHAR(10),
    last_synced TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_sync (sync_date, country_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- If you have old tables with data that you want to migrate:
-- RENAME TABLE holidays TO ta_holidays;
-- RENAME TABLE holiday_sync_log TO ta_holiday_sync_log;
