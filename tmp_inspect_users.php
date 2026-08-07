<?php
$db = new PDO('mysql:host=localhost;dbname=hr_data;charset=utf8mb4','root','');
$stmt = $db->query('SHOW COLUMNS FROM users');
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $c) {
    echo $c['Field'] . ' ' . $c['Type'] . "\n";
}
