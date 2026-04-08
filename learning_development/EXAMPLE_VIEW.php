<?php

/**
 * Example: How to Use the OOP Architecture in View Files
 * 
 * This shows how to integrate the OOP classes into your existing HTML views
 */

require_once __DIR__ . '/autoload.php';

use HRManagement\Services\TrainingService;
use HRManagement\Utils\AuthManager;
use HRManagement\Utils\Helper;

// Start session and check authentication
AuthManager::startSession();

if (!AuthManager::isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Initialize services
$trainingService = new TrainingService();

// Get data for the view
$page = $_GET['page'] ?? 1;
$limit = 20;
$searchTerm = $_GET['search'] ?? '';

if ($searchTerm) {
    $programs = $trainingService->searchPrograms($searchTerm, $limit);
} else {
    $programs = $trainingService->getAvailablePrograms($limit);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Training Programs</title>
</head>
<body>
    <h1>Available Training Programs</h1>
    
    <div class="search">
        <form method="get">
            <input type="text" name="search" placeholder="Search programs..." value="<?= htmlspecialchars($searchTerm) ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="programs-list">
        <?php foreach ($programs as $program): ?>
            <div class="program-card">
                <h3><?= htmlspecialchars($program->getName()) ?></h3>
                <p><?= htmlspecialchars(Helper::truncate($program->getDescription(), 150)) ?></p>
                
                <div class="program-details">
                    <span class="category"><?= htmlspecialchars($program->getCategory()) ?></span>
                    <span class="duration"><?= $program->getDuration() ?>h</span>
                    <span class="status">
                        <?php if ($program->isActive()): ?>
                            <span class="badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge-danger">Inactive</span>
                        <?php endif; ?>
                    </span>
                </div>

                <div class="actions">
                    <form method="post" action="api/training.php" style="display: inline;">
                        <input type="hidden" name="action" value="enroll">
                        <input type="hidden" name="id" value="<?= $program->getId() ?>">
                        <button type="submit" class="btn-enroll">Enroll</button>
                    </form>
                    
                    <?php if (AuthManager::isAdmin()): ?>
                        <a href="edit_program.php?id=<?= $program->getId() ?>" class="btn-edit">Edit</a>
                        <form method="post" action="api/training.php" style="display: inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $program->getId() ?>">
                            <button type="submit" class="btn-delete" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($programs)): ?>
        <div class="no-results">
            <p>No programs found. Try a different search.</p>
        </div>
    <?php endif; ?>
</body>
</html>
