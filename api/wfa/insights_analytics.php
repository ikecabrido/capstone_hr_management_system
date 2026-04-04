<?php
/**
 * Workforce Analytics - Insights & Intelligence
 * Analyzes employee data to provide insights, predictions, and recommendations
 */

header('Content-Type: application/json');
ini_set('display_errors', 0);

$host = 'localhost';
$db = 'hr_management';
$user = 'root';
$pass = '';

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Get all employees
    $sql = "SELECT 
                e.employee_id,
                e.full_name,
                e.department,
                e.position,
                e.employment_status,
                YEAR(CURDATE()) - YEAR(e.date_hired) as years_employed,
                DATEDIFF(CURDATE(), e.date_hired) as days_employed,
                e.date_hired
            FROM employees e
            ORDER BY e.date_hired DESC";
    
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }
    
    $employees = array();
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
    
    // ANALYTICS CALCULATIONS
    $totalEmployees = count($employees);
    
    // 1. Attrition Analysis
    $activeEmployees = 0;
    $inactiveEmployees = 0;
    foreach ($employees as $emp) {
        if ($emp['employment_status'] === 'Active') {
            $activeEmployees++;
        } else {
            $inactiveEmployees++;
        }
    }
    
    $attritionRate = $totalEmployees > 0 ? round(($inactiveEmployees / $totalEmployees) * 100, 2) : 0;
    
    // 2. Tenure Analysis
    $averageTenure = 0;
    $tenureSum = 0;
    $tenureCount = 0;
    foreach ($employees as $emp) {
        if ($emp['years_employed'] !== null) {
            $tenureSum += intval($emp['years_employed']);
            $tenureCount++;
        }
    }
    if ($tenureCount > 0) {
        $averageTenure = round($tenureSum / $tenureCount, 1);
    }
    
    // Department analysis
    $deptData = array();
    foreach ($employees as $emp) {
        $dept = $emp['department'];
        if (!isset($deptData[$dept])) {
            $deptData[$dept] = array(
                'total' => 0,
                'active' => 0,
                'inactive' => 0,
                'tenure_sum' => 0,
                'tenure_count' => 0,
                'avg_tenure' => 0
            );
        }
        $deptData[$dept]['total']++;
        if ($emp['employment_status'] === 'Active') {
            $deptData[$dept]['active']++;
        } else {
            $deptData[$dept]['inactive']++;
        }
        if ($emp['years_employed'] !== null) {
            $deptData[$dept]['tenure_sum'] += intval($emp['years_employed']);
            $deptData[$dept]['tenure_count']++;
        }
    }
    
    // Calculate department averages
    foreach ($deptData as $dept => &$stats) {
        if ($stats['tenure_count'] > 0) {
            $stats['avg_tenure'] = round($stats['tenure_sum'] / $stats['tenure_count'], 1);
        }
        $stats['turnover_rate'] = $stats['total'] > 0 ? round(($stats['inactive'] / $stats['total']) * 100, 2) : 0;
    }
    
    // 3. Risk Analysis
    $riskEmployees = array();
    foreach ($employees as $emp) {
        if ($emp['employment_status'] === 'Active') {
            $riskScore = 0;
            $reasons = array();
            
            // Low tenure (less than 1 year) = high risk
            if (intval($emp['years_employed']) < 1) {
                $riskScore += 25;
                $reasons[] = "New employee (<1 year)";
            }
            
            // Junior positions = higher risk
            $pos = strtolower($emp['position']);
            if (strpos($pos, 'trainee') !== false || strpos($pos, 'intern') !== false) {
                $riskScore += 20;
                $reasons[] = "Junior position";
            }
            
            if ($riskScore > 0) {
                $riskEmployees[] = array(
                    'employee_id' => $emp['employee_id'],
                    'full_name' => $emp['full_name'],
                    'position' => $emp['position'],
                    'department' => $emp['department'],
                    'tenure_years' => intval($emp['years_employed']),
                    'risk_score' => $riskScore,
                    'risk_level' => $riskScore >= 40 ? 'High' : ($riskScore >= 25 ? 'Medium' : 'Low'),
                    'reasons' => $reasons
                );
            }
        }
    }
    
    // Sort by risk score (descending)
    usort($riskEmployees, function($a, $b) {
        return $b['risk_score'] - $a['risk_score'];
    });
    $topRisks = array_slice($riskEmployees, 0, 5);
    
    // 4. Department Performance Analysis
    $deptPerformance = array();
    foreach ($deptData as $dept => $data) {
        $deptPerformance[] = array(
            'department' => $dept,
            'total_employees' => $data['total'],
            'active_employees' => $data['active'],
            'inactive_employees' => $data['inactive'],
            'avg_tenure' => $data['avg_tenure'],
            'turnover_rate' => $data['turnover_rate'],
            'health_status' => $data['turnover_rate'] > 20 ? 'At Risk' : ($data['turnover_rate'] > 10 ? 'Caution' : 'Healthy')
        );
    }
    
    // Sort by turnover rate (highest first)
    usort($deptPerformance, function($a, $b) {
        return $b['turnover_rate'] - $a['turnover_rate'];
    });
    
    // 5. Growth Analysis
    $hiredThisYear = 0;
    $currentYear = date('Y');
    foreach ($employees as $emp) {
        if (date('Y', strtotime($emp['date_hired'])) == $currentYear) {
            $hiredThisYear++;
        }
    }
    $growthRate = $totalEmployees > 0 ? round(($hiredThisYear / $totalEmployees) * 100, 2) : 0;
    
    // 6. Generate Insights & Recommendations
    $insights = array();
    
    // Insight 1: Attrition Risk
    if ($attritionRate > 15) {
        $insights[] = array(
            'type' => 'warning',
            'icon' => '⚠️',
            'title' => 'High Attrition Risk',
            'message' => "Attrition rate is {$attritionRate}% - above healthy threshold (10%)",
            'recommendation' => 'Review retention strategies, conduct exit interviews, analyze separation patterns'
        );
    } else if ($attritionRate > 10) {
        $insights[] = array(
            'type' => 'info',
            'icon' => '📊',
            'title' => 'Moderate Attrition',
            'message' => "Attrition rate is {$attritionRate}% - monitor closely",
            'recommendation' => 'Continue monitoring, identify trends by department'
        );
    } else {
        $insights[] = array(
            'type' => 'success',
            'icon' => '✅',
            'title' => 'Stable Workforce',
            'message' => "Attrition rate is {$attritionRate}% - healthy retention",
            'recommendation' => 'Maintain current HR policies and engagement programs'
        );
    }
    
    // Insight 2: Department Health
    if (!empty($deptPerformance)) {
        $worstDept = $deptPerformance[0];
        if ($worstDept['turnover_rate'] > 15) {
            $insights[] = array(
                'type' => 'danger',
                'icon' => '🚨',
                'title' => 'Department Under Pressure',
                'message' => $worstDept['department'] . " has " . $worstDept['turnover_rate'] . "% turnover",
                'recommendation' => "Investigate " . $worstDept['department'] . ": review workload, management, compensation"
            );
        }
    }
    
    // Insight 3: New Hire Risk
    if (count($topRisks) > 0) {
        $riskCount = 0;
        foreach ($topRisks as $emp) {
            if ($emp['tenure_years'] < 1) {
                $riskCount++;
            }
        }
        if ($riskCount > 0) {
            $insights[] = array(
                'type' => 'info',
                'icon' => '👤',
                'title' => 'New Hires Onboarding',
                'message' => "{$riskCount} new employees (<1 year) need engagement focus",
                'recommendation' => 'Strengthen onboarding, assign mentors, check-in regularly'
            );
        }
    }
    
    // Insight 4: Growth Analysis
    if ($growthRate > 15) {
        $insights[] = array(
            'type' => 'success',
            'icon' => '📈',
            'title' => 'Workforce Expansion',
            'message' => "{$growthRate}% of workforce hired this year - strong growth",
            'recommendation' => 'Ensure proper training and cultural integration of new hires'
        );
    }
    
    // 7. KPI Summary
    $kpis = array(
        'total_employees' => $totalEmployees,
        'active_employees' => $activeEmployees,
        'inactive_employees' => $inactiveEmployees,
        'attrition_rate' => $attritionRate,
        'average_tenure' => $averageTenure,
        'hired_this_year' => $hiredThisYear,
        'growth_rate' => $growthRate,
        'at_risk_count' => count($topRisks),
        'departments' => count($deptData)
    );
    
    $response = array(
        'success' => true,
        'status' => 'success',
        'timestamp' => date('Y-m-d H:i:s'),
        'data' => array(
            'kpis' => $kpis,
            'insights' => $insights,
            'at_risk_employees' => $topRisks,
            'department_performance' => $deptPerformance,
            'attrition_analysis' => array(
                'total_rate' => $attritionRate,
                'active' => $activeEmployees,
                'inactive' => $inactiveEmployees,
                'by_department' => $deptData
            )
        )
    );
    
    echo json_encode($response);
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ));
}
?>
