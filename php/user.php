<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth.html");
    exit();
}
include 'db.php';

$aksi = $_POST['aksi'] ?? '';

if ($aksi === 'add') {
    $nama = $conn->real_escape_string($_POST['nama'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $password = $conn->real_escape_string($_POST['password'] ?? '');
    $nomor_hp = $conn->real_escape_string($_POST['nomor_hp'] ?? '');
    $role = $conn->real_escape_string($_POST['role'] ?? 'CUSTOMER');
    $now = date('Y-m-d H:i:s');
    $conn->query("INSERT INTO user (nama, email, password, nomor_hp, role, created_at)
                  VALUES ('$nama', '$email', '$password', '$nomor_hp', '$role', '$now')");

} elseif ($aksi === 'edit') {
    $id = (int) ($_POST['id_user'] ?? 0);
    $nama = $conn->real_escape_string($_POST['nama'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $nomor_hp = $conn->real_escape_string($_POST['nomor_hp'] ?? '');
    $role = $conn->real_escape_string($_POST['role'] ?? 'CUSTOMER');
    $conn->query("UPDATE user SET nama='$nama', email='$email', nomor_hp='$nomor_hp', role='$role' WHERE id_user=$id");

} elseif ($aksi === 'delete') {
    $id = (int) ($_POST['id_user'] ?? 0);
    $conn->query("DELETE FROM loyalty_card WHERE id_user=$id");
    $conn->query("DELETE FROM user WHERE id_user=$id");
}

$conn->close();
header("Location: ../admin.php?section=users&msg=$aksi");
exit();
?>