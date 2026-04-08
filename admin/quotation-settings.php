<?php
require_once __DIR__ . '/../includes/db.php';require_once __DIR__ . '/../includes/auth.php';requireLogin();
$settings=$pdo->query('SELECT * FROM settings LIMIT 1')->fetch();
if($_SERVER['REQUEST_METHOD']==='POST'){
  $pdo->prepare('UPDATE settings SET quotation_prefix=? WHERE id=?')->execute([trim($_POST['quotation_prefix']),$settings['id']]);
  setFlash('success','Quotation settings updated.');redirect('quotation-settings.php');
}
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Quotation Settings</h1>
<div class="card p-3 shadow-sm"><form method="post" class="row g-3"><div class="col-md-4"><label>Quotation Prefix</label><input name="quotation_prefix" class="form-control" value="<?= htmlspecialchars($settings['quotation_prefix']); ?>"></div><div class="col-12"><button class="btn btn-dark">Save</button></div></form></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
