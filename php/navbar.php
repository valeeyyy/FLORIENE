<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($nav_active)) {
    $nav_active = $nav_active;
} else {
    $nav_active = '';
}
$nav_links = [
    ['href' => 'index.html',        'label' => 'Home',     'key' => 'home'],
    ['href' => 'about.html',        'label' => 'About',    'key' => 'about'],
    ['href' => 'index.html#produk', 'label' => 'Products', 'key' => 'products'],
    ['href' => 'index.html#loyalty','label' => 'Loyalty',  'key' => 'loyalty'],
];
$logged_in = isset($_SESSION['id_user']);
$nav_nama  = $_SESSION['nama'] ?? 'User';
$nav_role  = $_SESSION['role'] ?? '';
$dash_href  = $nav_role === 'admin' ? 'admin.php' : 'profile.php';
$dash_label = $nav_role === 'admin' ? 'View Dashboard' : 'My Dashboard';
?>
<nav class="navbar" id="navbar">
  <a href="index.html" class="nav-logo">
    <img src="img/logo.jpeg" alt="Floriene">
    <span class="nav-brand">Floriene</span>
  </a>
  <button class="nav-hamburger" onclick="toggleNav()"><span></span><span></span><span></span></button>
  <ul class="nav-links" id="nav-links">
    <?php
    $class_home = '';
    if ($nav_active === 'home') {
        $class_home = 'active';
    }
    ?>
    <li><a href="index.html" class="<?php echo $class_home; ?>">Home</a></li>

    <?php
    $class_about = '';
    if ($nav_active === 'about') {
        $class_about = 'active';
    }
    ?>
    <li><a href="about.html" class="<?php echo $class_about; ?>">About</a></li>

    <?php
    $class_products = '';
    if ($nav_active === 'products') {
        $class_products = 'active';
    }
    ?>
    <li><a href="index.html#produk" class="<?php echo $class_products; ?>">Products</a></li>

    <?php
    $class_loyalty = '';
    if ($nav_active === 'loyalty') {
        $class_loyalty = 'active';
    }
    ?>
<li><a href="index.html#loyalty" class="<?php echo $class_loyalty; ?>">Loyalty</a></li>
    <?php if ($logged_in): ?>
      <li class="nav-mobile-login">
        <span style="display:block;padding:12px;color:var(--muted);font-size:13px;">Hi, <?= htmlspecialchars($nav_nama) ?></span>
        <a href="<?= $dash_href ?>" style="display:block;"><?= $dash_label ?></a>
        <a href="php/auth.php?aksi=logout" style="display:block;">Logout</a>
      </li>
    <?php else: ?>
      <li class="nav-mobile-login"><a href="auth.html">Login / Register</a></li>
    <?php endif; ?>
  </ul>
  <div class="nav-cta">
    <?php if ($logged_in): ?>
      <span class="nav-greeting">Hi, <?= htmlspecialchars($nav_nama) ?></span>
      <a href="<?= $dash_href ?>" class="btn btn-pink btn-sm"><?= $dash_label ?></a>
      <a href="php/auth.php?aksi=logout" class="btn btn-outline btn-sm">Logout</a>
    <?php else: ?>
      <a href="auth.html" class="btn btn-pink btn-sm">Login / Register</a>
    <?php endif; ?>
  </div>
</nav>
