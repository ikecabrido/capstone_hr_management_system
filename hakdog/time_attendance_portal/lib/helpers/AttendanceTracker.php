<?php
/**
 * Attendance Tracking Helper
 * Ensures both QR and manual time in/out are recorded consistently
 * This is the main helper for dual-tracking system
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
                // IMPORTANT: Reset status to PENDING_APPROVAL when employee updates their time
                // Reset is_approved and approved_by so admin must re-approve the new times
                $update_query = "UPDATE {$this->table} 
                               SET time_in = ?,
                                   time_out = ?,
                                   recorded_by = ?,
                                   shift_id = ?,
                                   status = 'PENDING_APPROVAL',
                                   is_approved = 0,
                                   approved_by = NULL,
                                   updated_at = NOW()
                               WHERE employee_id = ? AND attendance_date = ?";
                $update_stmt = $this->conn->prepare($update_query);
                $result = $update_stmt->execute([$time_in, $time_out, $recorded_by, $shift_id, $employee_id, $attendance_date]);

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
     * Get attendance records within a date range with optional method filter
     */
    public function getAttendanceRange($employee_id, $start_date, $end_date, $recorded_by = null)
    {
        $query = "SELECT * FROM {$this->table} 
                 WHERE employee_id = ? AND attendance_date BETWEEN ? AND ?";
        
        if ($recorded_by) {
            $query .= " AND recorded_by = ?";
        }
        
        $query .= " ORDER BY attendance_date DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($recorded_by) {
            $stmt->execute([$employee_id, $start_date, $end_date, $recorded_by]);
        } else {
            $stmt->execute([$employee_id, $start_date, $end_date]);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get summary of recording methods (QR vs MANUAL)
     */
    public function getRecordingMethodSummary($employee_id, $start_date = null, $end_date = null)
    {
        $query = "SELECT recorded_by, COUNT(*) as count FROM {$this->table} 
                 WHERE employee_id = ?";
        
        if ($start_date && $end_date) {
            $query .= " AND attendance_date BETWEEN ? AND ?";
        }
        
        $query .= " GROUP BY recorded_by";
        
        $stmt = $this->conn->prepare($query);
        
        if ($start_date && $end_date) {
            $stmt->execute([$employee_id, $start_date, $end_date]);
        } else {
            $stmt->execute([$employee_id]);
        }
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $summary = ['QR' => 0, 'MANUAL' => 0, 'SYSTEM' => 0, 'total' => 0];
        
        foreach ($results as $row) {
            $summary[$row['recorded_by']] = $row['count'];
            $summary['total'] += $row['count'];
        }
        
        return $summary;
    }

    /**
     * Check for duplicate QR scans (within 5-minute window)
     */
    public function checkDuplicateQRScans($employee_id, $time_in)
    {
        $query = "SELECT attendance_id FROM {$this->table} 
                 WHERE employee_id = ? 
                 AND recorded_by = 'QR' 
                 AND ABS(TIMESTAMPDIFF(MINUTE, time_in, ?)) < 5 
                 AND attendance_date = CURDATE()";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$employee_id, $time_in]);
        
        return $stmt->fetch() ? true : false;
    }

    /**
     * Get today's status (whether time in, time out recorded)
     */
    public function getTodayStatus($employee_id)
    {
        $query = "SELECT time_in, time_out, recorded_by, attendance_date FROM {$this->table} 
                 WHERE employee_id = ? AND attendance_date = CURDATE()
                 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$employee_id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get comparison of QR vs MANUAL recordings with percentages
     */
    public function getRecordingMethodComparison($employee_id, $start_date = null, $end_date = null)
    {
        $summary = $this->getRecordingMethodSummary($employee_id, $start_date, $end_date);
        
        if ($summary['total'] == 0) {
            return [
                'QR' => ['count' => 0, 'percentage' => 0],
                'MANUAL' => ['count' => 0, 'percentage' => 0],
                'SYSTEM' => ['count' => 0, 'percentage' => 0],
                'total' => 0
            ];
        }
        
        return [
            'QR' => [
                'count' => $summary['QR'],
                'percentage' => round(($summary['QR'] / $summary['total']) * 100, 2)
            ],
            'MANUAL' => [
                'count' => $summary['MANUAL'],
                'percentage' => round(($summary['MANUAL'] / $summary['total']) * 100, 2)
            ],
            'SYSTEM' => [
                'count' => $summary['SYSTEM'],
                'percentage' => round(($summary['SYSTEM'] / $summary['total']) * 100, 2)
            ],
            'total' => $summary['total']
        ];
    }
}
