<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth.html");
    exit();
}

include 'db.php';
$id_user = (int) $_SESSION['id_user'];
$aksi = $_REQUEST['aksi'] ?? '';

if ($aksi === 'redeem') {
    $warna = $conn->real_escape_string($_POST['warna'] ?? '');

    $loyalty = $conn->query("SELECT poin FROM loyalty_card WHERE id_user=$id_user")->fetch_assoc();
    $poin = 0;

    if ($loyalty) {
        $poin = (int) $loyalty['poin'];
    } else {
        $poin = 0;
    }

    if ($poin < 10) {
        header("Location: ../profile.php?msg=redeem_fail");
        exit();
    }

    $conn->query("INSERT INTO loyalty_redemption (id_user, poin_digunakan, warna_bloomies, status) VALUES ($id_user, 10, '$warna', 'pending')");
    $conn->query("UPDATE loyalty_card SET poin = poin - 10, last_update_poin = NOW() WHERE id_user=$id_user");

    header("Location: ../profile.php?msg=redeem_ok");
    exit();
}

header("Location: ../profile.php");
exit();
?>