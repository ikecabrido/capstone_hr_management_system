<?php
/**
 * Save Employee Schedule API
 * Updates shift assignments and attendance records
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../../auth/database.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        throw new Exception('Invalid JSON data');
    }

    $employee_id = $data['employee_id'] ?? null;
    $date = $data['date'] ?? null;
    $shifts = $data['shifts'] ?? [];

    if (!$employee_id || !$date) {
        throw new Exception('Employee ID and date are required');
    }

    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Verify employee exists
    $verify_query = "SELECT employee_id FROM employees WHERE employee_id = ? AND employment_status = 'Active'";
    $stmt = $conn->prepare($verify_query);
    $stmt->execute([$employee_id]);
    
    if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception('Employee not found');
    }

    // Start transaction
    $conn->beginTransaction();

    // For this implementation, we're creating custom_shifts table to store day-specific overrides
    // First, check if custom shift entry exists for this date
    $check_query = "SELECT custom_shift_id FROM ta_custom_shifts 
                   WHERE employee_id = ? AND shift_date = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->execute([$employee_id, $date]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Update existing custom shift
        $delete_query = "DELETE FROM ta_custom_shift_times WHERE custom_shift_id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->execute([$existing['custom_shift_id']]);
    } else {
        // Create new custom shift entry
        $insert_query = "INSERT INTO ta_custom_shifts (employee_id, shift_date, created_at, updated_at) 
                        VALUES (?, ?, NOW(), NOW())";
        $stmt = $conn->prepare($insert_query);
        $stmt->execute([$employee_id, $date]);
        $custom_shift_id = $conn->lastInsertId();
    }

    // Get custom shift ID
    $get_id_query = "SELECT custom_shift_id FROM ta_custom_shifts 
                    WHERE employee_id = ? AND shift_date = ?";
    $stmt = $conn->prepare($get_id_query);
    $stmt->execute([$employee_id, $date]);
    $custom_shift_row = $stmt->fetch(PDO::FETCH_ASSOC);
    $custom_shift_id = $custom_shift_row['custom_shift_id'];

    // Insert new shift times (support optional break_start / break_end)
    if (!empty($shifts)) {
        $insert_times_query = "INSERT INTO ta_custom_shift_times (custom_shift_id, start_time, end_time, break_start, break_end) 
                              VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_times_query);

        foreach ($shifts as $shift) {
            $start_time_raw = $shift['start_time'] ?? null;
            $end_time_raw = $shift['end_time'] ?? null;
            $break_start_raw = isset($shift['break_start']) ? $shift['break_start'] : null;
            $break_end_raw = isset($shift['break_end']) ? $shift['break_end'] : null;

            if (!$start_time_raw || !$end_time_raw) {
                $conn->rollBack();
                throw new Exception('Start and end time are required for each shift entry');
            }

            $start_ts = strtotime($start_time_raw);
            $end_ts = strtotime($end_time_raw);
            if ($start_ts === false || $end_ts === false) {
                $conn->rollBack();
                throw new Exception('Invalid time format for start or end time');
            }
            if ($start_ts >= $end_ts) {
                $conn->rollBack();
                throw new Exception('Start time must be before end time');
            }

            // normalize to full datetime
            $start_time = $date . ' ' . date('H:i:s', $start_ts);
            $end_time = $date . ' ' . date('H:i:s', $end_ts);

            $break_start = null;
            $break_end = null;
            if ($break_start_raw || $break_end_raw) {
                if (!$break_start_raw || !$break_end_raw) {
                    $conn->rollBack();
                    throw new Exception('Both break_start and break_end must be provided if specifying a break');
                }
                $bstart_ts = strtotime($break_start_raw);
                $bend_ts = strtotime($break_end_raw);
                if ($bstart_ts === false || $bend_ts === false) {
                    $conn->rollBack();
                    throw new Exception('Invalid break time format');
                }
                if ($bstart_ts < $start_ts || $bend_ts > $end_ts || $bstart_ts >= $bend_ts) {
                    $conn->rollBack();
                    throw new Exception('Break times must be within the work period and break start < break end');
                }
                $break_start = date('H:i:s', $bstart_ts);
                $break_end = date('H:i:s', $bend_ts);
            }

            $stmt->execute([
                $custom_shift_id,
                $start_time,
                $end_time,
                $break_start,
                $break_end
            ]);
        }
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Schedule saved successfully'
    ]);

} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
