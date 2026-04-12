<?php
require_once __DIR__ . '/modules/config.php';

try {
    // Update training programs
    $pdo->exec("UPDATE training_programs SET cover_photo = 'img/placeholder.gif' WHERE cover_photo IS NULL OR cover_photo = ''");
    echo "Updated training programs\n";
    
    // Update career paths
    $pdo->exec("UPDATE career_paths SET cover_photo = 'img/placeholder.gif' WHERE cover_photo IS NULL OR cover_photo = ''");
    echo "Updated career paths\n";
    
    // Update leadership programs
    $pdo->exec("UPDATE leadership_programs SET cover_photo = 'img/placeholder.gif' WHERE cover_photo IS NULL OR cover_photo = ''");
    echo "Updated leadership programs\n";
    
    // Update team activities
    $pdo->exec("UPDATE team_activities SET cover_photo = 'img/placeholder.gif' WHERE cover_photo IS NULL OR cover_photo = ''");
    echo "Updated team activities\n";
    
    echo "\nAll cover photos updated successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
