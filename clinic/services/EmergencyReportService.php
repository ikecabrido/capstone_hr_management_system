<?php
require_once __DIR__ . '/../models/EmergencyCase.php';

class EmergencyReportService {
    private $db;
    private $emergency_case;

    public function __construct($db) {
        $this->db = $db;
        $this->emergency_case = new EmergencyCase($this->db);
    }

    public function generateReport($start_date, $end_date) {
        return $this->emergency_case->getEmergencyCasesByDateRange($start_date, $end_date);
    }
}
?>