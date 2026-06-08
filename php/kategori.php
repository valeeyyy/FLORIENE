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
    $deskripsi = $conn->real_escape_string($_POST['deskripsi'] ?? '');
    $conn->query("INSERT INTO kategori (nama, deskripsi) VALUES ('$nama', '$deskripsi')");

} elseif ($aksi === 'edit') {
    $id = (int) ($_POST['id_kategori'] ?? 0);
    $nama = $conn->real_escape_string($_POST['nama'] ?? '');
    $deskripsi = $conn->real_escape_string($_POST['deskripsi'] ?? '');
    $conn->query("UPDATE kategori SET nama='$nama', deskripsi='$deskripsi' WHERE id_kategori=$id");

} elseif ($aksi === 'delete') {
    $id = (int) ($_POST['id_kategori'] ?? 0);
    $conn->query("DELETE FROM kategori WHERE id_kategori=$id");
}

$conn->close();
header("Location: ../admin.php?section=categories&msg=$aksi");
exit();
?>