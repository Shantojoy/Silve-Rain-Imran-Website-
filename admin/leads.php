<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lead_id'])) {
    $leadId = (int)$_POST['lead_id'];
    $status = sanitize($_POST['status'] ?? 'New');
    $notes = trim($_POST['notes'] ?? '');
    if (in_array($status, ['New','Contacted','Qualified','Converted','Closed'], true)) {
        $pdo->prepare('UPDATE leads SET status=?, notes=? WHERE id=?')->execute([$status,$notes,$leadId]);
        setFlash('success', 'Lead updated.');
    }
    redirect('leads.php');
}

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
<div class="card shadow-sm"><div class="card-body border-bottom"><form class="row g-2"><div class="col-md-4"><input name="search" class="form-control" placeholder="Search by name or phone" value="<?= htmlspecialchars($search); ?>"></div><div class="col-md-2"><button class="btn btn-outline-dark">Search</button></div></form></div><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Date</th><th>Name</th><th>Contact</th><th>Item</th><th>Status/Notes</th><th>Image</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= htmlspecialchars($r['created_at']); ?></td><td><?= htmlspecialchars($r['name']); ?></td><td><?= htmlspecialchars($r['phone']); ?><br><small><?= htmlspecialchars($r['email']); ?></small></td><td><?= htmlspecialchars($r['product_name'] ?: ($r['service_type'] ?: '-')); ?><br><small><?= htmlspecialchars($r['message']); ?></small></td><td><form method="post" class="d-flex flex-column gap-1"><input type="hidden" name="lead_id" value="<?= $r['id']; ?>"><select name="status" class="form-select form-select-sm"><?php foreach(['New','Contacted','Qualified','Converted','Closed'] as $s): ?><option <?= ($r['status'] ?? 'New')===$s?'selected':''; ?>><?= $s; ?></option><?php endforeach; ?></select><textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Add notes"><?= htmlspecialchars($r['notes'] ?? ''); ?></textarea><button class="btn btn-sm btn-dark">Save</button></form></td><td><?php if($r['image']): ?><a href="../uploads/leads/<?= htmlspecialchars($r['image']); ?>" target="_blank">View</a><?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div><div class="card-body"><?php for($i=1;$i<=$pg['total_pages'];$i++): ?><a class="btn btn-sm <?= $i===$pg['page']?'btn-dark':'btn-outline-dark'; ?>" href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>"><?= $i; ?></a> <?php endfor; ?></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
