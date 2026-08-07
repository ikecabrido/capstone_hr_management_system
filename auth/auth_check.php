<?php

require_once __DIR__ . '/auth.php';

$auth = new Auth();

// Enforce inactivity timeout for authenticated sessions (will redirect to login if timed out)
$auth->enforceSessionTimeout();


if (!$auth->check()) {
    header('Location: ' . $auth->getLoginFormPath());
    exit;
}
