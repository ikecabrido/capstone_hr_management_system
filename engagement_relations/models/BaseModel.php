<?php
namespace App\Models;

use Database;
use PDO;

class BaseModel
{
    protected $db;

    public function __construct(PDO $connection = null)
    {
        if ($connection !== null) {
            $this->db = $connection;
        } else {
            $this->db = Database::getInstance()->getConnection();
        }
    }

    protected function execute($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
