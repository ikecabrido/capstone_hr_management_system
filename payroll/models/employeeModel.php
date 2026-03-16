<?php

class EmployeeModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getActiveEmployees(): array
    {
        $stmt = $this->db->query("
            SELECT e.*, pos.title as position, et.name as employment_type
            FROM employees e
            LEFT JOIN positions pos ON e.position_id = pos.id
            LEFT JOIN employment_types et ON e.employment_type_id = et.id
            WHERE e.status = 'active'
            ORDER BY e.last_name, e.first_name
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCount(): int
    {
        $stmt = $this->db->query("
            SELECT COUNT(*) as total 
            FROM employees 
            WHERE status = 'active'
        ");
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
