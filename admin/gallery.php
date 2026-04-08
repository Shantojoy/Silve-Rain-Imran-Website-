<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $q = $pdo->prepare('SELECT before_image,after_image FROM gallery WHERE id=?'); $q->execute([$id]); $row=$q->fetch();
    $pdo->prepare('DELETE FROM gallery WHERE id=?')->execute([$id]);
    if($row){deleteUploadedFile(__DIR__.'/../uploads/gallery/'.$row['before_image']); deleteUploadedFile(__DIR__.'/../uploads/gallery/'.$row['after_image']);}
    setFlash('success','Gallery item deleted.'); redirect('gallery.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id=(int)($_POST['id'] ?? 0);
        $title=trim($_POST['title'] ?? '');
        $categoryId=!empty($_POST['category_id'])?(int)$_POST['category_id']:null;
        $description=$_POST['description'] ?? '';
        $location=trim($_POST['location'] ?? '');
        if($title==='') throw new RuntimeException('Title is required.');
        $before=uploadImage('before_image', __DIR__.'/../uploads/gallery');
        $after=uploadImage('after_image', __DIR__.'/../uploads/gallery');

        if($id){
            $cur=$pdo->prepare('SELECT before_image,after_image FROM gallery WHERE id=?');$cur->execute([$id]);$old=$cur->fetch();
            $pdo->prepare('UPDATE gallery SET title=?,category_id=?,description=?,location=?,before_image=?,after_image=? WHERE id=?')->execute([$title,$categoryId,$description,$location,$before?:($old['before_image']??null),$after?:($old['after_image']??null),$id]);
            setFlash('success','Gallery updated.');
        } else {
            if(!$before || !$after) throw new RuntimeException('Both before and after images are required.');
            $pdo->prepare('INSERT INTO gallery (title,category_id,description,location,before_image,after_image) VALUES (?,?,?,?,?,?)')->execute([$title,$categoryId,$description,$location,$before,$after]);
            setFlash('success','Gallery added.');
        }
    } catch (Throwable $e) { setFlash('danger',$e->getMessage()); }
    redirect('gallery.php');
}

$cats=$pdo->query("SELECT id,name FROM categories WHERE type='gallery' ORDER BY name")->fetchAll();
$edit=null;
if(isset($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM gallery WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
$rows=$pdo->query('SELECT g.*, c.name category_name FROM gallery g LEFT JOIN categories c ON c.id=g.category_id ORDER BY g.created_at DESC')->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">All Gallery</h1>
<div class="card p-3 shadow-sm mb-3"><form method="post" enctype="multipart/form-data" class="row g-3"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0); ?>"><div class="col-md-4"><label class="form-label">Title *</label><input name="title" class="form-control" placeholder="Luxury Bedroom Makeover" value="<?= htmlspecialchars($edit['title'] ?? ''); ?>" required></div><div class="col-md-4"><label class="form-label">Category</label><select name="category_id" class="form-select"><option value="">Select</option><?php foreach($cats as $cat): ?><option value="<?= $cat['id']; ?>" <?= (int)($edit['category_id'] ?? 0)===(int)$cat['id']?'selected':''; ?>><?= htmlspecialchars($cat['name']); ?></option><?php endforeach; ?></select></div><div class="col-md-4"><label class="form-label">Location</label><input name="location" class="form-control" placeholder="Dhaka, Gulshan" value="<?= htmlspecialchars($edit['location'] ?? ''); ?>"></div><div class="col-md-6"><label class="form-label">Before Image</label><input type="file" name="before_image" class="form-control" accept="image/jpeg,image/png"></div><div class="col-md-6"><label class="form-label">After Image</label><input type="file" name="after_image" class="form-control" accept="image/jpeg,image/png"></div><div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control editor" rows="4"><?= htmlspecialchars($edit['description'] ?? ''); ?></textarea></div><div class="col-12"><button class="btn btn-dark"><i class="bi bi-save"></i> Save Gallery</button></div></form></div>
<div class="card shadow-sm"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Title</th><th>Location</th><th>Category</th><th>Action</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= htmlspecialchars($r['title']); ?></td><td><?= htmlspecialchars($r['location'] ?? '-'); ?></td><td><?= htmlspecialchars($r['category_name'] ?? '-'); ?></td><td><a class="btn btn-sm btn-primary" href="?edit=<?= $r['id']; ?>"><i class="bi bi-pencil"></i></a> <a class="btn btn-sm btn-danger" href="?delete=<?= $r['id']; ?>" data-confirm="Delete gallery?"><i class="bi bi-trash"></i></a></td></tr><?php endforeach; ?></tbody></table></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
