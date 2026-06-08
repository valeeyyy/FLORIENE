<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth.html");
    exit();
}
include 'db.php';

$aksi = $_POST['aksi'] ?? 'edit';
$id = (int) ($_POST['id_pesanan'] ?? 0);

if ($id <= 0) {
    header("Location: ../admin.php?section=orders&msg=error");
    exit();
}

if ($aksi === 'delete') {
    $result = $conn->query("DELETE FROM pesanan WHERE id_pesanan=$id");
    $msg = 'delete';
} else {
    $status = $conn->real_escape_string($_POST['status'] ?? '');

    $valid_statuses = ['pending', 'processing', 'completed', 'cancelled'];
    if (!in_array($status, $valid_statuses)) {
        header("Location: ../admin.php?section=orders&msg=error");
        exit();
    }

    $order = $conn->query("SELECT id_user, status FROM pesanan WHERE id_pesanan=$id")->fetch_assoc();
    if (!$order) {
        header("Location: ../admin.php?section=orders&msg=error");
        exit();
    }
    $id_user = $order['id_user'];
    $was_completed = ($order['status'] === 'completed');

    $result = $conn->query("UPDATE pesanan SET status='$status' WHERE id_pesanan=$id");

    if ($status === 'completed' && !$was_completed) {
        $detail_result = $conn->query("SELECT SUM(jumlah) as total_items FROM pesanan_detail WHERE id_pesanan=$id");
        $detail = $detail_result->fetch_assoc();
        $total_items = (int) ($detail['total_items'] ?? 0);

        if ($total_items > 0) {
            $conn->query("INSERT INTO loyalty_card (id_user, poin) VALUES ($id_user, $total_items)
                          ON DUPLICATE KEY UPDATE poin = poin + $total_items, last_update_poin = NOW()");
        }
    }

    $msg = 'edit';
}

$conn->close();
header("Location: ../admin.php?section=orders&msg=$msg");
exit();
?>

