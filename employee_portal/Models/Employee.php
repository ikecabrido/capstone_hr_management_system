<?php
require_once "../app/Core/Database.php";

class Employee extends Database
{
    protected $table = 'employees';

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare("
        INSERT INTO {$this->table}
        (
            user_id,
            employee_id,
            employee_type,
            department,
            position,
            campus,
            employment_status,
            date_hired,
            profile_image,
            cover_image
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

        return $stmt->execute([
            $data['user_id'],
            $data['employee_id'],
            $data['employee_type'],
            $data['department'],
            $data['position'],
            $data['campus'],
            $data['employment_status'],
            $data['date_hired'],
            $data['profile_image'],
            $data['cover_image']
        ]);
    }

    public function findByUserId($userId)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM {$this->table} WHERE user_id = ? LIMIT 1
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function all()
    {
        $stmt = $this->conn->prepare("
            SELECT 
                u.id AS user_id,
                e.id AS employee_id,
                u.name,
                u.email,
                u.isActive,
                e.employee_id AS emp_code,
                e.employee_type,
                e.department,
                e.position,
                e.campus,
                e.employment_status,
                e.date_hired
            FROM users u
            INNER JOIN {$this->table} e ON e.user_id = u.id
            ORDER BY u.id DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findFullProfileByUserId($userId)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                u.id AS user_id,
                u.name,
                u.email,
                u.isActive,
                e.id AS employee_id,
                e.employee_id AS emp_code,
                e.employee_type,
                e.department,
                e.position,
                e.campus,
                e.employment_status,
                e.date_hired,
                e.profile_image,
                e.cover_image
            FROM users u
            LEFT JOIN {$this->table} e ON e.user_id = u.id
            WHERE u.id = ?
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($userId, $data)
    {
        $stmt = $this->conn->prepare("
        UPDATE {$this->table} SET
            employee_id = ?,
            employee_type = ?,
            department = ?,
            position = ?,
            campus = ?,
            employment_status = ?,
            date_hired = ?,
            profile_image = ?,
            cover_image = ?
        WHERE user_id = ?
    ");

        return $stmt->execute([
            $data['employee_id'],
            $data['employee_type'],
            $data['department'],
            $data['position'],
            $data['campus'],
            $data['employment_status'],
            $data['date_hired'],
            $data['profile_image'] ?? null,
            $data['cover_image'] ?? null,
            $userId
        ]);
    }
}
