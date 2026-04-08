<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = sanitize($_POST['name'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $body = trim($_POST['body'] ?? '');
    $statusTrigger = sanitize($_POST['status_trigger'] ?? '');

    if ($id) {
        $pdo->prepare('UPDATE email_templates SET name=?, subject=?, body=?, status_trigger=? WHERE id=?')->execute([$name,$subject,$body,$statusTrigger,$id]);
        setFlash('success', 'Template updated.');
    } else {
        $pdo->prepare('INSERT INTO email_templates (name,subject,body,status_trigger) VALUES (?,?,?,?)')->execute([$name,$subject,$body,$statusTrigger]);
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
<div class="card p-3 shadow-sm mb-3"><form method="post" class="row g-2"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0); ?>"><div class="col-md-3"><input name="name" class="form-control" placeholder="Template Name" value="<?= htmlspecialchars($edit['name'] ?? ''); ?>" required></div><div class="col-md-3"><input name="status_trigger" class="form-control" placeholder="Trigger (new_order / status_Completed)" value="<?= htmlspecialchars($edit['status_trigger'] ?? ''); ?>" required></div><div class="col-md-6"><input name="subject" class="form-control" placeholder="Subject" value="<?= htmlspecialchars($edit['subject'] ?? ''); ?>" required></div><div class="col-12"><textarea name="body" class="form-control" rows="4" placeholder="Body with placeholders e.g. {{name}}" required><?= htmlspecialchars($edit['body'] ?? ''); ?></textarea></div><div class="col-12"><button class="btn btn-dark"><?= $edit ? 'Update' : 'Add'; ?> Template</button></div></form></div>
<div class="card shadow-sm"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Name</th><th>Trigger</th><th>Subject</th><th></th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= htmlspecialchars($r['name']); ?></td><td><?= htmlspecialchars($r['status_trigger']); ?></td><td><?= htmlspecialchars($r['subject']); ?></td><td><a class="btn btn-sm btn-primary" href="?edit=<?= $r['id']; ?>">Edit</a> <a data-confirm="Delete template?" class="btn btn-sm btn-danger" href="?delete=<?= $r['id']; ?>">Delete</a></td></tr><?php endforeach; ?></tbody></table></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
