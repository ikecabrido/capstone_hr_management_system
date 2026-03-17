<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Enable error reporting to log file instead of displaying
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error.log');

try {
    require_once __DIR__ . '/../config/db.php';
    require_once __DIR__ . '/../middleware/Auth.php';

    // Verify user is authenticated
    $user = Auth::requireAuth();
    
    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    // Only GET is allowed
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }

    // All roles can view dashboard (with filtered data)
    Auth::requirePermission('dashboard', 'view');
    
    $response = [
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'role' => $user['role'],
            'department' => $user['department'] ?? 'Unknown'
        ]
    ];
    
    // Build completely different dashboard for each role
    $role = strtolower($user['role'] ?? 'employee');

    if ($role === 'admin') {
        $response['dashboard'] = getAdminDashboard($pdo, $user);
    } elseif ($role === 'hr_manager' || $role === 'hr') {
        $response['dashboard'] = getHRDashboard($pdo, $user);
    } elseif ($role === 'employee') {
        $response['dashboard'] = getEmployeeDashboard($pdo, $user);
    } else {
        $response['dashboard'] = getDefaultDashboard($pdo, $user);
    }

    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    // Log error details
    error_log("Dashboard API Error: " . $e->getMessage() . " - " . $e->getFile() . ":" . $e->getLine());
    echo json_encode(['error' => $e->getMessage(), 'debug' => true]);
}

/**
 * ADMIN DASHBOARD - Full System Management Overview
 * Shows complete system metrics and control panels
 */
