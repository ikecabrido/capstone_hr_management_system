<?php
require_once __DIR__ . '/../models/MedicalRecord.php';

class MedicalRecordReportService {
    private $db;
    private $medical_record;

    public function __construct($db) {
        $this->db = $db;
        $this->medical_record = new MedicalRecord($this->db);
    }

    public function generateReport($start_date, $end_date) {
        return $this->medical_record->getMedicalRecordsByDateRange($start_date, $end_date);
    }
}
?>