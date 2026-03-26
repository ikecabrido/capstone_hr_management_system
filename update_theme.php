<?php

session_start();

require_once "auth/database.php";

if (!isset($_SESSION['user'])) {
    exit("Not logged in");
}

$userId = $_SESSION['user']['id'];
$theme = $_POST['theme'] ?? 'light';

/* create database connection */
$database = Database::getInstance();
$pdo = $database->getConnection();

/* update theme */
$stmt = $pdo->prepare("UPDATE users SET theme = ? WHERE id = ?");
$stmt->execute([$theme, $userId]);

/* update session so refresh keeps theme */
$_SESSION['user']['theme'] = $theme;

echo "Theme updated";
