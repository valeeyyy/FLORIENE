<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth.html");
    exit();
}

include 'db.php';
$id = (int) $_SESSION['id_user'];
$aksi = $_POST['aksi'] ?? '';

if ($aksi === 'update') {
    $nama = $conn->real_escape_string($_POST['nama'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $hp = $conn->real_escape_string($_POST['nomor_hp'] ?? '');
    $conn->query("UPDATE user SET nama='$nama', email='$email', nomor_hp='$hp' WHERE id_user=$id");
    $_SESSION['nama'] = $nama;
    header("Location: ../profile.php?msg=profile_updated");
    exit();

} elseif ($aksi === 'password') {
    $current = $conn->real_escape_string($_POST['current'] ?? '');
    $new = $conn->real_escape_string($_POST['new'] ?? '');
    $row = $conn->query("SELECT password FROM user WHERE id_user=$id")->fetch_assoc();
    if (!$row || $row['password'] !== $current) {
        header("Location: ../profile.php?msg=password_wrong");
        exit();
    }
    $conn->query("UPDATE user SET password='$new' WHERE id_user=$id");
    header("Location: ../profile.php?msg=password_changed");
    exit();

} elseif ($aksi === 'delete') {
    $conn->query("DELETE FROM loyalty_card WHERE id_user=$id");
    $conn->query("DELETE FROM pesanan WHERE id_user=$id");
    $conn->query("DELETE FROM user WHERE id_user=$id");
    session_destroy();
    header("Location: ../index.php");
    exit();
}

header("Location: ../profile.php");
exit();
?>
