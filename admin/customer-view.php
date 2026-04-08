<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM customers WHERE id=?');
$stmt->execute([$id]);
$customer = $stmt->fetch();
if (!$customer) { die('Customer not found'); }

$ordersStmt = $pdo->prepare('SELECT * FROM orders WHERE customer_id=? ORDER BY created_at DESC');
$ordersStmt->execute([$id]);
$orders = $ordersStmt->fetchAll();
$totalOrders = count($orders);
$totalSpending = array_sum(array_map(fn($o) => (float)$o['total_amount'], $orders));

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Customer Details</h1>
<div class="row g-3 mb-3">
<div class="col-md-4"><div class="card p-3 shadow-sm"><small>Name</small><strong><?= htmlspecialchars($customer['name']); ?></strong></div></div>
<div class="col-md-4"><div class="card p-3 shadow-sm"><small>Total Orders</small><strong><?= $totalOrders; ?></strong></div></div>
<div class="col-md-4"><div class="card p-3 shadow-sm"><small>Total Spending</small><strong>$<?= number_format($totalSpending,2); ?></strong></div></div>
</div>
<div class="card p-3 shadow-sm mb-3"><p class="mb-1"><i class="bi bi-telephone"></i> <?= htmlspecialchars($customer['phone']); ?></p><p class="mb-1"><i class="bi bi-envelope"></i> <?= htmlspecialchars($customer['email']); ?></p><p class="mb-0"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($customer['address']); ?></p></div>
<div class="card shadow-sm"><div class="card-header">Recent Activity / Orders</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Order ID</th><th>Status</th><th>Total</th><th>Date</th></tr></thead><tbody><?php foreach($orders as $o): ?><tr><td>#<?= $o['id']; ?></td><td><?= htmlspecialchars($o['status']); ?></td><td>$<?= number_format((float)$o['total_amount'],2); ?></td><td><?= htmlspecialchars($o['created_at']); ?></td></tr><?php endforeach; ?></tbody></table></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
