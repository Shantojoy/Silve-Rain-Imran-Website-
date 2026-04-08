<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$isAdminArea = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePrefix = $isAdminArea ? '../' : '';
$currentFile = basename($_SERVER['PHP_SELF']);
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$setting = $pdo->query('SELECT * FROM settings LIMIT 1')->fetch();
$siteName = $setting['site_name'] ?? 'PaintPro';
$showAdminShell = $isAdminArea && isset($_SESSION['admin_id']);

$notifications = [];
$unreadCount = 0;
if ($showAdminShell) {
    $unreadCount = (int)$pdo->query('SELECT COUNT(*) FROM notifications WHERE is_read = 0')->fetchColumn();
    $notifications = $pdo->query('SELECT * FROM notifications ORDER BY created_at DESC LIMIT 8')->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($siteName); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $basePrefix; ?>assets/css/style.css">
</head>
<body>
<?php if ($showAdminShell): ?>
<div class="admin-shell d-flex">
    <aside class="admin-sidebar bg-white border-end" id="adminSidebar">
        <div class="sidebar-brand d-flex align-items-center justify-content-center py-3 border-bottom">
            <i class="bi bi-brush fs-5"></i>
            <span class="menu-text ms-2 fw-semibold"><?= htmlspecialchars($siteName); ?></span>
        </div>
        <?php
        $menu = [
            ['dashboard.php','bi-speedometer2','Dashboard'],
            ['services.php','bi-brush','Services'],
            ['categories.php','bi-tags','Categories'],
            ['products.php','bi-box-seam','Products'],
            ['gallery.php','bi-images','Gallery'],
            ['leads.php','bi-person-lines-fill','Leads'],
            ['customers.php','bi-people','Customers'],
            ['orders.php','bi-receipt','Orders'],
            ['testimonials.php','bi-chat-square-quote','Testimonials'],
            ['settings.php','bi-gear','Settings'],
            ['email-templates.php','bi-envelope-paper','Email Templates'],
        ];
        foreach ($menu as $item):
            $active = $currentFile === $item[0] ? 'active' : '';
        ?>
            <a href="<?= $item[0]; ?>" class="admin-link <?= $active; ?> d-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="right" title="<?= htmlspecialchars($item[2]); ?>">
                <i class="bi <?= $item[1]; ?>"></i>
                <span class="menu-text ms-3"><?= $item[2]; ?></span>
            </a>
        <?php endforeach; ?>
    </aside>

    <main class="admin-main flex-grow-1">
        <nav class="navbar bg-white border-bottom px-3">
            <button class="btn btn-outline-secondary btn-sm" id="sidebarToggle" title="Toggle sidebar"><i class="bi bi-list"></i></button>
            <div class="ms-auto d-flex align-items-center gap-2">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary position-relative btn-sm" data-bs-toggle="dropdown" title="Notifications">
                        <i class="bi bi-bell"></i>
                        <?php if ($unreadCount > 0): ?><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $unreadCount; ?></span><?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-2" style="min-width:320px;">
                        <li class="px-2 small text-muted">Recent Notifications</li>
                        <?php foreach ($notifications as $n): ?>
                            <li><a class="dropdown-item" href="<?= htmlspecialchars($n['link'] ?: '#'); ?>"><strong><?= htmlspecialchars($n['title']); ?></strong><br><small><?= htmlspecialchars($n['message']); ?></small></a></li>
                        <?php endforeach; ?>
                        <?php if (!$notifications): ?><li class="dropdown-item text-muted">No notifications yet.</li><?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="mark-notifications-read.php">Mark all as read</a></li>
                    </ul>
                </div>

                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
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
                <li class="nav-item"><a class="btn btn-warning ms-lg-3" href="<?= $basePrefix; ?>quote.php"><i class="bi bi-send"></i> Get Quote</a></li>
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
