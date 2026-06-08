<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Floriene - Handmade Pipe Cleaner Flowers, Surabaya</title>
  <meta name="description" content="Floriene - handmade pipe cleaner flower keychains & single stems from Surabaya. Beautiful, kawaii, and full of love. Made by order.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config = { corePlugins: { preflight: false } };</script>
  <link rel="stylesheet" href="css/shared.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div id="navbar-root"></div>
<section class="hero" id="home">
  <div class="container">
    <div class="hero-inner">
      <div>
        <div class="hero-pill">🌸 Handmade · Surabaya · Est. 2026</div>
        <h1 class="hero-h1">
          Share the <span class="accent">Beauty</span><br>
          in Little Things
        </h1>
        <p class="hero-p">
          Beautiful, kawaii, and heartfelt handcrafted flower keychains &amp; single stem flowers - made from pipe cleaner, one by one. A gift that never withers.
        </p>
        <div class="hero-btns">
          <a href="#pesan" class="btn btn-pink">🌷 Order Now</a>
          <a href="#produk" class="btn btn-ghost">View Collection</a>
        </div>
        <div class="hero-trust">
          <div class="trust-item"><span>✓</span> 100% Handmade</div>
          <div class="trust-item"><span>✓</span> Made by Order</div>
          <div class="trust-item"><span>✓</span> Kawaii Packaging</div>
          <div class="trust-item"><span>✓</span> Campus Pick-Up</div>
        </div>
      </div>

      <div class="hero-visual">
        <div class="hero-card-wrap">
          <div class="hero-card">
            <img src="img/logo.png" alt="Floriene">
            <div class="hero-card-name">Floriene</div>
            <div class="hero-card-sub">Handmade Flowers &amp; Accessories<br>Share the beauty in little things</div>
            <span class="hero-card-badge">🌸 Floriene Surabaya</span>
          </div>
          <div class="hbadge hb-tl">
            <span class="hbadge-icon">🎀</span>
            <div>Free Wrapping<span class="hbadge-sub">All orders</span></div>
          </div>
          <div class="hbadge hb-tr">
            <span class="hbadge-icon">⭐</span>
            <div>Trusted<span class="hbadge-sub">30+ customers</span></div>
          </div>
          <div class="hbadge hb-bl">
            <span class="hbadge-icon">🌸</span>
            <div>Handmade<span class="hbadge-sub">Made with ❤</span></div>
          </div>
          <div class="hbadge hb-br">
            <span class="hbadge-icon">🎓</span>
            <div>Pick-Up<span class="hbadge-sub">UC Campus</span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="stats">
  <div class="container">
    <div class="stats-grid">
      <div class="stat-cell"><div class="stat-n">30+</div><div class="stat-l">Satisfied Customers</div></div>
      <div class="stat-cell"><div class="stat-n">2</div><div class="stat-l">Product Collections</div></div>
      <div class="stat-cell"><div class="stat-n">9</div><div class="stat-l">Catalog Variants</div></div>
      <div class="stat-cell"><div class="stat-n">100%</div><div class="stat-l">Handmade</div></div>
    </div>
  </div>
</div>

