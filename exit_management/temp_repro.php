<?php
session_start();
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['ajax_action'] = 'get_interview';
$_POST['controller'] = 'interview';
$_POST['interview_id'] = 1;
require 'exit_management.php';
