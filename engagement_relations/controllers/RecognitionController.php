<?php
namespace App\Controllers;

use App\Models\Recognition;

class RecognitionController
{
    private $recognition;

    public function __construct()
    {
        $this->recognition = new Recognition();
    }

    public function getRecognitions()
    {
        return $this->recognition->getRecognitions();
    }

    public function sendRecognition($sender_id, $receiver_id, $message, $points)
    {
        return $this->recognition->sendRecognition($sender_id, $receiver_id, $message, $points);
    }
}