<section class="products" id="produk">
  <div class="container">
    <div class="sec-head-center">
      <span class="sec-label">Collection</span>
      <h2 class="sec-title">Our Products</h2>
      <p class="sec-sub">Two handmade pipe cleaner flower collections - from cute keychains to elegant single stems. Made by order, picked up on campus.</p>
    </div>

    <?php
    include 'php/db.php';
    $icons = ['🔑', '🌷', '🌸', '🌼'];
    $icon_index = 0;
    $kat_result = $conn->query("SELECT * FROM kategori ORDER BY id_kategori ASC");
    while ($kat = $kat_result->fetch_assoc()):
      $id_kat = $kat['id_kategori'];
      $produk_result = $conn->query("SELECT * FROM produk WHERE id_kategori=$id_kat AND status='active' ORDER BY id_produk ASC");
      $icon = $icons[$icon_index % count($icons)];
      $icon_index++;
    ?>
    <div class="cat-block">
      <div class="cat-head">
        <div class="cat-icon a"><?= $icon ?></div>
        <div class="cat-head-info">
          <h3><?= htmlspecialchars($kat['nama']) ?></h3>
          <p><?= htmlspecialchars($kat['deskripsi']) ?></p>
        </div>
      </div>
      <div class="cat-body">
        <div class="grid gap-4 grid-cols-1 min-[560px]:grid-cols-2 min-[860px]:grid-cols-4">
          <?php while ($p = $produk_result->fetch_assoc()): ?>
          <div class="prod-card">
            <div class="prod-img-wrap">
              <?php if ($p['url_foto_produk']): ?>
              <img src="<?= htmlspecialchars($p['url_foto_produk']) ?>" alt="<?= htmlspecialchars($p['nama_produk']) ?>" class="prod-thumb" loading="lazy"
                   onerror="this.onerror=null;this.src='img/logo.png';this.style.objectFit='contain';this.style.background='#fdf2f6';this.style.padding='40px';">
              <?php else: ?>
              <div class="prod-thumb-emoji">🌸</div>
              <?php endif; ?>
              <?php if ($p['warna']): ?>
              <span class="prod-ribbon" style="background:#c85a80;"><?= htmlspecialchars($p['warna']) ?></span>
              <?php endif; ?>
            </div>
            <div class="prod-body">
              <div class="prod-name"><?= htmlspecialchars($p['nama_produk']) ?></div>
              <?php if ($p['deskripsi_produk']): ?>
              <p class="prod-desc"><?= htmlspecialchars($p['deskripsi_produk']) ?></p>
              <?php endif; ?>
              <div class="prod-footer" style="margin-top:8px;">
                <div class="prod-price">Rp <?= number_format($p['harga'], 0, ',', '.') ?></div>
                <button class="btn btn-pink btn-sm" onclick="orderProduk(<?= $p['id_produk'] ?>)">Order</button>
              </div>
            </div>
          </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>
    <?php endwhile; ?>

  </div>
</section>

<section class="benefits" id="keunggulan">
  <div class="container">
    <div class="sec-head-center">
      <span class="sec-label">Benefits</span>
      <h2 class="sec-title">Why Floriene?</h2>
      <p class="sec-sub">Three things that make Floriene different and always loved by its customers.</p>
    </div>
    <div class="grid grid-cols-1 min-[860px]:grid-cols-3 gap-5">
      <div class="benefit-card">
        <div class="b-icon">🌸</div>
        <h3>100% Handmade</h3>
        <p>Each product is made one by one by hand - every pipe cleaner twisted and shaped with care and love.</p>
      </div>
      <div class="benefit-card">
        <div class="b-icon">🎀</div>
        <h3>Kawaii Packaging</h3>
        <p>Packed in cute packaging - ready to be an instant gift. The unboxing itself becomes a joyful moment!</p>
      </div>
      <div class="benefit-card">
        <div class="b-icon">♾️</div>
        <h3>Flowers That Last</h3>
        <p>Unlike real flowers, our pipe cleaner flowers never wilt, never fade, and never need water - they stay beautiful forever.</p>
      </div>
    </div>
  </div>
</section>

<section class="loyalty" id="loyalty">
  <div class="container">
    <div class="sec-head-center">
      <span class="sec-label">Loyalty Program</span>
      <h2 class="sec-title">Floriene Loyalty Card</h2>
      <p class="sec-sub">Every completed order earns you loyalty points. Redeem your points for a free Bloomies Keychain!</p>
    </div>

    <div class="loyalty-steps">
      <div class="loyalty-step">
        <div class="step-num">1</div>
        <h4>Place an Order</h4>
        <p>Order any product from the catalog. Points are credited when your order status is marked <strong>completed</strong>.</p>
      </div>
      <div class="loyalty-step">
        <div class="step-num">2</div>
        <h4>Accumulate Points</h4>
        <p>Track your loyalty points on your <strong>Profile page</strong>. Each completed order earns you points.</p>
      </div>
      <div class="loyalty-step">
        <div class="step-num">3</div>
        <h4>Redeem Reward 🎉</h4>
        <p>Reach the required points and redeem for a <strong>free Bloomies Keychain</strong>!</p>
      </div>
    </div>

    <div class="tnc-box">
      <h4>📋 Terms &amp; Conditions</h4>
      <ul class="tnc-list">
        <li>Rewards can only be redeemed for Bloomies Keychain products</li>
        <li>Rewards cannot be exchanged for cash or other products</li>
        <li>Loyalty points are credited automatically after order status is set to completed</li>
        <li>Loyalty cards are exclusive to customer accounts</li>
        <li>All orders are picked up on campus - no delivery service</li>
      </ul>
    </div>

    <div class="loyalty-card-grid">
      <div>
        <div class="lcard-frame"><img src="img/loyaltyfront.png" alt="Floriene Loyalty Card Front"></div>
        <p class="lcard-cap">Front View</p>
      </div>
      <div>
        <div class="lcard-frame"><img src="img/loyaltyback.jpg" alt="Floriene Loyalty Card Back"></div>
        <p class="lcard-cap">Back View</p>
      </div>
    </div>
  </div>
