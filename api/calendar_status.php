<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../auth/database.php';

try {
    $db = Database::getInstance()->getConnection();

    $month = isset($_GET['month']) ? (int) $_GET['month'] : (int) date('m');
    $year  = isset($_GET['year'])  ? (int) $_GET['year']  : (int) date('Y');

    $startDate = sprintf('%04d-%02d-01', $year, $month);
    $endDate   = date('Y-m-t', strtotime($startDate));

    $hasCalendarEvents = false;
    $hasNotifications  = false;
    $unreadCount       = 0;

    $queries = [
        "SELECT 1 FROM compliance_items WHERE due_date IS NOT NULL AND due_date >= :start AND due_date <= :end LIMIT 1",
        "SELECT 1 FROM compliance_tasks WHERE deadline IS NOT NULL AND deadline >= :start AND deadline <= :end LIMIT 1",
        "SELECT 1 FROM incidents WHERE incident_date IS NOT NULL AND incident_date >= :start AND incident_date <= :end LIMIT 1",
        "SELECT 1 FROM notice_to_explain WHERE deadline_date IS NOT NULL AND deadline_date >= :start AND deadline_date <= :end LIMIT 1",
        "SELECT 1 FROM disciplinary_actions WHERE effective_date IS NOT NULL AND effective_date >= :start AND effective_date <= :end LIMIT 1"
    ];

    foreach ($queries as $sql) {
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([':start' => $startDate, ':end' => $endDate]);
            if ($stmt->fetchColumn()) {
                $hasCalendarEvents = true;
                break;
            }
        } catch (Exception $e) {
        }
    }

    try {
        $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE is_read = 0 AND type IN ('reminder', 'alert')");
        $stmt->execute();
        $unreadCount = (int) $stmt->fetchColumn();
        $hasNotifications = $unreadCount > 0;
    } catch (Exception $e) {
    }

    echo json_encode([
        'success'            => true,
        'has_calendar_events' => $hasCalendarEvents,
        'has_notifications'  => $hasNotifications,
        'unread_count'       => $unreadCount,
        'show_dot'           => $hasCalendarEvents || $hasNotifications
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success'  => false,
        'message'  => 'Database error: ' . $e->getMessage(),
        'show_dot' => false
    ]);
}
