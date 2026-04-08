<?php
require_once __DIR__ . '/../includes/db.php';require_once __DIR__ . '/../includes/auth.php';require_once __DIR__ . '/../includes/functions.php';requireLogin();
if($_SERVER['REQUEST_METHOD']==='POST'){
  try{
   $invoiceId=(int)$_POST['invoice_id'];$amount=(float)$_POST['amount'];$date=$_POST['payment_date'];$method=trim($_POST['method']);
   if(!$invoiceId||$amount<=0) throw new RuntimeException('Valid invoice and amount required.');
   $pdo->beginTransaction();
   $pdo->prepare('INSERT INTO payments (invoice_id,amount,payment_date,method,note) VALUES (?,?,?,?,?)')->execute([$invoiceId,$amount,$date,$method,trim($_POST['note'] ?? '')]);
   $inv=$pdo->prepare('SELECT subtotal, paid_amount FROM invoices WHERE id=?');$inv->execute([$invoiceId]);$row=$inv->fetch();
   $paid=$row['paid_amount']+$amount;$due=max(0,$row['subtotal']-$paid);$status=$due==0?'Paid':($paid>0?'Partial':'Due');
   $pdo->prepare('UPDATE invoices SET paid_amount=?, due_amount=?, status=? WHERE id=?')->execute([$paid,$due,$status,$invoiceId]);
   $pdo->commit();setFlash('success','Payment recorded.');
  }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();setFlash('danger',$e->getMessage());}
  redirect('payments.php');
}
$invoices=$pdo->query('SELECT id,invoice_no,due_amount FROM invoices ORDER BY created_at DESC')->fetchAll();
$rows=$pdo->query('SELECT p.*, i.invoice_no FROM payments p JOIN invoices i ON i.id=p.invoice_id ORDER BY p.created_at DESC')->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Payments</h1>
<div class="card p-3 shadow-sm mb-3"><form method="post" class="row g-2"><div class="col-md-3"><select name="invoice_id" class="form-select"><?php foreach($invoices as $i): ?><option value="<?= $i['id']; ?>"><?= htmlspecialchars($i['invoice_no']); ?> (Due $<?= number_format($i['due_amount'],2); ?>)</option><?php endforeach; ?></select></div><div class="col-md-2"><input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount"></div><div class="col-md-2"><input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d'); ?>"></div><div class="col-md-2"><input name="method" class="form-control" value="Cash"></div><div class="col-md-2"><input name="note" class="form-control" placeholder="Note"></div><div class="col-md-1"><button class="btn btn-dark w-100">Add</button></div></form></div>
<div class="card shadow-sm"><table class="table mb-0"><thead><tr><th>Invoice</th><th>Amount</th><th>Date</th><th>Method</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= htmlspecialchars($r['invoice_no']); ?></td><td>$<?= number_format($r['amount'],2); ?></td><td><?= htmlspecialchars($r['payment_date']); ?></td><td><?= htmlspecialchars($r['method']); ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
