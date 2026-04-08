<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$search = trim($_GET['search'] ?? '');
$where = $search !== '' ? 'WHERE l.name LIKE ? OR l.phone LIKE ?' : '';
$params = $search !== '' ? ["%$search%","%$search%"] : [];
$count = $pdo->prepare("SELECT COUNT(*) FROM leads l $where");
$count->execute($params);
$pg = paginate((int)$count->fetchColumn(), 10);
$stmt = $pdo->prepare("SELECT l.*, p.name AS product_name FROM leads l LEFT JOIN products p ON p.id=l.product_id $where ORDER BY l.created_at DESC LIMIT {$pg['per_page']} OFFSET {$pg['offset']}");
$stmt->execute($params);
$rows = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Leads</h1>
<div class="card shadow-sm"><div class="card-body border-bottom"><form class="row g-2"><div class="col-md-4"><input name="search" class="form-control" placeholder="Search by name or phone" value="<?= htmlspecialchars($search); ?>"></div><div class="col-md-2"><button class="btn btn-outline-dark">Search</button></div></form></div><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Date</th><th>Name</th><th>Phone</th><th>Product/Service</th><th>Message</th><th>Image</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= htmlspecialchars($r['created_at']); ?></td><td><?= htmlspecialchars($r['name']); ?><br><small><?= htmlspecialchars($r['email']); ?></small></td><td><?= htmlspecialchars($r['phone']); ?></td><td><?= htmlspecialchars($r['product_name'] ?: ($r['service_type'] ?: '-')); ?></td><td><?= htmlspecialchars($r['message']); ?></td><td><?php if($r['image']): ?><a href="../uploads/leads/<?= htmlspecialchars($r['image']); ?>" target="_blank">View</a><?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div><div class="card-body"><?php for($i=1;$i<=$pg['total_pages'];$i++): ?><a class="btn btn-sm <?= $i===$pg['page']?'btn-dark':'btn-outline-dark'; ?>" href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>"><?= $i; ?></a> <?php endfor; ?></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
