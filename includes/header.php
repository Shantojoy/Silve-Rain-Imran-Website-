<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isAdminArea = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePrefix = $isAdminArea ? '../' : '';
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PaintPro | Painting & Wallpaper Experts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $basePrefix; ?>assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= $basePrefix; ?>index.php">PaintPro</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <?php if ($isAdminArea): ?>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="gallery.php">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="leads.php">Leads</a></li>
                    <li class="nav-item"><a class="nav-link" href="testimonials.php">Testimonials</a></li>
                    <li class="nav-item"><a class="nav-link text-warning" href="logout.php">Logout</a></li>
                </ul>
            <?php else: ?>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= $basePrefix; ?>index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $basePrefix; ?>services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $basePrefix; ?>gallery.php">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $basePrefix; ?>shop.php">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $basePrefix; ?>contact.php">Contact</a></li>
                    <li class="nav-item"><a class="btn btn-warning ms-lg-3" href="<?= $basePrefix; ?>quote.php">Get Quote</a></li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container py-4">
    <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type']; ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
