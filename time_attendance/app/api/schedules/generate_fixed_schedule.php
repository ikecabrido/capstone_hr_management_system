<?php
/**
 * Generate Fixed Schedule API
 * Generates per-date custom shifts (ta_custom_shifts + ta_custom_shift_times)
 */
header('Content-Type: application/json');

require_once __DIR__ . '/../../../auth/database.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) throw new Exception('Invalid JSON data');

    $employee_id = $data['employee_id'] ?? null;
    $start_date = $data['start_date'] ?? null;
    $end_date = $data['end_date'] ?? null;
    $days = $data['days'] ?? []; // expected format: [0=>['start'=>'08:00','end'=>'17:00'], 1=>...]

    if (!$employee_id || !$start_date || !$end_date) {
        throw new Exception('employee_id, start_date and end_date are required');
    }

    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Validate employee exists
    $emp_check = $conn->prepare("SELECT employee_id FROM employees WHERE employee_id = ? OR employee_id LIKE ? LIMIT 1");
    $emp_check->execute([$employee_id, $employee_id]);
    if ($emp_check->rowCount() === 0) {
        throw new Exception('Employee not found');
    }

    // Basic validation of dates
    $sd = new DateTime($start_date);
    $ed = new DateTime($end_date);
    if ($ed < $sd) throw new Exception('end_date must be >= start_date');

    $conn->beginTransaction();

    $interval = new DateInterval('P1D');
    $period = new DatePeriod($sd, $interval, $ed->modify('+1 day'));

    $insertCount = 0;

    foreach ($period as $date) {
        $dstr = $date->format('Y-m-d');
        $w = (int)$date->format('w'); // 0=Sunday

        if (!isset($days[$w])) continue; // day not selected

        $cfg = $days[$w];
        $start_time = $cfg['start'] ?? null;
        $end_time = $cfg['end'] ?? null;

        if (!$start_time || !$end_time) {
            $conn->rollBack();
            throw new Exception('Start and end time required for selected weekdays');
        }

        // validate time formats and logical order
        $start_ts = strtotime($start_time);
        $end_ts = strtotime($end_time);
        if ($start_ts === false || $end_ts === false) {
            $conn->rollBack();
            throw new Exception('Invalid start or end time format');
        }
        if ($start_ts >= $end_ts) {
            $conn->rollBack();
            throw new Exception('Start time must be before end time');
        }

        $break_start = isset($cfg['break_start']) ? $cfg['break_start'] : null;
        $break_end = isset($cfg['break_end']) ? $cfg['break_end'] : null;

        if ($break_start || $break_end) {
            if (!$break_start || !$break_end) {
                $conn->rollBack();
                throw new Exception('Both break_start and break_end must be provided if specifying a break');
            }
            $bstart_ts = strtotime($break_start);
            $bend_ts = strtotime($break_end);
            if ($bstart_ts === false || $bend_ts === false) {
                $conn->rollBack();
                throw new Exception('Invalid break time format');
            }
            // ensure break inside work window
            if ($bstart_ts < $start_ts || $bend_ts > $end_ts || $bstart_ts >= $bend_ts) {
                $conn->rollBack();
                throw new Exception('Break times must be within the work period and break start < break end');
            }
            // normalize
            $break_start = date('H:i:s', $bstart_ts);
            $break_end = date('H:i:s', $bend_ts);
        }

        // Upsert ta_custom_shifts
        $check_query = "SELECT custom_shift_id FROM ta_custom_shifts WHERE employee_id = ? AND shift_date = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->execute([$employee_id, $dstr]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $custom_shift_id = $existing['custom_shift_id'];
            // remove previous times
            $del = $conn->prepare("DELETE FROM ta_custom_shift_times WHERE custom_shift_id = ?");
            $del->execute([$custom_shift_id]);
        } else {
            $ins = $conn->prepare("INSERT INTO ta_custom_shifts (employee_id, shift_date, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            $ins->execute([$employee_id, $dstr]);
            $custom_shift_id = $conn->lastInsertId();
        }

        // Insert the time entry (support optional break_start / break_end)
        $insert_time = $conn->prepare("INSERT INTO ta_custom_shift_times (custom_shift_id, start_time, end_time, break_start, break_end) VALUES (?, ?, ?, ?, ?)");
        // store as full datetime to match existing save API
        $start_dt = $dstr . ' ' . date('H:i:s', $start_ts);
        $end_dt = $dstr . ' ' . date('H:i:s', $end_ts);

        $insert_time->execute([$custom_shift_id, $start_dt, $end_dt, $break_start, $break_end]);

        $insertCount++;
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'inserted' => $insertCount,
        'message' => 'Fixed schedule generated'
    ]);

} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) $conn->rollBack();
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