</section>

<section class="testimonials" id="testimoni">
  <div class="container">
    <div class="sec-head-center">
      <span class="sec-label">Reviews</span>
      <h2 class="sec-title">What They Say</h2>
      <p class="sec-sub">Real stories from customers who have experienced the beauty of Floriene.</p>
    </div>
    <div class="testi-grid">
      <div class="testi-card">
        <div class="testi-stars">★★★★★</div>
        <p class="testi-text">"The Bloomies keychains are so cute! The colors are exactly like the photos, the packaging is neat and kawaii. Immediately gave it as a gift to my best friend - she loves it! Will definitely order again."</p>
        <div class="testi-person">
          <div class="testi-av">👩</div>
          <div>
            <div class="testi-name">Christa K.</div>
            <div class="testi-role">Floriene Customer</div>
          </div>
        </div>
      </div>
      <div class="testi-card">
        <div class="testi-stars">★★★★★</div>
        <p class="testi-text">"Bought Everbloom Single Stem for mom's birthday - the result is so beautiful! The flower details are neat, the packaging is aesthetic. Mom is very happy. Highly recommended!"</p>
        <div class="testi-person">
          <div class="testi-av">🧑</div>
          <div>
            <div class="testi-name">Kevin R.</div>
            <div class="testi-role">Floriene Customer</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="cta-banner">
  <img src="img/logo.png" alt="Floriene" class="cta-logo">
  <div class="cta-label">Ready to Order?</div>
  <div class="cta-h2">Order Now,<br>Right Here!</div>
  <p class="cta-p">Fill out the order form below. All orders are made by hand and picked up on campus. We'll process it with love!</p>
  <button class="btn btn-white" onclick="document.getElementById('pesan').scrollIntoView({behavior:'smooth'})">
    🌷 Fill Order Form
  </button>
  <p class="cta-footnote">
    <strong>Made by Order</strong> &nbsp;·&nbsp;
    <strong>Campus Pick-Up</strong> &nbsp;·&nbsp;
    <strong>Kawaii</strong> Packaging
  </p>
</section>

<section class="form-section" id="pesan">
  <div class="container">
    <div class="form-card">
      <div class="form-logo-row">
        <img src="img/logo.png" alt="Floriene">
      </div>
      <div class="form-title">Order Form</div>
      <p class="form-sub">Select from our catalog below. All orders are picked up on campus - no delivery. We'll prepare it with love! 🌸</p>

      <div id="order-form">
        <div class="grid grid-cols-1 min-[860px]:grid-cols-2 gap-3">
          <div class="fg">
            <label for="f-nama">Full Name</label>
            <input type="text" id="f-nama" placeholder="Your name..." required>
          </div>
          <div class="fg">
            <label for="f-hp">WhatsApp Number</label>
            <input type="tel" id="f-hp" placeholder="08xx xxxx xxxx" required>
          </div>
        </div>
        <div class="fg">
          <label for="f-produk">Select Product</label>
          <select id="f-produk" required>
            <option value="">- Choose a product -</option>
          </select>
        </div>
        <div class="grid grid-cols-1 min-[860px]:grid-cols-2 gap-3">
          <div class="fg">
            <label for="f-qty">Quantity</label>
            <input type="number" id="f-qty" min="1" value="1">
          </div>
          <div class="fg">
            <label for="f-tgl">Pickup Date Needed</label>
            <input type="date" id="f-tgl">
          </div>
        </div>
        <div class="fg">
          <label for="f-catatan">Notes (optional)</label>
          <textarea id="f-catatan" rows="3" placeholder="Any special notes for your order..."></textarea>
        </div>

        <div id="price-preview" style="background:var(--pink-bg); border:1px solid var(--border); border-radius:10px; padding:14px 18px; margin-bottom:14px;">
          <div class="flex justify-between items-center">
            <span style="font-size:13px; color:var(--muted);">Total</span>
            <span id="price-total" style="font-size:18px; font-weight:800; color:var(--pink-dark);">Rp 0</span>
          </div>
          <div style="font-size:11px; color:var(--muted); margin-top:4px;">📍 Campus pick-up only · No delivery</div>
        </div>

        <button class="btn-submit" onclick="submitOrder()">🌸 Submit Order</button>
        <p class="form-privacy">🔒 Your data is kept safe and used only to process your order.</p>
      </div>

      <div class="form-ok" id="form-ok">
        <span class="ok-icon">🎉</span>
        <h3>Order Received!</h3>
        <p>Thank you, <strong id="ok-name"></strong>!<br>
        Your order has been received and will be processed shortly. We'll contact you via WhatsApp to confirm. 🌸</p>
        <p style="font-size:12px; color:var(--muted); margin-top:12px;">📍 Remember: pick up your order on campus.</p>
      </div>
    </div>
  </div>
