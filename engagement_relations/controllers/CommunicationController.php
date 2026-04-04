<?php
namespace App\Controllers;

use App\Models\Announcement;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Policy;

class CommunicationController
{
    private $announcement;
    private $message;
    private $notification;
    private $policy;

    public function __construct()
    {
        $this->announcement = new Announcement();
        $this->message = new Message();
        $this->notification = new Notification();
        $this->policy = new Policy();
    }

    public function getAnnouncements()
    {
        return $this->announcement->getAnnouncements();
    }

    public function postAnnouncement($title, $content, $created_by)
    {
        return $this->announcement->postAnnouncement($title, $content, $created_by);
    }

    public function postEvent($title, $description, $date, $created_by)
    {
        return $this->announcement->postEvent($title, $description, $date, $created_by);
    }

    public function postPolicy($title, $content, $created_by)
    {
        return $this->policy->postPolicy($title, $content, $created_by);
    }

    public function getPolicyUpdates()
    {
        return $this->policy->getAllPolicies();
    }

    public function deletePolicy($id)
    {
        return $this->policy->deletePolicy($id);
    }

    public function sendMessage($sender_id, $receiver_id, $message)
    {
        return $this->message->create(['sender_id' => $sender_id, 'receiver_id' => $receiver_id, 'message' => $message]);
    }

    public function messageThreads($employee_id)
    {
        return $this->message->threads($employee_id);
    }

    public function getMessageHistory($sender_id, $receiver_id)
    {
        return $this->message->getMessageHistory($sender_id, $receiver_id);
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
