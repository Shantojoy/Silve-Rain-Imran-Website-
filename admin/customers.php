<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        $pdo->prepare('DELETE FROM customers WHERE id=?')->execute([$id]);
        setFlash('success', 'Customer deleted.');
    }
    redirect('customers.php');
}

$search = trim($_GET['search'] ?? '');
$where = $search !== '' ? 'WHERE name LIKE ? OR phone LIKE ? OR email LIKE ?' : '';
$params = $search !== '' ? ["%$search%","%$search%","%$search%"] : [];
$c = $pdo->prepare("SELECT COUNT(*) FROM customers $where");
$c->execute($params);
$pg = paginate((int)$c->fetchColumn(), 12);
$stmt = $pdo->prepare("SELECT * FROM customers $where ORDER BY created_at DESC LIMIT {$pg['per_page']} OFFSET {$pg['offset']}");
$stmt->execute($params);
$rows = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Customers</h1>
<div class="d-flex justify-content-end mb-3">
    <a href="customer-add.php" class="btn btn-dark"><i class="bi bi-plus-circle"></i> Add Customer</a>
</div>
<div class="card shadow-sm"><div class="card-body border-bottom"><form class="row g-2"><div class="col-md-4"><input name="search" class="form-control" placeholder="Search customers" value="<?= htmlspecialchars($search); ?>"></div><div class="col-md-2"><button class="btn btn-outline-dark"><i class="bi bi-search"></i> Search</button></div></form></div>
<div class="table-responsive"><table class="table mb-0"><thead><tr><th>Name</th><th>Phone</th><th>Email</th><th>Action</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= htmlspecialchars($r['name']); ?></td><td><?= htmlspecialchars($r['phone']); ?></td><td><?= htmlspecialchars($r['email']); ?></td><td><a class="btn btn-sm btn-primary" href="customer-view.php?id=<?= $r['id']; ?>"><i class="bi bi-eye"></i> View</a> <a class="btn btn-sm btn-outline-secondary" href="customer-edit.php?id=<?= $r['id']; ?>"><i class="bi bi-pencil"></i> Edit</a> <a class="btn btn-sm btn-danger" href="?delete=<?= $r['id']; ?>" data-confirm="Delete this customer?"><i class="bi bi-trash"></i></a></td></tr><?php endforeach; ?></tbody></table></div>
<div class="card-body"><?php for($i=1;$i<=$pg['total_pages'];$i++): ?><a class="btn btn-sm <?= $pg['page']===$i?'btn-dark':'btn-outline-dark'; ?>" href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>"><?= $i; ?></a> <?php endfor; ?></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
