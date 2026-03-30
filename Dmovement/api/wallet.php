<?php
header('Content-Type: application/json');
require_once 'db.php';

$token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$db = getDB();

$stmt = $db->prepare("SELECT * FROM users WHERE token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();
if (!$user) {
    echo json_encode(['code' => 401, 'msg' => 'Unauthorized']);
    exit;
}

$path = $_SERVER['REQUEST_URI'];

if (strpos($path, '/user/wallet/withdrawalDetails') !== false) {
    // History stub
    echo json_encode(['code' => 200, 'data' => []]);
} elseif (strpos($path, '/casher/withdraw/request') !== false) {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $amount = $data['amount'] ?? 0;
    $pwd = $data['withdrawPwd'] ?? '';
    
    if ($amount > $user['wallet'] || $amount < 10) {
        echo json_encode(['code' => 402, 'msg' => 'Insufficient balance']);
        exit;
    }
    
    // Update wallet
    $db->prepare("UPDATE users SET wallet = wallet - ? WHERE id = ?")->execute([$amount, $user['id']]);
    
    echo json_encode(['code' => 200, 'msg' => 'Withdrawal requested', 'amount' => $amount]);
} elseif (strpos($path, '/casher/withdraw/history') !== false) {
    echo json_encode(['code' => 200, 'data' => []]); // Add withdrawals table later
} elseif (strpos($path, '/casher/deposit/history') !== false) {
    echo json_encode(['code' => 200, 'data' => []]);
} else {
    echo json_encode(['code' => 404]);
}
?>
