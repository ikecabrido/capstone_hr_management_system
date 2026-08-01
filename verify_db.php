<?php
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=hr_management;charset=utf8mb4",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "✓ Database connection successful\n\n";
    
    $result = $pdo->query("SHOW TABLES");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tables in hr_management database:\n";
    foreach ($tables as $table) {
        echo "  - " . $table . "\n";
    }
    
    echo "\n✓ Total tables: " . count($tables) . "\n";
    
    // Check users table specifically
    $checkUsers = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $checkUsers->fetchColumn();
    echo "\nUsers in database: " . $userCount . "\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage();
}
?>
