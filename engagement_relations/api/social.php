<?php
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/utils.php';

use App\Controllers\SocialController;

session_start();
$action = $_GET['action'] ?? 'list';
if (!isset($_SESSION['user']) && $action !== 'list') {
   jsonResponse(['error' => 'Unauthorized'], 401);
}

$ctrl = new SocialController();
$action = $_GET['action'] ?? 'feed';
$data = inputData();

try {
    switch ($action) {
        case 'feed':
            jsonResponse(['success' => true, 'data' => $ctrl->getPosts()]);
            break;
        case 'post':
            if (empty($data['content'])) jsonResponse(['error' => 'content required'], 400);
            $employeeId = $_SESSION['user']['employee_id'] ?? null;
            if (empty($employeeId)) jsonResponse(['error' => 'employee_id required'], 400);
            $id = $ctrl->createPost($employeeId, $data['content']);
            jsonResponse(['id' => $id], 201);
            break;
        case 'like':
            if (empty($data['post_id'])) jsonResponse(['error' => 'post_id required'], 400);
            $employeeId = $_SESSION['user']['employee_id'] ?? null;
            $userId = $_SESSION['user']['id'] ?? null;
            if (empty($employeeId) && empty($userId)) jsonResponse(['error' => 'employee_id or user_id required'], 400);
            $ctrl->addReaction((int)$data['post_id'], $employeeId, $userId, 'like');
            jsonResponse(['message' => 'Post liked successfully'], 200);
            break;
        case 'comment':
            foreach (['post_id', 'comment'] as $f) {
                if (empty($data[$f])) jsonResponse(['error' => "$f is required"], 400);
            }
            $employeeId = $_SESSION['user']['employee_id'] ?? null;
            if (empty($employeeId)) jsonResponse(['error' => 'employee_id required'], 400);
            $id = $ctrl->addComment((int)$data['post_id'], $employeeId, $data['comment']);
            jsonResponse(['id' => $id], 201);
            break;
        case 'delete':
            if (empty($data['post_id'])) jsonResponse(['error' => 'post_id required'], 400);
            $ctrl->deletePost((int)$data['post_id']);
            jsonResponse(['message' => 'Post deleted successfully'], 200);
            break;
        case 'edit':
            foreach (['post_id', 'content'] as $f) {
                if (empty($data[$f])) jsonResponse(['error' => "$f is required"], 400);
            }
            $ctrl->editPost((int)$data['post_id'], $data['content']);
            jsonResponse(['message' => 'Post updated successfully'], 200);
            break;
        default:
            jsonResponse(['error' => 'unknown action'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
