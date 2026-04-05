<?php
require_once "auth/database.php";

$database = Database::getInstance();
$db = $database->getConnection();

try {
    $stmt = $db->query("DESCRIBE cm_medicine_inventory");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($results as $row) {
        echo $row['Field'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
