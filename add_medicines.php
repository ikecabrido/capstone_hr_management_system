<?php
require_once "auth/database.php";

$database = Database::getInstance();
$db = $database->getConnection();

try {
    // 1. Update the database schema to support 'Unavailable' status and update the trigger
    echo "Updating database schema...\n";
    
    // Check current status ENUM values
    $stmt = $db->query("SHOW COLUMNS FROM cm_medicine_inventory LIKE 'status'");
    $column = $stmt->fetch(PDO::FETCH_ASSOC);
    $type = $column['Type']; // enum('Available','Low Stock','Out of Stock','Expired')
    
    if (strpos($type, 'Unavailable') === false) {
        $db->exec("ALTER TABLE cm_medicine_inventory MODIFY COLUMN status ENUM('Available', 'Low Stock', 'Out of Stock', 'Unavailable', 'Expired') DEFAULT 'Available'");
        echo "Status ENUM updated.\n";
    }

    // Update the trigger if it exists
    $db->exec("DROP TRIGGER IF EXISTS tr_medicine_stock_update");
    $db->exec("
        CREATE TRIGGER tr_medicine_stock_update 
        BEFORE UPDATE ON cm_medicine_inventory
        FOR EACH ROW
        BEGIN
            IF NEW.expiry_date < CURDATE() THEN
                SET NEW.status = 'Unavailable';
            ELSEIF NEW.current_stock <= 0 THEN
                SET NEW.status = 'Out of Stock';
            ELSEIF NEW.current_stock <= NEW.reorder_level THEN
                SET NEW.status = 'Low Stock';
            ELSE
                SET NEW.status = 'Available';
            END IF;
        END
    ");
    echo "Trigger updated.\n";

    // 2. Add sample medicines
    $medicines = [
        [
            'medicine_id' => '1',
            'medicine_name' => 'Mefenamic Acid',
            'generic_name' => 'Ponstan',
            'category' => 'Analgesic',
            'dosage_form' => 'Capsule',
            'strength' => '500mg',
            'current_stock' => 100,
            'reorder_level' => 20,
            'unit_cost' => 5.50,
            'expiry_date' => '2026-12-31',
            'created_by' => 'Admin'
        ],
        [
            'medicine_id' => '2',
            'medicine_name' => 'Cetirizine',
            'generic_name' => 'Zyrtec',
            'category' => 'Antihistamine',
            'dosage_form' => 'Tablet',
            'strength' => '10mg',
            'current_stock' => 150,
            'reorder_level' => 30,
            'unit_cost' => 8.00,
            'expiry_date' => '2026-11-15',
            'created_by' => 'Admin'
        ],
        [
            'medicine_id' => '3',
            'medicine_name' => 'Aluminum Hydroxide',
            'generic_name' => 'Maalox',
            'category' => 'Antacid',
            'dosage_form' => 'Liquid',
            'strength' => '200mg/5ml',
            'current_stock' => 50,
            'reorder_level' => 10,
            'unit_cost' => 120.00,
            'expiry_date' => '2026-08-20',
            'created_by' => 'Admin'
        ],
        [
            'medicine_id' => '4',
            'medicine_name' => 'Ascorbic Acid',
            'generic_name' => 'Vitamin C',
            'category' => 'Vitamin',
            'dosage_form' => 'Tablet',
            'strength' => '500mg',
            'current_stock' => 500,
            'reorder_level' => 100,
            'unit_cost' => 3.50,
            'expiry_date' => '2027-01-10',
            'created_by' => 'Admin'
        ],
        [
            'medicine_id' => '5',
            'medicine_name' => 'Betadine',
            'generic_name' => 'Povidone-Iodine',
            'category' => 'Ointment',
            'dosage_form' => 'Liquid',
            'strength' => '10%',
            'current_stock' => 25,
            'reorder_level' => 5,
            'unit_cost' => 45.00,
            'expiry_date' => '2025-05-15',
            'created_by' => 'Admin'
        ]
    ];

    $insert_sql = "INSERT INTO cm_medicine_inventory (
        medicine_id, medicine_name, generic_name, category, dosage_form, 
        strength, current_stock, reorder_level, unit_cost, expiry_date, created_by, status
    ) VALUES (
        :medicine_id, :medicine_name, :generic_name, :category, :dosage_form, 
        :strength, :current_stock, :reorder_level, :unit_cost, :expiry_date, :created_by, :status
    ) ON DUPLICATE KEY UPDATE 
        current_stock = VALUES(current_stock), 
        expiry_date = VALUES(expiry_date),
        status = VALUES(status)";

    $stmt = $db->prepare($insert_sql);

    foreach ($medicines as $med) {
        // Calculate status manually for initial insert
        $status = 'Available';
        $today = date('Y-m-d');
        if ($med['expiry_date'] < $today) {
            $status = 'Unavailable';
        } elseif ($med['current_stock'] <= 0) {
            $status = 'Out of Stock';
        } elseif ($med['current_stock'] <= $med['reorder_level']) {
            $status = 'Low Stock';
        }
        
        $med['status'] = $status;
        $stmt->execute($med);
        echo "Added/Updated medicine: {$med['medicine_name']}\n";
    }

    echo "Done!\n";

} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
