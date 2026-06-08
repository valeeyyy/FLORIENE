<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['success' => false, 'need_login' => true, 'message' => 'Please login to place an order']);
    exit();
}

include 'db.php';
$id_user = (int) $_SESSION['id_user'];

$id_produk = (int) ($_POST['id_produk'] ?? 0);
$jumlah = (int) ($_POST['jumlah'] ?? 0);
$tanggal = $conn->real_escape_string($_POST['tanggal_pengiriman'] ?? '');
$catatan = $conn->real_escape_string($_POST['catatan'] ?? '');

if ($id_produk <= 0 || $jumlah <= 0) {
    echo json_encode(['success' => false, 'message' => 'Please select a product and quantity']);
    exit();
}

// Look up the real price from DB (never trust the client)
$produk = $conn->query("SELECT nama_produk, harga FROM produk WHERE id_produk=$id_produk AND status='active'")->fetch_assoc();
if (!$produk) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit();
}

$harga_satuan = (float) $produk['harga'];
$subtotal = $harga_satuan * $jumlah;

// Create the order header
$tanggal_sql = $tanggal ? "'$tanggal'" : 'NULL';
$conn->query("INSERT INTO pesanan (id_user, status, total_harga, tanggal_pengiriman, catatan)
              VALUES ($id_user, 'pending', $subtotal, $tanggal_sql, '$catatan')");
$id_pesanan = $conn->insert_id;

// Create the order line item (this is what loyalty points are counted from)
$conn->query("INSERT INTO pesanan_detail (id_pesanan, id_produk, jumlah, harga_satuan, subtotal)
              VALUES ($id_pesanan, $id_produk, $jumlah, $harga_satuan, $subtotal)");

echo json_encode([
    'success' => true,
    'id_pesanan' => $id_pesanan,
    'total' => $subtotal,
    'message' => 'Order placed successfully! Your order is now pending.'
]);

$conn->close();
?>
