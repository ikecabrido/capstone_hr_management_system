<?php
namespace App\Models;

class Recognition extends BaseModel
{
    protected function getSenderNameSql($senderIdExpr, $alias = 'sender_name')
    {
        return "COALESCE(u.full_name, u.username, $senderIdExpr) AS $alias";
    }
    public function getRecognitions()
    {
        // Get both manual recognitions and performance report recognitions
        $sql = "
        SELECT 
            er.eer_recognition_id as id,
            er.sender_id,
            er.receiver_id,
            COALESCE(e.full_name, CONCAT('Employee #', er.receiver_id)) as receiver_name,
            COALESCE(us.full_name, 'engage') as sender_name,
            er.message,
            er.points,
            er.created_at,
            'manual' as source,
            er.category
        FROM eer_recognitions er
        LEFT JOIN users us ON er.sender_id = us.id
        LEFT JOIN employees e ON er.receiver_id = e.employee_id
        WHERE er.source IS NULL OR er.source != 'performance'
        ORDER BY er.created_at DESC";

        return $this->execute($sql)->fetchAll();
    }

    public function getRecognitionRecommendations($limit = 10)
    {
        $sql = "SELECT 
                    pr.report_id,
                    pr.employee_id,
                    COALESCE(e.full_name, CONCAT('Employee #', pr.employee_id)) as employee_name,
                    pr.evaluation_period,
                    pr.period_start,
                    pr.period_end,
                    pr.kpi_score,
                    pr.attendance_score,
                    pr.overall_rating_percent,
                    pr.overall_rating_5,
                    pr.final_rating_percent,
                    pr.final_grade,
                    pr.remarks,
                    CONCAT('Performance Review: ', pr.final_grade, ' (', pr.final_rating_percent, '%)') as message
                FROM (
                    SELECT 
                        pr.report_id,
                        pr.employee_id,
                        pr.evaluation_period,
                        pr.period_start,
                        pr.period_end,
                        pr.kpi_score,
                        pr.attendance_score,
                        pr.overall_rating_percent,
                        pr.overall_rating_5,
                        pr.final_rating_percent,
                        pr.final_grade,
                        pr.remarks
                    FROM pm_reports pr
                    UNION ALL
                    SELECT
                        pa.appraisal_id as report_id,
                        pa.employee_id,
                        pa.review_period as evaluation_period,
                        pa.review_date as period_start,
                        pa.review_date as period_end,
                        NULL as kpi_score,
                        NULL as attendance_score,
                        pa.overall_score as overall_rating_percent,
                        CASE
                            WHEN pa.overall_score >= 98 THEN 5
                            WHEN pa.overall_score >= 80 THEN 4
                            WHEN pa.overall_score >= 70 THEN 3
                            ELSE 2
                        END as overall_rating_5,
                        pa.overall_score as final_rating_percent,
                        CASE
                            WHEN pa.overall_score >= 98 THEN 'Excellent'
                            WHEN pa.overall_score >= 95 THEN 'Outstanding'
                            WHEN pa.overall_score >= 85 THEN 'Very Satisfactory'
                            WHEN pa.overall_score >= 70 THEN 'Satisfactory'
                            ELSE 'Fair'
                        END as final_grade,
                        pa.comments as remarks
                    FROM pm_appraisals pa
                ) pr
                LEFT JOIN employees e ON pr.employee_id = e.employee_id
                WHERE pr.period_end >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                    AND pr.final_rating_percent >= 80
                    AND pr.final_grade IN ('Outstanding', 'Very Satisfactory', 'Excellent')
                ORDER BY pr.final_rating_percent DESC, pr.period_end DESC
                LIMIT " . (int)$limit;

        return $this->execute($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function sendRecognition($sender_id, $receiver_id, $message, $points)
    {
        // Insert recognition into eer_recognitions table
        $sql = "INSERT INTO eer_recognitions (sender_id, receiver_id, message, points, category, created_at) 
                VALUES (:sender_id, :receiver_id, :message, :points, 'general', NOW())";
        
        $this->execute($sql, [
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'message' => $message,
            'points' => $points
        ]);
        
        $db = \Database::getInstance()->getConnection();
        $recognitionId = (int)$db->lastInsertId();

        return $recognitionId;
    }

    public function autoNominateForEmployeeOfTheMonth($employeeId, $reason = null)
    {
        $monthYear = date('Y-m');
        $existing = $this->execute(
            'SELECT * FROM eer_award_history WHERE employee_id = :employee_id AND month_year = :month_year AND award_type = :award_type',
            ['employee_id' => $employeeId, 'month_year' => $monthYear, 'award_type' => 'employee_of_month']
        )->fetch();

        if (!$existing) {
            $this->execute(
                'INSERT INTO eer_award_history (employee_id, award_name, reason, nominated_by, award_type, month_year, status, created_at) 
                VALUES (:employee_id, :award_name, :reason, :nominated_by, :award_type, :month_year, :status, NOW())',
                [
                    'employee_id' => $employeeId,
                    'award_name' => 'Employee of the Month Nomination',
                    'reason' => $reason ?: 'Auto-nominated after recognition entry',
                    'nominated_by' => null,
                    'award_type' => 'employee_of_month',
                    'month_year' => $monthYear,
                    'status' => 'nominated'
                ]
            );
        }
    }

    public function addVotePoints($voter_id, $receiver_id)
    {
        // Add vote recognition (5 points for voting)
        $sql = "INSERT INTO eer_recognitions (sender_id, receiver_id, message, points, category, created_at) 
                VALUES (:sender_id, :receiver_id, :message, 5, 'vote', NOW())";
        
        $result = $this->execute($sql, [
            'sender_id' => $voter_id,
            'receiver_id' => $receiver_id,
            'message' => 'Vote recognition'
        ]);
        
        // Get the inserted ID
        $db = \Database::getInstance()->getConnection();
        return $db->lastInsertId();
    }

    public function getHistoryByEmployee($employeeId)
    {
                // Return performance report history for the employee
                $sql = "SELECT pr.*, e.full_name as employee_name,
                                             CONCAT('Performance Review: ', pr.final_grade, ' (', pr.final_rating_percent, '%)') as message,
                                             CASE
                                                 WHEN pr.overall_rating_5 = 5 AND pr.final_rating_percent >= 95 THEN 100
                                                 WHEN pr.overall_rating_5 >= 4 AND pr.final_rating_percent >= 80 THEN 50
                                                 WHEN pr.overall_rating_5 >= 3 AND pr.final_rating_percent >= 70 THEN 25
                                                 ELSE 0
                                             END as points
                                FROM pm_reports pr
                                JOIN employees e ON pr.employee_id = e.employee_id
                                WHERE pr.employee_id = :employeeId
                                ORDER BY pr.period_end DESC";
                return $this->execute($sql, ['employeeId' => $employeeId])->fetchAll();
    }

    public function updateRewardsCatalog($action, $data)
    {
        if ($action === 'add') {
            $sql = "INSERT INTO rewards_catalog (name, description, points) VALUES (:name, :description, :points)";
            return $this->execute($sql, $data);
        } elseif ($action === 'delete') {
            $sql = "DELETE FROM rewards_catalog WHERE id = :id";
            return $this->execute($sql, ['id' => $data['id']]);
        }
    }

    public function assignBadge($employeeId, $badgeId, $awardedBy = null, $performanceScore = null)
    {
        $sql = "INSERT INTO employee_badges (employee_id, badge_id, awarded_by, performance_score, awarded_at)
                VALUES (:employeeId, :badgeId, :awardedBy, :performanceScore, NOW())";
        return $this->execute($sql, [
            'employeeId' => $employeeId,
            'badgeId' => $badgeId,
            'awardedBy' => $awardedBy,
            'performanceScore' => $performanceScore !== null ? (float)$performanceScore : 0.00
        ]);
    }

    public function getTopRecognizedEmployees($limit = 10)
    {
        // Compute leaderboard based on both manual recognitions and performance reports
        $sql = "
        SELECT 
            e.employee_id as receiver_id,
            e.full_name as receiver_name,
            COUNT(*) as recognition_count,
            SUM(points) as total_points
        FROM (
            SELECT receiver_id, points FROM eer_recognitions
            UNION ALL
            SELECT 
                pr.employee_id as receiver_id,
                CASE
                    WHEN pr.overall_rating_5 = 5 AND pr.final_rating_percent >= 95 THEN 100
                    WHEN pr.overall_rating_5 >= 4 AND pr.final_rating_percent >= 80 THEN 50
                    WHEN pr.overall_rating_5 >= 3 AND pr.final_rating_percent >= 70 THEN 25
                    ELSE 0
                END as points
            FROM pm_reports pr
        ) combined_recognitions
        JOIN employees e ON combined_recognitions.receiver_id = e.employee_id
        GROUP BY combined_recognitions.receiver_id
        ORDER BY total_points DESC, recognition_count DESC
        LIMIT " . (int)$limit;
        
        return $this->execute($sql)->fetchAll();
    }

    public function getRecentlyRecognizedEmployees($days = 30)
    {
        $sql = "SELECT DISTINCT e.employee_id, e.full_name, COUNT(er.eer_recognition_id) as recognition_count
            FROM employees e
            JOIN eer_recognitions er ON e.employee_id = er.receiver_id
            WHERE (er.source IS NULL OR er.source != 'performance')
              AND er.created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
            GROUP BY e.employee_id
            ORDER BY recognition_count DESC";
        return $this->execute($sql, ['days' => $days])->fetchAll();
    }

    public function getEmployeeFromAwardHistory($awardHistoryId)
    {
        $sql = "SELECT employee_id FROM eer_award_history WHERE eer_award_history_id = :id";
        $result = $this->execute($sql, ['id' => $awardHistoryId])->fetch();
        return $result ? $result['employee_id'] : null;
    }

    public function hasVotedForAwardHistory($awardHistoryId, $voterUserId)
    {
        $sql = "SELECT 1 FROM eer_award_votes WHERE award_history_id = :award_history_id AND voter_user_id = :voter_user_id LIMIT 1";
        $result = $this->execute($sql, [
            'award_history_id' => $awardHistoryId,
            'voter_user_id' => $voterUserId
        ])->fetchColumn();
        return (bool)$result;
    }

    public function recordEmployeeMonthVote($awardHistoryId, $voterUserId, $nomineeEmployeeId)
    {
        $sql = "INSERT INTO eer_award_votes (award_history_id, voter_user_id, nominee_employee_id, created_at)
                VALUES (:award_history_id, :voter_user_id, :nominee_employee_id, NOW())";
        $this->execute($sql, [
            'award_history_id' => $awardHistoryId,
            'voter_user_id' => $voterUserId,
            'nominee_employee_id' => $nomineeEmployeeId
        ]);

        $this->execute(
            "UPDATE eer_award_history SET vote_count = vote_count + 1 WHERE eer_award_history_id = :id",
            ['id' => $awardHistoryId]
        );
    }

    /**
     * Get comprehensive leaderboard with all recognition sources
     */
    public function getComprehensiveLeaderboard($limit = 20)
    {
        $sql = "
        SELECT 
            e.employee_id,
            e.full_name as employee_name,
            e.department,
            e.position,
            -- Recognition points
            COALESCE(SUM(CASE WHEN src = 'recognition' THEN points ELSE 0 END), 0) as recognition_points,
            -- Performance-based points
            COALESCE(SUM(CASE WHEN src = 'performance' THEN points ELSE 0 END), 0) as performance_points,
            -- Badge points
            COALESCE(SUM(CASE WHEN src = 'badge' THEN points ELSE 0 END), 0) as badge_points,
            -- Award points
            COALESCE(SUM(CASE WHEN src = 'award' THEN points ELSE 0 END), 0) as award_points,
            -- Total
            COALESCE(SUM(points), 0) as total_points,
            COUNT(DISTINCT src) as source_count,
            RANK() OVER (ORDER BY COALESCE(SUM(points), 0) DESC) as rank_position
        FROM (
            SELECT e2.employee_id, 'recognition' as src, er.points
            FROM employees e2
            LEFT JOIN eer_recognitions er ON e2.employee_id = er.receiver_id
            WHERE er.points IS NOT NULL
            
            UNION ALL
            
            SELECT e2.employee_id, 'performance' as src, 
                CASE
                    WHEN pr.overall_rating_5 = 5 AND pr.final_rating_percent >= 95 THEN 100
                    WHEN pr.overall_rating_5 >= 4 AND pr.final_rating_percent >= 80 THEN 50
                    WHEN pr.overall_rating_5 >= 3 AND pr.final_rating_percent >= 70 THEN 25
                    ELSE 0
                END as points
            FROM employees e2
            LEFT JOIN pm_reports pr ON e2.employee_id = pr.employee_id
            WHERE pr.employee_id IS NOT NULL
            
            UNION ALL
            
            SELECT e2.employee_id, 'performance' as src,
                CASE
                    WHEN pa.overall_score >= 98 THEN 100
                    WHEN pa.overall_score >= 95 THEN 100
                    WHEN pa.overall_score >= 85 THEN 50
                    WHEN pa.overall_score >= 70 THEN 25
                    ELSE 0
                END as points
            FROM employees e2
            LEFT JOIN pm_appraisals pa ON e2.employee_id = pa.employee_id
            WHERE pa.employee_id IS NOT NULL
            
            UNION ALL
            
            SELECT e2.employee_id, 'badge' as src, b.points_value
            FROM employees e2
            LEFT JOIN employee_badges eb ON e2.employee_id = eb.employee_id
            LEFT JOIN eer_badges b ON eb.badge_id = b.eer_badge_id
            WHERE b.points_value IS NOT NULL
            
            UNION ALL
            
            SELECT e2.employee_id, 'award' as src, ah.points
            FROM employees e2
            LEFT JOIN eer_award_history ah ON e2.employee_id = ah.employee_id AND ah.points > 0
            WHERE ah.points IS NOT NULL
        ) combined
        JOIN employees e ON combined.employee_id = e.employee_id
        GROUP BY e.employee_id
        ORDER BY total_points DESC
        LIMIT " . (int)$limit;
        
        return $this->execute($sql)->fetchAll();
    }

    /**
     * Sync performance reports into eer_recognitions table.
     * Inserts a recognition row for each performance report that has points
     * and is not already recorded in eer_recognitions (matched by performance_report_id).
     * Returns the number of rows inserted.
     */
    public function syncPerformanceRecognitions()
    {
        $sql = "INSERT INTO eer_recognitions (sender_id, receiver_id, message, points, category, source, performance_report_id, created_at)
                SELECT
                    pr.created_by as sender_id,
                    COALESCE(u.id, NULL) as receiver_id,
                    CONCAT('Performance Review: ', pr.final_grade, ' (', pr.final_rating_percent, '%)') as message,
                    CASE
                        WHEN pr.overall_rating_5 = 5 AND pr.final_rating_percent >= 95 THEN 100
                        WHEN pr.overall_rating_5 >= 4 AND pr.final_rating_percent >= 80 THEN 50
                        WHEN pr.overall_rating_5 >= 3 AND pr.final_rating_percent >= 70 THEN 25
                        ELSE 0
                    END as points,
                    'performance' as category,
                    'performance' as source,
                    pr.report_id as performance_report_id,
                    pr.created_at as created_at
                FROM pm_reports pr
                LEFT JOIN users u ON u.employee_id = pr.employee_id
                WHERE (CASE
                        WHEN pr.overall_rating_5 = 5 AND pr.final_rating_percent >= 95 THEN 100
                        WHEN pr.overall_rating_5 >= 4 AND pr.final_rating_percent >= 80 THEN 50
                        WHEN pr.overall_rating_5 >= 3 AND pr.final_rating_percent >= 70 THEN 25
                        ELSE 0
                    END) > 0
                AND NOT EXISTS (
                    SELECT 1 FROM eer_recognitions er WHERE er.performance_report_id = pr.report_id
                )";

        $stmt = $this->execute($sql);
        $rowsInserted = $stmt->rowCount();

        if ($rowsInserted > 0) {
            $this->execute(
                "UPDATE eer_recognitions SET sender_id = eer_recognition_id WHERE source = 'performance' AND sender_id != eer_recognition_id"
            );
        }

        return $rowsInserted;
    }

    /**
     * Get employee of the month candidates based on performance and recognitions
     */
    public function getEmployeeOfTheMonthCandidates($month = null, $year = null, $currentUserId = null)
    {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');
        $monthYear = sprintf('%04d-%02d', $year, $month);
        
        $userVoteJoin = '';
        $userVoteSelect = '0 as has_voted';
        $params = [
            'month' => (int)$month,
            'year' => (int)$year,
            'month_year' => $monthYear,
            'award_type' => 'employee_of_month'
        ];

        if ($currentUserId) {
            $userVoteJoin = "
            LEFT JOIN eer_award_votes ev ON ev.award_history_id = ah.eer_award_history_id
                AND ev.voter_user_id = :current_user_id";
            $userVoteSelect = "CASE WHEN ev.eer_award_vote_id IS NOT NULL THEN 1 ELSE 0 END as has_voted";
            $params['current_user_id'] = $currentUserId;
        }

        $sql = "
        SELECT 
            e.employee_id,
            e.full_name as employee_name,
            e.department,
            e.position,
            ah.month_year,
            ah.status,
            COALESCE(ah.vote_count, 0) as votes,
            $userVoteSelect,
            COALESCE(
                ah.performance_score,
                (
                    SELECT pr2.final_rating_percent
                    FROM pm_reports pr2
                    WHERE pr2.employee_id = e.employee_id
                    ORDER BY pr2.period_end DESC
                    LIMIT 1
                ),
                (
                    SELECT pa2.overall_score
                    FROM pm_appraisals pa2
                    WHERE pa2.employee_id = e.employee_id
                    ORDER BY pa2.review_date DESC
                    LIMIT 1
                ),
                0
            ) as performance_score,
            COALESCE(SUM(CASE WHEN er.receiver_id = e.employee_id THEN er.points ELSE 0 END), 0) as recognition_total,
            COALESCE(COUNT(DISTINCT er.eer_recognition_id), 0) as recognition_count,
            COALESCE(ah.points, 0) as award_points,
            COALESCE(ah.reason, '') as nomination_reason,
            ah.eer_award_history_id
        FROM eer_award_history ah
        INNER JOIN employees e ON e.employee_id = ah.employee_id
        LEFT JOIN eer_recognitions er ON e.employee_id = er.receiver_id 
            AND (er.source IS NULL OR er.source != 'performance')
            AND MONTH(er.created_at) = :month
            AND YEAR(er.created_at) = :year
        $userVoteJoin
        WHERE ah.month_year = :month_year
            AND ah.award_type = :award_type
            AND ah.nominated_by IS NOT NULL
        GROUP BY e.employee_id, ah.eer_award_history_id
        ORDER BY votes DESC, recognition_total DESC, performance_score DESC";
        
        return $this->execute($sql, $params)->fetchAll();
    }

    /**
     * Get total points for an employee from all sources
     */
    public function getEmployeeTotalPoints($employeeId)
    {
        $sql = "
        SELECT
            " . (int)$employeeId . " as employee_id,
            COALESCE(SUM(CASE WHEN source = 'recognition' THEN points ELSE 0 END), 0) as recognition_points,
            COALESCE(SUM(CASE WHEN source = 'performance' THEN points ELSE 0 END), 0) as performance_points,
            COALESCE(SUM(CASE WHEN source = 'badge' THEN points ELSE 0 END), 0) as badge_points,
            COALESCE(SUM(CASE WHEN source = 'award' THEN points ELSE 0 END), 0) as award_points,
            COALESCE(SUM(points), 0) as total_points
        FROM (
            SELECT 'recognition' as source, er.points
            FROM eer_recognitions er
            WHERE er.receiver_id = :employeeId
            
            UNION ALL
            
            SELECT 'performance' as source,
                CASE
                    WHEN pr.overall_rating_5 = 5 AND pr.final_rating_percent >= 95 THEN 100
                    WHEN pr.overall_rating_5 >= 4 AND pr.final_rating_percent >= 80 THEN 50
                    WHEN pr.overall_rating_5 >= 3 AND pr.final_rating_percent >= 70 THEN 25
                    ELSE 0
                END as points
            FROM pm_reports pr
            WHERE pr.employee_id = :employeeId
            
            UNION ALL
            
            SELECT 'performance' as source,
                CASE
                    WHEN pa.overall_score >= 98 THEN 100
                    WHEN pa.overall_score >= 95 THEN 100
                    WHEN pa.overall_score >= 85 THEN 50
                    WHEN pa.overall_score >= 70 THEN 25
                    ELSE 0
                END as points
            FROM pm_appraisals pa
            WHERE pa.employee_id = :employeeId
            
            UNION ALL
            
            SELECT 'badge' as source, b.points_value
            FROM employee_badges eb
            JOIN eer_badges b ON eb.badge_id = b.eer_badge_id
            WHERE eb.employee_id = :employeeId
            
            UNION ALL
            
            SELECT 'award' as source,
                CASE
                    WHEN ah.points > 0 THEN ah.points
                    ELSE COALESCE(ah.vote_count * 5, 0)
                END as points
            FROM eer_award_history ah
            WHERE ah.employee_id = :employeeId
        ) all_points";
        
        return $this->execute($sql, ['employeeId' => $employeeId])->fetch();
    }

    /**
     * Get badge recommendations based on employee performance and achievements
     */
    public function getBadgeRecommendations($employeeId)
    {
        $sql = "
        SELECT DISTINCT
            b.eer_badge_id,
            b.name,
            b.description,
            b.icon,
            b.tier,
            b.points_value,
            b.category,
            b.requirement_type,
            b.requirement_value,
            CASE 
                WHEN eb.employee_badge_id IS NOT NULL THEN 'owned'
                WHEN (
                    SELECT COUNT(*) FROM eer_recognitions er 
                    WHERE er.receiver_id = :employeeId
                ) >= COALESCE(b.requirement_value, 5) THEN 'eligible'
                ELSE 'not_eligible'
            END as status,
            eb.awarded_at as owned_since
        FROM eer_badges b
        LEFT JOIN employee_badges eb ON b.eer_badge_id = eb.badge_id 
            AND eb.employee_id = :employeeId
        WHERE b.status = 'active'
        ORDER BY b.tier, b.points_value DESC";
        
        return $this->execute($sql, ['employeeId' => $employeeId])->fetchAll();
    }

    /**
     * Get department-based leaderboard
     */
    public function getDepartmentLeaderboard($department = null, $limit = 10)
    {
        $sql = "
        SELECT 
            e.employee_id,
            e.full_name as employee_name,
            e.department,
            e.position,
            COALESCE(SUM(
                CASE WHEN er.receiver_id = e.employee_id THEN er.points ELSE 0 END +
                CASE WHEN pr.employee_id = e.employee_id THEN 
                    CASE
                        WHEN pr.overall_rating_5 = 5 AND pr.final_rating_percent >= 95 THEN 100
                        WHEN pr.overall_rating_5 >= 4 AND pr.final_rating_percent >= 80 THEN 50
                        WHEN pr.overall_rating_5 >= 3 AND pr.final_rating_percent >= 70 THEN 25
                        ELSE 0
                    END
                ELSE 0 END
            ), 0) as total_points,
            RANK() OVER (PARTITION BY e.department ORDER BY COALESCE(SUM(
                CASE WHEN er.receiver_id = e.employee_id THEN er.points ELSE 0 END +
                CASE WHEN pr.employee_id = e.employee_id THEN 
                    CASE
                        WHEN pr.overall_rating_5 = 5 AND pr.final_rating_percent >= 95 THEN 100
                        WHEN pr.overall_rating_5 >= 4 AND pr.final_rating_percent >= 80 THEN 50
                        WHEN pr.overall_rating_5 >= 3 AND pr.final_rating_percent >= 70 THEN 25
                        ELSE 0
                    END
                ELSE 0 END
            ), 0) DESC) as dept_rank
        FROM employees e
        LEFT JOIN eer_recognitions er ON e.employee_id = er.receiver_id
        LEFT JOIN pm_reports pr ON e.employee_id = pr.employee_id
        " . ($department ? "WHERE e.department = :department" : "") . "
        GROUP BY e.employee_id
        ORDER BY total_points DESC
        LIMIT " . (int)$limit;
        
        $params = $department ? ['department' => $department] : [];
        return $this->execute($sql, $params)->fetchAll();
    }
}
