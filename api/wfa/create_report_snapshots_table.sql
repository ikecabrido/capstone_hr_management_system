-- Create Report Snapshots Table
CREATE TABLE IF NOT EXISTS wfa_report_snapshots (
    snapshot_id INT PRIMARY KEY AUTO_INCREMENT,
    snapshot_type VARCHAR(50) NOT NULL, -- 'dashboard', 'attrition', 'diversity', 'performance'
    snapshot_name VARCHAR(255) NOT NULL,
    snapshot_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_employees INT,
    active_employees INT,
    inactive_employees INT,
    attrition_rate DECIMAL(5,2),
    average_tenure DECIMAL(5,1),
    department_count INT,
    position_count INT,
    snapshot_data JSON, -- Store full analytics data as JSON
    created_by VARCHAR(100),
    notes TEXT,
    INDEX idx_snapshot_type (snapshot_type),
    INDEX idx_snapshot_date (snapshot_date)
);

-- Create Report History Table
CREATE TABLE IF NOT EXISTS wfa_report_history (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    report_type VARCHAR(50) NOT NULL, -- 'dashboard', 'attrition', 'diversity', 'performance'
    report_title VARCHAR(255),
    report_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    file_path VARCHAR(500),
    file_format VARCHAR(20), -- 'pdf', 'csv', 'json'
    file_size INT,
    created_by VARCHAR(100),
    is_favorite BOOLEAN DEFAULT FALSE,
    notes TEXT,
    INDEX idx_report_type (report_type),
    INDEX idx_report_date (report_date)
);

-- Create Insights History Table
CREATE TABLE IF NOT EXISTS wfa_insights_history (
    insight_id INT PRIMARY KEY AUTO_INCREMENT,
    insight_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_kpis INT,
    critical_insights INT,
    warning_insights INT,
    info_insights INT,
    insights_data JSON,
    action_taken VARCHAR(500),
    resolved_by VARCHAR(100),
    resolution_date TIMESTAMP NULL,
    INDEX idx_insight_date (insight_date)
);
