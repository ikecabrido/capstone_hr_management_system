<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=hr_management;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $stmt = $pdo->query('SELECT COUNT(*) as cnt FROM announcements');
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
    echo "announcements: $count\n";

    $stmt = $pdo->query('SELECT COUNT(*) as cnt FROM employees');
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
    echo "employees: $count\n";
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
