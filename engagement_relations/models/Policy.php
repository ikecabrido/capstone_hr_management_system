<?php
namespace App\Models;

class Policy extends BaseModel
{
    public function getAllPolicies()
    {
        $sql = 'SELECT * FROM eer_policies';
        return $this->execute($sql)->fetchAll();
    }

    public function postPolicy($title, $content, $created_by)
    {
        $sql = 'INSERT INTO eer_policies (title, content, created_by, created_at) 
                VALUES (:title, :content, :created_by, NOW())';
        $this->execute($sql, ['title' => $title, 'content' => $content, 'created_by' => $created_by]);
        return $this->db->lastInsertId();
    }

    public function deletePolicy($id)
    {
        $sql = 'DELETE FROM eer_policies WHERE id = :id';
        return $this->execute($sql, ['id' => $id]);
    }
}