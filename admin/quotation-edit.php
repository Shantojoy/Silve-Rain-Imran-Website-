<?php
require_once __DIR__ . '/../includes/db.php';require_once __DIR__ . '/../includes/auth.php';require_once __DIR__ . '/../includes/functions.php';requireLogin();
$settings=$pdo->query('SELECT quotation_prefix FROM settings LIMIT 1')->fetch();
$customers=$pdo->query('SELECT id,name FROM customers ORDER BY name')->fetchAll();
if($_SERVER['REQUEST_METHOD']==='POST'){
  try{
   $customerId=(int)$_POST['customer_id'];$issueDate=$_POST['issue_date'];$valid=$_POST['valid_until'] ?: null;$item=trim($_POST['item_name']);$qty=(float)$_POST['quantity'];$unit=(float)$_POST['unit_price'];
   if(!$customerId||$item==='') throw new RuntimeException('Customer and item required.');
   $total=$qty*$unit;$quoNo=($settings['quotation_prefix'] ?? 'QUO').'-'.date('YmdHis');
   $pdo->beginTransaction();
   $pdo->prepare('INSERT INTO quotations (quotation_no,customer_id,issue_date,valid_until,status,subtotal,notes) VALUES (?,?,?,?,?,?,?)')->execute([$quoNo,$customerId,$issueDate,$valid,'Draft',$total,trim($_POST['notes'] ?? '')]);
   $qid=(int)$pdo->lastInsertId();
   $pdo->prepare('INSERT INTO quotation_items (quotation_id,item_name,quantity,unit_price,line_total) VALUES (?,?,?,?,?)')->execute([$qid,$item,$qty,$unit,$total]);
   $pdo->commit();setFlash('success','Quotation created.');redirect('quotations.php');
  }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();setFlash('danger',$e->getMessage());redirect('quotation-edit.php');}
}
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Create Quotation</h1>
<div class="card p-3 shadow-sm"><form method="post" class="row g-3"><div class="col-md-4"><label>Customer</label><select name="customer_id" class="form-select"><?php foreach($customers as $c): ?><option value="<?= $c['id']; ?>"><?= htmlspecialchars($c['name']); ?></option><?php endforeach; ?></select></div><div class="col-md-4"><label>Issue Date</label><input type="date" name="issue_date" class="form-control" value="<?= date('Y-m-d'); ?>"></div><div class="col-md-4"><label>Valid Until</label><input type="date" name="valid_until" class="form-control"></div><div class="col-md-6"><label>Item</label><input name="item_name" class="form-control"></div><div class="col-md-3"><label>Qty</label><input type="number" step="0.01" name="quantity" class="form-control" value="1"></div><div class="col-md-3"><label>Unit Price</label><input type="number" step="0.01" name="unit_price" class="form-control" value="0"></div><div class="col-12"><label>Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div><div class="col-12"><button class="btn btn-dark">Save Quotation</button></div></form></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
