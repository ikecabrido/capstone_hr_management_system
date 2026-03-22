<?php
/**
 * Test API Debug Script
 * Tests all API endpoints
 */

header('Content-Type: text/html');

echo "<h1>API Debug Test</h1>";
echo "<p>Testing all API endpoints for data retrieval...</p>";

// Test configuration
echo "<h2>1. Configuration Test</h2>";
require_once 'config/config.php';
echo "<pre>";
echo "Database Host: " . DB_HOST . "\n";
echo "Database Name: " . DB_NAME . "\n";
echo "Database User: " . DB_USER . "\n";
echo "</pre>";

// Test database connection
echo "<h2>2. Database Connection Test</h2>";
require_once 'config/Database.php';
try {
    $db = Database::getInstance();
    echo "<p style='color: green;'>✓ Database connected successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Test Analytics class
echo "<h2>3. Analytics Class Test</h2>";
require_once 'models/Analytics.php';
try {
    $analytics = new Analytics();
    echo "<p style='color: green;'>✓ Analytics class instantiated</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Analytics instantiation failed: " . $e->getMessage() . "</p>";
    exit;
}

// Test each method
echo "<h2>4. Testing Analytics Methods</h2>";

echo "<h3>getDashboardMetrics()</h3>";
try {
    $metrics = $analytics->getDashboardMetrics();
    echo "<pre>" . json_encode($metrics, JSON_PRETTY_PRINT) . "</pre>";
    if (!empty($metrics)) {
        echo "<p style='color: green;'>✓ Method returned data</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Method returned empty data</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<h3>getDepartmentDistribution()</h3>";
try {
    $data = $analytics->getDepartmentDistribution();
    echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
    if (!empty($data)) {
        echo "<p style='color: green;'>✓ Method returned data</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Method returned empty data</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<h3>getGenderDistribution()</h3>";
try {
    $data = $analytics->getGenderDistribution();
    echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
    if (!empty($data)) {
        echo "<p style='color: green;'>✓ Method returned data</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Method returned empty data</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<h3>getAttritionData()</h3>";
try {
    $data = $analytics->getAttritionData();
    echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
    if (!empty($data)) {
        echo "<p style='color: green;'>✓ Method returned data</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Method returned empty data</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<h3>getEmployeesAtRisk()</h3>";
try {
    $data = $analytics->getEmployeesAtRisk();
    echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
    if (!empty($data)) {
        echo "<p style='color: green;'>✓ Method returned data</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Method returned empty data (This is OK if no employees at risk)</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<h3>getPerformanceDistribution()</h3>";
try {
    $data = $analytics->getPerformanceDistribution();
    echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
    if (!empty($data)) {
        echo "<p style='color: green;'>✓ Method returned data</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Method returned empty data</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Test API endpoints
echo "<h2>5. Testing API Endpoints</h2>";
echo "<p>Visit these URLs in your browser to see the API responses:</p>";
echo "<ul>";
echo "<li><a href='api/dashboard_metrics.php' target='_blank'>api/dashboard_metrics.php</a></li>";
echo "<li><a href='api/department_distribution.php' target='_blank'>api/department_distribution.php</a></li>";
echo "<li><a href='api/gender_distribution.php' target='_blank'>api/gender_distribution.php</a></li>";
echo "<li><a href='api/age_distribution.php' target='_blank'>api/age_distribution.php</a></li>";
echo "<li><a href='api/attrition_data.php' target='_blank'>api/attrition_data.php</a></li>";
echo "<li><a href='api/at_risk_employees.php' target='_blank'>api/at_risk_employees.php</a></li>";
echo "<li><a href='api/performance_distribution.php' target='_blank'>api/performance_distribution.php</a></li>";
echo "</ul>";

echo "<h2>6. Console Log Check</h2>";
echo "<p>Open the main <a href='workforce.php' target='_blank'>workforce.php</a> page and check the browser console (F12) for any JavaScript errors.</p>";
?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        background-color: #f5f5f5;
    }
    h1, h2, h3 {
        color: #333;
    }
    pre {
        background-color: #f0f0f0;
        padding: 10px;
        border-radius: 5px;
        overflow-x: auto;
    }
    a {
        color: #0066cc;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
</style>
