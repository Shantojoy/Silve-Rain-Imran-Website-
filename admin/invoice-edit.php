<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();
$settings=$pdo->query('SELECT invoice_prefix FROM settings LIMIT 1')->fetch();
$customers=$pdo->query('SELECT id,name FROM customers ORDER BY name')->fetchAll();

if($_SERVER['REQUEST_METHOD']==='POST'){
    try{
        $customerId=(int)($_POST['customer_id'] ?? 0);
        $issueDate=$_POST['issue_date'] ?? date('Y-m-d');
        $dueDate=$_POST['due_date'] ?? null;
        $itemName=trim($_POST['item_name'] ?? '');
        $qty=(float)($_POST['quantity'] ?? 1);
        $price=(float)($_POST['unit_price'] ?? 0);
        $paid=(float)($_POST['paid_amount'] ?? 0);
        if(!$customerId || $itemName==='') throw new RuntimeException('Customer and item are required.');

        $lineTotal=$qty*$price;
        $subtotal=$lineTotal;
        $due=max(0,$subtotal-$paid);
        $status=$due==0?'Paid':($paid>0?'Partial':'Due');
        $invoiceNo=($settings['invoice_prefix'] ?? 'INV').'-'.date('YmdHis');

        $pdo->beginTransaction();
        $pdo->prepare('INSERT INTO invoices (invoice_no,customer_id,issue_date,due_date,status,subtotal,paid_amount,due_amount,notes) VALUES (?,?,?,?,?,?,?,?,?)')->execute([$invoiceNo,$customerId,$issueDate,$dueDate?:null,$status,$subtotal,$paid,$due,trim($_POST['notes'] ?? '')]);
        $invoiceId=(int)$pdo->lastInsertId();
        $pdo->prepare('INSERT INTO invoice_items (invoice_id,item_name,quantity,unit_price,line_total) VALUES (?,?,?,?,?)')->execute([$invoiceId,$itemName,$qty,$price,$lineTotal]);
        if($paid>0){$pdo->prepare('INSERT INTO payments (invoice_id,amount,payment_date,method,note) VALUES (?,?,?,?,?)')->execute([$invoiceId,$paid,$issueDate,'Cash','Initial payment']);}
        $pdo->commit();
        setFlash('success','Invoice created.');redirect('invoice-view.php?id='.$invoiceId);
    }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();setFlash('danger',$e->getMessage());redirect('invoice-edit.php');}
}
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Create Invoice</h1>
<div class="card p-3 shadow-sm"><form method="post" class="row g-3"><div class="col-md-4"><label class="form-label">Customer *</label><select name="customer_id" class="form-select" required><option value="">Select</option><?php foreach($customers as $c): ?><option value="<?= $c['id']; ?>"><?= htmlspecialchars($c['name']); ?></option><?php endforeach; ?></select></div><div class="col-md-4"><label class="form-label">Issue Date</label><input type="date" name="issue_date" class="form-control" value="<?= date('Y-m-d'); ?>"></div><div class="col-md-4"><label class="form-label">Due Date</label><input type="date" name="due_date" class="form-control"></div><div class="col-md-6"><label class="form-label">Item Name *</label><input name="item_name" class="form-control" placeholder="Wallpaper Installation"></div><div class="col-md-2"><label class="form-label">Qty</label><input type="number" step="0.01" name="quantity" class="form-control" value="1"></div><div class="col-md-2"><label class="form-label">Unit Price</label><input type="number" step="0.01" name="unit_price" class="form-control" value="0"></div><div class="col-md-2"><label class="form-label">Paid Amount</label><input type="number" step="0.01" name="paid_amount" class="form-control" value="0"></div><div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div><div class="col-12"><button class="btn btn-dark">Save Invoice</button></div></form></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
