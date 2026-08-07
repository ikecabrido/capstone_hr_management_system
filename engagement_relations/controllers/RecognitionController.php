<?php
namespace App\Controllers;

use App\Models\Recognition;

class RecognitionController
{
    private $recognition;

    public function __construct()
    {
        $this->recognition = new Recognition();
    }

    public function getRecognitions()
    {
        return $this->recognition->getRecognitions();
    }

    public function sendRecognition($sender_id, $receiver_id, $message, $points)
    {
        return $this->recognition->sendRecognition($sender_id, $receiver_id, $message, $points);
    }

    public function addVotePoints($voter_id, $receiver_id)
    {
        return $this->recognition->addVotePoints($voter_id, $receiver_id);
    }

    public function getLeaderboard()
    {
        return $this->recognition->getTopRecognizedEmployees();
    }

    public function getRecentlyRecognizedEmployees($days = 30)
    {
        return $this->recognition->getRecentlyRecognizedEmployees($days);
    }

    public function getEmployeeFromAwardHistory($awardHistoryId)
    {
        return $this->recognition->getEmployeeFromAwardHistory($awardHistoryId);
    }

    public function getRecognitionHistory($employeeId)
    {
        return $this->recognition->getHistoryByEmployee($employeeId);
    }

    public function manageRewardsCatalog($action, $data)
    {
        return $this->recognition->updateRewardsCatalog($action, $data);
    }

    public function assignAchievementBadge($employeeId, $badgeId, $awardedBy = null, $performanceScore = null)
    {
        return $this->recognition->assignBadge($employeeId, $badgeId, $awardedBy, $performanceScore);
    }

