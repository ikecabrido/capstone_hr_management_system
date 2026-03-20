<?php
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/utils.php';

use App\Controllers\SocialController;

session_start();
if (!isset($_SESSION['user'])) {
    jsonResponse(['error' => 'Unauthorized'], 401);
}

$ctrl = new SocialController();
$action = $_GET['action'] ?? 'feed';
$data = inputData();

try {
    switch ($action) {
        case 'feed':
            jsonResponse($ctrl->getPosts());
            break;
        case 'post':
            if (empty($data['content'])) jsonResponse(['error' => 'content required'], 400);
            $id = $ctrl->createPost($_SESSION['user']['id'], $data['content']);
            jsonResponse(['id' => $id], 201);
            break;
        case 'comment':
            foreach (['post_id', 'comment'] as $f) {
                if (empty($data[$f])) jsonResponse(['error' => "$f is required"], 400);
            }
            $id = $ctrl->addComment((int)$data['post_id'], $_SESSION['user']['id'], $data['comment']);
            jsonResponse(['id' => $id], 201);
            break;
        default:
            jsonResponse(['error' => 'unknown action'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
