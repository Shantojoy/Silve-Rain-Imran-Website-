<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
$isAdminArea = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePrefix = $isAdminArea ? '../' : '';
$currentFile = basename($_SERVER['PHP_SELF']);
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
$setting = $pdo->query('SELECT * FROM settings LIMIT 1')->fetch();
$siteName = $setting['site_name'] ?? 'PaintPro';
$favicon = $setting['favicon'] ?? '';
$showAdminShell = $isAdminArea && isset($_SESSION['admin_id']);
$notifications=[];$unreadCount=0;
if($showAdminShell){$unreadCount=(int)$pdo->query('SELECT COUNT(*) FROM notifications WHERE is_read=0')->fetchColumn();$notifications=$pdo->query('SELECT * FROM notifications ORDER BY created_at DESC LIMIT 8')->fetchAll();}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($siteName); ?></title>
<?php if($favicon): ?><link rel="icon" href="<?= $basePrefix.'uploads/products/'.htmlspecialchars($favicon); ?>" type="image/png"><?php endif; ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="<?= $basePrefix; ?>assets/css/style.css">
</head><body>
<?php if($showAdminShell): ?>
<div class="admin-shell d-flex">
<aside class="admin-sidebar bg-white border-end expanded" id="adminSidebar">
<div class="sidebar-brand py-3 text-center border-bottom fw-bold"><i class="bi bi-brush"></i> <span class="menu-text"><?= htmlspecialchars($siteName); ?></span></div>
<div class="p-2">
<a href="dashboard.php" class="admin-link <?= $currentFile==='dashboard.php'?'active':''; ?>"><i class="bi bi-speedometer2"></i><span class="menu-text">Dashboard</span></a>
<a class="admin-link" data-bs-toggle="collapse" href="#menuServices"><i class="bi bi-briefcase"></i><span class="menu-text">Services</span></a>
<div class="collapse <?= in_array($currentFile,['services.php'])?'show':''; ?>" id="menuServices"><a href="services.php" class="admin-sublink">All Services</a></div>
<a class="admin-link" data-bs-toggle="collapse" href="#menuCatalog"><i class="bi bi-box-seam"></i><span class="menu-text">Catalog</span></a>
<div class="collapse <?= in_array($currentFile,['categories.php','products.php','product-add.php','product-edit.php','products-list.php'])?'show':''; ?>" id="menuCatalog"><a href="categories.php" class="admin-sublink">Categories</a><a href="products-list.php" class="admin-sublink">Product List</a><a href="product-add.php" class="admin-sublink">Add Product</a></div>
<a class="admin-link" data-bs-toggle="collapse" href="#menuGallery"><i class="bi bi-images"></i><span class="menu-text">Gallery</span></a>
<div class="collapse <?= $currentFile==='gallery.php'?'show':''; ?>" id="menuGallery"><a href="gallery.php" class="admin-sublink">All Gallery</a></div>
<a class="admin-link" data-bs-toggle="collapse" href="#menuSales"><i class="bi bi-receipt"></i><span class="menu-text">Sales</span></a>
<div class="collapse <?= in_array($currentFile,['invoices.php','invoice-edit.php','invoice-view.php','quotations.php','quotation-edit.php','payments.php'])?'show':''; ?>" id="menuSales"><a href="invoices.php" class="admin-sublink">Invoices</a><a href="quotations.php" class="admin-sublink">Quotations</a><a href="payments.php" class="admin-sublink">Payments</a></div>
<a class="admin-link" data-bs-toggle="collapse" href="#menuCRM"><i class="bi bi-people"></i><span class="menu-text">CRM</span></a>
<div class="collapse <?= in_array($currentFile,['leads.php','customers.php','customer-view.php'])?'show':''; ?>" id="menuCRM"><a href="leads.php" class="admin-sublink">Leads</a><a href="customers.php" class="admin-sublink">Customers</a></div>
<a class="admin-link" data-bs-toggle="collapse" href="#menuBlog"><i class="bi bi-journal-richtext"></i><span class="menu-text">Blog</span></a>
<div class="collapse <?= in_array($currentFile,['blog-categories.php','blogs.php','blog-edit.php'])?'show':''; ?>" id="menuBlog"><a href="blog-categories.php" class="admin-sublink">Categories</a><a href="blogs.php" class="admin-sublink">Blogs</a></div>
<a href="testimonials.php" class="admin-link <?= $currentFile==='testimonials.php'?'active':''; ?>"><i class="bi bi-chat-square-quote"></i><span class="menu-text">Testimonials</span></a>
<a class="admin-link" data-bs-toggle="collapse" href="#menuSettings"><i class="bi bi-gear"></i><span class="menu-text">Settings</span></a>
<div class="collapse <?= in_array($currentFile,['settings.php','email-templates.php'])?'show':''; ?>" id="menuSettings"><a href="settings.php" class="admin-sublink">General Settings</a><a href="settings.php#invoice-settings" class="admin-sublink">Invoice Settings</a><a href="email-templates.php" class="admin-sublink">Email Templates</a></div>
</div>
<div class="p-2 border-top"><button class="btn btn-outline-secondary w-100 btn-sm" id="sidebarToggle"><i class="bi bi-layout-sidebar"></i> <span class="menu-text">Collapse</span></button></div>
</aside>
<main class="admin-main flex-grow-1"><nav class="navbar bg-white border-bottom px-3"><span class="fw-semibold">Admin Panel</span><div class="ms-auto d-flex gap-2"><div class="dropdown"><button class="btn btn-outline-secondary btn-sm position-relative" data-bs-toggle="dropdown"><i class="bi bi-bell"></i><?php if($unreadCount>0): ?><span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill"><?= $unreadCount; ?></span><?php endif; ?></button><ul class="dropdown-menu dropdown-menu-end" style="min-width:320px"><?php foreach($notifications as $n): ?><li><a class="dropdown-item" href="<?= htmlspecialchars($n['link'] ?: '#'); ?>"><strong><?= htmlspecialchars($n['title']); ?></strong><br><small><?= htmlspecialchars($n['message']); ?></small></a></li><?php endforeach; ?><li><hr class="dropdown-divider"></li><li><a class="dropdown-item text-center" href="mark-notifications-read.php">Mark all as read</a></li></ul></div><div class="dropdown"><button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></button><ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="profile.php">Profile</a></li><li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li></ul></div></div></nav><div class="container-fluid py-3">
<?php else: ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm"><div class="container"><a class="navbar-brand fw-bold" href="<?= $basePrefix; ?>index.php"><?= htmlspecialchars($siteName); ?></a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="mainNav"><ul class="navbar-nav ms-auto"><li class="nav-item"><a class="nav-link" href="<?= $basePrefix; ?>index.php">Home</a></li><li class="nav-item"><a class="nav-link" href="<?= $basePrefix; ?>services.php">Services</a></li><li class="nav-item"><a class="nav-link" href="<?= $basePrefix; ?>gallery.php">Gallery</a></li><li class="nav-item"><a class="nav-link" href="<?= $basePrefix; ?>shop.php">Shop</a></li><li class="nav-item"><a class="nav-link" href="<?= $basePrefix; ?>blog.php">Blog</a></li><li class="nav-item"><a class="nav-link" href="<?= $basePrefix; ?>contact.php">Contact</a></li><li class="nav-item"><a class="btn btn-warning ms-lg-3" href="<?= $basePrefix; ?>quote.php">Get Quote</a></li></ul></div></div></nav><div class="container py-4">
<?php endif; ?>
<?php if($flash): ?><div class="alert alert-<?= $flash['type']; ?> alert-dismissible fade show"><?= htmlspecialchars($flash['message']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
