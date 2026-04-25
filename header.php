<?php
// FILE: header.php
// Safe session start and header include for all pages.
// Place this file in the project root and include with: <?php include 'header.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="site-header">
  <div class="container d-flex align-items-center justify-content-between py-3">
    <a class="brand" href="index.php">Credit<span class="accent">Without</span>Barriers</a>

    <nav class="d-none d-md-flex gap-3 align-items-center">
      <a class="nav-link" href="index.php">Home</a>
      <a class="nav-link" href="services.php">Services</a>
      <a class="nav-link" href="aboutus.php">About</a>
      <a class="nav-link" href="lenderlogin.php">Lender Login</a>
      <a class="nav-link" href="customerlogin.php">Customer Login</a>
      <a class="nav-link" href="adminlogin.php">Admin</a>
    </nav>

    <div class="d-flex align-items-center gap-2">
      <?php if (!empty($_SESSION['user_id'])): ?>
        <!-- Logged in (adjust session key / role check as needed) -->
        <a class="btn btn-secondary" href="dashboard.php">Dashboard</a>
        <a class="btn btn-danger" href="logout.php">Logout</a>
      <?php else: ?>
        <!-- Not logged in -->
        <a class="btn btn-primary" href="customerlogin.php">Login</a>
        <a class="btn btn-primary" href="register.php">Register</a>
      <?php endif; ?>

      <button id="mobileMenuBtn" class="nav-toggle d-md-none" aria-label="Toggle navigation">
        Menu
      </button>
    </div>
  </div>

  <!-- MOBILE MENU -->
  <div id="mobileMenu" class="mobile-menu d-md-none" aria-hidden="true">
    <a href="index.php">Home</a>
    <a href="services.php">Services</a>
    <a href="aboutus.php">About</a>
    <a href="lenderlogin.php">Lender Login</a>
    <a href="customerlogin.php">Customer Login</a>
    <a href="adminlogin.php">Admin</a>
    <a class="btn btn-primary w-100 mt-2" href="apply.php">Apply Now</a>
  </div>
</header>