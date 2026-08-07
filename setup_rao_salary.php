<?php
$pdo = new PDO('mysql:host=localhost;dbname=data_hr', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check rao_hired_applicants structure
echo "=== rao_hired_applicants records ===\n";
$stmt = $pdo->query("SELECT * FROM rao_hired_applicants LIMIT 5");
$hired = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($hired as $h) {
    echo "App ID: {$h['application_id']}, Employee ID: {$h['employee_id']}, Position: {$h['position']}\n";
}

// Check rao_offer_salary structure
echo "\n=== rao_offer_salary columns ===\n";
$stmt = $pdo->query("DESC rao_offer_salary");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo "  {$col['Field']} ({$col['Type']})\n";
}

// Check if there are any offers at all
echo "\n=== Existing offers in rao_offer_salary ===\n";
$stmt = $pdo->query("SELECT * FROM rao_offer_salary LIMIT 5");
$offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($offers) > 0) {
    foreach ($offers as $o) {
        echo "App ID: {$o['application_id']}, Status: {$o['offer_status']}, Salary: {$o['salary']}\n";
    }
} else {
    echo "No offers found\n";
}

// Now update existing offers to 'accepted' status
echo "\n=== Setting offers to 'accepted' ===\n";
$result = $pdo->exec("UPDATE rao_offer_salary SET offer_status = 'accepted' WHERE offer_status != 'refused' LIMIT 10");
echo "Updated $result offers\n";

// Verify join works
echo "\n=== Verifying join between rao_hired_applicants and rao_offer_salary ===\n";
$stmt = $pdo->query("
    SELECT rha.employee_id, rha.position, ros.salary, ros.offer_status
    FROM rao_hired_applicants rha
    LEFT JOIN rao_offer_salary ros ON rha.application_id = ros.application_id
    LIMIT 5
");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($results as $r) {
    echo "Employee: {$r['employee_id']}, Pos: {$r['position']}, Salary: {$r['salary']}, Status: {$r['offer_status']}\n";
}
?>
