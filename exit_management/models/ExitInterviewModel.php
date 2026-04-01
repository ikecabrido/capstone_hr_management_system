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
     * Update an exit interview
     */
    public function updateInterview(int $interviewId, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE exit_interviews
            SET employee_id = ?, interviewer_id = ?, scheduled_date = ?,
                scheduled_time = ?, location = ?, notes = ?, updated_at = NOW()
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['employee_id'],
            $data['interviewer_id'],
            $data['scheduled_date'],
            $data['scheduled_time'],
            $data['location'] ?? 'Virtual',
            $data['notes'] ?? null,
            $interviewId
        ]);
    }

    /**
     * Get exit interview by ID
     */
    public function getInterviewById(int $interviewId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT ei.*, e.full_name, e.employee_id as emp_id,
                   u.full_name as interviewer_name
            FROM exit_interviews ei
            JOIN employees e ON ei.employee_id = e.employee_id
            LEFT JOIN users u ON ei.interviewer_id = u.id
            WHERE ei.id = ?
        ");
        $stmt->execute([$interviewId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get interviews by employee ID
     */
    public function getInterviewsByEmployee(string $employeeId): array
    {
        $stmt = $this->db->prepare("
            SELECT ei.*, i.full_name as interviewer_name
            FROM exit_interviews ei
            LEFT JOIN users i ON ei.interviewer_id = i.id
            WHERE ei.employee_id = ?
            ORDER BY ei.scheduled_date DESC
        ");
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all interviews with optional status filter
     */
    public function getAllInterviews(string $status = null): array
    {
        $sql = "
            SELECT 
                ei.id,
                ei.employee_id,
                ei.interviewer_id,
                ei.scheduled_date,
                ei.scheduled_time,
                ei.location,
                ei.notes,
                ei.status,
                ei.created_at,
                ei.updated_at,
                e.full_name as employee_name,
                u.full_name as interviewer_name
            FROM exit_interviews ei
            JOIN employees e ON ei.employee_id = e.employee_id
            LEFT JOIN users u ON ei.interviewer_id = u.id
        ";

        if ($status && $status !== 'all') {
            $sql .= " WHERE ei.status = ?";
            $stmt = $this->db->prepare($sql . " ORDER BY ei.scheduled_date DESC");
            $stmt->execute([$status]);
        } else {
            $stmt = $this->db->query($sql . " ORDER BY ei.scheduled_date DESC");
        }

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
            SELECT ei.*, e.full_name, e.employee_id as emp_id,
                   CONCAT(u.first_name, ' ', u.last_name) as interviewer_name
            FROM exit_interviews ei
            JOIN employees e ON ei.employee_id = e.employee_id
            LEFT JOIN users u ON ei.interviewer_id = u.id
            WHERE ei.status = 'scheduled'
            ORDER BY ei.scheduled_date ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update interview status
     */
    public function updateInterviewStatus(int $interviewId, string $status): bool
    {
        $stmt = $this->db->prepare("
            UPDATE exit_interviews
            SET status = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$status, $interviewId]);
    }
}