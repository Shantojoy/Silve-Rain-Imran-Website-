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
        $logo = uploadImage('site_logo', __DIR__ . '/../uploads/products');
        $logo = $logo ?: ($settings['site_logo'] ?? null);

        $pdo->prepare('UPDATE settings SET site_name=?,site_logo=?,site_description=?,contact_email=?,phone=?,address=?,payment_instructions=? WHERE id=?')
            ->execute([$siteName,$logo,$siteDescription,$contactEmail,$phone,$address,$paymentInstructions,$settings['id']]);
        setFlash('success', 'Settings updated.');
    } catch (Throwable $e) {
        setFlash('danger', $e->getMessage());
    }
    redirect('settings.php');
}

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Settings</h1>
<div class="card p-3 shadow-sm">
<form method="post" enctype="multipart/form-data" class="row g-3">
<div class="col-md-6"><label class="form-label">Site Name</label><input name="site_name" class="form-control" value="<?= htmlspecialchars($settings['site_name'] ?? ''); ?>" required></div>
<div class="col-md-6"><label class="form-label">Site Logo</label><input type="file" name="site_logo" class="form-control" accept="image/jpeg,image/png"></div>
<div class="col-12"><label class="form-label">Site Description</label><textarea name="site_description" class="form-control" rows="2"><?= htmlspecialchars($settings['site_description'] ?? ''); ?></textarea></div>
<div class="col-md-4"><label class="form-label">Contact Email</label><input name="contact_email" class="form-control" value="<?= htmlspecialchars($settings['contact_email'] ?? ''); ?>"></div>
<div class="col-md-4"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?= htmlspecialchars($settings['phone'] ?? ''); ?>"></div>
<div class="col-md-4"><label class="form-label">Address</label><input name="address" class="form-control" value="<?= htmlspecialchars($settings['address'] ?? ''); ?>"></div>
<div class="col-12"><label class="form-label">Payment Instructions (COD text)</label><textarea name="payment_instructions" class="form-control" rows="3"><?= htmlspecialchars($settings['payment_instructions'] ?? ''); ?></textarea></div>
<div class="col-12"><button class="btn btn-dark">Save Settings</button></div>
</form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
