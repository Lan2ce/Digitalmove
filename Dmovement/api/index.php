<?php
header('Content-Type: application/json');
require_once 'db.php';

$token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$db = getDB();

$stmt = $db->prepare("SELECT * FROM users WHERE token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();
if (!$user) {
    echo json_encode(['code' => 401]);
    exit;
}

// Check if signed today
$stmt = $db->prepare("SELECT id FROM sign_ins WHERE user_id = ? AND DATE(created_at) = CURDATE()");
$stmt->execute([$user['id']]);
if ($stmt->fetch()) {
    echo json_encode(['code' => 200, 'msg' => 'Already signed today']);
} else {
    $reward = 10; // Daily reward
    $db->prepare("UPDATE users SET wallet = wallet + ? WHERE id = ?")->execute([$reward, $user['id']]);
    $db->prepare("INSERT INTO sign_ins (user_id) VALUES (?)")->execute([$user['id']]);
    echo json_encode(['code' => 200, 'msg' => 'Signed in +'.$reward]);
}
?>
