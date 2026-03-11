<?php

require_once "auth.php";

$auth = new Auth();

if (!$auth->check()) {
    header("Location: ../login_form.php");
    exit;
}
