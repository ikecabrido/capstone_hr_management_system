<?php

require_once "database.php";

class User
{

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByUsername($username)
    {

        $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);

        return $stmt->fetch();
    }
    public function findById($id)
    {
        $sql = "SELECT * FROM users WHERE user_id = :id LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }
    public function updateProfile($id, $full_name)
    {
        $sql = "UPDATE users SET full_name = :full_name WHERE user_id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'full_name' => $full_name,
            'id' => $id
        ]);
    }
    public function updatePassword($id, $password)
    {
        $sql = "UPDATE users SET password = :password WHERE user_id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'id' => $id
        ]);
    }
}
