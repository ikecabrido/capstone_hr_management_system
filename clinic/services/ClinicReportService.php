<?php
/**
 * ClinicReportService Class
 * 
 * Service class for generating clinic reports and statistics
 */

require_once __DIR__ . "/../core/BaseModel.php";

class ClinicReportService {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Get comprehensive statistics for all modules
     * @return array
     */
    public function getComprehensiveStats() {
        $stats = [];
        
        try {
            // Patient statistics
            $sql = "SELECT COUNT(*) as total_patients FROM cm_patients WHERE status = 'Active'";
            $result = $this->db->query($sql);
            $stats['total_patients'] = $result->fetchColumn() ?? 0;
            
            // Total visits
            $sql = "SELECT COUNT(*) as total_visits FROM cm_medical_records";
            $result = $this->db->query($sql);
            $stats['total_visits'] = $result->fetchColumn() ?? 0;
            
            // Total medicines
            $sql = "SELECT COUNT(*) as total_medicines FROM cm_medicine_inventory";
            $result = $this->db->query($sql);
            $stats['total_medicines'] = $result->fetchColumn() ?? 0;
            
            // Total emergencies
            $sql = "SELECT COUNT(*) as total_emergencies FROM cm_emergency_cases";
            $result = $this->db->query($sql);
            $stats['total_emergencies'] = $result->fetchColumn() ?? 0;
            
            // Today's visits
            $sql = "SELECT COUNT(*) as today_visits FROM cm_medical_records WHERE DATE(visit_date) = CURDATE()";
            $result = $this->db->query($sql);
            $stats['today_visits'] = $result->fetchColumn() ?? 0;
            
            // Most common diagnosis
            $sql = "SELECT diagnosis, COUNT(*) as count FROM cm_medical_records 
                    WHERE diagnosis IS NOT NULL AND diagnosis != ''
                    GROUP BY diagnosis ORDER BY count DESC LIMIT 1";
            $result = $this->db->query($sql);
            $stats['most_common_diagnosis'] = $result->fetchColumn(0) ?? 'N/A';
            
            // Medicine usage
            $sql = "SELECT SUM(quantity_used) as total_usage FROM cm_medicine_usage_logs 
                    WHERE usage_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                    AND purpose != 'Stock Addition'";
            $result = $this->db->query($sql);
            $stats['medicine_usage'] = $result->fetchColumn() ?? 0;
            
            // Emergency response time
            $sql = "SELECT AVG(TIME_TO_SEC(TIMEDIFF(ambulance_arrival_time, incident_date))/60) as avg_time
                    FROM cm_emergency_cases 
                    WHERE ambulance_arrival_time IS NOT NULL";
            $result = $this->db->query($sql);
            $stats['emergency_response_time'] = round($result->fetchColumn() ?? 0, 2);
            
        } catch (Exception $e) {
            error_log("Error getting comprehensive stats: " . $e->getMessage());
        }
        
        return $stats;
    }
    
