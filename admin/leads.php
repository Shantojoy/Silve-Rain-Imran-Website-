<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
$rows = $pdo->query('SELECT l.*, p.name AS product_name FROM leads l LEFT JOIN products p ON p.id = l.product_id ORDER BY l.created_at DESC')->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Lead Requests</h1>
<div class="card shadow-sm">
<div class="table-responsive">
<table class="table table-striped mb-0">
<thead><tr><th>Date</th><th>Customer</th><th>Contact</th><th>Requested Item</th><th>Room Size</th><th>Message</th><th>Image</th></tr></thead>
<tbody>
<?php foreach ($rows as $r): ?>
<tr>
<td><?= htmlspecialchars($r['created_at']); ?></td>
<td><?= htmlspecialchars($r['name']); ?></td>
<td><?= htmlspecialchars($r['phone']); ?><br><small><?= htmlspecialchars($r['email']); ?></small></td>
<td><?= htmlspecialchars($r['product_name'] ?: ($r['service_type'] ?: 'General Quote')); ?></td>
<td><?= htmlspecialchars($r['room_size'] ?: '-'); ?></td>
<td><?= htmlspecialchars($r['message']); ?></td>
<td>
<?php if ($r['image']): ?>
<a href="../uploads/leads/<?= htmlspecialchars($r['image']); ?>" download class="btn btn-sm btn-outline-dark">Download</a>
<?php else: ?>-
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
