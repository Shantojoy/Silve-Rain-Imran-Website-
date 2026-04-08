<?php
require_once __DIR__ . '/../includes/db.php';require_once __DIR__ . '/../includes/auth.php';requireLogin();
$settings=$pdo->query('SELECT * FROM settings LIMIT 1')->fetch();
if($_SERVER['REQUEST_METHOD']==='POST'){
  $pdo->prepare('UPDATE settings SET invoice_prefix=?, invoice_terms=?, invoice_footer=? WHERE id=?')->execute([trim($_POST['invoice_prefix']),trim($_POST['invoice_terms']),trim($_POST['invoice_footer']),$settings['id']]);
  setFlash('success','Invoice settings updated.');redirect('invoice-settings.php');
}
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Invoice Settings</h1>
<div class="card p-3 shadow-sm"><form method="post" class="row g-3"><div class="col-md-4"><label>Invoice Prefix</label><input name="invoice_prefix" class="form-control" value="<?= htmlspecialchars($settings['invoice_prefix']); ?>"></div><div class="col-12"><label>Terms & Conditions</label><textarea name="invoice_terms" class="form-control" rows="3"><?= htmlspecialchars($settings['invoice_terms']); ?></textarea></div><div class="col-12"><label>Footer Text</label><textarea name="invoice_footer" class="form-control" rows="2"><?= htmlspecialchars($settings['invoice_footer']); ?></textarea></div><div class="col-12"><button class="btn btn-dark">Save</button></div></form></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
