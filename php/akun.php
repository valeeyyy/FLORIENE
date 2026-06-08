<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['success' => false, 'message' => 'Belum login']);
    exit();
}

include 'db.php';
$id = (int) $_SESSION['id_user'];
$aksi = $_REQUEST['aksi'] ?? 'get';

if ($aksi === 'get') {
    $user = $conn->query("SELECT id_user, nama, email, nomor_hp, role, created_at FROM user WHERE id_user=$id")->fetch_assoc();
    $loyalty = $conn->query("SELECT id_loyalty_card, poin FROM loyalty_card WHERE id_user=$id")->fetch_assoc();
    $pending_redemption = $conn->query("SELECT id_redemption, warna_bloomies, status, created_at FROM loyalty_redemption WHERE id_user=$id AND status='pending' LIMIT 1")->fetch_assoc();
    $orders = [];
    $res = $conn->query("SELECT id_pesanan, total_harga, status, created_at FROM pesanan WHERE id_user=$id ORDER BY created_at DESC");
    while ($r = $res->fetch_assoc())
        $orders[] = $r;
    echo json_encode(['success' => true, 'user' => $user, 'loyalty' => $loyalty, 'pending_redemption' => $pending_redemption, 'orders' => $orders, 'siap_redeem' => $loyalty && (int)$loyalty['poin'] >= 10]);

} elseif ($aksi === 'update') {
    $nama = $conn->real_escape_string($_POST['nama'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $hp = $conn->real_escape_string($_POST['nomor_hp'] ?? '');
    $conn->query("UPDATE user SET nama='$nama', email='$email', nomor_hp='$hp' WHERE id_user=$id");
    $_SESSION['nama'] = $nama;
    echo json_encode(['success' => true]);

} elseif ($aksi === 'password') {
    $current = $conn->real_escape_string($_POST['current'] ?? '');
    $new = $conn->real_escape_string($_POST['new'] ?? '');
    $row = $conn->query("SELECT password FROM user WHERE id_user=$id")->fetch_assoc();
    if (!$row || $row['password'] !== $current) {
        echo json_encode(['success' => false, 'message' => 'Password lama salah.']);
    } else {
        $conn->query("UPDATE user SET password='$new' WHERE id_user=$id");
        echo json_encode(['success' => true]);
    }

} elseif ($aksi === 'delete') {
    $conn->query("DELETE FROM loyalty_card WHERE id_user=$id");
    $conn->query("DELETE FROM pesanan WHERE id_user=$id");
    $conn->query("DELETE FROM user WHERE id_user=$id");
    session_destroy();
    echo json_encode(['success' => true]);

} else {
    echo json_encode(['success' => false, 'message' => 'Unknown action']);
}

$conn->close();
?>