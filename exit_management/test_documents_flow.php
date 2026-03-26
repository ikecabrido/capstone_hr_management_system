<?php
session_start();
$_SESSION['user']['id'] = 10; // Simulate user session

require_once "models/DocumentationModel.php";
require_once "controllers/DocumentationController.php";

echo "<h2>Testing Document Retrieval Flow</h2>";

// Test 1: Direct query
echo "<h3>Test 1: Direct Query</h3>";
require_once "controllers/ExitManagementController.php";
$pdo = new PDO("mysql:host=localhost;dbname=hr_management", "root", "");
$stmt = $pdo->query("SELECT id, employee_id, document_type, title, status FROM exit_documents ORDER BY created_at DESC LIMIT 5");
$directResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Direct query result count: " . count($directResult) . "<br>";
echo "<pre>" . json_encode($directResult, JSON_PRETTY_PRINT) . "</pre>";

// Test 2: Through DocumentationModel
echo "<h3>Test 2: DocumentationModel::getAllDocuments()</h3>";
$model = new DocumentationModel();
$modelResult = $model->getAllDocuments();
echo "Model result count: " . count($modelResult) . "<br>";
echo "<pre>" . json_encode($modelResult, JSON_PRETTY_PRINT) . "</pre>";

// Test 3: Through DocumentationController
echo "<h3>Test 3: DocumentationController::getDocuments()</h3>";
$controller = new DocumentationController();
$controllerResult = $controller->getDocuments();
echo "Controller result count: " . count($controllerResult) . "<br>";
echo "<pre>" . json_encode($controllerResult, JSON_PRETTY_PRINT) . "</pre>";

// Test 4: Through handleAjaxRequest
echo "<h3>Test 4: DocumentationController::handleAjaxRequest('get_documents')</h3>";
$ajaxResult = $controller->handleAjaxRequest('get_documents', []);
echo "AJAX result count: " . count($ajaxResult) . "<br>";
echo "<pre>" . json_encode($ajaxResult, JSON_PRETTY_PRINT) . "</pre>";

// Test 5: What would be returned to frontend
echo "<h3>Test 5: Final JSON Response (as sent to frontend)</h3>";
echo "<pre>" . json_encode($ajaxResult, JSON_PRETTY_PRINT) . "</pre>";

// Check error log for messages
echo "<h3>Recent Error Log Entries</h3>";
$logFile = "C:/xampp/apache/logs/error.log";
if (file_exists($logFile)) {
    $lines = file($logFile);
    $recentLines = array_slice($lines, -30);
    echo "<pre>";
    foreach ($recentLines as $line) {
        if (strpos($line, "getAllDocuments") !== false || 
            strpos($line, "get_documents") !== false ||
            strpos($line, "Document") !== false) {
            echo htmlspecialchars($line);
        }
    }
    echo "</pre>";
}
?>
