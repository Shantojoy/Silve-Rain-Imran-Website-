<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$totalServices = (int)$pdo->query('SELECT COUNT(*) FROM services')->fetchColumn();
$totalProducts = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$totalLeads = (int)$pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn();
$totalOrders = (int)$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$recentLeads = $pdo->query('SELECT l.*, p.name AS product_name FROM leads l LEFT JOIN products p ON p.id = l.product_id ORDER BY l.created_at DESC LIMIT 6')->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Dashboard</h1>
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3"><div class="card shadow-sm p-3"><small>Total Services</small><h3><?= $totalServices; ?></h3></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card shadow-sm p-3"><small>Total Products</small><h3><?= $totalProducts; ?></h3></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card shadow-sm p-3"><small>Total Leads</small><h3><?= $totalLeads; ?></h3></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card shadow-sm p-3"><small>Total Orders</small><h3><?= $totalOrders; ?></h3></div></div>
</div>
<div class="card shadow-sm"><div class="card-header">Recent Leads</div>
<div class="table-responsive"><table class="table mb-0"><thead><tr><th>Name</th><th>Phone</th><th>Item</th><th>Date</th></tr></thead><tbody>
<?php foreach ($recentLeads as $lead): ?><tr><td><?= htmlspecialchars($lead['name']); ?></td><td><?= htmlspecialchars($lead['phone']); ?></td><td><?= htmlspecialchars($lead['product_name'] ?: ($lead['service_type'] ?: 'General')); ?></td><td><?= htmlspecialchars($lead['created_at']); ?></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
