<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth.html");
    exit();
}
include 'db.php';

$aksi = $_POST['aksi'] ?? '';
$id_redemption = (int) ($_POST['id_redemption'] ?? 0);

if ($aksi === 'approve') {
    // Points were already deducted when the customer confirmed the redemption,
    // so approving only marks the reward as fulfilled.
    $conn->query("UPDATE loyalty_redemption SET status='completed' WHERE id_redemption=$id_redemption AND status='pending'");
} elseif ($aksi === 'cancel') {
    $redemption = $conn->query("SELECT id_user, poin_digunakan FROM loyalty_redemption WHERE id_redemption=$id_redemption AND status='pending'")->fetch_assoc();
    if ($redemption) {
        $id_user = $redemption['id_user'];
        $poin = (int) $redemption['poin_digunakan'];
        // Mark cancelled and refund the points the customer spent
        $conn->query("UPDATE loyalty_redemption SET status='cancelled' WHERE id_redemption=$id_redemption");
        $conn->query("UPDATE loyalty_card SET poin = poin + $poin, last_update_poin = NOW() WHERE id_user=$id_user");
    }
}

$conn->close();
header("Location: ../admin.php?section=loyalties&msg=" . ($aksi === 'approve' ? 'approved' : 'cancelled'));
exit();
?>
