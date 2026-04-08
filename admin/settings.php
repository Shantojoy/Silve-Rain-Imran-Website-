<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$settings = $pdo->query('SELECT * FROM settings LIMIT 1')->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $siteName = sanitize($_POST['site_name'] ?? '');
        $siteDescription = sanitize($_POST['site_description'] ?? '');
        $contactEmail = sanitize($_POST['contact_email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $paymentInstructions = sanitize($_POST['payment_instructions'] ?? '');
        $whatsappNumber = preg_replace('/[^0-9]/', '', $_POST['whatsapp_number'] ?? '');
        $whatsappMessage = sanitize($_POST['whatsapp_message'] ?? '');
        $senderName = sanitize($_POST['email_sender_name'] ?? '');
        $senderAddress = sanitize($_POST['email_sender_address'] ?? '');

        if ($senderAddress && !filter_var($senderAddress, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Email sender address is invalid.');
        }

        $logo = uploadImage('site_logo', __DIR__ . '/../uploads/products');
        $logo = $logo ?: ($settings['site_logo'] ?? null);

        $pdo->prepare('UPDATE settings SET site_name=?,site_logo=?,site_description=?,contact_email=?,phone=?,address=?,payment_instructions=?,whatsapp_number=?,whatsapp_message=?,email_sender_name=?,email_sender_address=? WHERE id=?')
            ->execute([$siteName,$logo,$siteDescription,$contactEmail,$phone,$address,$paymentInstructions,$whatsappNumber,$whatsappMessage,$senderName,$senderAddress,$settings['id']]);

        setFlash('success', 'Settings updated successfully.');
    } catch (Throwable $e) {
        setFlash('danger', $e->getMessage());
    }
    redirect('settings.php');
}

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Settings</h1>
<p class="text-muted">Manage business branding, WhatsApp, and email sender values from one place.</p>
<div class="card p-3 shadow-sm">
<form method="post" enctype="multipart/form-data" class="row g-3">
<div class="col-md-6"><label class="form-label">Site Name <span class="text-danger">*</span></label><input name="site_name" class="form-control" placeholder="PaintPro" value="<?= htmlspecialchars($settings['site_name'] ?? ''); ?>" required><?= helpText('Enter your brand/business name as shown in navbar and footer.'); ?></div>
<div class="col-md-6"><label class="form-label">Site Logo</label><input type="file" name="site_logo" class="form-control" accept="image/jpeg,image/png"><?= helpText('Upload PNG/JPG logo used in website branding.'); ?></div>
<div class="col-12"><label class="form-label">Site Description</label><textarea name="site_description" class="form-control" rows="2"><?= htmlspecialchars($settings['site_description'] ?? ''); ?></textarea></div>
<div class="col-md-4"><label class="form-label">Contact Email</label><input name="contact_email" class="form-control" value="<?= htmlspecialchars($settings['contact_email'] ?? ''); ?>"></div>
<div class="col-md-4"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?= htmlspecialchars($settings['phone'] ?? ''); ?>"></div>
<div class="col-md-4"><label class="form-label">Address</label><input name="address" class="form-control" value="<?= htmlspecialchars($settings['address'] ?? ''); ?>"></div>
<div class="col-12"><label class="form-label">Payment Instructions (COD)</label><textarea name="payment_instructions" class="form-control" rows="3" placeholder="Cash on delivery instructions shown at checkout"><?= htmlspecialchars($settings['payment_instructions'] ?? ''); ?></textarea><?= helpText('This text appears on checkout page before order placement.'); ?></div>
<hr>
<div class="col-md-6"><label class="form-label">WhatsApp Number (countrycode + number)</label><input name="whatsapp_number" class="form-control" value="<?= htmlspecialchars($settings['whatsapp_number'] ?? ''); ?>" placeholder="15551234567"><?= helpText('Used for floating WhatsApp button on frontend.'); ?></div>
<div class="col-md-6"><label class="form-label">WhatsApp Default Message</label><input name="whatsapp_message" class="form-control" value="<?= htmlspecialchars($settings['whatsapp_message'] ?? ''); ?>"></div>
<div class="col-md-6"><label class="form-label">Email Sender Name</label><input name="email_sender_name" class="form-control" value="<?= htmlspecialchars($settings['email_sender_name'] ?? ''); ?>"></div>
<div class="col-md-6"><label class="form-label">Email Sender Address</label><input name="email_sender_address" class="form-control" value="<?= htmlspecialchars($settings['email_sender_address'] ?? ''); ?>"></div>
<div class="col-12 d-flex gap-2"><button class="btn btn-dark"><i class="bi bi-save"></i> Save Settings</button><a href="email-templates.php" class="btn btn-outline-primary"><i class="bi bi-envelope-paper"></i> Manage Email Templates</a></div>
</form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
