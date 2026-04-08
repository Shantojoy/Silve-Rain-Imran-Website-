<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$totalServices = (int)$pdo->query('SELECT COUNT(*) FROM services')->fetchColumn();
$totalProducts = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$totalLeads = (int)$pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn();
$recentLeads = $pdo->query('SELECT l.*, p.name AS product_name FROM leads l LEFT JOIN products p ON p.id = l.product_id ORDER BY l.created_at DESC LIMIT 8')->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Admin Dashboard</h1>
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card p-3 shadow-sm"><h6>Total Services</h6><h2><?= $totalServices; ?></h2></div></div>
    <div class="col-md-4"><div class="card p-3 shadow-sm"><h6>Total Products</h6><h2><?= $totalProducts; ?></h2></div></div>
    <div class="col-md-4"><div class="card p-3 shadow-sm"><h6>Total Leads</h6><h2><?= $totalLeads; ?></h2></div></div>
</div>
<div class="card shadow-sm">
    <div class="card-header">Recent Leads</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Name</th><th>Phone</th><th>Email</th><th>Product</th><th>Date</th></tr></thead>
            <tbody>
                <?php foreach ($recentLeads as $lead): ?>
                    <tr>
                        <td><?= htmlspecialchars($lead['name']); ?></td>
                        <td><?= htmlspecialchars($lead['phone']); ?></td>
                        <td><?= htmlspecialchars($lead['email']); ?></td>
                        <td><?= htmlspecialchars($lead['product_name'] ?? $lead['service_type'] ?? 'General Quote'); ?></td>
                        <td><?= htmlspecialchars($lead['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
