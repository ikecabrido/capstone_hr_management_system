<?php

require_once 'ExitManagementModel.php';

class ExitInterviewModel extends ExitManagementModel
{
    /**
     * Schedule an exit interview
     */
    public function scheduleInterview(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO exit_interviews (employee_id, interviewer_id, scheduled_date,
                                       scheduled_time, location, notes, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 'scheduled', NOW())
        ");

        $stmt->execute([
            $data['employee_id'],
            $data['interviewer_id'],
            $data['scheduled_date'],
            $data['scheduled_time'],
            $data['location'] ?? 'Virtual',
            $data['notes'] ?? null
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Get exit interview by ID
     */
    public function getInterviewById(int $interviewId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT ei.*, u.full_name, u.username as emp_id,
                   i.full_name as interviewer_first
            FROM exit_interviews ei
            JOIN users u ON ei.employee_id = u.id
            LEFT JOIN users i ON ei.interviewer_id = i.id
            WHERE ei.id = ?
        ");
        $stmt->execute([$interviewId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get interviews by employee ID
     */
    public function getInterviewsByEmployee(int $employeeId): array
    {
        $stmt = $this->db->prepare("
            SELECT ei.*, i.full_name as interviewer_first
            FROM exit_interviews ei
            LEFT JOIN users i ON ei.interviewer_id = i.id
            WHERE ei.employee_id = ?
            ORDER BY ei.scheduled_date DESC
        ");
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Submit interview feedback
     */
    public function submitFeedback(int $interviewId, array $feedback): bool
    {
        // Start transaction
        $this->db->beginTransaction();

        try {
            // Update interview status
            $stmt = $this->db->prepare("
                UPDATE exit_interviews
                SET status = 'completed', completed_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$interviewId]);

            // Insert feedback
            $stmt = $this->db->prepare("
                INSERT INTO exit_interview_feedback (interview_id, overall_satisfaction,
                                                   work_environment_rating, management_rating,
                                                   compensation_rating, work_life_balance_rating,
                                                   reason_for_leaving, suggestions, would_recommend,
                                                   additional_comments, submitted_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $interviewId,
                $feedback['overall_satisfaction'],
                $feedback['work_environment_rating'],
                $feedback['management_rating'],
                $feedback['compensation_rating'],
                $feedback['work_life_balance_rating'],
                $feedback['reason_for_leaving'],
                $feedback['suggestions'] ?? null,
                $feedback['would_recommend'],
                $feedback['additional_comments'] ?? null
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Get feedback by interview ID
     */
    public function getFeedbackByInterview(int $interviewId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM exit_interview_feedback WHERE interview_id = ?
        ");
        $stmt->execute([$interviewId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get all scheduled interviews
     */
    public function getScheduledInterviews(): array
    {
        $stmt = $this->db->query("
            SELECT ei.*, e.first_name, e.last_name, e.employee_id as emp_id,
                   i.first_name as interviewer_first, i.last_name as interviewer_last
            FROM exit_interviews ei
            JOIN employees e ON ei.employee_id = e.id
            LEFT JOIN employees i ON ei.interviewer_id = i.id
            WHERE ei.status = 'scheduled'
            ORDER BY ei.scheduled_date ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}