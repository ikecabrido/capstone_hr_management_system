<?php
/**
 * Attendance Tracking Helper
 * Ensures both QR and manual time in/out are recorded consistently
 */

class AttendanceTracker
{
    private $conn;
    private $table = 'ta_attendance';

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    /**
     * Record time in/out with method tracking
     * 
     * @param string $employee_id Employee ID
     * @param string $attendance_date Date of attendance (YYYY-MM-DD)
     * @param string $time_in Time in datetime (YYYY-MM-DD HH:MM:SS)
     * @param string|null $time_out Time out datetime (optional)
     * @param string $recorded_by Method: 'MANUAL', 'QR', or 'SYSTEM'
     * @param int|null $shift_id Shift ID
     * 
     * @return array Result array with success status and message
     */
    public function recordAttendance($employee_id, $attendance_date, $time_in, $time_out = null, $recorded_by = 'MANUAL', $shift_id = null)
    {
        try {
            // Validate parameters
            if (empty($employee_id) || empty($attendance_date) || empty($time_in)) {
                return [
                    'success' => false,
                    'message' => 'Missing required parameters',
                    'recorded_by' => $recorded_by
                ];
            }

            // Validate recorded_by value
            if (!in_array($recorded_by, ['MANUAL', 'QR', 'SYSTEM'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid recording method',
                    'recorded_by' => $recorded_by
                ];
            }

            // Check if record exists for this date
            $check_query = "SELECT attendance_id FROM {$this->table} 
                           WHERE employee_id = ? AND attendance_date = ?";
            $check_stmt = $this->conn->prepare($check_query);
            $check_stmt->execute([$employee_id, $attendance_date]);
            $existing = $check_stmt->fetch();

            if ($existing) {
                // Update existing record
                $update_query = "UPDATE {$this->table} 
                               SET time_in = ?,
                                   time_out = ?,
                                   recorded_by = ?,
                                   updated_at = NOW()
                               WHERE employee_id = ? AND attendance_date = ?";
                $update_stmt = $this->conn->prepare($update_query);
                $result = $update_stmt->execute([$time_in, $time_out, $recorded_by, $employee_id, $attendance_date]);

                if ($result) {
                    return [
                        'success' => true,
                        'message' => 'Attendance updated successfully',
                        'recorded_by' => $recorded_by,
                        'attendance_id' => $existing['attendance_id']
                    ];
                }
            } else {
                // Insert new record
                $insert_query = "INSERT INTO {$this->table} 
                                (employee_id, attendance_date, time_in, time_out, recorded_by, shift_id, status)
                                VALUES (?, ?, ?, ?, ?, ?, 'PENDING_APPROVAL')";
                $insert_stmt = $this->conn->prepare($insert_query);
                $result = $insert_stmt->execute([$employee_id, $attendance_date, $time_in, $time_out, $recorded_by, $shift_id]);

                if ($result) {
                    return [
                        'success' => true,
                        'message' => 'Attendance recorded successfully',
                        'recorded_by' => $recorded_by,
                        'attendance_id' => $this->conn->lastInsertId()
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Failed to record attendance',
                'recorded_by' => $recorded_by
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'recorded_by' => $recorded_by
            ];
        }
    }

    /**
     * Get attendance for a specific date range
     * Filter by recording method if needed
     */
    public function getAttendanceRange($employee_id, $start_date, $end_date, $recorded_by = null)
    {
        $query = "SELECT * FROM {$this->table} 
                 WHERE employee_id = ? AND attendance_date BETWEEN ? AND ?";
        
        $params = [$employee_id, $start_date, $end_date];

        if ($recorded_by) {
            $query .= " AND recorded_by = ?";
            $params[] = $recorded_by;
        }

        $query .= " ORDER BY attendance_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get summary of recorded methods for a period
     * Returns count of QR vs MANUAL vs SYSTEM records
     */
    public function getRecordingMethodSummary($employee_id, $month_year)
    {
        $query = "SELECT recorded_by, COUNT(*) as count 
                 FROM {$this->table}
                 WHERE employee_id = ? AND DATE_FORMAT(attendance_date, '%Y-%m') = ?
                 GROUP BY recorded_by";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$employee_id, $month_year]);
        
        $summary = [
            'QR' => 0,
            'MANUAL' => 0,
            'SYSTEM' => 0,
            'total' => 0
        ];

        while ($row = $stmt->fetch()) {
            $summary[$row['recorded_by']] = $row['count'];
            $summary['total'] += $row['count'];
        }

        return $summary;
    }

    /**
     * Verify attendance consistency
     * Check for duplicate QR scans within a short time window
     */
    public function checkDuplicateQRScans($employee_id, $time_in, $window_minutes = 5)
    {
        $window_start = date('Y-m-d H:i:s', strtotime("$time_in -$window_minutes minutes"));
        $window_end = date('Y-m-d H:i:s', strtotime("$time_in +$window_minutes minutes"));

        $query = "SELECT COUNT(*) as duplicate_count FROM {$this->table}
                 WHERE employee_id = ? 
                 AND recorded_by = 'QR'
                 AND time_in BETWEEN ? AND ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$employee_id, $window_start, $window_end]);
        $result = $stmt->fetch();

        return $result['duplicate_count'] > 0;
    }

    /**
     * Get employee's time in/out status for today
     */
    public function getTodayStatus($employee_id)
    {
        $today = date('Y-m-d');
        
        $query = "SELECT * FROM {$this->table}
                 WHERE employee_id = ? AND attendance_date = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$employee_id, $today]);
        $record = $stmt->fetch();

        if (!$record) {
            return [
                'status' => 'NO_RECORD',
                'time_in' => null,
                'time_out' => null,
                'recorded_by' => null,
                'message' => 'No attendance record for today'
            ];
        }

        return [
            'status' => $record['status'],
            'time_in' => $record['time_in'],
            'time_out' => $record['time_out'],
            'recorded_by' => $record['recorded_by'],
            'message' => 'Record found'
        ];
    }

    /**
     * Get comparison: QR vs Manual attendance
     * Useful for analytics and verification
     */
    public function getRecordingMethodComparison($employee_id, $year, $month = null)
    {
        $date_filter = $month ? "DATE_FORMAT(attendance_date, '%Y-%m') = ?" : "YEAR(attendance_date) = ?";
        $params = $month ? [$employee_id, date('Y-m', strtotime("$year-$month-01"))] : [$employee_id, $year];

        $query = "SELECT 
                    recorded_by,
                    COUNT(*) as total,
                    COUNT(DISTINCT DATE(attendance_date)) as unique_days,
                    ROUND(COUNT(*) / (SELECT COUNT(*) FROM {$this->table} WHERE employee_id = ? AND $date_filter) * 100, 2) as percentage
                 FROM {$this->table}
                 WHERE employee_id = ? AND $date_filter
                 GROUP BY recorded_by";

        $stmt = $this->conn->prepare($query);
        $stmt->execute(array_merge([$employee_id], [$params[1]], [$employee_id], [$params[1]]));
        return $stmt->fetchAll();
    }
}
?>
