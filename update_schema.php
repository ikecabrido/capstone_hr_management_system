<?php
require_once "auth/database.php";

$database = Database::getInstance();
$db = $database->getConnection();

if ($db === null) {
    die("Database connection failed.");
}

try {
    echo "Updating schema...\n";

    // Add avatar column to cm_patients if it doesn't exist
    $stmt = $db->query("SHOW COLUMNS FROM cm_patients");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('avatar', $columns)) {
        $db->exec("ALTER TABLE cm_patients ADD COLUMN avatar VARCHAR(255) DEFAULT 'avatar.png' AFTER last_name");
        echo "Added 'avatar' column to 'cm_patients'.\n";
    } else {
        echo "'avatar' column already exists in 'cm_patients'.\n";
    }

    echo "Schema update completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
