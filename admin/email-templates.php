<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = sanitize($_POST['name'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $body = trim($_POST['body'] ?? '');
    $triggerType = sanitize($_POST['trigger_type'] ?? '');
    $status = sanitize($_POST['status'] ?? 'enabled');

    if (!in_array($triggerType, ['order_created','order_status_updated','new_lead'], true)) {
        setFlash('danger', 'Invalid trigger type selected.');
        redirect('email-templates.php');
    }

    if ($id) {
        $pdo->prepare('UPDATE email_templates SET name=?, subject=?, body=?, trigger_type=?, status=? WHERE id=?')->execute([$name,$subject,$body,$triggerType,$status,$id]);
        setFlash('success', 'Template updated.');
    } else {
        $pdo->prepare('INSERT INTO email_templates (name,subject,body,trigger_type,status) VALUES (?,?,?,?,?)')->execute([$name,$subject,$body,$triggerType,$status]);
        setFlash('success', 'Template added.');
    }
    redirect('email-templates.php');
}

if (isset($_GET['delete'])) {
    $pdo->prepare('DELETE FROM email_templates WHERE id=?')->execute([(int)$_GET['delete']]);
    setFlash('success', 'Template deleted.');
    redirect('email-templates.php');
}

$edit = null;
if (isset($_GET['edit'])) { $s=$pdo->prepare('SELECT * FROM email_templates WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch(); }
$rows = $pdo->query('SELECT * FROM email_templates ORDER BY id DESC')->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Email Templates</h1>
<p class="text-muted">Variables available: <code>{customer_name}</code>, <code>{order_id}</code>, <code>{order_status}</code>, <code>{product_name}</code></p>
<div class="card p-3 shadow-sm mb-3"><form method="post" class="row g-2"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0); ?>"><div class="col-md-3"><label class="form-label">Template Name</label><input name="name" class="form-control" value="<?= htmlspecialchars($edit['name'] ?? ''); ?>" required></div><div class="col-md-3"><label class="form-label">Trigger Type</label><select name="trigger_type" class="form-select"><option value="order_created" <?= ($edit['trigger_type'] ?? '')==='order_created'?'selected':''; ?>>order_created</option><option value="order_status_updated" <?= ($edit['trigger_type'] ?? '')==='order_status_updated'?'selected':''; ?>>order_status_updated</option><option value="new_lead" <?= ($edit['trigger_type'] ?? '')==='new_lead'?'selected':''; ?>>new_lead</option></select></div><div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="enabled" <?= ($edit['status'] ?? '')==='enabled'?'selected':''; ?>>Enabled</option><option value="disabled" <?= ($edit['status'] ?? '')==='disabled'?'selected':''; ?>>Disabled</option></select></div><div class="col-md-3"><label class="form-label">Subject</label><input name="subject" class="form-control" value="<?= htmlspecialchars($edit['subject'] ?? ''); ?>" required></div><div class="col-12"><label class="form-label">HTML Body</label><textarea name="body" class="form-control" rows="6" required><?= htmlspecialchars($edit['body'] ?? ''); ?></textarea></div><div class="col-12"><button class="btn btn-dark"><i class="bi bi-save"></i> <?= $edit ? 'Update' : 'Add'; ?> Template</button></div></form></div>
<?php if ($edit): ?><div class="card p-3 shadow-sm mb-3"><h6>Preview</h6><div class="border rounded p-3 bg-light"><?= $edit['body']; ?></div></div><?php endif; ?>
<div class="card shadow-sm"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Name</th><th>Trigger</th><th>Status</th><th>Subject</th><th>Action</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= htmlspecialchars($r['name']); ?></td><td><?= htmlspecialchars($r['trigger_type']); ?></td><td><span class="badge <?= $r['status']==='enabled'?'bg-success':'bg-secondary'; ?>"><?= htmlspecialchars($r['status']); ?></span></td><td><?= htmlspecialchars($r['subject']); ?></td><td><a class="btn btn-sm btn-primary" href="?edit=<?= $r['id']; ?>" data-bs-toggle="tooltip" title="Edit template"><i class="bi bi-pencil"></i></a> <a data-confirm="Delete template?" class="btn btn-sm btn-danger" href="?delete=<?= $r['id']; ?>" data-bs-toggle="tooltip" title="Delete template"><i class="bi bi-trash"></i></a></td></tr><?php endforeach; ?></tbody></table></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
