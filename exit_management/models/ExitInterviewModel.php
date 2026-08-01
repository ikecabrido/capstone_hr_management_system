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
     * Get all interviews with optional status filter and pagination support
     */
    public function getAllInterviews(string $status = null, int $page = 1, int $limit = 10, string $search = ''): array
    {
        $offset = ($page - 1) * $limit;

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

        $countSql = "
            SELECT COUNT(*) as total
            FROM exit_interviews ei
            JOIN employees e ON ei.employee_id = e.employee_id
            LEFT JOIN users u ON ei.interviewer_id = u.id
        ";

        $params = [];
        $whereClause = "";

        if ($status && $status !== 'all') {
            $whereClause = " WHERE ei.status = :status";
            $params['status'] = $status;
        }

        // Add search condition if provided
        if (!empty($search)) {
            $searchCondition = $whereClause ? " AND" : " WHERE";
            $searchCondition .= " (e.full_name LIKE :search0 OR u.full_name LIKE :search1 OR ei.location LIKE :search2)";
            $whereClause .= $searchCondition;
            $searchParam = "%$search%";
            $params['search0'] = $searchParam;
            $params['search1'] = $searchParam;
            $params['search2'] = $searchParam;
        }

        $sql .= $whereClause . ' ORDER BY ei.scheduled_date DESC LIMIT :limit OFFSET :offset';
        $countSql .= $whereClause;

        // Get total count
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Get paginated results
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $totalCount,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($totalCount / $limit)
        ];
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

    /**
     * Archive interview
     */
    public function archiveInterview(int $interviewId, string $archiveReason = 'Manual archive'): bool
    {
        // Get the full interview data
        $stmt = $this->db->prepare("SELECT * FROM exit_interviews WHERE id = ?");
        $stmt->execute([$interviewId]);
        $interview = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$interview) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            // Insert into exit_archive
            $archiveStmt = $this->db->prepare("
                INSERT INTO exit_archive (
                    archive_type, original_id, employee_id, title, description, content,
                    status, original_created_by, archived_by, archive_reason, archive_data
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $title = "Exit Interview - Employee " . ($interview['employee_id'] ?? 'Unknown');
            $description = "Archived exit interview record";
            $content = json_encode($interview);
            $archivedBy = $_SESSION['user']['id'] ?? 1;

            $archiveStmt->execute([
                'interview',
                $interviewId,
                $interview['employee_id'],
                $title,
                $description,
                $content,
                $interview['status'],
                $interview['created_by'],
                $archivedBy,
                $archiveReason,
                $content
            ]);

            // Delete from exit_interviews
            $deleteStmt = $this->db->prepare("DELETE FROM exit_interviews WHERE id = ?");
            $deleteStmt->execute([$interviewId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Interview archive error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Unarchive interview
     */
    public function unarchiveInterview(int $interviewId): bool
    {
        // Get archived data
        $stmt = $this->db->prepare("SELECT * FROM exit_archive WHERE archive_type = 'interview' AND original_id = ?");
        $stmt->execute([$interviewId]);
        $archive = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$archive) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            // Decode the archived data
            $interviewData = json_decode($archive['archive_data'], true);
            if (!$interviewData) {
                return false;
            }

            // Insert back into exit_interviews
            $insertStmt = $this->db->prepare("
                INSERT INTO exit_interviews (
                    id, employee_id, interviewer_id, scheduled_date, status,
                    notes, feedback_summary, created_by, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $insertStmt->execute([
                $interviewData['id'],
                $interviewData['employee_id'],
                $interviewData['interviewer_id'],
                $interviewData['scheduled_date'],
                $interviewData['status'] ?? 'scheduled',
                $interviewData['notes'],
                $interviewData['feedback_summary'],
                $interviewData['created_by'],
                $interviewData['created_at'],
                date('Y-m-d H:i:s')
            ]);

            // Update archive record to mark as restored
            $updateStmt = $this->db->prepare("
                UPDATE exit_archive
                SET restored = 1, restored_by = ?, restored_at = NOW()
                WHERE id = ?
            ");
            $restoredBy = $_SESSION['user']['id'] ?? 1;
            $updateStmt->execute([$restoredBy, $archive['id']]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Interview unarchive error: " . $e->getMessage());
            return false;
        }
    }
}