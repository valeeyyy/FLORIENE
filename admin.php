<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: auth.html");
    exit();
}
include 'php/db.php';

$section = $_GET['section'] ?? 'dashboard';
$msg = $_GET['msg'] ?? '';

$users = $produk = $kategori = $pesanan = [];
$dash = [];

if ($section === 'dashboard') {
    $dash['users'] = $conn->query("SELECT COUNT(*) c FROM user WHERE role='CUSTOMER'")->fetch_assoc()['c'];
    $dash['produk'] = $conn->query("SELECT COUNT(*) c FROM produk WHERE status='active'")->fetch_assoc()['c'];
    $dash['pesanan'] = $conn->query("SELECT COUNT(*) c FROM pesanan")->fetch_assoc()['c'];
    $dash['pending'] = $conn->query("SELECT COUNT(*) c FROM pesanan WHERE status='pending'")->fetch_assoc()['c'];
    $dash['revenue'] = $conn->query("SELECT COALESCE(SUM(total_harga),0) c FROM pesanan WHERE status='completed'")->fetch_assoc()['c'];
    $recent_orders = $conn->query("SELECT p.id_pesanan, p.status, p.total_harga, u.nama FROM pesanan p LEFT JOIN user u ON p.id_user=u.id_user ORDER BY p.created_at DESC LIMIT 5");
    $recent_users = $conn->query("SELECT nama, email, created_at FROM user WHERE role='CUSTOMER' ORDER BY created_at DESC LIMIT 5");
}
if ($section === 'users') {
    $res = $conn->query("SELECT * FROM user ORDER BY created_at DESC");
    while ($r = $res->fetch_assoc())
        $users[] = $r;
}
if ($section === 'products') {
    $res = $conn->query("SELECT p.*, k.nama AS nama_kategori FROM produk p LEFT JOIN kategori k ON p.id_kategori=k.id_kategori ORDER BY p.created_at DESC");
    while ($r = $res->fetch_assoc())
        $produk[] = $r;
    $res2 = $conn->query("SELECT * FROM kategori ORDER BY id_kategori");
    while ($r = $res2->fetch_assoc())
        $kategori[] = $r;
}
if ($section === 'categories') {
    $res = $conn->query("SELECT k.*, COUNT(p.id_produk) AS jml FROM kategori k LEFT JOIN produk p ON k.id_kategori=p.id_kategori GROUP BY k.id_kategori");
    while ($r = $res->fetch_assoc())
        $kategori[] = $r;
}
if ($section === 'orders') {
    $fs = $conn->real_escape_string($_GET['status'] ?? '');
    $where = $fs ? "WHERE p.status='$fs'" : '';
    $res = $conn->query("SELECT p.*, u.nama AS nama_user FROM pesanan p LEFT JOIN user u ON p.id_user=u.id_user $where ORDER BY p.created_at DESC");
    while ($r = $res->fetch_assoc())
        $pesanan[] = $r;
}
if ($section === 'loyalties') {
    $res = $conn->query("SELECT lr.*, u.nama, u.email FROM loyalty_redemption lr LEFT JOIN user u ON lr.id_user=u.id_user ORDER BY lr.created_at DESC");
    while ($r = $res->fetch_assoc())
        $pesanan[] = $r;
}

