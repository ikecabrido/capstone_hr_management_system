<?php
namespace App\Controllers;

use App\Models\Announcement;
use App\Models\Message;

class CommunicationController
{
    private $announcement;
    private $message;

    public function __construct()
    {
        $this->announcement = new Announcement();
        $this->message = new Message();
    }

    public function getAnnouncements()
    {
        return $this->announcement->getAnnouncements();
    }

    public function postAnnouncement($title, $content, $created_by)
    {
        return $this->announcement->postAnnouncement($title, $content, $created_by);
    }

    public function sendMessage($sender_id, $receiver_id, $message)
    {
        return $this->message->create(['sender_id' => $sender_id, 'receiver_id' => $receiver_id, 'message' => $message]);
    }

    public function messageThreads($employee_id)
    {
        return $this->message->threads($employee_id);
    }
}
