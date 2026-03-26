<?php
/**
 * Logout Handler
 */

require_once "../app/controllers/AuthController.php";

$auth = new AuthController();
$auth->logout();
