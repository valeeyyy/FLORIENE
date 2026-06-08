<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: auth.html");
    exit();
}
include 'php/db.php';
$id = (int) $_SESSION['id_user'];

$user = $conn->query("SELECT id_user, nama, email, nomor_hp, role, created_at FROM user WHERE id_user=$id")->fetch_assoc();
if (!$user) {
    session_destroy();
    header("Location: auth.html");
    exit();
}
$loyalty = $conn->query("SELECT id_loyalty_card, poin FROM loyalty_card WHERE id_user=$id")->fetch_assoc();
$pending = $conn->query("SELECT id_redemption, warna_bloomies, status, created_at FROM loyalty_redemption WHERE id_user=$id AND status='pending' LIMIT 1")->fetch_assoc();
$orders = [];
$res = $conn->query("SELECT id_pesanan, total_harga, status, created_at FROM pesanan WHERE id_user=$id ORDER BY created_at DESC");
while ($r = $res->fetch_assoc()) {
    $orders[] = $r;
}

$poin = $loyalty ? (int) $loyalty['poin'] : 0;
$siap_redeem = $poin >= 10;

$msg = $_GET['msg'] ?? '';
$banner_map = [
    'profile_updated'  => ['success', '✅ Profile updated successfully!'],
    'password_changed' => ['success', '✅ Password changed successfully!'],
    'password_wrong'   => ['error',   '⚠️ Current password is incorrect.'],
    'redeem_ok'        => ['success', '✅ Redemption successful! 10 points used. Admin will process your reward.'],
    'redeem_fail'      => ['error',   '⚠️ Failed to redeem reward. Make sure you have at least 10 points.'],
];
$banner = $banner_map[$msg] ?? null;

