<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';
$isAdminArea = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePrefix = $isAdminArea ? '../' : '';
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
    <aside class="admin-sidebar bg-dark text-light p-3" id="adminSidebar">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><?= htmlspecialchars($siteName); ?></h5>
            <button class="btn btn-sm btn-outline-light d-lg-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
        </div>
        <?php
        $menu = [
            ['dashboard.php','bi-speedometer2','Dashboard','Overview and latest activity'],
            ['services.php','bi-brush','Services','Manage service offerings'],
            ['categories.php','bi-tags','Categories','Global categories'],
            ['products.php','bi-box-seam','Products','Manage wallpaper designs'],
            ['gallery.php','bi-images','Gallery','Before/after projects'],
            ['leads.php','bi-person-lines-fill','Leads','Customer quote requests'],
            ['customers.php','bi-people','Customers','Customer CRM and history'],
            ['orders.php','bi-receipt','Orders','Track and update orders'],
            ['testimonials.php','bi-chat-square-quote','Testimonials','Client feedback management'],
            ['settings.php','bi-gear','Settings','Site and communication settings'],
            ['email-templates.php','bi-envelope-paper','Email Templates','Template setup under settings'],
        ];
        foreach ($menu as $item):
        ?>
            <a href="<?= $item[0]; ?>" class="admin-link d-flex gap-2 py-2 px-2 rounded mb-1 text-light text-decoration-none">
                <i class="bi <?= $item[1]; ?> fs-5"></i>
                <span><strong class="d-block"><?= $item[2]; ?></strong><small class="text-light-emphasis"><?= $item[3]; ?></small></span>
            </a>
        <?php endforeach; ?>
        <a href="logout.php" class="admin-link d-flex gap-2 py-2 px-2 rounded mt-2 text-warning text-decoration-none"><i class="bi bi-box-arrow-right fs-5"></i><span><strong class="d-block">Logout</strong><small class="text-light-emphasis">Securely sign out</small></span></a>
    </aside>
    <main class="admin-main flex-grow-1">
        <nav class="navbar bg-white border-bottom px-3">
            <span class="navbar-brand mb-0 h6">Admin Panel</span>
            <div class="dropdown">
                <button class="btn btn-outline-secondary position-relative" data-bs-toggle="dropdown" title="Notifications">
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
