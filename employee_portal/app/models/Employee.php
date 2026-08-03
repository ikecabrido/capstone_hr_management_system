<?php

/**
 * Employee Model for Time & Attendance System
 * Manages employee data, credentials, and information
 */

require_once __DIR__ . '/../config/Database.php';

class Employee
{
    private $conn;
    private $table = "employees";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    public function getByUserId($user_id)
    {
        $query = "
        SELECT 
            e.*,
            u.username,
            u.role,
            u.is_admin,
            p.title AS position
        FROM {$this->table} e
        JOIN users u 
            ON e.user_id = u.id
        LEFT JOIN positions p
            ON e.position_id = p.id
        WHERE e.user_id = :user_id
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':user_id' => $user_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getById($employee_id)
    {
        die;
        $query = "SELECT e.*, u.username, u.role 
                  FROM " . $this->table . " e
                  LEFT JOIN users u ON e.user_id = u.id
                  WHERE e.employee_no = :employee_no LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_no', $employee_no);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getAll($limit = 100, $offset = 0)
    {
        $query = "
        SELECT e.*, u.username, u.role
        FROM {$this->table} e
        LEFT JOIN users u ON e.user_id = u.id
        ORDER BY e.last_name
        LIMIT :limit OFFSET :offset
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getFullName($employee_no)
    {
        $query = "SELECT full_name 
                  FROM " . $this->table . " 
                  WHERE employee_no = :employee_no LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_no', $employee_no);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['full_name'] ?? 'Unknown';
    }
    public function update($employee_no, $data)
    {
        $query = "UPDATE " . $this->table . " SET ";
        $fields = [];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }

        $query .= implode(", ", $fields) . " WHERE employee_no = :employee_no";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_no', $employee_no);

        foreach ($data as $key => $value) {
            $stmt->bindParam(':' . $key, $data[$key]);
        }

        return $stmt->execute();
    }
    public function getTotalCount($status = 'Active')
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE employment_status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
    public function all()
    {
        $query = "
        SELECT *, first_name, last_name
        FROM " . $this->table . "
        ORDER BY last_name ASC
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findByUserId($user_id)
    {
        $query = "SELECT * FROM $this->table WHERE user_id = :user_id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $user_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getByEmployeeId($id)
    {
        $query = "SELECT e.*, u.username, u.role
              FROM " . $this->table . " e
              LEFT JOIN users u ON e.user_id = u.id
              WHERE e.employee_id = :id
              LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getNonAdminEmployees()
    {
        $query = "
        SELECT 
            e.*
        FROM $this->table e
        INNER JOIN users u 
            ON e.user_id = u.id
        WHERE u.is_admin = 0
        ORDER BY e.last_name ASC
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function find($employeeId)
    {
        $query = "
        SELECT *
        FROM {$this->table}
        WHERE employee_id = :employee_id
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':employee_id' => $employeeId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function createProfile($data)
    {
        $sql = "
        INSERT INTO {$this->table}
        (
            user_id,
            first_name,
            middle_name,
            last_name,
            suffix,
            gender,
            birth_date,
            birth_place,
            civil_status,
            citizenship,
            religion,
            mobile_no,
            phone_no,
            current_address,
            permanent_address,
            profile_image,
            credentials,
            graduate_level,
            created_by
        )
        VALUES
        (
            :user_id,
            :first_name,
            :middle_name,
            :last_name,
            :suffix,
            :gender,
            :birth_date,
            :birth_place,
            :civil_status,
            :citizenship,
            :religion,
            :mobile_no,
            :phone_no,
            :current_address,
            :permanent_address,
            :profile_image,
            :credentials,
            :graduate_level,
            :created_by
        )
    ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':user_id'           => $data['user_id'],
            ':first_name'        => $data['first_name'],
            ':middle_name'       => $data['middle_name'],
            ':last_name'         => $data['last_name'],
            ':suffix'            => $data['suffix'],
            ':gender'            => $data['gender'],
            ':birth_date'        => $data['birth_date'],
            ':birth_place'       => $data['birth_place'],
            ':civil_status'      => $data['civil_status'],
            ':citizenship'       => $data['citizenship'],
            ':religion'          => $data['religion'],
            ':mobile_no'         => $data['mobile_no'],
            ':phone_no'          => $data['phone_no'],
            ':current_address'   => $data['current_address'],
            ':permanent_address' => $data['permanent_address'],
            ':profile_image'     => $data['profile_image'],
            ':credentials'       => $data['credentials'],
            ':graduate_level'    => $data['graduate_level'],
            ':created_by'        => $data['created_by']
        ]);
    }
    public function getEmployeeListForHR()
    {
        $sql = "
        SELECT 
            e.*,
            u.username,
            u.email
        FROM {$this->table} e
        LEFT JOIN users u
            ON u.id = e.user_id
        ORDER BY e.created_at DESC
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getPositions()
    {
        $query = "
        SELECT 
            id,
            title
        FROM positions
        ORDER BY id ASC
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function hrStore(array $data)
    {
        $sql = "
        UPDATE {$this->table}
        SET
            employee_code = :employee_code,
            department_id = :department_id,
            position_id = :position_id,
            position_title_enum = :position_title_enum,
            employment_status = :employment_status,
            employment_type = :employment_type,
            hire_date = :hire_date,
            regular_date = :regular_date,
            unit_load = :unit_load,
            faculty_notes = :faculty_notes
        WHERE employee_id = :employee_id
    ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':employee_id'         => (int) $data['employee_id'],
            ':employee_code'       => $data['employee_code'],
            ':department_id'       => !empty($data['department_id']) ? $data['department_id'] : null,
            ':position_id'         => !empty($data['position_id']) ? $data['position_id'] : null,
            ':position_title_enum' => !empty($data['position_title_enum']) ? $data['position_title_enum'] : null,
            ':employment_status'   => $data['employment_status'],
            ':employment_type'     => $data['employment_type'],
            ':hire_date'           => $data['hire_date'],
            ':regular_date'        => !empty($data['regular_date']) ? $data['regular_date'] : null,
            ':unit_load'           => $data['unit_load'] !== '' ? $data['unit_load'] : null,
            ':faculty_notes'       => !empty($data['faculty_notes']) ? $data['faculty_notes'] : null,
        ]);
    }
    public function hrStorePending(array $data)
    {
        $sql = "
        UPDATE {$this->table}
        SET
            employee_code = COALESCE(employee_code, :employee_code),
            department_id = COALESCE(department_id, :department_id),
            position_id = COALESCE(position_id, :position_id),
            position_title_enum = COALESCE(position_title_enum, :position_title_enum),
            employment_status = COALESCE(employment_status, :employment_status),
            employment_type = COALESCE(employment_type, :employment_type),
            hire_date = COALESCE(hire_date, :hire_date),
            regular_date = COALESCE(regular_date, :regular_date),
            unit_load = COALESCE(unit_load, :unit_load),
            faculty_notes = COALESCE(faculty_notes, :faculty_notes)
        WHERE employee_id = :employee_id
    ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':employee_id'         => $data['employee_id'],
            ':employee_code'       => $data['employee_code'],
            ':department_id'       => $data['department_id'],
            ':position_id'         => $data['position_id'],
            ':position_title_enum' => $data['position_title_enum'],
            ':employment_status'   => $data['employment_status'],
            ':employment_type'     => $data['employment_type'],
            ':hire_date'           => $data['hire_date'],
            ':regular_date'        => $data['regular_date'],
            ':unit_load'           => $data['unit_load'],
            ':faculty_notes'       => $data['faculty_notes'],
        ]);
    }
    public function hrUpdate(array $data)
    {
        $sql = "
        UPDATE {$this->table}
        SET
            employee_code = :employee_code,
            department_id = :department_id,
            position_id = :position_id,
            position_title_enum = :position_title_enum,
            employment_status = :employment_status,
            employment_type = :employment_type,
            hire_date = :hire_date,
            regular_date = :regular_date,
            unit_load = :unit_load,
            faculty_notes = :faculty_notes
        WHERE employee_id = :employee_id
    ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':employee_id'         => $data['employee_id'],
            ':employee_code'       => $data['employee_code'],
            ':department_id'       => $data['department_id'],
            ':position_id'         => $data['position_id'],
            ':position_title_enum' => $data['position_title_enum'],
            ':employment_status'   => $data['employment_status'],
            ':employment_type'     => $data['employment_type'],
            ':hire_date'           => $data['hire_date'],
            ':regular_date'        => $data['regular_date'],
            ':unit_load'           => $data['unit_load'],
            ':faculty_notes'       => $data['faculty_notes'],
        ]);
    }
}
