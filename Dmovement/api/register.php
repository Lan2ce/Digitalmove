<?php
header('Content-Type: application/json');
require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true) ?? [];

$username = $data['username'] ?? '';
$password = $data['password'] ?? '';
$inviteCode = $data['inviteCode'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['code' => 400, 'msg' => 'Missing fields']);
    exit;
}

$db = getDB();
$stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);
if ($stmt->fetch()) {
    echo json_encode(['code' => 400, 'msg' => 'Username exists']);
    exit;
}

// Hash password
$hashed = hash('md5', $password);

$stmt = $db->prepare("INSERT INTO users (username, password, wallet) VALUES (?, ?, 100)");
$stmt->execute([$username, $hashed]);

echo json_encode(['code' => 200, 'msg' => 'Registered']);
?>
