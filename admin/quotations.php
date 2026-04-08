<?php
require_once __DIR__ . '/../includes/db.php';require_once __DIR__ . '/../includes/auth.php';require_once __DIR__ . '/../includes/functions.php';requireLogin();
if(isset($_GET['convert'])){
  $id=(int)$_GET['convert'];
  $q=$pdo->prepare('SELECT * FROM quotations WHERE id=?');$q->execute([$id]);$quo=$q->fetch();
  if($quo){
    $set=$pdo->query('SELECT invoice_prefix FROM settings LIMIT 1')->fetch();
    $invoiceNo=($set['invoice_prefix'] ?? 'INV').'-'.date('YmdHis');
    $pdo->beginTransaction();
    $pdo->prepare('INSERT INTO invoices (invoice_no,customer_id,issue_date,due_date,status,subtotal,paid_amount,due_amount,notes) VALUES (?,?,?,?,?,?,?,?,?)')->execute([$invoiceNo,$quo['customer_id'],date('Y-m-d'),null,'Due',$quo['subtotal'],0,$quo['subtotal'],$quo['notes']]);
    $newId=(int)$pdo->lastInsertId();
    $items=$pdo->prepare('SELECT * FROM quotation_items WHERE quotation_id=?');$items->execute([$id]);
    foreach($items->fetchAll() as $it){$pdo->prepare('INSERT INTO invoice_items (invoice_id,item_name,quantity,unit_price,line_total) VALUES (?,?,?,?,?)')->execute([$newId,$it['item_name'],$it['quantity'],$it['unit_price'],$it['line_total']]);}
    $pdo->prepare("UPDATE quotations SET status='Converted' WHERE id=?")->execute([$id]);
    $pdo->commit(); setFlash('success','Quotation converted to invoice.'); redirect('invoice-view.php?id='.$newId);
  }
}
$rows=$pdo->query('SELECT q.*, c.name customer_name FROM quotations q JOIN customers c ON c.id=q.customer_id ORDER BY q.created_at DESC')->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Quotations</h1><a href="quotation-edit.php" class="btn btn-dark mb-3">New Quotation</a>
<div class="card shadow-sm"><table class="table mb-0"><thead><tr><th>No</th><th>Customer</th><th>Total</th><th>Status</th><th></th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= htmlspecialchars($r['quotation_no']); ?></td><td><?= htmlspecialchars($r['customer_name']); ?></td><td>$<?= number_format($r['subtotal'],2); ?></td><td><?= htmlspecialchars($r['status']); ?></td><td><?php if($r['status']!=='Converted'): ?><a href="?convert=<?= $r['id']; ?>" class="btn btn-sm btn-success">Convert</a><?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