    public function getEmployeePerformanceScore($employeeId)
    {
        $db = \Database::getInstance()->getConnection();
        $sql = "SELECT COALESCE(
                    (
                        SELECT pr.final_rating_percent
                        FROM pm_reports pr
                        WHERE pr.employee_id = :employee_id
                        ORDER BY pr.period_end DESC, pr.report_id DESC
                        LIMIT 1
                    ),
                    (
                        SELECT pa.overall_score
                        FROM pm_appraisals pa
                        WHERE pa.employee_id = :employee_id
                        ORDER BY pa.review_date DESC, pa.appraisal_id DESC
                        LIMIT 1
                    ),
                    0
                ) as performance_score";
        $stmt = $db->prepare($sql);
        $stmt->execute(['employee_id' => $employeeId]);
        $row = $stmt->fetch();
        return $row ? (float)$row['performance_score'] : 0.0;
    }

    /**
     * Get recognition recommendations based on performance scores
     * Suggest employees who deserve recognition based on their high performance ratings
     */
    public function getRecognitionRecommendations($limit = 10)
    {
        return $this->recognition->getRecognitionRecommendations($limit);
    }

    /**
     * Get performance-based recognition leaderboard
     * Combined view of recognition points + performance scores
     */
    public function getPerformanceLeaderboard($limit = 20)
    {
        $db = \Database::getInstance()->getConnection();
        
                // Leaderboard combining performance points derived from reports
                $sql = "SELECT 
                                        e.employee_id,
                                        e.full_name as employee_name,
                                        AVG(pr.final_rating_percent) as performance_score,
                                        MAX(pr.final_grade) as final_grade,
                                        SUM(
                                            CASE
                                                WHEN pr.overall_rating_5 = 5 AND pr.final_rating_percent >= 95 THEN 100
                                                WHEN pr.overall_rating_5 >= 4 AND pr.final_rating_percent >= 80 THEN 50
                                                WHEN pr.overall_rating_5 >= 3 AND pr.final_rating_percent >= 70 THEN 25
                                                ELSE 0
                                            END
                                        ) as total_performance_points,
                                        COUNT(pr.report_id) as report_count,
                                        (SUM(
                                            CASE
                                                WHEN pr.overall_rating_5 = 5 AND pr.final_rating_percent >= 95 THEN 100
                                                WHEN pr.overall_rating_5 >= 4 AND pr.final_rating_percent >= 80 THEN 50
                                                WHEN pr.overall_rating_5 >= 3 AND pr.final_rating_percent >= 70 THEN 25
                                                ELSE 0
                                            END
                                        ) + (AVG(pr.final_rating_percent) / 10)) as combined_score
                                FROM employees e
                                LEFT JOIN (
                                    SELECT 
                                        pr.report_id,
                                        pr.employee_id,
                                        pr.final_rating_percent,
                                        pr.overall_rating_5,
                                        pr.final_grade,
                                        pr.period_end
                                    FROM pm_reports pr
                                    WHERE pr.period_end >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                                    UNION ALL
                                    SELECT 
                                        pa.appraisal_id as report_id,
                                        pa.employee_id,
                                        pa.overall_score as final_rating_percent,
                                        CASE
                                            WHEN pa.overall_score >= 98 THEN 5
                                            WHEN pa.overall_score >= 80 THEN 4
                                            WHEN pa.overall_score >= 70 THEN 3
                                            ELSE 2
                                        END as overall_rating_5,
                                        CASE
                                            WHEN pa.overall_score >= 98 THEN 'Excellent'
                                            WHEN pa.overall_score >= 95 THEN 'Outstanding'
                                            WHEN pa.overall_score >= 85 THEN 'Very Satisfactory'
                                            WHEN pa.overall_score >= 70 THEN 'Satisfactory'
                                            ELSE 'Fair'
                                        END as final_grade,
                                        pa.review_date as period_end
                                    FROM pm_appraisals pa
                                    WHERE pa.review_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                                ) pr ON e.employee_id = pr.employee_id
                                GROUP BY e.employee_id
                                HAVING report_count > 0
                                ORDER BY combined_score DESC
                                LIMIT " . (int)$limit;

        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get employees who do not yet have a performance report
     */
    public function getEmployeesWithoutPerformanceReports()
    {
        $db = \Database::getInstance()->getConnection();
        $sql = "SELECT e.employee_id, e.full_name as employee_name, e.department
                FROM employees e
                LEFT JOIN pm_reports pr ON e.employee_id = pr.employee_id
                WHERE pr.employee_id IS NULL
                ORDER BY e.full_name";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get suggested awards based on performance
     */
    public function getSuggestedAwardsForPerformer($employeeId)
    {
        $db = \Database::getInstance()->getConnection();
        
        $sql = "SELECT pr.final_grade, pr.overall_rating_5, pr.kpi_score, 
                       pr.attendance_score, e.department
                FROM pm_reports pr
                INNER JOIN employees e ON pr.employee_id = e.employee_id
                WHERE pr.employee_id = :employee_id
                ORDER BY pr.period_end DESC
                LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute([':employee_id' => $employeeId]);
        $report = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$report) {
            return [];
        }

        $suggestions = [];
        if ($report['final_grade'] === 'Outstanding') {
            $suggestions[] = [
                'award_name' => 'Top Performer Award',
                'reason' => 'Outstanding performance rating',
                'points_value' => 100
            ];
        }
        
        if ($report['overall_rating_5'] == 5 && $report['kpi_score'] >= 95) {
            $suggestions[] = [
                'award_name' => 'Excellent Achievement Badge',
                'reason' => 'Perfect rating with exceptional KPI scores',
                'points_value' => 75
            ];
        }
        
        if ($report['attendance_score'] >= 95) {
            $suggestions[] = [
                'award_name' => 'Perfect Attendance Award',
                'reason' => 'Exceptional attendance and punctuality',
                'points_value' => 50
            ];
        }

        return $suggestions;
    }

    /**
     * Get comprehensive leaderboard with all sources
     */
    public function getComprehensiveLeaderboard($limit = 20)
    {
        return $this->recognition->getComprehensiveLeaderboard($limit);
    }

    /**
     * Get employee of the month candidates
     */
    public function getEmployeeOfTheMonthCandidates($month = null, $year = null, $currentUserId = null)
    {
        return $this->recognition->getEmployeeOfTheMonthCandidates($month, $year, $currentUserId);
    }

    public function hasVotedForAwardHistory($awardHistoryId, $voterUserId)
    {
        return $this->recognition->hasVotedForAwardHistory($awardHistoryId, $voterUserId);
    }

    public function recordEmployeeMonthVote($awardHistoryId, $voterUserId, $nomineeEmployeeId)
    {
        return $this->recognition->recordEmployeeMonthVote($awardHistoryId, $voterUserId, $nomineeEmployeeId);
    }

    /**
     * Sync performance reports into recognitions table
     */
    public function syncPerformanceRecognitions()
    {
        return $this->recognition->syncPerformanceRecognitions();
    }

    /**
     * Get total points for an employee
     */
    public function getEmployeeTotalPoints($employeeId)
    {
        return $this->recognition->getEmployeeTotalPoints($employeeId);
    }

    /**
     * Get badge recommendations for an employee
     */
    public function getBadgeRecommendations($employeeId)
    {
        return $this->recognition->getBadgeRecommendations($employeeId);
    }

    /**
     * Get department-based leaderboard
     */
    public function getDepartmentLeaderboard($department = null, $limit = 10)
    {
        return $this->recognition->getDepartmentLeaderboard($department, $limit);
    }
}
