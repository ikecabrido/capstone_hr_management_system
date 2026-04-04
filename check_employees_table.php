<?php
require_once "auth/database.php";
$db = Database::getInstance()->getConnection();
try {
    $stmt = $db->query("DESCRIBE employees");
    var_dump($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>