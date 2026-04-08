<?php
require_once __DIR__ . '/../includes/db.php';require_once __DIR__ . '/../includes/auth.php';require_once __DIR__ . '/../includes/functions.php';requireLogin();
$settings=$pdo->query('SELECT * FROM settings LIMIT 1')->fetch();
if($_SERVER['REQUEST_METHOD']==='POST'){
 try{
  $siteName=sanitize($_POST['site_name'] ?? '');$contactEmail=sanitize($_POST['contact_email'] ?? '');$phone=sanitize($_POST['phone'] ?? '');$address=sanitize($_POST['address'] ?? '');
  $siteDescription=sanitize($_POST['site_description'] ?? '');$paymentInstructions=sanitize($_POST['payment_instructions'] ?? '');
  $waNum=preg_replace('/[^0-9]/','',$_POST['whatsapp_number'] ?? '');$waMsg=sanitize($_POST['whatsapp_message'] ?? '');
  $senderName=sanitize($_POST['email_sender_name'] ?? '');$senderEmail=sanitize($_POST['email_sender_email'] ?? '');
  $invoicePrefix=sanitize($_POST['invoice_prefix'] ?? 'INV');$quotationPrefix=sanitize($_POST['quotation_prefix'] ?? 'QUO');
  $invoiceTerms=sanitize($_POST['invoice_terms'] ?? '');$invoiceFooter=sanitize($_POST['invoice_footer'] ?? '');

  $logo=uploadImage('site_logo', __DIR__.'/../uploads/products');$logo=$logo ?: ($settings['site_logo'] ?? null);
  $favicon=uploadImage('favicon', __DIR__.'/../uploads/products', ['image/jpeg','image/png','image/x-icon']);$favicon=$favicon ?: ($settings['favicon'] ?? null);

  $pdo->prepare('UPDATE settings SET site_name=?,site_logo=?,favicon=?,site_description=?,contact_email=?,phone=?,address=?,payment_instructions=?,whatsapp_number=?,whatsapp_message=?,email_sender_name=?,email_sender_email=?,invoice_prefix=?,quotation_prefix=?,invoice_terms=?,invoice_footer=? WHERE id=?')
      ->execute([$siteName,$logo,$favicon,$siteDescription,$contactEmail,$phone,$address,$paymentInstructions,$waNum,$waMsg,$senderName,$senderEmail,$invoicePrefix,$quotationPrefix,$invoiceTerms,$invoiceFooter,$settings['id']]);
  setFlash('success','Settings updated.');
 }catch(Throwable $e){setFlash('danger',$e->getMessage());}
 redirect('settings.php');
}
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">General Settings</h1>
<div class="card p-3 shadow-sm"><form method="post" enctype="multipart/form-data" class="row g-3">
<div class="col-md-6"><label>Site Name *</label><input name="site_name" class="form-control" value="<?= htmlspecialchars($settings['site_name'] ?? ''); ?>" required><?= helpText('Business brand name shown across the website.'); ?></div>
<div class="col-md-3"><label>Site Logo</label><input type="file" name="site_logo" class="form-control" accept="image/jpeg,image/png"></div>
<div class="col-md-3"><label>Favicon</label><input type="file" name="favicon" class="form-control" accept="image/png,image/x-icon"></div>
<div class="col-12"><label>Site Description</label><textarea name="site_description" class="form-control" rows="2"><?= htmlspecialchars($settings['site_description'] ?? ''); ?></textarea></div>
<div class="col-md-4"><label>Contact Email</label><input name="contact_email" class="form-control" value="<?= htmlspecialchars($settings['contact_email'] ?? ''); ?>"></div>
<div class="col-md-4"><label>Phone</label><input name="phone" class="form-control" value="<?= htmlspecialchars($settings['phone'] ?? ''); ?>"></div>
<div class="col-md-4"><label>Address</label><input name="address" class="form-control" value="<?= htmlspecialchars($settings['address'] ?? ''); ?>"></div>
<div class="col-12"><label>Payment Instructions</label><textarea name="payment_instructions" class="form-control" rows="2"><?= htmlspecialchars($settings['payment_instructions'] ?? ''); ?></textarea></div>
<div class="col-md-6"><label>WhatsApp Number</label><input name="whatsapp_number" class="form-control" value="<?= htmlspecialchars($settings['whatsapp_number'] ?? ''); ?>"></div>
<div class="col-md-6"><label>WhatsApp Message</label><input name="whatsapp_message" class="form-control" value="<?= htmlspecialchars($settings['whatsapp_message'] ?? ''); ?>"></div>
<div class="col-md-6"><label>Email Sender Name</label><input name="email_sender_name" class="form-control" value="<?= htmlspecialchars($settings['email_sender_name'] ?? ''); ?>"></div>
<div class="col-md-6"><label>Email Sender Email</label><input name="email_sender_email" class="form-control" value="<?= htmlspecialchars($settings['email_sender_email'] ?? ''); ?>"></div>
<hr id="invoice-settings">
<div class="col-md-6"><label>Invoice Prefix</label><input name="invoice_prefix" class="form-control" value="<?= htmlspecialchars($settings['invoice_prefix'] ?? 'INV'); ?>"></div>
<div class="col-md-6"><label>Quotation Prefix</label><input name="quotation_prefix" class="form-control" value="<?= htmlspecialchars($settings['quotation_prefix'] ?? 'QUO'); ?>"></div>
<div class="col-12"><label>Invoice Terms</label><textarea name="invoice_terms" class="form-control" rows="2"><?= htmlspecialchars($settings['invoice_terms'] ?? ''); ?></textarea></div>
<div class="col-12"><label>Invoice Footer</label><textarea name="invoice_footer" class="form-control" rows="2"><?= htmlspecialchars($settings['invoice_footer'] ?? ''); ?></textarea></div>
<div class="col-12"><button class="btn btn-dark">Save Settings</button></div>
</form></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
