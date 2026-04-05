<?php
require_once "auth/database.php";
$db = Database::getInstance()->getConnection();

echo "Medical Records count: ";
$res = $db->query("SELECT COUNT(*) FROM cm_medical_records");
echo $res->fetchColumn() . "\n";

echo "Usage Logs count: ";
$res = $db->query("SELECT COUNT(*) FROM cm_medicine_usage_logs");
echo $res->fetchColumn() . "\n";

echo "Usage Logs for charts: ";
$res = $db->query("SELECT COUNT(*) FROM cm_medicine_usage_logs WHERE purpose != 'Stock Addition'");
echo $res->fetchColumn() . "\n";

echo "Recent Medical Records:\n";
$res = $db->query("SELECT visit_date FROM cm_medical_records ORDER BY visit_date DESC LIMIT 5");
print_r($res->fetchAll(PDO::FETCH_ASSOC));

echo "Recent Usage Logs:\n";
$res = $db->query("SELECT * FROM cm_medicine_usage_logs ORDER BY usage_date DESC LIMIT 5");
print_r($res->fetchAll(PDO::FETCH_ASSOC));
