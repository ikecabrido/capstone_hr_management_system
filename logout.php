<?php

require_once "auth/auth.php";

$auth = new Auth();
$auth->logout();

header("Location: login_form.php");
exit;
