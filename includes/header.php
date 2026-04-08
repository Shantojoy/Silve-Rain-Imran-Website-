<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';
$isAdminArea = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePrefix = $isAdminArea ? '../' : '';
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$setting = $pdo->query('SELECT * FROM settings LIMIT 1')->fetch();
$siteName = $setting['site_name'] ?? 'PaintPro';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($siteName); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $basePrefix; ?>assets/css/style.css">
</head>
<body>
<?php $showAdminShell = $isAdminArea && isset($_SESSION['admin_id']); ?>
<?php if ($showAdminShell): ?>
<div class="admin-shell d-flex">
    <aside class="admin-sidebar bg-dark text-light p-3">
        <h5 class="mb-4"><?= htmlspecialchars($siteName); ?></h5>
        <a href="dashboard.php" class="d-block text-light mb-2">Dashboard</a>
        <a href="services.php" class="d-block text-light mb-2">Services</a>
        <a href="categories.php" class="d-block text-light mb-2">Categories</a>
        <a href="products.php" class="d-block text-light mb-2">Products</a>
        <a href="gallery.php" class="d-block text-light mb-2">Gallery</a>
        <a href="leads.php" class="d-block text-light mb-2">Leads</a>
        <a href="orders.php" class="d-block text-light mb-2">Orders</a>
        <a href="testimonials.php" class="d-block text-light mb-2">Testimonials</a>
        <a href="email-templates.php" class="d-block text-light mb-2">Email Templates</a>
        <a href="settings.php" class="d-block text-light mb-2">Settings</a>
        <a href="logout.php" class="d-block text-warning mt-3">Logout</a>
    </aside>
    <main class="admin-main flex-grow-1">
        <nav class="navbar bg-white border-bottom px-3"><span class="navbar-brand mb-0 h6">Admin Panel</span></nav>
        <div class="container-fluid py-3">
<?php else: ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= $basePrefix; ?>index.php"><?= htmlspecialchars($siteName); ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="<?= $basePrefix; ?>index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= $basePrefix; ?>services.php">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= $basePrefix; ?>gallery.php">Gallery</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= $basePrefix; ?>shop.php">Shop</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= $basePrefix; ?>contact.php">Contact</a></li>
                <li class="nav-item"><a class="btn btn-warning ms-lg-3" href="<?= $basePrefix; ?>quote.php">Get Quote</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container py-4">
<?php endif; ?>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type']; ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($flash['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
