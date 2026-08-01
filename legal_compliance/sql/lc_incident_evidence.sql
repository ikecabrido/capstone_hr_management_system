-- Evidence file metadata for lc_incidents (run once if table is missing)
CREATE TABLE IF NOT EXISTS lc_incident_evidence (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incident_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(512) NOT NULL,
    file_type VARCHAR(120) DEFAULT NULL,
    file_size INT DEFAULT NULL,
    uploaded_by INT DEFAULT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_lc_incident_evidence_incident (incident_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
