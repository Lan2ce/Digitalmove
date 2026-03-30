<?php
header('Content-Type: application/json');
require_once 'db.php';

$token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$db = getDB();

$stmt = $db->prepare("SELECT * FROM users WHERE token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();
if (!$user || $user['wallet'] < 10) { // Min balance check
    echo json_encode(['code' => 402, 'msg' => 'Insufficient balance']);
    exit;
}

$prizes = [
    'special' => ['prob' => 0.01, 'amt' => 10000],
    'first' => ['prob' => 0.05, 'amt' => 1000],
    // ...
    'participation' => ['prob' => 0.5, 'amt' => 1],
];

$rand = mt_rand(1, 10000) / 10000.0;
$prize = null;
foreach ($prizes as $type => $p) {
    if ($rand < $p['prob']) {
        $prize = $type;
        break;
    }
}
if (!$prize) $prize = 'participation';

$amt = $prizes[$prize]['amt'];
$db->prepare("UPDATE users SET wallet = wallet + ? WHERE id = ?")->execute([$amt, $user['id']]);
$db->prepare("INSERT INTO lottery_draws (user_id, prize_type, amount) VALUES (?, ?, ?)")->execute([$user['id'], $prize, $amt]);

echo json_encode(['code' => 200, 'data' => ['prize' => $prize, 'amount' => $amt]]);
?>
