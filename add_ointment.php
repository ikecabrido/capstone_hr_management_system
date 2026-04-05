<?php
require_once "auth/database.php";
require_once "clinic/core/BaseModel.php";
require_once "clinic/models/MedicineInventory.php";

$database = Database::getInstance();
$db = $database->getConnection();

try {
    echo "Adding one ointment dummy data...\n";

    $medicine = [
        'medicine_id' => '17',
        'medicine_name' => 'Hydrocortisone',
        'generic_name' => 'Hytone',
        'category' => 'Topical Steroid',
        'current_stock' => 50,
        'reorder_level' => 10,
        'expiry_date' => '2026-11-20',
        'created_by' => 'Admin'
    ];

    $sql = "INSERT INTO cm_medicine_inventory 
            (medicine_id, medicine_name, generic_name, category, current_stock, reorder_level, expiry_date, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            medicine_name = VALUES(medicine_name),
            generic_name = VALUES(generic_name),
            category = VALUES(category),
            current_stock = VALUES(current_stock),
            reorder_level = VALUES(reorder_level),
            expiry_date = VALUES(expiry_date)";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        $medicine['medicine_id'],
        $medicine['medicine_name'],
        $medicine['generic_name'],
        $medicine['category'],
        $medicine['current_stock'],
        $medicine['reorder_level'],
        $medicine['expiry_date'],
        $medicine['created_by']
    ]);

    echo "Added: {$medicine['medicine_name']} (ID: {$medicine['medicine_id']})\n";

    // Update status
    $inventory = new MedicineInventory($db);
    $inventory->updateStatus($medicine['medicine_id']);
    
    echo "Done! Ointment dummy data added successfully.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
