<?php
require_once __DIR__ . "/../../auth/database.php";

class Archive {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all archives with user information
     */
    public function getAllArchives() {
        $query = "
            SELECT 
                a.*,
                u.full_name as archived_by_name,
                cu.full_name as created_by_name
            FROM ld_archive a
            LEFT JOIN users u ON a.archived_by = u.id
            LEFT JOIN users cu ON a.original_created_by = cu.id
            WHERE a.restored = FALSE
            ORDER BY a.archived_at DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get archives by type (course or program)
     */
    public function getArchivesByType($type) {
        $query = "
            SELECT 
                a.*,
                u.full_name as archived_by_name
            FROM ld_archive a
            LEFT JOIN users u ON a.archived_by = u.id
            WHERE a.archive_type = ? AND a.restored = FALSE
            ORDER BY a.archived_at DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Archive an item (course, program, or certification)
     */
    public function archiveItem($itemId, $itemType, $userId) {
        try {
            if ($itemType === 'course') {
                $query = "
                    SELECT * FROM ld_courses WHERE ld_courses_id = ?
                ";
            } elseif ($itemType === 'program') {
                $query = "
                    SELECT * FROM ld_training_programs WHERE ld_training_programs_id = ?
                ";
            } elseif ($itemType === 'certification') {
                $query = "
                    SELECT c.*,
                           u.full_name as employee_name,
                           co.title as course_title,
                           i.full_name as issued_by_name
                    FROM ld_certification c
                    LEFT JOIN users u ON c.employee_id = u.id
                    LEFT JOIN ld_courses co ON c.ld_courses_id = co.ld_courses_id
                    LEFT JOIN users i ON c.issued_by_user_id = i.id
                    WHERE c.ld_certification_id = ?
                ";
            } else {
                return ['success' => false, 'message' => 'Invalid item type'];
            }

            $stmt = $this->db->prepare($query);
            $stmt->execute([$itemId]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$item) {
                return ['success' => false, 'message' => 'Item not found'];
            }

            // Determine title for display
            if ($itemType === 'certification') {
                $title = ($item['certification_name'] ?? 'Certification') . ' - ' . ($item['employee_name'] ?? 'Unknown');
                $description = 'Certification issued to ' . ($item['employee_name'] ?? 'Unknown') . ' for ' . ($item['course_title'] ?? 'N/A');
                $createdBy = $item['issued_by'] ?? null;
            } else {
                $title = $item['title'] ?? 'Unknown';
                $description = $item['description'] ?? '';
                $createdBy = $item['created_by'] ?? null;
            }

            // Archive the item
            $archiveQuery = "
                INSERT INTO ld_archive (
                    archive_type,
                    original_id,
                    title,
                    description,
                    original_created_by,
                    archived_by,
                    archive_data
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ";

            $archiveStmt = $this->db->prepare($archiveQuery);
            $archiveStmt->execute([
                $itemType,
                $itemId,
                $title,
                $description,
                $createdBy,
                $userId,
                json_encode($item)
            ]);

            return ['success' => true, 'message' => ucfirst($itemType) . ' archived successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get archive by ID
     */
    public function getArchiveById($archiveId) {
        $query = "
            SELECT 
                a.*,
                u.full_name as archived_by_name,
                cu.full_name as created_by_name
            FROM ld_archive a
            LEFT JOIN users u ON a.archived_by = u.id
            LEFT JOIN users cu ON a.original_created_by = cu.id
            WHERE a.id = ?
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$archiveId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Restore an archived item
     */
    public function restoreArchive($archiveId, $restoredBy) {
        try {
            $archive = $this->getArchiveById($archiveId);
            
            if (!$archive) {
                return ['success' => false, 'message' => 'Archive not found'];
            }

            $data = json_decode($archive['archive_data'], true);
            $archiveType = strtolower($archive['archive_type'] ?? '');

            switch ($archiveType) {
                case 'course':
                    $query = "
                        INSERT INTO ld_courses (
                            ld_courses_id, title, description, instructor, duration_hours,
                            ld_training_programs_id, content_type, status, created_by_user_id
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE status = VALUES(status)
                    ";
                    $params = [
                        $data['id'],
                        $data['title'],
                        $data['description'],
                        $data['instructor'],
                        $data['duration_hours'],
                        $data['training_program_id'],
                        $data['content_type'],
                        'active',
                        $data['created_by']
                    ];
                    break;

                case 'program':
                    $query = "
                        INSERT INTO ld_training_programs (
                            ld_training_programs_id, title, description, trainer, start_date, end_date,
                            max_participants, status, created_by_user_id
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE status = VALUES(status)
                    ";
                    $params = [
                        $data['id'],
                        $data['title'],
                        $data['description'],
                        $data['trainer'],
                        $data['start_date'],
                        $data['end_date'],
                        $data['max_participants'],
                        'active',
                        $data['created_by']
                    ];
                    break;

                case 'certification':
                    $query = "
                        INSERT INTO ld_certification (
                            ld_certification_id, employee_id, ld_courses_id, certification_name, issued_date,
                            expiry_date, issued_by_user_id, status
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE status = VALUES(status)
                    ";
                    $params = [
                        $data['id'],
                        $data['employee_id'],
                        $data['course_id'],
                        $data['certification_name'],
                        $data['issued_date'],
                        $data['expiry_date'],
                        $data['issued_by'],
                        'active'
                    ];
                    break;

                default:
                    return ['success' => false, 'message' => 'Unknown archive type: ' . ($archive['archive_type'] ?? 'null')];
            }

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);

            // Mark archive as restored
            $updateQuery = "
                UPDATE ld_archive
                SET restored = TRUE, restored_by = ?, restored_at = NOW()
                WHERE id = ?
            ";
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->execute([$restoredBy, $archiveId]);

            return ['success' => true, 'message' => ucfirst($archiveType) . ' restored successfully'];

        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get archives by creator
     */
    public function getArchivesByCreator($userId) {
        $query = "
            SELECT 
                a.*,
                u.full_name as archived_by_name
            FROM ld_archive a
            LEFT JOIN users u ON a.archived_by = u.id
            WHERE a.original_created_by = ? AND a.restored = FALSE
            ORDER BY a.archived_at DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete an archive permanently
     */
    public function deleteArchive($archiveId) {
        try {
            $query = "DELETE FROM ld_archive WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$archiveId]);

            return ['success' => true, 'message' => 'Archive deleted successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get archive statistics
     */
    public function getStatistics() {
        $query = "
            SELECT
                archive_type,
                COUNT(*) as total_archived,
                SUM(CASE WHEN restored = TRUE THEN 1 ELSE 0 END) as restored_count
            FROM ld_archive
            GROUP BY archive_type
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
