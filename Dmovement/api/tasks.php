<?php
header('Content-Type: application/json');
require_once 'db.php';

$token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$db = getDB();

// Get user
$stmt = $db->prepare("SELECT * FROM users WHERE token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();
if (!$user) {
    echo json_encode(['code' => 401, 'msg' => 'Unauthorized']);
    exit;
}

$path = $_SERVER['REQUEST_URI'];

if (strpos($path, '/user/task/start') !== false) {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    // Start task stub
    echo json_encode(['code' => 200, 'data' => ['taskId' => 1, 'code' => 'TASK001']]);
} elseif (strpos($path, '/user/task/submit') !== false) {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $taskCode = $data['taskCode'] ?? '';
    $rating = $data['rating'] ?? 0;
    $profit = rand(10, 100);
    $stmt = $db->prepare("INSERT INTO tasks (user_id, task_code, rating, profit) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user['id'], $taskCode, $rating, $profit]);
    $db->prepare("UPDATE users SET wallet = wallet + ? WHERE id = ?")->execute([$profit, $user['id']]);
    echo json_encode(['code' => 200, 'msg' => 'Submitted', 'profit' => $profit]);
} elseif (strpos($path, '/user/task/history') !== false) {
    $stmt = $db->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC LIMIT 20");
    $stmt->execute([$user['id']]);
    echo json_encode(['code' => 200, 'data' => $stmt->fetchAll()]);
} else {
    echo json_encode(['code' => 404]);
}
?>
