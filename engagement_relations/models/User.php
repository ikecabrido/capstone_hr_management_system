<?php
namespace App\Models;

class User extends BaseModel
{
    public function all()
    {
        return $this->execute('SELECT * FROM users')->fetchAll();
    }

    public function find($id)
    {
        return $this->execute('SELECT * FROM users WHERE user_id = :id', ['id' => $id])->fetch();
    }

    public function create($data)
    {
        $sql = 'INSERT INTO users (employee_id, username, email, password, full_name, role, status, theme) VALUES (:employee_id, :username, :email, :password, :full_name, :role, :status, :theme)';
        $this->execute($sql, $data);
        return $this->db->lastInsertId();
    }
}