function rupiah($n)
{
    return 'Rp ' . number_format($n, 0, ',', '.');
}
function badge($s)
{
    $map = [
        'pending' => 'b-yellow',
        'processing' => 'b-blue',
        'completed' => 'b-green',
        'cancelled' => 'b-red',
        'ADMIN' => 'b-pink',
        'CUSTOMER' => 'b-purple',
        'active' => 'b-green',
        'inactive' => 'b-gray',
    ];
    return '<span class="badge ' . ($map[$s] ?? 'b-gray') . '">' . htmlspecialchars($s) . '</span>';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Floriene</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Dancing+Script:wght@700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { corePlugins: { preflight: false } };</script>
    <link rel="stylesheet" href="css/admin.css">
</head>

<body>

    <div class="header">
        <div class="header-logo">
            <img src="img/logo.png" alt="Floriene">
            <span class="brand">Floriene</span>
            <span class="role-tag">Admin Panel</span>
        </div>
        <div class="header-right">
            <span style="font-size:13px;color:#9a7080;">Hello,
                <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong></span>
            <a href="index.php" target="_blank">View Website</a>
            <a href="php/auth.php?aksi=logout" class="btn-logout">Logout</a>
        </div>
    </div>

    <nav class="nav">
        <a href="admin.php?section=dashboard" class="<?= $section === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
        <a href="admin.php?section=users" class="<?= $section === 'users' ? 'active' : '' ?>">Users</a>
        <a href="admin.php?section=products" class="<?= $section === 'products' ? 'active' : '' ?>">Products</a>
        <a href="admin.php?section=categories" class="<?= $section === 'categories' ? 'active' : '' ?>">Categories</a>
        <a href="admin.php?section=orders" class="<?= $section === 'orders' ? 'active' : '' ?>">Orders</a>
        <a href="admin.php?section=loyalties" class="<?= $section === 'loyalties' ? 'active' : '' ?>">Loyalty Redemptions</a>
    </nav>

    <div class="main">

        <?php if ($msg): ?>
            <div class="notif">
                <?= ($msg === 'add' ? 'Data added successfully.' :
                     ($msg === 'edit' ? 'Data updated successfully.' :
                     ($msg === 'delete' ? 'Data deleted successfully.' :
                     ($msg === 'approved' ? 'Redemption approved successfully!' :
                     ($msg === 'cancelled' ? 'Redemption cancelled successfully.' : $msg))))) ?>
            </div>
        <?php endif; ?>

        <?php if ($section === 'dashboard'): ?>

            <div class="grid grid-cols-1 min-[480px]:grid-cols-2 min-[768px]:grid-cols-4 gap-4 mb-6">
                <div class="stat-box">
                    <div class="label">Total Customers</div>
                    <div class="value"><?= $dash['users'] ?></div>
                </div>
                <div class="stat-box s-purple">
                    <div class="label">Active Products</div>
                    <div class="value"><?= $dash['produk'] ?></div>
                </div>
                <div class="stat-box s-amber">
                    <div class="label">Total Orders</div>
                    <div class="value"><?= $dash['pesanan'] ?></div>
                </div>
                <div class="stat-box s-green">
                    <div class="label">Pending</div>
                    <div class="value"><?= $dash['pending'] ?></div>
                </div>
            </div>

            <div class="revenue-banner">
                <div>
                    <div class="rv-label">Total Revenue from Completed Orders</div>
                    <div class="rv-value"><?= rupiah($dash['revenue']) ?></div>
                </div>
                <div class="rv-note">Only from completed orders</div>
            </div>

            <div class="grid grid-cols-1 min-[768px]:grid-cols-2 gap-5 mb-6">
                <div class="card">
                    <div class="card-title">Recent Orders</div>
                    <?php if ($recent_orders->num_rows === 0): ?>
                        <div class="empty">No orders yet.</div>
                    <?php else: ?>
                        <?php while ($r = $recent_orders->fetch_assoc()): ?>
                            <div class="mini-row">
                                <div>
                                    <div class="mini-name">#<?= $r['id_pesanan'] ?> - <?= htmlspecialchars($r['nama'] ?? '-') ?>
                                    </div>
                                    <div class="mini-sub"><?= rupiah($r['total_harga']) ?></div>
                                </div>
                                <?= badge($r['status']) ?>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
                <div class="card">
                    <div class="card-title">Recent Users</div>
                    <?php if ($recent_users->num_rows === 0): ?>
                        <div class="empty">No users yet.</div>
                    <?php else: ?>
                        <?php while ($r = $recent_users->fetch_assoc()): ?>
                            <div class="mini-row">
                                <div>
                                    <div class="mini-name"><?= htmlspecialchars($r['nama']) ?></div>
                                    <div class="mini-sub"><?= htmlspecialchars($r['email']) ?></div>
                                </div>
                                <span style="font-size:11px;color:#9a7080;"><?= substr($r['created_at'], 0, 10) ?></span>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>

        <?php endif; ?>

        <?php if ($section === 'users'): ?>

            <div class="sec-head">
                <h2>User Management</h2>
                <div class="sec-head-right">
                    <input type="text" class="input input-search" placeholder="Search name / email..."
                        oninput="cariTabel(this,'tbody-users')">
                    <button class="btn btn-pink" onclick="bukaModal('modal-tambah-user')">+ Add User</button>
                </div>
            </div>

            <div class="card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Joined</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-users">
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="7" class="empty">Belum ada user.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $i => $u): ?>
                                    <tr>
                                        <td style="color:#9a7080;"><?= $i + 1 ?></td>
                                        <td style="font-weight:600;"><?= htmlspecialchars($u['nama']) ?></td>
                                        <td><?= htmlspecialchars($u['email']) ?></td>
                                        <td><?= htmlspecialchars($u['nomor_hp'] ?? '-') ?></td>
                                        <td><?= badge($u['role']) ?></td>
                                        <td style="color:#9a7080;"><?= substr($u['created_at'], 0, 10) ?></td>
                                        <td style="display:flex;gap:6px;">
                                            <button class="btn btn-sm btn-outline"
                                                onclick='isiEditUser(<?= htmlspecialchars(json_encode($u)) ?>)'>Edit</button>
                                            <form method="POST" action="php/user.php" style="margin:0;"
                                                onsubmit="return confirm('Delete this user?')">
                                                <input type="hidden" name="aksi" value="delete">
                                                <input type="hidden" name="id_user" value="<?= $u['id_user'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-overlay" id="modal-tambah-user">
                <div class="modal-box">
                    <div class="modal-head">
                        <h3>Add User</h3>
                        <button onclick="tutupModal('modal-tambah-user')">&#x2715;</button>
                    </div>
                    <form method="POST" action="php/user.php">
                        <input type="hidden" name="aksi" value="add">
                        <div class="fg"><label>Nama</label><input type="text" name="nama" required></div>
                        <div class="fg"><label>Email</label><input type="email" name="email" required></div>
                        <div class="fg"><label>Password</label><input type="password" name="password" required></div>
                        <div class="fg"><label>No. HP</label><input type="text" name="nomor_hp"></div>
                        <div class="fg"><label>Role</label>
                            <select name="role">
                                <option value="CUSTOMER">Customer</option>
                                <option value="ADMIN">Admin</option>
                            </select>
                        </div>
                        <div class="modal-foot">
                            <button type="button" class="btn btn-outline"
                                onclick="tutupModal('modal-tambah-user')">Cancel</button>
                            <button type="submit" class="btn btn-pink">Save</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal-overlay" id="modal-edit-user">
                <div class="modal-box">
                    <div class="modal-head">
                        <h3>Edit User</h3>
                        <button onclick="tutupModal('modal-edit-user')">&#x2715;</button>
                    </div>
                    <form method="POST" action="php/user.php">
                        <input type="hidden" name="aksi" value="edit">
                        <input type="hidden" name="id_user" id="eu-id">
                        <div class="fg"><label>Nama</label><input type="text" name="nama" id="eu-nama" required></div>
                        <div class="fg"><label>Email</label><input type="email" name="email" id="eu-email" required></div>
                        <div class="fg"><label>No. HP</label><input type="text" name="nomor_hp" id="eu-hp"></div>
                        <div class="fg"><label>Role</label>
                            <select name="role" id="eu-role">
                                <option value="CUSTOMER">Customer</option>
                                <option value="ADMIN">Admin</option>
                            </select>
                        </div>
                        <div class="modal-foot">
                            <button type="button" class="btn btn-outline"
                                onclick="tutupModal('modal-edit-user')">Batal</button>
                            <button type="submit" class="btn btn-pink">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

        <?php endif; ?>

        <?php if ($section === 'products'): ?>

            <div class="sec-head">
                <h2>Product Management</h2>
                <div class="sec-head-right">
                    <input type="text" class="input input-search" placeholder="Search products..."
                        oninput="cariTabel(this,'tbody-produk')">
                    <button class="btn btn-pink" onclick="bukaModal('modal-tambah-produk')">+ Add Product</button>
                </div>
            </div>

            <div class="card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Color</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-produk">
                            <?php if (empty($produk)): ?>
                                <tr>
                                    <td colspan="7" class="empty">No products yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($produk as $p): ?>
                                    <tr>
                                        <td>
                                            <?php if ($p['url_foto_produk']): ?>
                                                <img src="<?= htmlspecialchars($p['url_foto_produk']) ?>" class="prod-thumb"
                                                    onerror="this.style.display='none'">
                                            <?php else: ?>
                                                <div class="placeholder-thumb">&#x1F337;</div>
                                            <?php endif; ?>
                                        </td>
                                        <td style="font-weight:600;"><?= htmlspecialchars($p['nama_produk']) ?></td>
                                        <td><?= htmlspecialchars($p['nama_kategori'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($p['warna'] ?? '-') ?></td>
                                        <td style="font-weight:700;color:#c85a80;"><?= rupiah($p['harga']) ?></td>
                                        <td><?= badge($p['status']) ?></td>
                                        <td style="display:flex;gap:6px;">
                                            <button class="btn btn-sm btn-outline"
                                                onclick='isiEditProduk(<?= htmlspecialchars(json_encode($p)) ?>)'>Edit</button>
                                            <form method="POST" action="php/produk.php" style="margin:0;"
                                                onsubmit="return confirm('Delete this product?')">
                                                <input type="hidden" name="aksi" value="delete">
                                                <input type="hidden" name="id_produk" value="<?= $p['id_produk'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-overlay" id="modal-tambah-produk">
                <div class="modal-box">
                    <div class="modal-head">
                        <h3>Add Product</h3>
                        <button onclick="tutupModal('modal-tambah-produk')">&#x2715;</button>
                    </div>
                    <form method="POST" action="php/produk.php" enctype="multipart/form-data">
                        <input type="hidden" name="aksi" value="add">
                        <div class="fg"><label>Nama Produk</label><input type="text" name="nama_produk" required></div>
                        <div class="fg"><label>Warna</label><input type="text" name="warna"></div>
                        <div class="fg"><label>Deskripsi</label><textarea name="deskripsi_produk" rows="3"></textarea></div>
                        <div class="fg"><label>Harga (Rp)</label><input type="number" name="harga" required></div>
                        <div class="fg"><label>Kategori</label>
                            <select name="id_kategori">
                                <?php foreach ($kategori as $k): ?>
                                    <option value="<?= $k['id_kategori'] ?>"><?= htmlspecialchars($k['nama']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="fg"><label>Status</label>
                            <select name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="fg"><label>Foto Produk</label><input type="file" name="foto" accept="image/*"></div>
                        <div class="modal-foot">
                            <button type="button" class="btn btn-outline"
                                onclick="tutupModal('modal-tambah-produk')">Batal</button>
                            <button type="submit" class="btn btn-pink">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal-overlay" id="modal-edit-produk">
                <div class="modal-box">
                    <div class="modal-head">
                        <h3>Edit Product</h3>
                        <button onclick="tutupModal('modal-edit-produk')">&#x2715;</button>
                    </div>
                    <form method="POST" action="php/produk.php" enctype="multipart/form-data">
                        <input type="hidden" name="aksi" value="edit">
                        <input type="hidden" name="id_produk" id="ep-id">
                        <div class="fg"><label>Nama Produk</label><input type="text" name="nama_produk" id="ep-nama"
                                required></div>
                        <div class="fg"><label>Warna</label><input type="text" name="warna" id="ep-warna"></div>
                        <div class="fg"><label>Deskripsi</label><textarea name="deskripsi_produk" id="ep-desk"
                                rows="3"></textarea></div>
                        <div class="fg"><label>Harga (Rp)</label><input type="number" name="harga" id="ep-harga" required>
                        </div>
                        <div class="fg"><label>Kategori</label>
                            <select name="id_kategori" id="ep-kat">
                                <?php foreach ($kategori as $k): ?>
                                    <option value="<?= $k['id_kategori'] ?>"><?= htmlspecialchars($k['nama']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="fg"><label>Status</label>
                            <select name="status" id="ep-status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="fg"><label>Ganti Foto (opsional)</label><input type="file" name="foto" accept="image/*">
                        </div>
                        <div class="modal-foot">
                            <button type="button" class="btn btn-outline"
                                onclick="tutupModal('modal-edit-produk')">Batal</button>
                            <button type="submit" class="btn btn-pink">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

        <?php endif; ?>

        <?php if ($section === 'categories'): ?>

            <div class="sec-head">
                <h2>Category Management</h2>
                <button class="btn btn-pink" onclick="bukaModal('modal-tambah-kategori')">+ Add Category</button>
            </div>

            <div class="card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category Name</th>
                                <th>Description</th>
                                <th>Total Products</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($kategori)): ?>
                                <tr>
                                    <td colspan="5" class="empty">No categories yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($kategori as $i => $k): ?>
                                    <tr>
                                        <td style="color:#9a7080;"><?= $i + 1 ?></td>
                                        <td style="font-weight:600;"><?= htmlspecialchars($k['nama']) ?></td>
                                        <td style="color:#9a7080;"><?= htmlspecialchars($k['deskripsi'] ?? '-') ?></td>
                                        <td><span class="badge b-purple"><?= $k['jml'] ?> produk</span></td>
                                        <td style="display:flex;gap:6px;">
                                            <button class="btn btn-sm btn-outline"
                                                onclick='isiEditKategori(<?= htmlspecialchars(json_encode($k)) ?>)'>Edit</button>
                                            <form method="POST" action="php/kategori.php" style="margin:0;"
                                                onsubmit="return confirm('Hapus kategori ini?')">
                                                <input type="hidden" name="aksi" value="delete">
                                                <input type="hidden" name="id_kategori" value="<?= $k['id_kategori'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-overlay" id="modal-tambah-kategori">
                <div class="modal-box">
                    <div class="modal-head">
                        <h3>Tambah Kategori</h3>
                        <button onclick="tutupModal('modal-tambah-kategori')">&#x2715;</button>
                    </div>
                    <form method="POST" action="php/kategori.php">
                        <input type="hidden" name="aksi" value="add">
                        <div class="fg"><label>Nama Kategori</label><input type="text" name="nama" required></div>
                        <div class="fg"><label>Deskripsi</label><textarea name="deskripsi" rows="3"></textarea></div>
                        <div class="modal-foot">
                            <button type="button" class="btn btn-outline"
                                onclick="tutupModal('modal-tambah-kategori')">Batal</button>
                            <button type="submit" class="btn btn-pink">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal-overlay" id="modal-edit-kategori">
                <div class="modal-box">
                    <div class="modal-head">
                        <h3>Edit Kategori</h3>
                        <button onclick="tutupModal('modal-edit-kategori')">&#x2715;</button>
                    </div>
                    <form method="POST" action="php/kategori.php">
                        <input type="hidden" name="aksi" value="edit">
                        <input type="hidden" name="id_kategori" id="ek-id">
                        <div class="fg"><label>Nama Kategori</label><input type="text" name="nama" id="ek-nama" required>
                        </div>
                        <div class="fg"><label>Deskripsi</label><textarea name="deskripsi" id="ek-desk" rows="3"></textarea>
                        </div>
                        <div class="modal-foot">
                            <button type="button" class="btn btn-outline"
                                onclick="tutupModal('modal-edit-kategori')">Batal</button>
                            <button type="submit" class="btn btn-pink">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

        <?php endif; ?>

        <?php if ($section === 'orders'): ?>

            <div class="sec-head">
                <h2>Order Management</h2>
                <form method="GET" action="admin.php" style="display:flex;gap:8px;align-items:center;">
                    <input type="hidden" name="section" value="orders">
                    <select name="status" class="input" style="width:auto;" onchange="this.form.submit()">
                        <option value="" <?= (($_GET['status'] ?? '') === '') ? 'selected' : '' ?>>All Status</option>
                        <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="processing" <?= ($_GET['status'] ?? '') === 'processing' ? 'selected' : '' ?>>Processing
                        </option>
                        <option value="completed" <?= ($_GET['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed
                        </option>
                        <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled
                        </option>
                    </select>
                </form>
            </div>

            <div class="card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Change Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pesanan)): ?>
                                <tr>
                                    <td colspan="6" class="empty">No orders yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pesanan as $o): ?>
                                    <tr>
                                        <td style="font-weight:700;">#<?= $o['id_pesanan'] ?></td>
                                        <td><?= htmlspecialchars($o['nama_user'] ?? '-') ?></td>
                                        <td style="font-weight:700;color:#c85a80;"><?= rupiah($o['total_harga']) ?></td>
                                        <td><?= badge($o['status']) ?></td>
                                        <td style="color:#9a7080;"><?= substr($o['created_at'], 0, 10) ?></td>
                                        <td style="display:flex;gap:6px;align-items:center;">
                                            <form method="POST" action="php/pesanan.php" style="display:flex;gap:6px;margin:0;">
                                                <input type="hidden" name="aksi" value="edit">
                                                <input type="hidden" name="id_pesanan" value="<?= $o['id_pesanan'] ?>">
                                                <select name="status" class="input" style="padding:5px 10px;font-size:12px;">
                                                    <option value="pending" <?= $o['status'] === 'pending' ? 'selected' : '' ?>>Pending
                                                    </option>
                                                    <option value="processing" <?= $o['status'] === 'processing' ? 'selected' : '' ?>>
                                                        Processing</option>
                                                    <option value="completed" <?= $o['status'] === 'completed' ? 'selected' : '' ?>>
                                                        Completed</option>
                                                    <option value="cancelled" <?= $o['status'] === 'cancelled' ? 'selected' : '' ?>>
                                                        Cancelled</option>
                                                </select>
                                                <button type="submit" class="btn btn-xs btn-pink">OK</button>
                                            </form>
                                            <form method="POST" action="php/pesanan.php" style="margin:0;" onsubmit="return confirm('Delete this order?')">
                                                <input type="hidden" name="aksi" value="delete">
                                                <input type="hidden" name="id_pesanan" value="<?= $o['id_pesanan'] ?>">
                                                <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php endif; ?>

        <?php if ($section === 'loyalties'): ?>

            <div class="sec-head">
                <h2>Loyalty Redemption Management</h2>
            </div>

            <div class="card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer Name</th>
                                <th>Email</th>
                                <th>Bloomies Color</th>
                                <th>Points Used</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pesanan)): ?>
                                <tr>
                                    <td colspan="8" class="empty">No redemptions yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pesanan as $r): ?>
                                    <tr>
                                        <td style="font-weight:700;">#<?= $r['id_redemption'] ?></td>
                                        <td><?= htmlspecialchars($r['nama'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($r['email'] ?? '-') ?></td>
                                        <td style="font-weight:600;color:#c85a80;"><?= htmlspecialchars($r['warna_bloomies']) ?></td>
                                        <td><span class="badge b-yellow"><?= $r['poin_digunakan'] ?> pts</span></td>
                                        <td><?= badge($r['status']) ?></td>
                                        <td style="color:#9a7080;"><?= substr($r['created_at'], 0, 10) ?></td>
                                        <td style="display:flex;gap:6px;align-items:center;">
                                            <?php if ($r['status'] === 'pending'): ?>
                                                <form method="POST" action="php/loyalty-admin.php" style="display:flex;gap:6px;margin:0;">
                                                    <input type="hidden" name="aksi" value="approve">
                                                    <input type="hidden" name="id_redemption" value="<?= $r['id_redemption'] ?>">
                                                    <button type="submit" class="btn btn-xs btn-pink">Approve</button>
                                                </form>
                                                <form method="POST" action="php/loyalty-admin.php" style="margin:0;" onsubmit="return confirm('Cancel this redemption?')">
                                                    <input type="hidden" name="aksi" value="cancel">
                                                    <input type="hidden" name="id_redemption" value="<?= $r['id_redemption'] ?>">
                                                    <button type="submit" class="btn btn-xs btn-danger">Cancel</button>
                                                </form>
                                            <?php else: ?>
                                                <span style="font-size:12px;color:#9a7080;">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php endif; ?>

    </div>

    <script>
        function bukaModal(id) {
            document.getElementById(id).classList.add('open');
        }
        function tutupModal(id) {
            document.getElementById(id).classList.remove('open');
        }

        document.querySelectorAll('.modal-overlay').forEach(function (el) {
            el.addEventListener('click', function (e) {
                if (e.target === el) el.classList.remove('open');
            });
        });

        function isiEditUser(u) {
            document.getElementById('eu-id').value = u.id_user;
            document.getElementById('eu-nama').value = u.nama;
            document.getElementById('eu-email').value = u.email;
            document.getElementById('eu-hp').value = u.nomor_hp || '';
            document.getElementById('eu-role').value = u.role;
            bukaModal('modal-edit-user');
        }

        function isiEditProduk(p) {
            document.getElementById('ep-id').value = p.id_produk;
            document.getElementById('ep-nama').value = p.nama_produk;
            document.getElementById('ep-warna').value = p.warna || '';
            document.getElementById('ep-desk').value = p.deskripsi_produk || '';
            document.getElementById('ep-harga').value = p.harga;
            document.getElementById('ep-status').value = p.status;
            document.getElementById('ep-kat').value = p.id_kategori;
            bukaModal('modal-edit-produk');
        }

        function isiEditKategori(k) {
            document.getElementById('ek-id').value = k.id_kategori;
            document.getElementById('ek-nama').value = k.nama;
            document.getElementById('ek-desk').value = k.deskripsi || '';
            bukaModal('modal-edit-kategori');
        }

        function cariTabel(input, tbodyId) {
            var keyword = input.value.toLowerCase();
            var rows = document.getElementById(tbodyId).querySelectorAll('tr');
            rows.forEach(function (row) {
                var teks = row.textContent.toLowerCase();
                row.style.display = teks.includes(keyword) ? '' : 'none';
            });
        }
    </script>

</body>

</html>