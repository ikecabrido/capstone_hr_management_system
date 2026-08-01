<?php
session_start();
require_once "../auth/auth_check.php";

echo "<h1>Running Table Creation Script</h1>";
echo "<pre>";

try {
    require_once "create_tables.php";
    echo "\n✅ Table creation completed successfully!";
} catch (Exception $e) {
    echo "\n❌ Error during table creation: " . $e->getMessage();
}

echo "</pre>";
echo "<hr>";
echo "<p><a href='exit_management.php'>Back to Exit Management</a></p>";
echo "<p><a href='check_tables.php'>Check Tables</a></p>";
?>