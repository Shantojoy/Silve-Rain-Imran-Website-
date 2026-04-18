<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$order = null;
$error = '';
$orderId = trim($_GET['order_id'] ?? '');

if (isset($_GET['search'])) {
    if ($orderId === '' || !ctype_digit($orderId)) {
        $error = 'Please enter a valid order ID.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE id=? LIMIT 1');
        $stmt->execute([(int)$orderId]);
        $order = $stmt->fetch();
        if (!$order) $error = 'No order found for this ID.';
    }
}

$statusClass = 'bg-secondary';
$statusText = '';
if ($order) {
    if ($order['status'] === 'Pending') {
        $statusClass = 'bg-warning text-dark';
        $statusText = 'Your order is under review.';
    } elseif ($order['status'] === 'Approved') {
        $statusClass = 'bg-success';
        $statusText = 'Your order is approved. Check your email.';
    } else {
        $statusText = 'Current status: ' . $order['status'];
    }
}
?>
<h1 class="section-title">Track Your Order</h1>
<div class="card page-card p-4 mb-4">
    <form method="get" class="row g-3 align-items-end">
        <input type="hidden" name="search" value="1">
        <div class="col-md-8">
            <label class="form-label">Order ID <span class="text-danger">*</span></label>
            <input type="text" name="order_id" class="form-control" value="<?= htmlspecialchars($orderId); ?>" placeholder="Enter order ID" required>
        </div>
        <div class="col-md-4">
            <button class="btn btn-dark w-100">Track Order</button>
        </div>
    </form>
</div>

<?php if ($error): ?>
    <div class="alert alert-warning"><?= htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($order): ?>
<div class="card page-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Order #<?= (int)$order['id']; ?></h5>
        <span class="badge <?= $statusClass; ?> px-3 py-2"><?= htmlspecialchars($order['status']); ?></span>
    </div>
    <div class="row g-3">
        <div class="col-md-6"><div class="border rounded p-3"><small class="text-muted d-block">Customer</small><strong><?= htmlspecialchars($order['name']); ?></strong></div></div>
        <div class="col-md-6"><div class="border rounded p-3"><small class="text-muted d-block">Total Amount</small><strong>$<?= number_format((float)$order['total_amount'],2); ?></strong></div></div>
    </div>
    <p class="mt-3 mb-0"><?= htmlspecialchars($statusText); ?></p>
</div>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
