function renderNavbar(activePage) {
    var links = [
        { href: 'index.php',         label: 'Home',     key: 'home' },
        { href: 'about.html',         label: 'About',    key: 'about' },
        { href: 'index.php#produk',  label: 'Products', key: 'products' },
        { href: 'index.php#loyalty', label: 'Loyalty',  key: 'loyalty' },
    ];

    var navItems = links.map(function(l) {
        return '<li><a href="' + l.href + '" class="' + (activePage === l.key ? 'active' : '') + '">' + l.label + '</a></li>';
    }).join('');

    return '<nav class="navbar" id="navbar">'
        + '<a href="index.php" class="nav-logo">'
        + '<img src="img/logo.png" alt="Floriene">'
        + '<span class="nav-brand">Floriene</span>'
        + '</a>'
        + '<button class="nav-hamburger" onclick="toggleNav()"><span></span><span></span><span></span></button>'
        + '<ul class="nav-links" id="nav-links">' + navItems + '<li class="nav-mobile-login"><a href="auth.html">Login / Register</a></li></ul>'
        + '<div class="nav-cta">'
        + '<a href="auth.html" class="btn btn-pink btn-sm">Login / Register</a>'
        + '</div>'
        + '</nav>';
}

function toggleNav() {
    document.getElementById('nav-links').classList.toggle('open');
}

function updateNavbarAuth() {
    fetch('php/auth.php?aksi=status')
        .then(function(r) { return r.json(); })
        .then(function(s) {
            if (!s.loggedIn) return;
            var dash = s.role === 'admin' ? 'admin.php' : 'profile.html';
            var dashLabel = s.role === 'admin' ? 'Lihat Dashboard' : 'My Dashboard';
            var nama = s.nama || 'User';
            var cta = document.querySelector('.nav-cta');
            if (cta) {
                cta.innerHTML = '<span class="nav-greeting">Hi, ' + nama + '</span>'
                    + '<a href="' + dash + '" class="btn btn-pink btn-sm">' + dashLabel + '</a>'
                    + '<a href="php/auth.php?aksi=logout" class="btn btn-outline btn-sm">Logout</a>';
            }
            var ml = document.querySelector('.nav-mobile-login');
            if (ml) {
                ml.innerHTML = '<span style="display:block;padding:12px;color:var(--muted);font-size:13px;">Hi, ' + nama + '</span>'
                    + '<a href="' + dash + '" style="display:block;">' + dashLabel + '</a>'
                    + '<a href="php/auth.php?aksi=logout" style="display:block;">Logout</a>';
            }
        })
        .catch(function() {});
}

document.addEventListener('DOMContentLoaded', updateNavbarAuth);

window.addEventListener('scroll', function() {
    var nb = document.getElementById('navbar');
    if (nb) nb.classList.toggle('scrolled', window.scrollY > 10);
});

function formatRp(n) {
    return 'Rp ' + parseInt(n || 0).toLocaleString('id-ID');
}
