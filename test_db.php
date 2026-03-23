<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=hr_management;charset=utf8mb4', 'root', '');
    echo 'Connection successful';
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>