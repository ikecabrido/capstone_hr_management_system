<?php
namespace App\Models;

class Notification extends BaseModel
{
    public function getAll()
    {
        $sql = 'SELECT * FROM eer_notifications';
        return $this->execute($sql)->fetchAll();
    }

    public function markAsRead($notification_id)
    {
        $sql = 'UPDATE eer_notifications SET is_read = 1 WHERE id = :id';
        $this->execute($sql, ['id' => $notification_id]);
    }

    public function postHRNotification($title, $content, $notificationType, $targetEmployees)
    {
        // Combine title and content for the message
        $message = $title . ': ' . $content;
        
        // Build the target employees list based on selections
        $selectedEmployees = [];
        
        if (!empty($targetEmployees) && is_array($targetEmployees)) {
            foreach ($targetEmployees as $target) {
                if ($target === 'all') {
                    // Get all employee IDs
                    $sql = 'SELECT employee_id FROM employees WHERE employee_id IS NOT NULL';
                    $results = $this->execute($sql)->fetchAll();
                    foreach ($results as $row) {
                        $selectedEmployees[] = $row['employee_id'];
                    }
                    break; // No need to process other selections if "all" is selected
                } elseif ($target === 'management') {
                    // Get management employees (typically those with manager/supervisor positions)
                    $sql = 'SELECT employee_id FROM employees WHERE position IN ("Manager", "Supervisor", "Director", "Head") OR department = "Management"';
                    $results = $this->execute($sql)->fetchAll();
                    foreach ($results as $row) {
                        $selectedEmployees[] = $row['employee_id'];
                    }
                } elseif (!empty($target)) {
                    // Treat as department name - get employees from that department
                    $sql = 'SELECT employee_id FROM employees WHERE department = :department';
                    $results = $this->execute($sql, ['department' => $target])->fetchAll();
                    foreach ($results as $row) {
                        $selectedEmployees[] = $row['employee_id'];
                    }
                } elseif (is_numeric($target)) {
                    // Direct employee ID
                    $selectedEmployees[] = (int)$target;
                }
            }
        }
        
        // Remove duplicates
        $selectedEmployees = array_unique($selectedEmployees);
        
        if (empty($selectedEmployees)) {
            // If no selection or no results, send to all
            $sql = 'INSERT INTO eer_notifications (employee_id, message, type, created_at) 
                    SELECT employee_id, :message, :type, NOW() FROM employees WHERE employee_id IS NOT NULL';
            $this->execute($sql, ['message' => $message, 'type' => $notificationType]);
        } else {
            // Send to selected employees
            foreach ($selectedEmployees as $employeeId) {
                if ($employeeId > 0) {
                    $sql = 'INSERT INTO eer_notifications (employee_id, message, type, created_at) 
                            VALUES (:employee_id, :message, :type, NOW())';
                    $this->execute($sql, [
                        'employee_id' => $employeeId,
                        'message' => $message,
                        'type' => $notificationType
                    ]);
                }
            }
        }
    }
}