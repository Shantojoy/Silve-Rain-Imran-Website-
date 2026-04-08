<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $q = $pdo->prepare('SELECT before_image,after_image FROM gallery WHERE id=?');
    $q->execute([$id]);
    $row = $q->fetch();
    $pdo->prepare('DELETE FROM gallery WHERE id=?')->execute([$id]);
    if ($row) {
        deleteUploadedFile(__DIR__ . '/../uploads/gallery/' . $row['before_image']);
        deleteUploadedFile(__DIR__ . '/../uploads/gallery/' . $row['after_image']);
    }
    setFlash('success', 'Gallery item deleted.');
    redirect('gallery.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = (int)($_POST['id'] ?? 0);
        $title = sanitize($_POST['title'] ?? '');
        $category = sanitize($_POST['category'] ?? '');
        if (!$title || !$category) throw new RuntimeException('Title and category required.');

        $before = uploadImage('before_image', __DIR__ . '/../uploads/gallery');
        $after = uploadImage('after_image', __DIR__ . '/../uploads/gallery');

        if ($id) {
            $cur = $pdo->prepare('SELECT before_image,after_image FROM gallery WHERE id=?');
            $cur->execute([$id]);
            $existing = $cur->fetch();
            $before = $before ?: ($existing['before_image'] ?? null);
            $after = $after ?: ($existing['after_image'] ?? null);
            $stmt = $pdo->prepare('UPDATE gallery SET title=?,category=?,before_image=?,after_image=? WHERE id=?');
            $stmt->execute([$title,$category,$before,$after,$id]);
            setFlash('success', 'Gallery item updated.');
        } else {
            if (!$before || !$after) throw new RuntimeException('Both before and after images are required for new entry.');
            $stmt = $pdo->prepare('INSERT INTO gallery (title,category,before_image,after_image) VALUES (?,?,?,?)');
            $stmt->execute([$title,$category,$before,$after]);
            setFlash('success', 'Gallery item added.');
        }
    } catch (Throwable $e) {
        setFlash('danger', $e->getMessage());
    }
    redirect('gallery.php');
}

$edit = null;
if (isset($_GET['edit'])) { $s=$pdo->prepare('SELECT * FROM gallery WHERE id=?'); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }
$rows = $pdo->query('SELECT * FROM gallery ORDER BY created_at DESC')->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Manage Gallery</h1>
<div class="card p-3 shadow-sm mb-4">
<form method="post" enctype="multipart/form-data" class="row g-2">
<input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0); ?>">
<div class="col-md-3"><input name="title" class="form-control" placeholder="Title" value="<?= htmlspecialchars($edit['title'] ?? ''); ?>" required></div>
<div class="col-md-3"><input name="category" class="form-control" placeholder="Category" value="<?= htmlspecialchars($edit['category'] ?? ''); ?>" required></div>
<div class="col-md-3"><input type="file" name="before_image" class="form-control" accept="image/*"></div>
<div class="col-md-3"><input type="file" name="after_image" class="form-control" accept="image/*"></div>
<div class="col-12"><button class="btn btn-dark"><?= $edit ? 'Update' : 'Add'; ?> Gallery Item</button></div>
</form></div>
<table class="table table-bordered bg-white shadow-sm"><thead><tr><th>Title</th><th>Category</th><th>Before</th><th>After</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= htmlspecialchars($r['title']); ?></td><td><?= htmlspecialchars($r['category']); ?></td><td><?php if($r['before_image']):?><img src="../uploads/gallery/<?= htmlspecialchars($r['before_image']); ?>" width="70"><?php endif;?></td><td><?php if($r['after_image']):?><img src="../uploads/gallery/<?= htmlspecialchars($r['after_image']); ?>" width="70"><?php endif;?></td><td><a class="btn btn-sm btn-primary" href="?edit=<?= $r['id']; ?>">Edit</a> <a data-confirm="Delete this gallery entry?" class="btn btn-sm btn-danger" href="?delete=<?= $r['id']; ?>">Delete</a></td></tr><?php endforeach; ?>
</tbody></table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
