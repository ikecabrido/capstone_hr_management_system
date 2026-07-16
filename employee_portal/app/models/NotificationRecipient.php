<?php

require_once __DIR__ . '/../config/Database.php';

class NotificationRecipient
{
    private $conn;
    private $table = "ep_notification_recipients";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createRecipients($notificationId, array $employeeIds)
    {
        $query = "INSERT INTO {$this->table}
                (
                    notification_id,
                    employee_id
                )
              VALUES
                (
                    :notification_id,
                    :employee_id
                )";

        $stmt = $this->conn->prepare($query);

        foreach ($employeeIds as $employeeId) {

            if (!$stmt->execute([
                ':notification_id' => $notificationId,
                ':employee_id'     => $employeeId
            ])) {

                throw new Exception(
                    implode(' | ', $stmt->errorInfo())
                );
            }

            // Verify that a row was actually inserted
            if ($stmt->rowCount() !== 1) {
                throw new Exception(
                    "Failed to insert recipient ID {$employeeId}."
                );
            }
        }

        return true;
    }

    public function getEmployeeIdsByNotification($notificationId)
    {
        $query = "SELECT employee_id
              FROM {$this->table}
              WHERE notification_id = :notification_id";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            ':notification_id' => $notificationId
        ]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function deleteRecipients($notificationId)
    {
        $query = "DELETE FROM {$this->table}
              WHERE notification_id = :notification_id";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':notification_id' => $notificationId
        ]);
    }

    public function getRecipients($notificationId)
    {
        $query = "SELECT
                e.employee_no,
                e.full_name,
                e.department
              FROM ep_notification_recipients r
              INNER JOIN employees e
                ON r.employee_id = e.id
              WHERE r.notification_id = :notification_id
              ORDER BY e.full_name ASC";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            ':notification_id' => $notificationId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
