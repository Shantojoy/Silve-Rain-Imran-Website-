<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$orderId = (int)($_GET['order_id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM orders WHERE id=?');
$stmt->execute([$orderId]);
$order = $stmt->fetch();
if (!$order) { echo '<div class="alert alert-danger">Order not found.</div>'; require_once __DIR__ . '/includes/footer.php'; exit; }

$itemStmt = $pdo->prepare('SELECT oi.*, p.name AS product_name FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=?');
$itemStmt->execute([$orderId]);
$items = $itemStmt->fetchAll();
?>
<div class="card shadow-sm p-4 text-center mb-4">
    <h1 class="text-success"><i class="bi bi-check-circle"></i> Thank you for your order!</h1>
    <p class="mb-0">Your order has been placed successfully with Cash on Delivery.</p>
</div>
<div class="row g-3">
    <div class="col-md-6"><div class="card p-3 shadow-sm"><strong>Order ID</strong><div>#<?= $order['id']; ?></div></div></div>
    <div class="col-md-6"><div class="card p-3 shadow-sm"><strong>Customer</strong><div><?= htmlspecialchars($order['name']); ?></div></div></div>
    <div class="col-md-6"><div class="card p-3 shadow-sm"><strong>Total</strong><div>$<?= number_format((float)$order['total_amount'],2); ?></div></div></div>
    <div class="col-md-6"><div class="card p-3 shadow-sm"><strong>Status</strong><div><?= htmlspecialchars($order['status']); ?></div></div></div>
</div>
<div class="card shadow-sm mt-3"><div class="card-header">Products Ordered</div><ul class="list-group list-group-flush"><?php foreach($items as $it): ?><li class="list-group-item d-flex justify-content-between"><span><?= htmlspecialchars($it['product_name']); ?> x <?= (int)$it['quantity']; ?></span><span>$<?= number_format((float)$it['price']*(int)$it['quantity'],2); ?></span></li><?php endforeach; ?></ul></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
