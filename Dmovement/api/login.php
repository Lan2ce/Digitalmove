<?php
// Dedicated login handler (for future expansion)
header('Content-Type: application/json');
require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['code' => 400, 'msg' => 'Missing credentials']);
    exit;
}

$db = getDB();
$stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && hash('md5', $password) === $user['password']) {
    $token = bin2hex(random_bytes(32));
    $db->prepare("UPDATE users SET token = ? WHERE id = ?")->execute([$token, $user['id']]);
    setcookie('Genostra-token', $token, time() + 3600*24*30, '/');
    echo json_encode(['code' => 200, 'data' => $user]);
} else {
    echo json_encode(['code' => 401, 'msg' => 'Invalid credentials']);
}
?>
