<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
$id=(int)($_GET['id'] ?? 0);
$stmt=$pdo->prepare('SELECT i.*, c.name customer_name,c.phone,c.email,c.address FROM invoices i JOIN customers c ON c.id=i.customer_id WHERE i.id=?');$stmt->execute([$id]);$inv=$stmt->fetch();
if(!$inv) die('Invoice not found');
$item=$pdo->prepare('SELECT * FROM invoice_items WHERE invoice_id=?');$item->execute([$id]);$items=$item->fetchAll();
$settings=$pdo->query('SELECT invoice_terms,invoice_footer FROM settings LIMIT 1')->fetch();
$qr=urlencode('Invoice '.$inv['invoice_no'].' Amount '.$inv['subtotal'].' Status '.$inv['status']);
require_once __DIR__ . '/../includes/header.php';
?>
<div class="card p-4 shadow-sm"><h3>Invoice <?= htmlspecialchars($inv['invoice_no']); ?></h3><p><strong>Customer:</strong> <?= htmlspecialchars($inv['customer_name']); ?> | <?= htmlspecialchars($inv['phone']); ?></p><p><strong>Status:</strong> <?= htmlspecialchars($inv['status']); ?></p><table class="table"><thead><tr><th>Item</th><th>Qty</th><th>Unit</th><th>Total</th></tr></thead><tbody><?php foreach($items as $it): ?><tr><td><?= htmlspecialchars($it['item_name']); ?></td><td><?= $it['quantity']; ?></td><td>$<?= number_format($it['unit_price'],2); ?></td><td>$<?= number_format($it['line_total'],2); ?></td></tr><?php endforeach; ?></tbody></table><p><strong>Subtotal:</strong> $<?= number_format($inv['subtotal'],2); ?> | <strong>Paid:</strong> $<?= number_format($inv['paid_amount'],2); ?> | <strong>Due:</strong> $<?= number_format($inv['due_amount'],2); ?></p><p><?= nl2br(htmlspecialchars($settings['invoice_terms'] ?? '')); ?></p><img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=<?= $qr; ?>" alt="qr"><p class="mt-2 small text-muted"><?= htmlspecialchars($settings['invoice_footer'] ?? ''); ?></p></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
