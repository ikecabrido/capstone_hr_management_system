<?php
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../controllers/reportPdfController.php';

$db = Database::getInstance()->getConnection();

if (!$db instanceof PDO) {
    die('Database connection failed.');
}

$periodId = (int) ($_GET['period_id'] ?? 0);

if (!$periodId) {
    die('Invalid Period');
}

$controller = new ReportPdfController($db);
$controller->generate($periodId);
