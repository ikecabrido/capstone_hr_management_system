<?php

require_once __DIR__ . '/../models/Notification.php';

class NotificationHelper
{

    public static function getEmployeeNotifications($employeeId)
    {
        $notificationModel = new Notification();

        return [
            'count' => $notificationModel->countUnread($employeeId),
            'latest' => $notificationModel->latest($employeeId)
        ];
    }

}