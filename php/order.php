<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth.html");
    exit();
}

include 'db.php';
$id_user = (int) $_SESSION['id_user'];

$id_produk = (int) ($_POST['id_produk'] ?? 0);
$jumlah = (int) ($_POST['jumlah'] ?? 0);
$nama = $conn->real_escape_string($_POST['nama'] ?? '');
$hp = $conn->real_escape_string($_POST['nomor_hp'] ?? '');
$tanggal = $conn->real_escape_string($_POST['tanggal_pengiriman'] ?? '');
$note = trim($_POST['catatan'] ?? '');

if ($id_produk <= 0 || $jumlah <= 0) {
    header("Location: ../index.html?order=error#pesan");
    exit();
}

$produk = $conn->query("SELECT nama_produk, harga FROM produk WHERE id_produk=$id_produk AND status='active'")->fetch_assoc();
if (!$produk) {
    header("Location: ../index.html?order=error#pesan");
    exit();
}

$harga_satuan = (float) $produk['harga'];
$subtotal = $harga_satuan * $jumlah;

$catatan = "Contact: $nama ($hp)";
if ($note !== '') {
    $catatan .= ' | Notes: ' . $note;
}
$catatan = $conn->real_escape_string($catatan);

if ($tanggal) {
    $tanggal_sql = "'$tanggal'";
} else {
    $tanggal_sql = 'NULL';
}

$conn->query("INSERT INTO pesanan (id_user, status, total_harga, tanggal_pengiriman, catatan)
              VALUES ($id_user, 'pending', $subtotal, $tanggal_sql, '$catatan')");

$id_pesanan = $conn->insert_id;

$conn->query("INSERT INTO pesanan_detail (id_pesanan, id_produk, jumlah, harga_satuan, subtotal)
              VALUES ($id_pesanan, $id_produk, $jumlah, $harga_satuan, $subtotal)");

$conn->close();
header("Location: ../index.html?order=success#pesan");
exit();
?>