    /**
     * Get medicine usage breakdown for a period
     * @param string $start_date
     * @param string $end_date
     * @return array
     */
    private function getMedicineUsageBreakdown($start_date, $end_date) {
        try {
            $sql = "SELECT mi.medicine_name, SUM(mul.quantity_used) as total_used 
                    FROM cm_medicine_usage_logs mul
                    JOIN cm_medicine_inventory mi ON mul.medicine_id = mi.medicine_id
                    WHERE DATE(mul.usage_date) BETWEEN ? AND ?
                    AND mul.purpose != 'Stock Addition'
                    GROUP BY mi.medicine_id
                    ORDER BY total_used DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$start_date, $end_date]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting medicine usage breakdown: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get visit details for a specific period
     * @param string $start_date
     * @param string $end_date
     * @return array
     */
    private function getVisitDetails($start_date, $end_date) {
        try {
            $sql = "SELECT mr.visit_date, p.first_name, p.last_name, mr.chief_complaint, mr.diagnosis
                    FROM cm_medical_records mr
                    INNER JOIN cm_patients p ON mr.patient_id = p.patient_id
                    WHERE DATE(mr.visit_date) BETWEEN ? AND ?
                    ORDER BY mr.visit_date ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$start_date, $end_date]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting visit details: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate daily report
     * @param string $date - Date for the report
     * @return array
     */
    public function generateDailyReport($date) {
        $report = [
            'date' => $date,
            'summary' => [],
            'details' => [],
            'trends' => []
        ];
        
        try {
            // Daily visits
            $sql = "SELECT COUNT(*) as visits FROM cm_medical_records WHERE DATE(visit_date) = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$date]);
            $report['summary']['total_visits'] = $stmt->fetchColumn();
            
            // Emergency cases
            $sql = "SELECT COUNT(*) as emergencies FROM cm_emergency_cases WHERE DATE(incident_date) = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$date]);
            $report['summary']['emergency_cases'] = $stmt->fetchColumn();
            
            // Medicine usage
            $sql = "SELECT COUNT(*) as usage FROM cm_medicine_usage_logs WHERE DATE(usage_date) = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$date]);
            $report['summary']['medicine_usage'] = $stmt->fetchColumn();
            
            // Visit details
            $report['details']['visits'] = $this->getVisitDetails($date, $date);

            // Medicine usage
            $report['trends']['medicine_usage'] = $this->getMedicineUsageBreakdown($date, $date);
            
        } catch (Exception $e) {
            error_log("Error generating daily report: " . $e->getMessage());
        }
        
        return $report;
    }

    /**
     * Generate weekly report
     * @param string $start_date - Start date
     * @param string $end_date - End date
     * @return array
     */
    public function generateWeeklyReport($start_date, $end_date) {
        // If end_date is missing, set to start_date + 6 days
        if (empty($end_date)) {
            $end_date = date('Y-m-d', strtotime($start_date . ' + 6 days'));
        }
        
        $report = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'summary' => [],
            'trends' => [],
            'details' => []
        ];
        
        try {
            // Weekly visits
            $sql = "SELECT COUNT(*) as visits FROM cm_medical_records 
                    WHERE DATE(visit_date) BETWEEN ? AND ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$start_date, $end_date]);
            $report['summary']['total_visits'] = $stmt->fetchColumn();
            
            // Daily visit breakdown
            $sql = "SELECT DATE(visit_date) as visit_date, COUNT(*) as visits
                    FROM cm_medical_records 
                    WHERE DATE(visit_date) BETWEEN ? AND ?
                    GROUP BY DATE(visit_date)
                    ORDER BY visit_date";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$start_date, $end_date]);
            $report['trends']['daily_visits'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Common diagnoses
            $sql = "SELECT diagnosis, COUNT(*) as count
                    FROM cm_medical_records 
                    WHERE DATE(visit_date) BETWEEN ? AND ? 
                    AND diagnosis IS NOT NULL AND diagnosis != ''
                    GROUP BY diagnosis
                    ORDER BY count DESC
                    LIMIT 10";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$start_date, $end_date]);
            $report['trends']['common_diagnoses'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Visit details
             $report['details']['visits'] = $this->getVisitDetails($start_date, $end_date);

             // Medicine usage
             $report['trends']['medicine_usage'] = $this->getMedicineUsageBreakdown($start_date, $end_date);

             // Medicine usage
             $report['trends']['medicine_usage'] = $this->getMedicineUsageBreakdown($start_date, $end_date);
            
        } catch (Exception $e) {
            error_log("Error generating weekly report: " . $e->getMessage());
        }
        
        return $report;
    }

    /**
     * Generate monthly report
     * @param string $month - Month and year (YYYY-MM or YYYY-MM-DD)
     * @return array
     */
    public function generateMonthlyReport($month) {
        // Handle YYYY-MM-DD or YYYY-MM
        $start_date = date('Y-m-01', strtotime($month));
        $end_date = date('Y-m-t', strtotime($month));
        $month_formatted = date('Y-m', strtotime($month));
        
        $report = [
            'month' => $month_formatted,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'summary' => [],
            'analytics' => [],
            'details' => []
        ];
        
        try {
            // Monthly visits
            $sql = "SELECT COUNT(*) as visits FROM cm_medical_records 
                    WHERE DATE_FORMAT(visit_date, '%Y-%m') = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$month_formatted]);
            $report['summary']['total_visits'] = $stmt->fetchColumn();
            
            // Patient demographics
            $sql = "SELECT p.patient_type, COUNT(*) as count
                    FROM cm_medical_records mr
                    INNER JOIN cm_patients p ON mr.patient_id = p.patient_id
                    WHERE DATE_FORMAT(mr.visit_date, '%Y-%m') = ?
                    GROUP BY p.patient_type";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$month_formatted]);
            $report['analytics']['patient_demographics'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Department-wise visits
            $sql = "SELECT p.department, COUNT(*) as visits
                    FROM cm_medical_records mr
                    INNER JOIN cm_patients p ON mr.patient_id = p.patient_id
                    WHERE DATE_FORMAT(mr.visit_date, '%Y-%m') = ?
                    GROUP BY p.department
                    ORDER BY visits DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$month_formatted]);
            $report['analytics']['department_visits'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Visit details
            $report['details']['visits'] = $this->getVisitDetails($start_date, $end_date);

            // Medicine usage
            if (!isset($report['trends'])) $report['trends'] = [];
            $report['trends']['medicine_usage'] = $this->getMedicineUsageBreakdown($start_date, $end_date);
            
        } catch (Exception $e) {
            error_log("Error generating monthly report: " . $e->getMessage());
        }
        
