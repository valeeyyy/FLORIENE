<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

include 'db.php';
$id_user = (int) $_SESSION['id_user'];
$aksi = $_REQUEST['aksi'] ?? 'get';

if ($aksi === 'get') {
    $loyalty = $conn->query("SELECT poin FROM loyalty_card WHERE id_user=$id_user")->fetch_assoc();
    $poin = $loyalty ? (int)$loyalty['poin'] : 0;

    $redemption = $conn->query("SELECT id_redemption, warna_bloomies, status, created_at FROM loyalty_redemption WHERE id_user=$id_user AND status='pending' LIMIT 1")->fetch_assoc();

    echo json_encode([
        'success' => true,
        'poin' => $poin,
        'siap_redeem' => $poin >= 10,
        'pending_redemption' => $redemption
    ]);
    exit();
}

if ($aksi === 'redeem') {
    $warna = $conn->real_escape_string($_POST['warna'] ?? '');

    $loyalty = $conn->query("SELECT poin FROM loyalty_card WHERE id_user=$id_user")->fetch_assoc();
    $poin = $loyalty ? (int)$loyalty['poin'] : 0;

    if ($poin < 10) {
        echo json_encode(['success' => false, 'message' => 'Insufficient points']);
        exit();
    }

    // Record the redemption and immediately deduct 10 points (keep the remainder)
    $conn->query("INSERT INTO loyalty_redemption (id_user, poin_digunakan, warna_bloomies, status) VALUES ($id_user, 10, '$warna', 'pending')");
    $id_redemption = $conn->insert_id;
    $conn->query("UPDATE loyalty_card SET poin = poin - 10, last_update_poin = NOW() WHERE id_user=$id_user");

    echo json_encode([
        'success' => true,
        'id_redemption' => $id_redemption,
        'sisa_poin' => $poin - 10,
        'message' => 'Redemption successful! 10 points used. Waiting for admin confirmation.'
    ]);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Unknown action']);
$conn->close();
?>
