<?php
require_once __DIR__ . "/../../auth/database.php";

class Certification {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllCertifications() {
        $stmt = $this->db->prepare("
            SELECT c.*,
                   e.full_name as employee_name,
                   co.title as course_title,
                   i.full_name as issued_by_name
            FROM ld_certification c
            JOIN employees e ON c.employee_user_id = e.user_id
            LEFT JOIN ld_courses co ON c.ld_courses_id = co.ld_courses_id
            LEFT JOIN users i ON c.issued_by_user_id = i.id
            ORDER BY c.issued_date DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCertificationsByEmployee($employeeId) {
        $stmt = $this->db->prepare("
            SELECT c.*,
                   co.title as course_title,
                   i.full_name as issued_by_name
            FROM ld_certification c
            LEFT JOIN ld_courses co ON c.ld_courses_id = co.ld_courses_id
            LEFT JOIN users i ON c.issued_by_user_id = i.id
            WHERE c.employee_user_id = ?
            ORDER BY c.issued_date DESC
        ");
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function issueCertification($data) {
        $stmt = $this->db->prepare("INSERT INTO ld_certification (employee_user_id, ld_courses_id, name, issued_date, expiry_date, issued_by_user_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            (int)$data['employee_id'],
            (int)$data['course_id'],
            $data['certification_name'],
            $data['issued_date'],
            $data['expiry_date'],
            (int)$data['issued_by'],
            $data['status'] ?? 'active'
        ]);
    }

    public function revokeCertification($id) {
        $stmt = $this->db->prepare("UPDATE ld_certification SET status = 'revoked' WHERE ld_certification_id = ?");
        return $stmt->execute([$id]);
    }

    public function updateCertification($data) {
        $stmt = $this->db->prepare("UPDATE ld_certification SET employee_user_id = ?, ld_courses_id = ?, name = ?, issued_date = ?, expiry_date = ?, issued_by_user_id = ?, status = ? WHERE ld_certification_id = ?");
        return $stmt->execute([
            (int)$data['employee_id'],
            (int)$data['course_id'],
            $data['certification_name'],
            $data['issued_date'],
            $data['expiry_date'] ?: null,
            (int)$data['issued_by'],
            $data['status'] ?? 'active',
            (int)$data['id']
        ]);
    }

    public function getCertificationById($id) {
        $stmt = $this->db->prepare("SELECT * FROM ld_certification WHERE ld_certification_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>