function getAdminDashboard($pdo, $user) {
    try {
        return [
            'dashboard_type' => 'ADMIN',
            'dashboard_name' => 'System Administration Dashboard',
            'welcome' => 'Welcome, System Administrator',
            'access_level' => 'FULL SYSTEM ACCESS',
            
            'system_overview' => [
                'total_employees' => getCount($pdo, 'employees'),
                'active_users_today' => getActiveUsersToday($pdo),
                'total_departments' => getCount($pdo, 'departments'),
            ],
            
            'critical_metrics' => [
                'pending_grievances' => getCount($pdo, 'grievances', 'status', 'pending'),
                'unresolved_complaints' => getCount($pdo, 'grievances', 'status', 'under_investigation'),
                'pending_recognition_approvals' => getCount($pdo, 'recognitions'),
                'unread_feedback_items' => getCount($pdo, 'feedback', 'status', 'submitted'),
            ],
            
            'content_summary' => [
                'total_announcements' => getCount($pdo, 'announcements'),
                'active_surveys' => getCount($pdo, 'engagement_surveys'),
                'total_events' => getCount($pdo, 'events'),
                'total_feedback_submitted' => getCount($pdo, 'feedback'),
                'total_recognitions_given' => getCount($pdo, 'recognitions'),
            ],
            
            'recent_activity' => [
                'user_registrations_this_week' => getCountThisWeek($pdo, 'employees'),
            ],
            

        ];
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

/**
 * HR MANAGER DASHBOARD - Employee Relations & Engagement Focus
 * Shows HR-specific metrics and case management
 */
function getHRDashboard($pdo, $user) {
    try {
        return [
            'dashboard_type' => 'HR_MANAGER',
            'dashboard_name' => 'Human Resources Dashboard',
            'welcome' => 'Welcome, HR Manager',
            'access_level' => 'HR MODULE ACCESS',
            
                        'system_overview' => [
                'total_employees' => getCount($pdo, 'employees'),
                'active_users_today' => getActiveUsersToday($pdo),
                'total_departments' => getCount($pdo, 'departments'),
            ],
            
            'critical_metrics' => [
                'pending_grievances' => getCount($pdo, 'grievances', 'status', 'pending'),
                'grievances_under_investigation' => getCount($pdo, 'grievances', 'status', 'under_investigation'),
                'pending_feedback_responses' => getCount($pdo, 'feedback', 'status', 'submitted'),
                'pending_recognition_approvals' => getCount($pdo, 'recognitions'),
            ],
            
            'content_summary' => [
                'active_surveys' => getCount($pdo, 'engagement_surveys'),
                'total_announcements' => getCount($pdo, 'announcements'),
                'total_events' => getCount($pdo, 'events'),
                'employee_relations' => getCount($pdo, 'grievances'),
            ],
            
            'employee_relations' => [
                'pending_grievances' => getCount($pdo, 'grievances', 'status', 'pending'),
                'grievances_under_investigation' => getCount($pdo, 'grievances', 'status', 'under_investigation'),
                'grievances_resolved_this_month' => getCountThisMonth($pdo, 'grievances'),
            ],
            
            'engagement_metrics' => [
                'feedback_submissions_this_month' => getCount($pdo, 'feedback'),
                'pending_feedback_responses' => getCount($pdo, 'feedback', 'status', 'submitted'),
            ],
            
            'recognition_program' => [
                'pending_approvals' => getCount($pdo, 'recognition', 'status', 'pending'),
                'recognitions_this_month' => getCount($pdo, 'recognition'),
                'peer_recognition_count' => getCount($pdo, 'recognitions', 'type', 'peer'),
                'manager_recognition_count' => getCount($pdo, 'recognitions', 'type', 'manager'),
            ],
            
            'surveys_and_polls' => [
                'active_surveys' => getCount($pdo, 'engagement_surveys'),
                'pending_survey_deployments' => 0,
                'survey_completion_rate' => calculateSurveyCompletionRate($pdo),
                'responses_this_week' => getCountThisWeek($pdo, 'survey_responses'),
            ],
            
            'announcements' => [
                'total_announcements' => getCount($pdo, 'announcements'),
                'unread_by_employees' => countUnreadAnnouncements($pdo),
                'acknowledgment_rate' => calculateAnnouncementAcknowledgmentRate($pdo),
            ],
            
            'events' => [
                'upcoming_events' => getCount($pdo, 'events', 'event_date', 'CURDATE()', '>'),
                'registered_participants' => getCount($pdo, 'event_registrations'),
                'event_this_month' => getCountThisMonth($pdo, 'events'),
            ],
            
            'department_overview' => [
                'total_employees' => getCount($pdo, 'employees'),
                'new_employees_this_month' => getCountThisMonth($pdo, 'employees'),
                'active_team_members' => getCount($pdo, 'employees', 'status', 'active'),
            ]
        ];
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

/**
 * EMPLOYEE DASHBOARD - Personal Engagement & Self-Service Focus
 * Shows individual employee metrics and opportunities
 */
function getEmployeeDashboard($pdo, $user) {
    try {
        return [
            'dashboard_type' => 'EMPLOYEE',
            'dashboard_name' => 'My Employee Dashboard',
            'welcome' => 'Welcome, ' . ($user['name'] ?? 'Employee'),
            'access_level' => 'PERSONAL DATA ACCESS',
            'department' => $user['department'] ?? 'Unknown',
            
            'personal_performance' => [
                'total_recognitions_received' => getCount($pdo, 'recognitions', 'to_employee_id', $user['id']),
                'recognitions_this_year' => getCountThisYear($pdo, 'recognitions', 'to_employee_id', $user['id']),
                'recognitions_this_month' => getCountThisMonth($pdo, 'recognitions', 'to_employee_id', $user['id']),
                'engagement_score' => calculateUserEngagementScore($pdo, $user['id']),
                'participation_rate' => calculateUserParticipationRate($pdo, $user['id']),
                'surveys_completed' => countCompletedSurveysByUser($pdo, $user['id']),
                'events_attended' => getCount($pdo, 'event_registrations', 'employee_id', $user['id']),
                'feedback_submitted' => getCount($pdo, 'feedback', 'employee_id', $user['id']),
                'achievements' => 'View your achievements and milestones',
            ],
            
            'my_cases' => [
                'grievances_filed' => getCount($pdo, 'grievances', 'employee_id', $user['id']),
                'pending_grievances' => getCount($pdo, 'grievances', 'employee_id', $user['id']),
                'resolved_grievances' => getCount($pdo, 'grievances', 'status', 'resolved'),
                'grievance_status' => 'View my cases',
            ],
            
            'my_feedback' => [
                'feedback_submitted' => getCount($pdo, 'feedback', 'employee_id', $user['id']),
                'anonymous_feedback' => getCount($pdo, 'feedback', 'is_anonymous', 1),
                'named_feedback' => getCount($pdo, 'feedback', 'is_anonymous', 0),
                'pending_responses' => getCount($pdo, 'feedback', 'status', 'submitted'),
            ],
            
            'my_engagement' => [
                'surveys_completed' => countCompletedSurveysByUser($pdo, $user['id']),
                'pending_surveys' => countPendingSurveysByUser($pdo, $user['id']),
                'personal_engagement_score' => calculateUserEngagementScore($pdo, $user['id']),
                'participation_rate' => calculateUserParticipationRate($pdo, $user['id']),
            ],
            
            'recognition_status' => [
                'recognition_received' => getCount($pdo, 'recognitions', 'to_employee_id', $user['id']),
                'this_month' => getCountThisMonth($pdo, 'recognitions', 'to_employee_id', $user['id']),
                'this_year' => getCountThisYear($pdo, 'recognitions', 'to_employee_id', $user['id']),
                'recognitions_pending' => 0,
            ],
            
            'events_and_activities' => [
                'registered_events' => getCount($pdo, 'event_registrations', 'employee_id', $user['id']),
                'attended_events' => getCount($pdo, 'event_registrations', 'employee_id', $user['id']),
                'upcoming_events' => getCount($pdo, 'events', 'event_date', 'CURDATE()', '>'),
            ],
            
            'announcements' => [
                'total_announcements' => getCount($pdo, 'announcements'),
                'unread_announcements' => countUnreadAnnouncementsByUser($pdo, $user['id']),
            ],
            
            'my_statistics' => [
                'feedback_given' => getCount($pdo, 'feedback', 'employee_id', $user['id']),
            ]
        ];
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

/**
 * Helper function to safely count records
 */
/**
 * Get count of active users today (unique employees with LOGIN actions in last 24 hours)
 */
function getActiveUsersToday($pdo) {
    try {
        // Count distinct users who have logged in today
        $sql = "SELECT COUNT(DISTINCT performed_by) as count FROM audit_logs WHERE action = 'LOGIN' AND performed_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

function getCount($pdo, $table, $condition = null, $value = null, $operator = '=') {
    try {
        if ($condition && $value !== null) {
            if (is_numeric($value)) {
                $sql = "SELECT COUNT(*) as count FROM $table WHERE $condition $operator $value";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
            } else {
                $sql = "SELECT COUNT(*) as count FROM $table WHERE $condition $operator ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$value]);
            }
        } else {
            $sql = "SELECT COUNT(*) as count FROM $table";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        }
        
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Count records from this week
 */
function getCountThisWeek($pdo, $table) {
    try {
        $sql = "SELECT COUNT(*) as count FROM $table WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Count records from this month
 */
function getCountThisMonth($pdo, $table, $filterColumn = null, $filterId = null) {
    try {
        if ($filterColumn && $filterId) {
            $sql = "SELECT COUNT(*) as count FROM $table WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) AND $filterColumn = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$filterId]);
        } else {
            $sql = "SELECT COUNT(*) as count FROM $table WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        }
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Count records from this year
 */
function getCountThisYear($pdo, $table, $filterColumn = null, $filterId = null) {
    try {
        if ($filterColumn && $filterId) {
            $sql = "SELECT COUNT(*) as count FROM $table WHERE YEAR(created_at) = YEAR(CURDATE()) AND $filterColumn = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$filterId]);
        } else {
            $sql = "SELECT COUNT(*) as count FROM $table WHERE YEAR(created_at) = YEAR(CURDATE())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        }
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Count unread announcements
 */
function countUnreadAnnouncements($pdo) {
    try {
        $sql = "SELECT COUNT(DISTINCT a.id) as count FROM announcements a 
                LEFT JOIN announcement_reads ar ON a.id = ar.announcement_id
                WHERE ar.id IS NULL";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Calculate survey completion rate
 */
function calculateSurveyCompletionRate($pdo) {
    try {
        $sql = "SELECT 
                    (COUNT(DISTINCT sr.id) / COUNT(DISTINCT es.id)) * 100 as rate
                FROM engagement_surveys es
                LEFT JOIN survey_responses sr ON es.id = sr.survey_id
                WHERE es.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return (int) ($result['rate'] ?? 0);
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Calculate announcement acknowledgment rate
 */
function calculateAnnouncementAcknowledgmentRate($pdo) {
    try {
        $sql = "SELECT 
                    (COUNT(DISTINCT ar.announcement_id) / COUNT(DISTINCT a.id)) * 100 as rate
                FROM announcements a
                LEFT JOIN announcement_reads ar ON a.id = ar.announcement_id
                WHERE a.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return (int) ($result['rate'] ?? 0);
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Count unread announcements by user
 */
function countUnreadAnnouncementsByUser($pdo, $userId) {
    try {
        $sql = "SELECT COUNT(DISTINCT a.id) as count FROM announcements a 
                LEFT JOIN announcement_reads ar ON a.id = ar.announcement_id AND ar.employee_id = ?
                WHERE ar.id IS NULL";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Count completed surveys by user
 */
function countCompletedSurveysByUser($pdo, $userId) {
    try {
        $sql = "SELECT COUNT(DISTINCT survey_id) as count FROM survey_responses WHERE employee_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Count pending surveys for user
 */
function countPendingSurveysByUser($pdo, $userId) {
    try {
        // engagement_surveys has no status column in schema, so do not filter by status
        $sql = "SELECT COUNT(DISTINCT es.id) as count FROM engagement_surveys es
                WHERE es.id NOT IN (
                    SELECT sr.survey_id FROM survey_responses sr WHERE sr.employee_id = ?
                )";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Calculate user engagement score
 */
function calculateUserEngagementScore($pdo, $userId) {
    try {
        $sql = "SELECT AVG(engagement_score) as score FROM (
                    SELECT (COUNT(sr.id) * 2) + (COUNT(f.id) * 1.5) + (COUNT(er.id) * 1) as engagement_score
                    FROM employees e
                    LEFT JOIN survey_responses sr ON e.id = sr.employee_id
                    LEFT JOIN feedback f ON e.id = f.employee_id
                    LEFT JOIN event_registrations er ON e.id = er.employee_id
                    WHERE e.id = ?
                ) as scores";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        $score = $result['score'] ?? 0;
        return min(round($score / 2, 1), 10) . '/10';
    } catch (Exception $e) {
        return '0/10';
    }
}

/**
 * Calculate user participation rate
 */
function calculateUserParticipationRate($pdo, $userId) {
    try {
        $sql = "SELECT 
                    (COUNT(DISTINCT sr.id) + COUNT(DISTINCT f.id) + COUNT(DISTINCT er.id)) as engagement_count
                FROM employees e
                LEFT JOIN survey_responses sr ON e.id = sr.employee_id
                LEFT JOIN feedback f ON e.id = f.employee_id
                LEFT JOIN event_registrations er ON e.id = er.employee_id
                WHERE e.id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        $count = $result['engagement_count'] ?? 0;
        $rate = min(($count * 10), 100);
        return (int)$rate . '%';
    } catch (Exception $e) {
        return '0%';
    }
}

/**
 * Default dashboard for unknown/legacy roles.
 */
function getDefaultDashboard($pdo, $user) {
    try {
        $system_overview = [
            'total_employees' => getCount($pdo, 'employees'),
            'active_users_today' => getActiveUsersToday($pdo),
            'total_departments' => getCount($pdo, 'departments'),
        ];

        $critical_metrics = [
            'pending_grievances' => getCount($pdo, 'grievances', 'status', 'pending'),
            'open_grievances' => getCount($pdo, 'grievances', 'status', 'open'),
            'under_investigation' => getCount($pdo, 'grievances', 'status', 'under_investigation'),
            'pending_feedback' => getCount($pdo, 'feedback', 'status', 'submitted'),
        ];

        return [
            'dashboard_type' => 'DEFAULT',
            'dashboard_name' => 'Engagement Dashboard',
            'welcome' => 'Welcome, ' . ($user['name'] ?? 'User'),
            'system_overview' => $system_overview,
            'critical_metrics' => $critical_metrics,
            'active_surveys' => getCount($pdo, 'engagement_surveys'),
            'total_events' => getCount($pdo, 'events'),
            'my_reports' => getCount($pdo, 'audit_logs', 'performed_by', $user['id']),
        ];
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}
?>

