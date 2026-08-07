<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/auth.php';

$auth = new Auth();

if (!$auth->check()) {
    header("Location: ../login_form.php");
    exit;
}
