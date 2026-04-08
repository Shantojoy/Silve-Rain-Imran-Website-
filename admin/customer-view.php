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

$invoiceStmt = $pdo->prepare("SELECT id, invoice_no, issue_date, due_date, status, due_amount FROM invoices WHERE customer_id=? ORDER BY created_at DESC");
$invoiceStmt->execute([$id]);
$invoices = $invoiceStmt->fetchAll();
$pendingInvoices = array_values(array_filter($invoices, fn($inv) => in_array($inv['status'], ['Due','Partial'], true)));
$pendingInvoiceAmount = array_sum(array_map(fn($inv) => (float)$inv['due_amount'], $pendingInvoices));

$quotationStmt = $pdo->prepare("SELECT id, quotation_no, issue_date, valid_until, status, subtotal FROM quotations WHERE customer_id=? ORDER BY created_at DESC");
$quotationStmt->execute([$id]);
$quotations = $quotationStmt->fetchAll();
$pendingQuotations = array_values(array_filter($quotations, fn($q) => in_array($q['status'], ['Draft','Sent'], true)));
$pendingQuotationAmount = array_sum(array_map(fn($q) => (float)$q['subtotal'], $pendingQuotations));

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Customer Details</h1>
<div class="row g-3 mb-3">
<div class="col-md-4"><div class="card p-3 shadow-sm"><small>Name</small><strong><?= htmlspecialchars($customer['name']); ?></strong></div></div>
<div class="col-md-4"><div class="card p-3 shadow-sm"><small>Total Orders</small><strong><?= $totalOrders; ?></strong></div></div>
<div class="col-md-4"><div class="card p-3 shadow-sm"><small>Total Spending</small><strong>$<?= number_format($totalSpending,2); ?></strong></div></div>
</div>
<div class="row g-3 mb-3">
<div class="col-md-6"><div class="card p-3 shadow-sm"><small>Pending Invoices</small><strong><?= count($pendingInvoices); ?></strong><div class="text-muted small">Due amount: $<?= number_format($pendingInvoiceAmount,2); ?></div></div></div>
<div class="col-md-6"><div class="card p-3 shadow-sm"><small>Pending Quotations</small><strong><?= count($pendingQuotations); ?></strong><div class="text-muted small">Quotation amount: $<?= number_format($pendingQuotationAmount,2); ?></div></div></div>
</div>
<div class="card p-3 shadow-sm mb-3"><p class="mb-1"><i class="bi bi-telephone"></i> <?= htmlspecialchars($customer['phone']); ?></p><p class="mb-1"><i class="bi bi-envelope"></i> <?= htmlspecialchars($customer['email']); ?></p><p class="mb-0"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($customer['address']); ?></p></div>
<div class="card shadow-sm"><div class="card-header">Recent Activity / Orders</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Order ID</th><th>Status</th><th>Total</th><th>Date</th></tr></thead><tbody><?php foreach($orders as $o): ?><tr><td>#<?= $o['id']; ?></td><td><?= htmlspecialchars($o['status']); ?></td><td>$<?= number_format((float)$o['total_amount'],2); ?></td><td><?= htmlspecialchars($o['created_at']); ?></td></tr><?php endforeach; ?></tbody></table></div></div>
<div class="row g-3 mt-1">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100"><div class="card-header">Pending / Due Invoices</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>No</th><th>Status</th><th>Due</th></tr></thead><tbody><?php foreach($pendingInvoices as $inv): ?><tr><td><?= htmlspecialchars($inv['invoice_no']); ?></td><td><?= htmlspecialchars($inv['status']); ?></td><td>$<?= number_format((float)$inv['due_amount'],2); ?></td></tr><?php endforeach; ?><?php if(!$pendingInvoices): ?><tr><td colspan="3" class="text-muted">No pending invoices.</td></tr><?php endif; ?></tbody></table></div></div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm h-100"><div class="card-header">Pending Quotations</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>No</th><th>Status</th><th>Total</th></tr></thead><tbody><?php foreach($pendingQuotations as $q): ?><tr><td><?= htmlspecialchars($q['quotation_no']); ?></td><td><?= htmlspecialchars($q['status']); ?></td><td>$<?= number_format((float)$q['subtotal'],2); ?></td></tr><?php endforeach; ?><?php if(!$pendingQuotations): ?><tr><td colspan="3" class="text-muted">No pending quotations.</td></tr><?php endif; ?></tbody></table></div></div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