        return $report;
    }

    /**
     * Generate custom report
     * @param string $start_date - Start date
     * @param string $end_date - End date
     * @return array
     */
    public function generateCustomReport($start_date, $end_date) {
        // If end_date is missing, set to today
        if (empty($end_date)) {
            $end_date = date('Y-m-d');
        }
        
        $report = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'summary' => [],
            'details' => []
        ];
        
        try {
            // All statistics for the period
            $sql = "SELECT 
                    (SELECT COUNT(*) FROM cm_medical_records WHERE DATE(visit_date) BETWEEN ? AND ?) as total_visits,
                    (SELECT COUNT(*) FROM cm_emergency_cases WHERE DATE(incident_date) BETWEEN ? AND ?) as total_emergencies,
                    (SELECT COUNT(*) FROM cm_patients WHERE created_at BETWEEN ? AND ?) as new_patients,
                    (SELECT COUNT(*) FROM cm_medicine_usage_logs WHERE DATE(usage_date) BETWEEN ? AND ?) as medicine_usage";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$start_date, $end_date, $start_date, $end_date, $start_date, $end_date, $start_date, $end_date]);
            $report['summary'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Daily breakdown
            $sql = "SELECT DATE(visit_date) as date, COUNT(*) as visits
                    FROM cm_medical_records 
                    WHERE DATE(visit_date) BETWEEN ? AND ?
                    GROUP BY DATE(visit_date)
                    ORDER BY date";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$start_date, $end_date]);
            $report['daily_visits'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Visit details
            $report['details']['visits'] = $this->getVisitDetails($start_date, $end_date);

            // Medicine usage
            if (!isset($report['trends'])) $report['trends'] = [];
            $report['trends']['medicine_usage'] = $this->getMedicineUsageBreakdown($start_date, $end_date);
            
        } catch (Exception $e) {
            error_log("Error generating custom report: " . $e->getMessage());
        }
        
        return $report;
    }

    /**
     * Generate annual report
     * @param string $year - Year (YYYY or YYYY-MM-DD)
     * @return array
     */
    public function generateAnnualReport($year) {
        // Extract year if full date provided
        $year_val = date('Y', strtotime($year));
        $start_date = $year_val . '-01-01';
        $end_date = $year_val . '-12-31';
        
        $report = [
            'year' => $year_val,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'summary' => [],
            'monthly_stats' => [],
            'details' => []
        ];
        
        try {
            // Annual total visits
            $sql = "SELECT COUNT(*) FROM cm_medical_records WHERE YEAR(visit_date) = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$year_val]);
            $report['summary']['total_visits'] = $stmt->fetchColumn();
            
            // Monthly breakdown
            $sql = "SELECT MONTH(visit_date) as month, COUNT(*) as visits
                    FROM cm_medical_records 
                    WHERE YEAR(visit_date) = ?
                    GROUP BY MONTH(visit_date)
                    ORDER BY month";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$year_val]);
            $report['monthly_stats'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Visit details (Limit for annual reports to avoid massive files)
             $sql = "SELECT mr.visit_date, p.first_name, p.last_name, mr.chief_complaint, mr.diagnosis
                     FROM cm_medical_records mr
                     INNER JOIN cm_patients p ON mr.patient_id = p.patient_id
                     WHERE YEAR(mr.visit_date) = ?
                     ORDER BY mr.visit_date ASC
                     LIMIT 1000";
             $stmt = $this->db->prepare($sql);
             $stmt->execute([$year_val]);
             $report['details']['visits'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

             // Medicine usage
             if (!isset($report['trends'])) $report['trends'] = [];
             $report['trends']['medicine_usage'] = $this->getMedicineUsageBreakdown($start_date, $end_date);
            
        } catch (Exception $e) {
            error_log("Error generating annual report: " . $e->getMessage());
        }
        
        return $report;
    }
    
    /**
     * Save report to database
     * @param array $report_data - Report data
     * @return bool
     */
    public function saveReport($report_data) {
        try {
            $sql = "INSERT INTO cm_clinic_reports 
                    (report_id, report_type, report_date, start_date, end_date, report_data, generated_by, status, file_format)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $report_data['report_id'],
                $report_data['report_type'],
                $report_data['report_date'],
                $report_data['start_date'],
                $report_data['end_date'],
                $report_data['report_data'],
                $report_data['generated_by'],
                $report_data['status'],
                $report_data['file_format']
            ]);
        } catch (Exception $e) {
            error_log("Error saving report: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get saved reports
     * @param int $limit - Limit number of reports
     * @return array
     */
    public function getSavedReports($limit = 20) {
        try {
            $sql = "SELECT * FROM cm_clinic_reports 
                    ORDER BY created_at DESC 
                    LIMIT :limit";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting saved reports: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Delete a saved report
     * @param string $report_id - Report ID
     * @return bool
     */
    public function deleteReport($report_id) {
        try {
            $sql = "DELETE FROM cm_clinic_reports WHERE report_id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$report_id]);
        } catch (Exception $e) {
            error_log("Error deleting report: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get a single report by ID
     * @param string $report_id - Report ID
     * @return array|null
     */
    public function getReport($report_id) {
        try {
            $sql = "SELECT * FROM cm_clinic_reports WHERE report_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$report_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting report: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Export report to different formats
     * @param string $report_id - Report ID
     * @param string $format - Export format (PDF, Excel, HTML, JSON)
     * @return bool
     */
    public function exportReport($report_id, $format) {
        try {
            // Get report data
            $sql = "SELECT * FROM cm_clinic_reports WHERE report_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$report_id]);
            $report = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$report) {
                return false;
            }
            
            // Update file format
            $update_sql = "UPDATE cm_clinic_reports SET file_format = ? WHERE report_id = ?";
            $update_stmt = $this->db->prepare($update_sql);
            $update_stmt->execute([$format, $report_id]);
            
            // In a real implementation, you would generate the actual file here
            // For now, we'll just return true to indicate the export was "successful"
            return true;
            
        } catch (Exception $e) {
            error_log("Error exporting report: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get total number of generated reports
     * @return int
     */
    public function getTotalReportsCount() {
        try {
            $sql = "SELECT COUNT(*) FROM cm_clinic_reports WHERE status = 'Generated'";
            $result = $this->db->query($sql);
            return (int)($result->fetchColumn() ?? 0);
        } catch (Exception $e) {
            error_log("Error counting reports: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get top illness cases (diagnoses)
     * @param int $limit
     * @return array
     */
    public function getTopIllnesses($limit = 5) {
        try {
            $sql = "SELECT diagnosis as illness, COUNT(*) as count 
                    FROM cm_medical_records 
                    WHERE diagnosis IS NOT NULL AND diagnosis != ''
                    GROUP BY diagnosis 
                    ORDER BY count DESC 
                    LIMIT :limit";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting top illnesses: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get medicine usage stats for chart
     * @param int $limit
     * @return array
     */
    public function getMedicineUsageStats($limit = 5) {
        try {
            $sql = "SELECT mi.medicine_name, SUM(mul.quantity_used) as total_used 
                    FROM cm_medicine_usage_logs mul
                    JOIN cm_medicine_inventory mi ON mul.medicine_id = mi.medicine_id
                    WHERE mul.purpose != 'Stock Addition'
                    GROUP BY mi.medicine_id
                    ORDER BY total_used DESC
                    LIMIT :limit";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting medicine usage stats: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get visit trends logic improved
     * @param int $days
     * @return array
     */
    public function getVisitTrends($days = 7) {
        try {
            $sql = "SELECT DATE(visit_date) as date, COUNT(*) as visits
                    FROM cm_medical_records 
                    WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                    GROUP BY DATE(visit_date)
                    ORDER BY date ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':days', (int)$days, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting visit trends: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get health trends
     * @param int $days - Number of days to analyze
     * @return array
     */
    public function getHealthTrends($days = 30) {
        try {
            // Most common diagnoses
            $sql = "SELECT diagnosis, COUNT(*) as count
                    FROM cm_medical_records 
                    WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                    AND diagnosis IS NOT NULL AND diagnosis != ''
                    GROUP BY diagnosis
                    ORDER BY count DESC
                    LIMIT 10";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':days', (int)$days, PDO::PARAM_INT);
            $stmt->execute();
            $trends['common_diagnoses'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Patient types
            $sql = "SELECT p.patient_type, COUNT(*) as count
                    FROM cm_medical_records mr
                    INNER JOIN cm_patients p ON mr.patient_id = p.patient_id
                    WHERE mr.visit_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                    GROUP BY p.patient_type";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':days', (int)$days, PDO::PARAM_INT);
            $stmt->execute();
            $trends['patient_types'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $trends;
            
        } catch (Exception $e) {
            error_log("Error getting health trends: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get medicine usage trends
     * @param int $days - Number of days to analyze
     * @return array
     */
    public function getMedicineUsageTrends($days = 30) {
        try {
            $sql = "SELECT mi.medicine_name, SUM(mul.quantity_used) as total_usage
                    FROM cm_medicine_usage_logs mul
                    INNER JOIN cm_medicine_inventory mi ON mul.medicine_id = mi.medicine_id
                    WHERE mul.usage_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                    AND mul.purpose != 'Stock Addition'
                    GROUP BY mi.medicine_id, mi.medicine_name
                    ORDER BY total_usage DESC
                    LIMIT 10";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':days', (int)$days, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting medicine usage trends: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get emergency response statistics
     * @param int $days - Number of days to analyze
     * @return array
     */
    public function getEmergencyResponseStats($days = 30) {
        try {
            $sql = "SELECT 
                    COUNT(*) as total_cases,
                    AVG(TIME_TO_SEC(TIMEDIFF(ambulance_arrival_time, incident_date))/60) as avg_response_time,
                    COUNT(CASE WHEN ambulance_called = 1 THEN 1 END) as ambulance_calls
                    FROM cm_emergency_cases 
                    WHERE incident_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':days', (int)$days, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting emergency response stats: " . $e->getMessage());
            return [];
        }
    }
}
