<?php
namespace App\Models;

class Employee extends BaseModel
{
    public function all()
    {
        return $this->execute('SELECT * FROM employees')->fetchAll();
    }

    public function find($id)
    {
        return $this->execute('SELECT * FROM employees WHERE employee_id = :id', ['id' => $id])->fetch();
    }

    public function findByNameOrEmail($name, $email)
    {
        $sql = 'SELECT * FROM employees WHERE name = :name OR email = :email LIMIT 1';
        return $this->execute($sql, ['name' => $name, 'email' => $email])->fetch();
    }

    public function create($data)
    {
        $sql = 'INSERT INTO employees (name, department, position, email, phone, created_at) VALUES (:name, :department, :position, :email, :phone, NOW())';
        $this->execute($sql, $data);
        return $this->db->lastInsertId();
    }
}
