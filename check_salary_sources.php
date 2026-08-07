<?php
require_once 'auth/database.php';
$db = Database::getInstance()->getConnection();

echo "=== CHECK lc_employee_salary FOR EMPLOYEE 8 ===\n";
$stmt = $db->prepare('SELECT * FROM lc_employee_salary WHERE employee_id = ? LIMIT 5');
$stmt->execute(['8']);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!empty($data)) {
    print_r($data[0]);
} else {
    echo "NO DATA\n";
}

echo "\n=== CHECK lc_salary_structures ===\n";
$stmt = $db->query('DESC lc_salary_structures');
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo "- " . $col['Field'] . "\n";
}

echo "\n=== CHECK rao_offer_salary COLUMNS ===\n";
$stmt = $db->query('DESC rao_offer_salary');
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo "- " . $col['Field'] . "\n";
}

echo "\n=== CHECK IF THERE ARE ANY rao_offer_salary RECORDS FOR EMPLOYEE 8 ===\n";
$stmt = $db->prepare('SELECT * FROM rao_offer_salary ros JOIN rao_hired_applicants rha ON ros.application_id = rha.application_id WHERE rha.employee_id = ?');
$stmt->execute(['8']);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Found " . count($data) . " records\n";
if (!empty($data)) {
    print_r($data[0]);
}
?>
