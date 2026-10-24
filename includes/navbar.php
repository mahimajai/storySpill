<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION["user_id"]);
$isAdmin = isset($_SESSION["admin_id"]);
?>
<nav class="top-nav">
  <div class="nav-inner">
    <a href="index.php" class="nav-logo">StorySpill</a>
    
    <button class="nav-toggle" id="navToggle" aria-label="Menu">
      <i class="fas fa-bars"></i>
    </button>

    <ul class="nav-links" id="navLinks">
      <li><a href="index.php">Shelf</a></li>
      
      <?php if ($isLoggedIn): ?>
        <li><a href="wishlist.php">Wishlist</a></li>
        <li><a href="dashboard.php">My Profile</a></li>
        <li><a href="logout.php" class="nav-logout">Logout</a></li>
      <?php else: ?>
        <li><a href="login.php">Login</a></li>
        <li><a href="signup.php" class="nav-cta">Sign Up</a></li>
      <?php endif; ?>

      <li class="nav-dropdown">
        <button type="button" class="nav-admin-btn" id="adminDropBtn">
          Admin <i class="fas fa-caret-down"></i>
        </button>
        <div class="nav-dropdown-menu" id="adminDropMenu">
          <?php if ($isAdmin): ?>
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="admin_logout.php">Admin Logout</a>
          <?php else: ?>
            <a href="admin_login.php">Admin Login</a>
            <a href="admin_signup.php">Admin Signup</a>
          <?php endif; ?>
        </div>
      </li>
    </ul>
  </div>
</nav>
<style>
.nav-dropdown { position: relative; list-style: none; }
.nav-admin-btn {
  background: transparent;
  border: 1px solid #555;
  color: #ddd;
  font-size: 0.78rem;
  font-weight: 600;
  letter-spacing: 0.5px;
  padding: 7px 14px;
  border-radius: 50px;
  cursor: pointer;
  font-family: inherit;
  transition: all 0.2s;
}
.nav-admin-btn:hover { border-color: #c9a84c; color: #fff; }
.nav-dropdown-menu {
  display: none !important;
  position: absolute;
  top: calc(100% + 10px);
  right: 0;
  background: #1c1c1c;
  border: 1px solid #444;
  border-radius: 12px;
  padding: 8px;
  min-width: 170px;
  z-index: 9999;
  box-shadow: 0 16px 40px rgba(0,0,0,0.6);
}
.nav-dropdown-menu.open-menu {
  display: block !important;
}
.nav-dropdown-menu a {
  display: block;
  padding: 11px 14px;
  color: #ccc !important;
  font-size: 0.85rem;
  border-radius: 8px;
  text-decoration: none;
  letter-spacing: 0.3px;
}
.nav-dropdown-menu a:hover {
  background: #2a2a2a;
  color: #fff !important;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var btn = document.getElementById('adminDropBtn');
  var menu = document.getElementById('adminDropMenu');
  if (btn && menu) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      menu.classList.toggle('open-menu');
    });
    document.addEventListener('click', function (e) {
      if (!menu.contains(e.target) && e.target !== btn) {
        menu.classList.remove('open-menu');
      }
    });
  }
  var toggle = document.getElementById('navToggle');
  var links = document.getElementById('navLinks');
  if (toggle && links) {
    toggle.addEventListener('click', function () {
      links.classList.toggle('open');
    });
  }
});
</script>