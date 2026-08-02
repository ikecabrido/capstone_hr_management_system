<?php
session_start();
$_SESSION['user'] = ['id' => 1, 'role' => 'admin'];
require 'controllers/ExitManagementController.php';
require 'controllers/ExitInterviewController.php';
$controller = new ExitInterviewController();
var_export($controller->getInterview(1));
