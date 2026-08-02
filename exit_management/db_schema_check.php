<?php
require_once '../auth/database.php';
$db = Database::getInstance()->getConnection();
$tables = ['employees','exit_interviews','exit_resignations','exit_terminations','users','exit_survey_responses','grievances','exit_interview_feedback','exit_interview_hr_assessments'];
foreach ($tables as $table) {
    echo "TABLE $table\n";
    try {
        $stmt = $db->query("SHOW COLUMNS FROM `$table`");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
            echo $col['Field'] . ' ' . $col['Type'] . "\n";
        }
    } catch (Exception $e) {
        echo 'ERROR: ' . $e->getMessage() . "\n";
    }
    echo "\n";
}
