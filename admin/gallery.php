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
    if ($row) { deleteUploadedFile(__DIR__.'/../uploads/gallery/'.$row['before_image']); deleteUploadedFile(__DIR__.'/../uploads/gallery/'.$row['after_image']); }
    setFlash('success', 'Gallery item deleted.');
    redirect('gallery.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = (int)($_POST['id'] ?? 0);
        $title = sanitize($_POST['title'] ?? '');
        $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        if (!$title) throw new RuntimeException('Title required.');
        $before = uploadImage('before_image', __DIR__.'/../uploads/gallery');
        $after = uploadImage('after_image', __DIR__.'/../uploads/gallery');

        if ($id) {
            $cur = $pdo->prepare('SELECT before_image,after_image FROM gallery WHERE id=?'); $cur->execute([$id]); $old = $cur->fetch();
            $pdo->prepare('UPDATE gallery SET title=?, category_id=?, before_image=?, after_image=? WHERE id=?')->execute([$title,$categoryId,$before ?: $old['before_image'],$after ?: $old['after_image'],$id]);
            setFlash('success', 'Gallery item updated.');
        } else {
            if (!$before || !$after) throw new RuntimeException('Both before and after images are required.');
            $pdo->prepare('INSERT INTO gallery (title,category_id,before_image,after_image) VALUES (?,?,?,?)')->execute([$title,$categoryId,$before,$after]);
            setFlash('success', 'Gallery item added.');
        }
    } catch (Throwable $e) { setFlash('danger', $e->getMessage()); }
    redirect('gallery.php');
}

$cats = $pdo->query("SELECT id,name FROM categories WHERE type='gallery' ORDER BY name")->fetchAll();
$edit = null;
if (isset($_GET['edit'])) { $s=$pdo->prepare('SELECT * FROM gallery WHERE id=?'); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }
$rows = $pdo->query('SELECT g.*, c.name category_name FROM gallery g LEFT JOIN categories c ON c.id=g.category_id ORDER BY g.created_at DESC')->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Manage Gallery</h1>
<div class="card p-3 shadow-sm mb-4"><form method="post" enctype="multipart/form-data" class="row g-2"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0); ?>"><div class="col-md-3"><input name="title" class="form-control" placeholder="Title" value="<?= htmlspecialchars($edit['title'] ?? ''); ?>" required></div><div class="col-md-3"><select name="category_id" class="form-select"><option value="">Category</option><?php foreach($cats as $cat): ?><option value="<?= $cat['id']; ?>" <?= (int)($edit['category_id'] ?? 0)===(int)$cat['id']?'selected':''; ?>><?= htmlspecialchars($cat['name']); ?></option><?php endforeach; ?></select></div><div class="col-md-3"><input type="file" name="before_image" class="form-control" accept="image/jpeg,image/png"></div><div class="col-md-3"><input type="file" name="after_image" class="form-control" accept="image/jpeg,image/png"></div><div class="col-12"><button class="btn btn-dark"><?= $edit ? 'Update':'Add'; ?> Gallery</button></div></form></div>
<div class="card shadow-sm"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Title</th><th>Category</th><th>Before</th><th>After</th><th>Action</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= htmlspecialchars($r['title']); ?></td><td><?= htmlspecialchars($r['category_name'] ?? '-'); ?></td><td><?php if($r['before_image']): ?><img src="../uploads/gallery/<?= htmlspecialchars($r['before_image']); ?>" width="70"><?php endif; ?></td><td><?php if($r['after_image']): ?><img src="../uploads/gallery/<?= htmlspecialchars($r['after_image']); ?>" width="70"><?php endif; ?></td><td><a class="btn btn-sm btn-primary" href="?edit=<?= $r['id']; ?>">Edit</a> <a class="btn btn-sm btn-danger" data-confirm="Delete this gallery item?" href="?delete=<?= $r['id']; ?>">Delete</a></td></tr><?php endforeach; ?></tbody></table></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
