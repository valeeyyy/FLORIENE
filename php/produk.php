<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth.html");
    exit();
}
include 'db.php';

$aksi = $_POST['aksi'] ?? '';

function upload_foto($conn)
{
    if (empty($_FILES['foto']['name']))
        return '';
    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $filename = 'produk_' . time() . '.' . $ext;
    $upload_dir = '../img/produk/';
    if (!is_dir($upload_dir))
        mkdir($upload_dir, 0777, true);
    move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $filename);
    return $conn->real_escape_string('img/produk/' . $filename);
}

if ($aksi === 'add') {
    $nama = $conn->real_escape_string($_POST['nama_produk'] ?? '');
    $warna = $conn->real_escape_string($_POST['warna'] ?? '');
    $deskripsi = $conn->real_escape_string($_POST['deskripsi_produk'] ?? '');
    $harga = (float) ($_POST['harga'] ?? 0);
    $status = $conn->real_escape_string($_POST['status'] ?? 'active');
    $id_kategori = (int) ($_POST['id_kategori'] ?? 0);
    $now = date('Y-m-d H:i:s');
    $url_foto = upload_foto($conn);
    $conn->query("INSERT INTO produk (id_kategori, nama_produk, warna, deskripsi_produk, harga, url_foto_produk, status, created_at)
                  VALUES ($id_kategori, '$nama', '$warna', '$deskripsi', $harga, '$url_foto', '$status', '$now')");

} elseif ($aksi === 'edit') {
    $id = (int) ($_POST['id_produk'] ?? 0);
    $nama = $conn->real_escape_string($_POST['nama_produk'] ?? '');
    $warna = $conn->real_escape_string($_POST['warna'] ?? '');
    $deskripsi = $conn->real_escape_string($_POST['deskripsi_produk'] ?? '');
    $harga = (float) ($_POST['harga'] ?? 0);
    $status = $conn->real_escape_string($_POST['status'] ?? 'active');
    $id_kategori = (int) ($_POST['id_kategori'] ?? 0);
    $url_foto = upload_foto($conn);
    if ($url_foto) {
        $foto_sql = ", url_foto_produk='$url_foto'";
    } else {
        $foto_sql = '';
    }
    $conn->query("UPDATE produk SET id_kategori=$id_kategori, nama_produk='$nama', warna='$warna',
                  deskripsi_produk='$deskripsi', harga=$harga, status='$status' $foto_sql
                  WHERE id_produk=$id");

} elseif ($aksi === 'delete') {
    $id = (int) ($_POST['id_produk'] ?? 0);
    $conn->query("DELETE FROM produk WHERE id_produk=$id");
}

$conn->close();
header("Location: ../admin.php?section=products&msg=$aksi");
exit();
?>