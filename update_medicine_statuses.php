<?php
require_once "auth/database.php";
require_once "clinic/core/BaseModel.php";
require_once "clinic/models/MedicineInventory.php";

$database = Database::getInstance();
$db = $database->getConnection();

try {
    $medicine = new MedicineInventory($db);
    $medicine->updateAllStatuses();
    echo "All medicine statuses updated successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
