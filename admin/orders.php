<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $orderId = (int)$_POST['order_id'];
    $status = sanitize($_POST['status'] ?? 'Pending');
    $allowed = ['Pending','Processing','Completed','Cancelled'];
    if (in_array($status, $allowed, true)) {
        $pdo->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$status,$orderId]);
        $order = $pdo->prepare('SELECT * FROM orders WHERE id=?'); $order->execute([$orderId]); $orderData = $order->fetch();
        if ($orderData) sendTemplateEmail($pdo, 'status_'.$status, $orderData['email'], ['name'=>$orderData['name'],'status'=>$status,'order_id'=>$orderId]);
        setFlash('success', 'Order status updated.');
    }
    redirect('orders.php');
}

$search = trim($_GET['search'] ?? '');
$where = $search !== '' ? 'WHERE name LIKE ? OR phone LIKE ?' : '';
$params = $search !== '' ? ["%$search%","%$search%"] : [];
$c = $pdo->prepare("SELECT COUNT(*) FROM orders $where"); $c->execute($params);
$pg = paginate((int)$c->fetchColumn(), 10);
$stmt = $pdo->prepare("SELECT * FROM orders $where ORDER BY created_at DESC LIMIT {$pg['per_page']} OFFSET {$pg['offset']}");
$stmt->execute($params);
$orders = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Orders</h1>
<div class="card shadow-sm"><div class="card-body border-bottom"><form class="row g-2"><div class="col-md-4"><input name="search" class="form-control" value="<?= htmlspecialchars($search); ?>" placeholder="Search orders"></div><div class="col-md-2"><button class="btn btn-outline-dark">Search</button></div></form></div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th><th>Update</th></tr></thead><tbody><?php foreach($orders as $o): ?><tr><td>#<?= $o['id']; ?></td><td><?= htmlspecialchars($o['name']); ?><br><small><?= htmlspecialchars($o['phone']); ?></small></td><td>$<?= number_format((float)$o['total_amount'],2); ?></td><td><span class="badge bg-secondary"><?= htmlspecialchars($o['status']); ?></span></td><td><?= htmlspecialchars($o['created_at']); ?></td><td><form method="post" class="d-flex gap-2"><input type="hidden" name="order_id" value="<?= $o['id']; ?>"><select name="status" class="form-select form-select-sm"><?php foreach(['Pending','Processing','Completed','Cancelled'] as $st): ?><option <?= $o['status']===$st?'selected':''; ?>><?= $st; ?></option><?php endforeach; ?></select><button class="btn btn-sm btn-dark">Save</button></form></td></tr><?php endforeach; ?></tbody></table></div><div class="card-body"><?php for($i=1;$i<=$pg['total_pages'];$i++): ?><a class="btn btn-sm <?= $i===$pg['page']?'btn-dark':'btn-outline-dark'; ?>" href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>"><?= $i; ?></a> <?php endfor; ?></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
