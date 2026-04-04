<?php
namespace App\Models;

class Group extends BaseModel
{
    public function createGroup($name, $description = null, $createdBy = null)
    {
        $sql = 'INSERT INTO eer_groups (name, created_by, created_at) 
                VALUES (:name, :created_by, NOW())';

        $this->execute($sql, [
            'name' => $name,
            'created_by' => $createdBy
        ]);

        return $this->db->lastInsertId();
    }

    public function getGroups()
    {
        $sql = 'SELECT * FROM eer_groups';
        return $this->execute($sql)->fetchAll();
    }

    public function deleteGroup($groupId)
    {
        $sql = 'DELETE FROM eer_groups WHERE eer_group_id = :group_id';
        $this->execute($sql, ['group_id' => $groupId]);
    }
}