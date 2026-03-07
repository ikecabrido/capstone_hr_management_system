<?php

require_once "auth.php";

$auth = new Auth();

if (!$auth->check()) {
    header("Location: /HRMSSSS/login_form.php");
    exit;
}