</section>

<footer class="footer">
  <div class="footer-brand">Floriene</div>
  <div class="footer-links">
    <a href="index.php">Home</a>
    <a href="about.html">About</a>
    <a href="index.php#produk">Products</a>
    <a href="index.php#loyalty">Loyalty Card</a>
    <a href="auth.html">Login / Register</a>
  </div>
  <p class="footer-copy">© 2026 Floriene Surabaya · Handcrafted with ❤️</p>
</footer>

<?php
$produk_for_js = $conn->query("SELECT id_produk, nama_produk, harga FROM produk WHERE status='active' ORDER BY id_produk ASC");
$produk_js_array = [];
while ($row = $produk_for_js->fetch_assoc()) {
    $produk_js_array[] = ['id' => (int)$row['id_produk'], 'nama' => $row['nama_produk'], 'harga' => (int)$row['harga']];
}
?>

<script src="js/shared.js"></script>
<script src="js/script.js"></script>
<script>
  document.getElementById('navbar-root').innerHTML = renderNavbar('home');

  var produkList = <?= json_encode($produk_js_array) ?>;

  var selProduk = document.getElementById('f-produk');
  produkList.forEach(function (p) {
    var opt = document.createElement('option');
    opt.value = p.id;
    opt.textContent = p.nama + ' - ' + formatRp(p.harga);
    selProduk.appendChild(opt);
  });

  function hitungTotal() {
    var id = parseInt(selProduk.value);
    var qty = parseInt(document.getElementById('f-qty').value) || 0;
    var p = produkList.find(function(x){ return x.id === id; });
    var total = p ? p.harga * qty : 0;
    document.getElementById('price-total').textContent = formatRp(total);
  }
  selProduk.addEventListener('change', hitungTotal);
  document.getElementById('f-qty').addEventListener('input', hitungTotal);
  hitungTotal();

  // Called from the "Order" button on each product card
  function orderProduk(id) {
    selProduk.value = String(id);
    hitungTotal();
    document.getElementById('pesan').scrollIntoView({ behavior: 'smooth' });
  }

  async function submitOrder() {
    var nama = document.getElementById('f-nama').value.trim();
    var hp   = document.getElementById('f-hp').value.trim();
    var id   = parseInt(selProduk.value);
    var qty  = parseInt(document.getElementById('f-qty').value) || 1;
    var tgl  = document.getElementById('f-tgl').value;
    var note = document.getElementById('f-catatan').value.trim();

    var p = produkList.find(function (x) { return x.id === id; });
    if (!nama || !hp || !p) {
      alert('Please fill in your name, WhatsApp number, and choose a product first. 🌸');
      return;
    }

    // Build notes with the contact info so admin has it (no dedicated columns for nama/hp)
    var catatan = 'Contact: ' + nama + ' (' + hp + ')';
    if (note) catatan += ' | Notes: ' + note;

    var fd = new FormData();
    fd.append('id_produk', id);
    fd.append('jumlah', qty);
    fd.append('tanggal_pengiriman', tgl);
    fd.append('catatan', catatan);

    var res;
    try {
      res = await fetch('php/order.php', { method: 'POST', body: fd }).then(function (r) { return r.json(); });
    } catch (e) {
      alert('Something went wrong. Please try again.');
      return;
    }

    if (res.need_login) {
      alert('Please login as a customer first to place an order. 🌸');
      window.location.href = 'auth.html';
      return;
    }
    if (!res.success) {
      alert(res.message || 'Sorry, your order could not be saved. Please try again.');
      return;
    }

    document.getElementById('ok-name').textContent = nama;
    document.getElementById('order-form').style.display = 'none';
    document.getElementById('form-ok').style.display = 'block';
  }
</script>
</body>
</html>