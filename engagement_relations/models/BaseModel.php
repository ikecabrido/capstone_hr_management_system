<?php
namespace App\Models;

use Database;
use PDO;

class BaseModel
{
    protected $db;
    protected $pdo;

    public function __construct(PDO $connection = null)
    {
        if ($connection !== null) {
            $this->db = $connection;
            $this->pdo = $connection;
        } else {
            $this->db = Database::getInstance()->getConnection();
            $this->pdo = $this->db;
        }
    }

    protected function execute($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    protected function getEmployeeNameSql($employeeAlias = 'e', $alias = 'employee_name')
    {
        $columns = $this->execute('SHOW COLUMNS FROM employees')->fetchAll(PDO::FETCH_ASSOC);
        $columnNames = array_column($columns, 'Field');

        if (in_array('name', $columnNames, true)) {
            return "$employeeAlias.name AS $alias";
        }

        if (in_array('first_name', $columnNames, true) && in_array('last_name', $columnNames, true)) {
            return "CONCAT($employeeAlias.first_name, ' ', $employeeAlias.last_name) AS $alias";
        }

        if (in_array('first_name', $columnNames, true)) {
            return "$employeeAlias.first_name AS $alias";
        }

        if (in_array('last_name', $columnNames, true)) {
            return "$employeeAlias.last_name AS $alias";
        }

        return "'' AS $alias";
    }
}
