<?php
namespace App\Controllers;

use App\Models\Announcement;
use App\Models\Notification;
use App\Models\Policy;

class CommunicationController
{
    private $announcement;
    private $notification;
    private $policy;

    public function __construct()
    {
        $this->announcement = new Announcement();
        $this->notification = new Notification();
        $this->policy = new Policy();
    }

    public function getAnnouncements()
    {
        return $this->announcement->getAnnouncements('announcement');
    }

    public function getRecognitionAnnouncements()
    {
        return $this->announcement->getRecognitionAnnouncements();
    }

    public function getDepartmentUpdates()
    {
        return $this->announcement->getDepartmentUpdates();
    }

    public function postAnnouncement($title, $content, $created_by_user_id, $category = 'general', $priority = 'normal', $targetAudience = 'all')
    {
        return $this->announcement->postAnnouncement($title, $content, $created_by_user_id, $category, $priority, $targetAudience);
    }

    public function postRecognitionAnnouncement($title, $content, $created_by_user_id)
    {
        return $this->announcement->postRecognitionAnnouncement($title, $content, $created_by_user_id);
    }

    public function postEvent($title, $description, $date, $created_by_user_id)
    {
        return $this->announcement->postEvent($title, $description, $date, $created_by_user_id);
    }

    public function postDepartmentUpdate($title, $content, $department, $priority, $created_by_user_id)
    {
        return $this->announcement->postDepartmentUpdate($title, $content, $department, $priority, $created_by_user_id);
    }

    public function postPolicy($title, $content, $category, $effectiveDate, $attachmentPath, $created_by_user_id)
    {
        return $this->policy->postPolicy($title, $content, $category, $effectiveDate, $attachmentPath, $created_by_user_id);
    }

    public function getPolicyUpdates()
    {
        return $this->policy->getAllPolicies();
    }

    public function getSharedFiles()
    {
        return $this->announcement->getSharedFiles();
    }

    public function shareFile($userId, $fileName, $filePath, $fileSize, $fileType, $description = '', $content = null)
    {
        return $this->announcement->shareFile($userId, $fileName, $filePath, $fileSize, $fileType, $description, $content);
    }

    public function getSharedFileById($id)
    {
        return $this->announcement->getSharedFileById($id);
    }

    public function deleteSharedFile($id)
    {
        return $this->announcement->deleteSharedFile($id);
    }

    public function deletePolicy($id)
    {
        return $this->policy->deletePolicy($id);
    }

    public function sendMessage($sender_id, $receiver_id, $message)
    {
        // Moved to MessageController
        $messageCtrl = new \App\Controllers\MessageController();
        return $messageCtrl->sendMessage($sender_id, $receiver_id, $message);
    }

    public function messageThreads($employee_id)
    {
        // Moved to MessageController
        $messageCtrl = new \App\Controllers\MessageController();
        return $messageCtrl->messageThreads($employee_id);
    }

    public function getMessageHistory($sender_id, $receiver_id)
    {
        // Moved to MessageController
        $messageCtrl = new \App\Controllers\MessageController();
        return $messageCtrl->getMessageHistory($sender_id, $receiver_id);
    }

    public function postHRNotification($title, $content, $notificationType, $targetEmployees, $currentUserId)
    {
        return $this->notification->postHRNotification($title, $content, $notificationType, $targetEmployees);
    }

    public function getNotifications()
    {
        return $this->notification->getAll();
    }

    public function markNotificationAsRead($notification_id)
    {
        return $this->notification->markAsRead($notification_id);
    }
}
