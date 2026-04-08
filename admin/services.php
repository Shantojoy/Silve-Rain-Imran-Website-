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

    $stmt = $pdo->prepare('DELETE FROM services WHERE id = ?');
    $stmt->execute([$id]);
    if ($row && $row['image']) {
        deleteUploadedFile(__DIR__ . '/../uploads/products/' . $row['image']);
    }
    setFlash('success', 'Service deleted.');
    redirect('services.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = (int)($_POST['id'] ?? 0);
        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        if (!$title || !$description) {
            throw new RuntimeException('Title and description are required.');
        }

        $newImage = uploadImage('image', __DIR__ . '/../uploads/products');

        if ($id > 0) {
            $current = $pdo->prepare('SELECT image FROM services WHERE id = ?');
            $current->execute([$id]);
            $existing = $current->fetch();
            $image = $newImage ?: ($existing['image'] ?? null);
            $stmt = $pdo->prepare('UPDATE services SET title=?, description=?, image=? WHERE id=?');
            $stmt->execute([$title, $description, $image, $id]);
            if ($newImage && !empty($existing['image'])) {
                deleteUploadedFile(__DIR__ . '/../uploads/products/' . $existing['image']);
            }
            setFlash('success', 'Service updated.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO services (title, description, image) VALUES (?, ?, ?)');
            $stmt->execute([$title, $description, $newImage]);
            setFlash('success', 'Service added.');
        }
    } catch (Throwable $e) {
        setFlash('danger', $e->getMessage());
    }
    redirect('services.php');
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM services WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch();
}

$rows = $pdo->query('SELECT * FROM services ORDER BY created_at DESC')->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Manage Services</h1>
<div class="card p-3 shadow-sm mb-4">
    <form method="post" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0); ?>">
        <div class="col-md-4"><input name="title" class="form-control" placeholder="Service title" value="<?= htmlspecialchars($edit['title'] ?? ''); ?>" required></div>
        <div class="col-md-5"><input name="description" class="form-control" placeholder="Description" value="<?= htmlspecialchars($edit['description'] ?? ''); ?>" required></div>
        <div class="col-md-3"><input type="file" name="image" class="form-control" accept="image/*"></div>
        <div class="col-12"><button class="btn btn-dark"><?= $edit ? 'Update' : 'Add'; ?> Service</button></div>
    </form>
</div>
<table class="table table-bordered bg-white shadow-sm">
<thead><tr><th>ID</th><th>Title</th><th>Image</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach ($rows as $r): ?>
<tr>
<td><?= $r['id']; ?></td><td><?= htmlspecialchars($r['title']); ?></td>
<td><?php if ($r['image']): ?><img src="../uploads/products/<?= htmlspecialchars($r['image']); ?>" width="80"><?php endif; ?></td>
<td>
<a class="btn btn-sm btn-primary" href="?edit=<?= $r['id']; ?>">Edit</a>
<a class="btn btn-sm btn-danger" data-confirm="Delete this service?" href="?delete=<?= $r['id']; ?>">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</tbody></table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
