<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$filterStatus = trim($_GET['status'] ?? '');
$filterCustomer = (int)($_GET['customer_id'] ?? 0);
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$where = 'WHERE 1'; $params=[];
if($filterStatus){$where.=' AND i.status=?';$params[]=$filterStatus;}
if($filterCustomer){$where.=' AND i.customer_id=?';$params[]=$filterCustomer;}
if($from){$where.=' AND i.issue_date>=?';$params[]=$from;}
if($to){$where.=' AND i.issue_date<=?';$params[]=$to;}

$c=$pdo->prepare("SELECT COUNT(*) FROM invoices i $where");$c->execute($params);$pg=paginate((int)$c->fetchColumn(),10);
$stmt=$pdo->prepare("SELECT i.*, c.name customer_name FROM invoices i JOIN customers c ON c.id=i.customer_id $where ORDER BY i.created_at DESC LIMIT {$pg['per_page']} OFFSET {$pg['offset']}");
$stmt->execute($params);$rows=$stmt->fetchAll();
$customers=$pdo->query('SELECT id,name FROM customers ORDER BY name')->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Invoices</h1>
<div class="d-flex justify-content-between mb-3"><form class="row g-2"><div class="col"><input type="date" name="from" class="form-control" value="<?= htmlspecialchars($from); ?>"></div><div class="col"><input type="date" name="to" class="form-control" value="<?= htmlspecialchars($to); ?>"></div><div class="col"><select name="status" class="form-select"><option value="">All Status</option><?php foreach(['Paid','Partial','Due'] as $s): ?><option <?= $filterStatus===$s?'selected':''; ?>><?= $s; ?></option><?php endforeach; ?></select></div><div class="col"><select name="customer_id" class="form-select"><option value="">All Customers</option><?php foreach($customers as $c): ?><option value="<?= $c['id']; ?>" <?= $filterCustomer===(int)$c['id']?'selected':''; ?>><?= htmlspecialchars($c['name']); ?></option><?php endforeach; ?></select></div><div class="col"><button class="btn btn-outline-dark">Filter</button></div></form><a href="invoice-edit.php" class="btn btn-dark"><i class="bi bi-plus-circle"></i> New Invoice</a></div>
<div class="card shadow-sm"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Invoice</th><th>Customer</th><th>Total</th><th>Paid</th><th>Due</th><th>Status</th><th></th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= htmlspecialchars($r['invoice_no']); ?></td><td><?= htmlspecialchars($r['customer_name']); ?></td><td>$<?= number_format((float)$r['subtotal'],2); ?></td><td>$<?= number_format((float)$r['paid_amount'],2); ?></td><td>$<?= number_format((float)$r['due_amount'],2); ?></td><td><span class="badge bg-secondary"><?= htmlspecialchars($r['status']); ?></span></td><td><a class="btn btn-sm btn-primary" href="invoice-view.php?id=<?= $r['id']; ?>"><i class="bi bi-eye"></i></a></td></tr><?php endforeach; ?></tbody></table></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
