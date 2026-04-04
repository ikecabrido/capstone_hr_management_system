<?php
require_once 'auth/database.php';
try {
    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->query('SHOW COLUMNS FROM eer_rewards');
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Columns in eer_rewards:\n";
    foreach ($cols as $col) {
        echo $col['Field'] . ' ' . $col['Type'] . ' ' . ($col['Null'] === 'NO' ? 'NOT NULL' : 'NULL') . '\n';
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>