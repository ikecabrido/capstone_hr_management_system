<?php
require_once "auth/database.php";
$db = Database::getInstance()->getConnection();
$stmt = $db->query("DESCRIBE users");
var_dump($stmt->fetchAll(PDO::FETCH_ASSOC));
?>