function fmt_date($s)
{
    if (!$s) return '-';
    $t = strtotime($s);
    return $t ? date('d M Y', $t) : htmlspecialchars($s);
}
function status_class($s)
{
    $map = ['pending' => 'status-pending', 'processing' => 'status-proses', 'completed' => 'status-selesai', 'cancelled' => 'status-pending'];
    return $map[$s] ?? 'status-pending';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile - Floriene</title>
  <meta name="description" content="Manage your Floriene account, view orders, and track loyalty points.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config = { corePlugins: { preflight: false } };</script>
  <link rel="stylesheet" href="css/shared.css">
</head>
<body class="profile-page">

<?php $nav_active = 'profile'; include 'php/navbar.php'; ?>

<?php if ($banner): ?>
<div style="max-width:960px;margin:90px auto 0;padding:0 20px;">
  <div style="padding:12px 16px;border-radius:10px;font-size:14px;font-weight:600;<?= $banner[0] === 'success' ? 'background:#d1fae5;color:#065f46;' : 'background:#fee2e2;color:#991b1b;' ?>">
    <?= htmlspecialchars($banner[1]) ?>
  </div>
</div>
<?php endif; ?>

<div class="profile-hero" id="profile-hero">
  <div class="profile-avatar" id="profile-av">👤</div>
  <div class="profile-name"  id="profile-name-hero"><?= htmlspecialchars($user['nama']) ?></div>
  <div class="profile-email" id="profile-email-hero"><?= htmlspecialchars($user['email']) ?></div>
  <div class="profile-role-badge">🌸 Floriene Customer</div>
</div>

<div class="profile-body">
  <div class="grid grid-cols-1 min-[900px]:grid-cols-2 gap-5">

    <div class="profile-card">
      <div class="profile-card-title"><span class="card-icon">👤</span> Account Information</div>
      <div class="profile-info-row">
        <span class="profile-info-label">Full Name</span>
        <span class="profile-info-val"><?= htmlspecialchars($user['nama']) ?></span>
      </div>
      <div class="profile-info-row">
        <span class="profile-info-label">Email Address</span>
        <span class="profile-info-val"><?= htmlspecialchars($user['email']) ?></span>
      </div>
      <div class="profile-info-row">
        <span class="profile-info-label">WhatsApp</span>
        <span class="profile-info-val"><?= $user['nomor_hp'] ? htmlspecialchars($user['nomor_hp']) : 'Not set' ?></span>
      </div>
      <div class="profile-info-row">
        <span class="profile-info-label">Account Role</span>
        <span class="profile-info-val"><?= $user['role'] === 'ADMIN' ? 'Admin' : 'Customer' ?></span>
      </div>
      <div class="profile-info-row">
        <span class="profile-info-label">Joined</span>
        <span class="profile-info-val"><?= fmt_date($user['created_at']) ?></span>
      </div>
      <div style="margin-top:18px;">
        <button class="btn btn-pink" style="width:100%;justify-content:center;" onclick="openEditModal()">✏️ Edit Profile</button>
      </div>
    </div>

    <div class="profile-card">
      <div class="profile-card-title"><span class="card-icon">🎀</span> Loyalty Card</div>
      <div class="loyalty-poin-big"><?= $poin ?></div>
      <div class="loyalty-poin-sub">Loyalty Points Collected</div>
      <?php if ($poin >= 10): ?>
      <div style="text-align:center; margin-top:10px; font-size:13px; font-weight:700; color:var(--pink-dark);">🎁 <?= floor($poin / 10) ?> reward(s) ready</div>
      <?php endif; ?>
      <div style="margin-top:18px; padding:14px; background:var(--pink-bg); border-radius:10px; border:1px solid var(--border);">
        <div style="font-size:12px; font-weight:700; color:var(--dark); margin-bottom:6px;">🎁 Reward</div>
        <div style="font-size:12px; color:var(--muted); line-height:1.7;">Collect <strong>10 points</strong> and get <strong>1 FREE Bloomies Keychain!</strong><br><span style="font-size:11px;">Points are credited automatically when your order is completed by admin.</span></div>
        <?php if (!$pending && $siap_redeem): ?>
        <button class="btn btn-pink" style="width:100%;justify-content:center;margin-top:10px;" onclick="openRedeemModal()">🎁 Redeem Reward</button>
        <?php endif; ?>
      </div>
      <?php if ($pending): ?>
      <div style="margin-top:12px; padding:12px; background:#fff3cd; border-radius:8px; border:1px solid #ffeaa7;">
        <div style="font-size:11px; font-weight:700; color:#856404;">⏳ Awaiting Admin Approval</div>
        <div style="font-size:11px; color:#856404; margin-top:4px;">Color: <strong><?= htmlspecialchars($pending['warna_bloomies']) ?></strong></div>
      </div>
      <?php endif; ?>
      <?php if ($loyalty): ?>
      <div style="margin-top:12px; font-size:11px; color:var(--muted); text-align:center;">Card ID: <?= (int) $loyalty['id_loyalty_card'] ?></div>
      <?php endif; ?>
    </div>

    <div class="profile-card profile-card-full">
      <div class="profile-card-title"><span class="card-icon">📦</span> Order History</div>
      <div class="order-list">
        <?php if (empty($orders)): ?>
        <div style="text-align:center; padding:28px; color:var(--muted); font-size:14px;">
          No orders yet. <a href="index.php#pesan" style="color:var(--pink-dark);font-weight:700;">Place your first order!</a>
        </div>
        <?php else: foreach ($orders as $o): ?>
        <div class="order-item">
          <div class="order-item-left">
            <div class="order-no">#<?= (int) $o['id_pesanan'] ?></div>
            <div class="order-date"><?= fmt_date($o['created_at']) ?> · Rp <?= number_format($o['total_harga'], 0, ',', '.') ?></div>
          </div>
          <span class="order-status <?= status_class($o['status']) ?>"><?= htmlspecialchars($o['status']) ?></span>
        </div>
        <?php endforeach; endif; ?>
      </div>
    </div>

    <div class="profile-card">
      <div class="profile-card-title"><span class="card-icon">🔐</span> Change Password</div>
      <form id="form-change-password" method="POST" action="php/akun.php" onsubmit="return validatePw()" novalidate>
        <input type="hidden" name="aksi" value="password">
        <div class="fg">
          <label for="pw-current">Current Password</label>
          <div class="fg-pw">
            <input type="password" id="pw-current" name="current" placeholder="Your current password" required>
            <button type="button" class="fg-pw-toggle" onclick="togglePw('pw-current',this)">👁️</button>
          </div>
        </div>
        <div class="fg">
          <label for="pw-new">New Password</label>
          <div class="fg-pw">
            <input type="password" id="pw-new" name="new" placeholder="Minimum 6 characters" required minlength="6">
            <button type="button" class="fg-pw-toggle" onclick="togglePw('pw-new',this)">👁️</button>
          </div>
        </div>
        <div class="fg">
          <label for="pw-confirm">Confirm New Password</label>
          <div class="fg-pw">
            <input type="password" id="pw-confirm" name="confirm" placeholder="Repeat new password" required>
            <button type="button" class="fg-pw-toggle" onclick="togglePw('pw-confirm',this)">👁️</button>
          </div>
        </div>
        <button type="submit" class="btn btn-success" style="width:100%;justify-content:center;margin-top:4px;">🔒 Save New Password</button>
      </form>
    </div>

    <div class="profile-card">
      <div class="profile-card-title" style="color:var(--danger)"><span class="card-icon">⚠️</span> Danger Zone</div>
      <div class="delete-zone">
        <p>Deleting your account is <strong>permanent</strong> and cannot be undone. All your data including order history and loyalty points will be permanently removed.</p>
        <form method="POST" action="php/akun.php" onsubmit="return confirm('Delete your account permanently? All your data and order history will be removed. This cannot be undone.');">
          <input type="hidden" name="aksi" value="delete">
          <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center;">🗑️ Delete My Account</button>
        </form>
      </div>
    </div>

  </div>
</div>

<div class="fl-modal-overlay" id="edit-modal" style="display:none;">
  <div class="fl-modal-box" style="max-width:480px;">
    <div class="fl-modal-icon">✏️</div>
    <h3 class="fl-modal-title">Edit Profile</h3>
    <form id="form-edit-profile" method="POST" action="php/akun.php" novalidate style="text-align:left;">
      <input type="hidden" name="aksi" value="update">
      <div class="fg">
        <label for="e-nama">Full Name <span style="color:var(--danger)">*</span></label>
        <input type="text" id="e-nama" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
      </div>
      <div class="fg">
        <label for="e-email">Email Address <span style="color:var(--danger)">*</span></label>
        <input type="email" id="e-email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
      </div>
      <div class="fg">
        <label for="e-hp">WhatsApp Number</label>
        <input type="tel" id="e-hp" name="nomor_hp" value="<?= htmlspecialchars($user['nomor_hp'] ?? '') ?>">
      </div>
      <div class="fl-modal-btns" style="margin-top:20px;">
        <button type="button" class="btn btn-ghost" onclick="closeEditModal()">Cancel</button>
        <button type="submit" class="btn btn-pink" id="btn-save-profile">💾 Save Changes</button>
      </div>
    </form>
  </div>
</div>

<div class="fl-modal-overlay" id="redeem-modal" style="display:none;">
  <div class="fl-modal-box" style="max-width:480px;">
    <div class="fl-modal-icon">🎁</div>
    <h3 class="fl-modal-title">Redeem Loyalty Points</h3>
    <form id="form-redeem" method="POST" action="php/loyalty.php" novalidate style="text-align:left;">
      <input type="hidden" name="aksi" value="redeem">
      <div style="margin-bottom:16px; padding:12px; background:#f0f0f0; border-radius:8px; text-align:center;">
        <div style="font-size:12px; color:var(--muted); margin-bottom:4px;">Redeeming</div>
        <div style="font-size:24px; font-weight:700; color:var(--pink-dark);">10 Points</div>
      </div>
      <div class="fg">
        <label for="redeem-warna">Choose Bloomies Keychain Color</label>
        <select id="redeem-warna" name="warna" required style="width:100%;">
          <option value="">Select Color</option>
          <option value="Pink">Pink</option>
          <option value="Purple">Purple</option>
          <option value="Yellow">Yellow</option>
          <option value="Blue">Blue</option>
        </select>
      </div>
      <div style="margin:16px 0; padding:12px; background:#e8f5e9; border-radius:8px; border-left:3px solid #4caf50;">
        <div style="font-size:12px; color:#2e7d32; line-height:1.6;">
          ✅ After you confirm, admin will process your bloomies keychain in your chosen color.<br><br>
          🎁 <strong>10 points</strong> will be deducted right away. Any extra points stay on your card for the next reward.
        </div>
      </div>
      <div class="fl-modal-btns" style="margin-top:20px;">
        <button type="button" class="btn btn-ghost" onclick="closeRedeemModal()">Cancel</button>
        <button type="submit" class="btn btn-pink">✓ Confirm Redemption</button>
      </div>
    </form>
  </div>
</div>

<footer class="footer">
  <div class="footer-brand">Floriene</div>
  <div class="footer-links">
    <a href="index.php">Home</a>
    <a href="about.html">About</a>
    <a href="index.php#produk">Products</a>
  </div>
  <p class="footer-copy">© 2026 Floriene Surabaya · Handcrafted with ❤️</p>
</footer>

<script src="js/shared.js"></script>
<script>
  function togglePw(id, btn) {
    var inp = document.getElementById(id);
    var show = inp.type === 'password';
    inp.type = show ? 'text' : 'password';
    btn.textContent = show ? '🙈' : '👁️';
  }

  function validatePw() {
    var newPw   = document.getElementById('pw-new').value;
    var confirm = document.getElementById('pw-confirm').value;
    if (newPw.length < 6) { alert('New password must be at least 6 characters.'); return false; }
    if (newPw !== confirm) { alert('New passwords do not match.'); return false; }
    return true;
  }

  function openEditModal() {
    document.getElementById('edit-modal').style.display = 'flex';
  }
  function closeEditModal() {
    document.getElementById('edit-modal').style.display = 'none';
  }
  document.getElementById('edit-modal').addEventListener('click', function (e) {
    if (e.target === document.getElementById('edit-modal')) closeEditModal();
  });

  function openRedeemModal() {
    document.getElementById('redeem-warna').value = '';
    document.getElementById('redeem-modal').style.display = 'flex';
  }
  function closeRedeemModal() {
    document.getElementById('redeem-modal').style.display = 'none';
  }
  document.getElementById('redeem-modal').addEventListener('click', function (e) {
    if (e.target === document.getElementById('redeem-modal')) closeRedeemModal();
  });
</script>
</body>
</html>
