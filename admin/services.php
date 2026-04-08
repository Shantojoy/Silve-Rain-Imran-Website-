<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $old = $pdo->prepare('SELECT image FROM services WHERE id = ?');
    $old->execute([$id]);
    $row = $old->fetch();
    $pdo->prepare('DELETE FROM services WHERE id = ?')->execute([$id]);
    if ($row && $row['image']) deleteUploadedFile(__DIR__ . '/../uploads/products/' . $row['image']);
    setFlash('success', 'Service deleted.');
    redirect('services.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        if (!$title || !$description) throw new RuntimeException('Title and description are required.');
        $newImage = uploadImage('image', __DIR__ . '/../uploads/products');

        if ($id > 0) {
            $existing = $pdo->prepare('SELECT image FROM services WHERE id = ?'); $existing->execute([$id]); $curr = $existing->fetch();
            $image = $newImage ?: ($curr['image'] ?? null);
            $pdo->prepare('UPDATE services SET title=?,description=?,image=?,category_id=? WHERE id=?')->execute([$title,$description,$image,$categoryId,$id]);
            setFlash('success', 'Service updated.');
        } else {
            $pdo->prepare('INSERT INTO services (title,description,image,category_id) VALUES (?,?,?,?)')->execute([$title,$description,$newImage,$categoryId]);
            setFlash('success', 'Service added.');
        }
    } catch (Throwable $e) { setFlash('danger', $e->getMessage()); }
    redirect('services.php');
}

$serviceCats = $pdo->query("SELECT id,name FROM categories WHERE type='service' ORDER BY name")->fetchAll();
$edit = null;
if (isset($_GET['edit'])) { $stmt=$pdo->prepare('SELECT * FROM services WHERE id=?'); $stmt->execute([(int)$_GET['edit']]); $edit=$stmt->fetch(); }
$rows = $pdo->query('SELECT s.*, c.name as category_name FROM services s LEFT JOIN categories c ON c.id=s.category_id ORDER BY s.created_at DESC')->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Manage Services</h1>
<div class="card p-3 shadow-sm mb-4">
    <h6 class="mb-3">Service Form</h6>
    <form method="post" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0); ?>">
        <div class="col-md-4">
            <label class="form-label">Service Title <span class="text-danger">*</span></label>
            <input name="title" class="form-control" placeholder="Interior Wall Painting" value="<?= htmlspecialchars($edit['title'] ?? ''); ?>" required>
            <?= helpText('Use a clear service name customers can understand quickly.'); ?>
        </div>
        <div class="col-md-4">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select"><option value="">Select category</option><?php foreach($serviceCats as $cat): ?><option value="<?= $cat['id']; ?>" <?= (int)($edit['category_id'] ?? 0)===(int)$cat['id']?'selected':''; ?>><?= htmlspecialchars($cat['name']); ?></option><?php endforeach; ?></select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Service Image</label>
            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png">
            <?= helpText('Upload JPG or PNG image, max 5MB.'); ?>
        </div>
        <div class="col-12">
            <label class="form-label">Description <span class="text-danger">*</span></label>
            <textarea name="description" class="form-control" rows="3" placeholder="Explain what this service includes" required><?= htmlspecialchars($edit['description'] ?? ''); ?></textarea>
            <?= helpText('Include what is covered, materials, and expected finish.'); ?>
        </div>
        <div class="col-12"><button class="btn btn-dark"><i class="bi bi-save"></i> <?= $edit ? 'Update' : 'Add'; ?> Service</button></div>
    </form>
</div>
<div class="card shadow-sm"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Title</th><th>Category</th><th>Image</th><th>Action</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= htmlspecialchars($r['title']); ?></td><td><?= htmlspecialchars($r['category_name'] ?? '-'); ?></td><td><?php if($r['image']): ?><img src="../uploads/products/<?= htmlspecialchars($r['image']); ?>" width="70"><?php endif; ?></td><td><a class="btn btn-sm btn-primary" href="?edit=<?= $r['id']; ?>" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil"></i></a> <a class="btn btn-sm btn-danger" data-confirm="Delete this service?" href="?delete=<?= $r['id']; ?>" data-bs-toggle="tooltip" title="Delete"><i class="bi bi-trash"></i></a></td></tr><?php endforeach; ?></tbody></table></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
