<?php
chdir(__DIR__); // Change to the script directory
require_once 'learning_development/controllers/TrainingProgramController.php';

try {
    $controller = new TrainingProgramController();
    $programs = $controller->index();

    echo "Training Programs Test:\n";
    echo "Found " . count($programs) . " programs\n";

    foreach ($programs as $program) {
        echo "- " . $program['title'] . " (Status: " . $program['status'] . ")\n";
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>