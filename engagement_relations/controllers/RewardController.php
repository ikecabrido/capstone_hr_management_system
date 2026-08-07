<?php
namespace App\Controllers;

use App\Models\Reward;

class RewardController
{
    private $reward;

    public function __construct()
    {
        $this->reward = new Reward();
    }

    public function index()
    {
        return $this->reward->all();
    }

    public function store(array $data)
    {
        return $this->reward->create($data);
    }

    public function categorizeRewards($category)
    {
        return $this->reward->getByCategory($category);
    }

    /**
     * Get employees eligible for rewards based on performance scores
     * Employees with high performance ratings (4+ out of 5 or 80%+) qualify
     */
    public function getPerformanceBasedCandidates()
    {
        $db = \Database::getInstance()->getConnection();
        
                $sql = "SELECT DISTINCT 
                                        e.employee_id,
                                        e.full_name as employee_name,
                                        e.department,
                                        AVG(pr.final_rating_percent) as avg_final_rating_percent,
                                        MAX(pr.final_grade) as final_grade,
                                        AVG(pr.overall_rating_5) as overall_rating_5,
                                        pr.evaluation_period,
                                        MAX(pr.period_end) as period_end,
                                        SUM(
                                            CASE
                                                WHEN pr.overall_rating_5 = 5 AND pr.final_rating_percent >= 95 THEN 100
                                                WHEN pr.overall_rating_5 >= 4 AND pr.final_rating_percent >= 80 THEN 50
                                                WHEN pr.overall_rating_5 >= 3 AND pr.final_rating_percent >= 70 THEN 25
                                                ELSE 0
                                            END
                                        ) as total_performance_points
                                FROM (
                                    SELECT 
                                        pr.employee_id,
                                        pr.final_rating_percent,
                                        pr.overall_rating_5,
                                        pr.final_grade,
                                        pr.evaluation_period,
                                        pr.period_end
                                    FROM pm_reports pr
                                    WHERE pr.final_rating_percent >= 80 
                                        AND pr.overall_rating_5 >= 4
                                        AND pr.period_end >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                                    UNION ALL
                                    SELECT
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
                                        pa.review_period as evaluation_period,
                                        pa.review_date as period_end
                                    FROM pm_appraisals pa
                                    WHERE pa.overall_score >= 80
                                        AND pa.review_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                                ) pr
                                INNER JOIN employees e ON pr.employee_id = e.employee_id
                                GROUP BY pr.employee_id
                                ORDER BY avg_final_rating_percent DESC, overall_rating_5 DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get top performers (Outstanding grade) - eligible for premium rewards
     */
    public function getTopPerformers()
    {
        $db = \Database::getInstance()->getConnection();
        
        $sql = "SELECT DISTINCT 
                    e.employee_id,
                    e.full_name as employee_name,
                    e.department,
                    pr.final_rating_percent,
                    pr.final_grade,
                    pr.kpi_score,
                    pr.attendance_score,
                    pr.period_end
                FROM (
                    SELECT 
                        pr.employee_id,
                        pr.final_rating_percent,
                        pr.final_grade,
                        pr.kpi_score,
                        pr.attendance_score,
                        pr.period_end
                    FROM pm_reports pr
                    WHERE pr.final_grade IN ('Outstanding', 'Very Satisfactory', 'Excellent')
                        AND pr.final_rating_percent >= 80
                        AND pr.period_end >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                    UNION ALL
                    SELECT
                        pa.employee_id,
                        pa.overall_score as final_rating_percent,
                        CASE
                            WHEN pa.overall_score >= 98 THEN 'Excellent'
                            WHEN pa.overall_score >= 95 THEN 'Outstanding'
                            WHEN pa.overall_score >= 85 THEN 'Very Satisfactory'
                            WHEN pa.overall_score >= 70 THEN 'Satisfactory'
                            ELSE 'Fair'
                        END as final_grade,
                        NULL as kpi_score,
                        NULL as attendance_score,
                        pa.review_date as period_end
                    FROM pm_appraisals pa
                    WHERE pa.overall_score >= 80
                        AND pa.review_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                ) pr
                INNER JOIN employees e ON pr.employee_id = e.employee_id
                ORDER BY pr.final_rating_percent DESC, pr.period_end DESC
                LIMIT 10";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get improvement candidates (Satisfactory or lower) - eligible for coaching rewards/development programs
     */
    public function getImprovementCandidates()
    {
        $db = \Database::getInstance()->getConnection();
        
        $sql = "SELECT DISTINCT 
                    e.employee_id,
                    e.full_name as employee_name,
                    e.department,
                    pr.final_rating_percent,
                    pr.final_grade,
                    pr.attendance_score,
                    pr.remarks,
                    pr.period_end
                FROM (
                    SELECT 
                        pr.employee_id,
                        pr.final_rating_percent,
                        pr.final_grade,
                        pr.attendance_score,
                        pr.remarks,
                        pr.period_end
                    FROM pm_reports pr
                    WHERE pr.final_grade IN ('Fair', 'Satisfactory')
                        AND pr.period_end >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
                    UNION ALL
                    SELECT
                        pa.employee_id,
                        pa.overall_score as final_rating_percent,
                        CASE
                            WHEN pa.overall_score >= 98 THEN 'Excellent'
                            WHEN pa.overall_score >= 95 THEN 'Outstanding'
                            WHEN pa.overall_score >= 85 THEN 'Very Satisfactory'
                            WHEN pa.overall_score >= 70 THEN 'Satisfactory'
                            ELSE 'Fair'
                        END as final_grade,
                        NULL as attendance_score,
                        pa.comments as remarks,
                        pa.review_date as period_end
                    FROM pm_appraisals pa
                    WHERE pa.overall_score < 80
                        AND pa.review_date >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
                ) pr
                INNER JOIN employees e ON pr.employee_id = e.employee_id
                ORDER BY pr.final_rating_percent ASC, pr.period_end DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Calculate points to award based on performance
     */
    public function calculatePerformancePoints($finalRating, $overall_rating_5)
    {
        if ($overall_rating_5 == 5 && $finalRating >= 95) {
            return 100; // Excellent - 100 points
        } elseif ($overall_rating_5 >= 4 && $finalRating >= 80) {
            return 50; // Very Good - 50 points
        } elseif ($overall_rating_5 >= 3 && $finalRating >= 70) {
            return 25; // Good - 25 points
        }
        return 0; // Below threshold
    }
}
