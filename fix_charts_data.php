<?php
require_once "auth/database.php";
$db = Database::getInstance()->getConnection();
$db->exec("UPDATE cm_medical_records SET visit_date = NOW() WHERE visit_date = '0000-00-00 00:00:00'");
echo "Updated medical records with today's date.\n";

// Add some dummy usage logs if empty
$res = $db->query("SELECT COUNT(*) FROM cm_medicine_usage_logs WHERE purpose != 'Stock Addition'");
if ($res->fetchColumn() == 0) {
    echo "Adding dummy usage logs...\n";
    $meds = $db->query("SELECT medicine_id FROM cm_medicine_inventory LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($meds as $med) {
        $log_id = 'LOG' . date('Y') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $qty = mt_rand(1, 10);
        $db->prepare("INSERT INTO cm_medicine_usage_logs (log_id, medicine_id, usage_date, quantity_used, remaining_stock, purpose, used_by) 
                      VALUES (?, ?, CURDATE(), ?, 90, 'Dummy Usage', 'Admin')")
           ->execute([$log_id, $med['medicine_id'], $qty]);
    }
}
