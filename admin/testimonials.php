<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

if (isset($_GET['delete'])) {
    $id=(int)$_GET['delete'];
    $q=$pdo->prepare('SELECT image FROM testimonials WHERE id=?');$q->execute([$id]);$row=$q->fetch();
    $pdo->prepare('DELETE FROM testimonials WHERE id=?')->execute([$id]);
    if(!empty($row['image'])) deleteUploadedFile(__DIR__.'/../uploads/products/'.$row['image']);
    setFlash('success','Testimonial deleted.'); redirect('testimonials.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id=(int)($_POST['id'] ?? 0);
        $name=sanitize($_POST['name'] ?? '');
        $review=sanitize($_POST['review'] ?? '');
        $rating=(int)($_POST['rating'] ?? 5);
        if(!$name || !$review || $rating < 1 || $rating > 5) throw new RuntimeException('Valid name, review and rating are required.');
        $newImage=uploadImage('image', __DIR__.'/../uploads/products');

        if($id){
            $cur=$pdo->prepare('SELECT image FROM testimonials WHERE id=?');$cur->execute([$id]);$ex=$cur->fetch();
            $img=$newImage ?: ($ex['image'] ?? null);
            $pdo->prepare('UPDATE testimonials SET name=?, review=?, rating=?, image=? WHERE id=?')->execute([$name,$review,$rating,$img,$id]);
            setFlash('success','Testimonial updated.');
        } else {
            $pdo->prepare('INSERT INTO testimonials (name,review,rating,image) VALUES (?,?,?,?)')->execute([$name,$review,$rating,$newImage]);
            setFlash('success','Testimonial added.');
        }
    } catch (Throwable $e) {
        setFlash('danger', $e->getMessage());
    }
    redirect('testimonials.php');
}

$edit=null;
if(isset($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM testimonials WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
$rows=$pdo->query('SELECT * FROM testimonials ORDER BY created_at DESC')->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Manage Testimonials</h1>
<div class="card p-3 shadow-sm mb-4"><form method="post" enctype="multipart/form-data" class="row g-2">
<input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0); ?>">
<div class="col-md-3"><input name="name" class="form-control" placeholder="Client name" value="<?= htmlspecialchars($edit['name'] ?? ''); ?>" required></div>
<div class="col-md-4"><input name="review" class="form-control" placeholder="Review" value="<?= htmlspecialchars($edit['review'] ?? ''); ?>" required></div>
<div class="col-md-2"><input type="number" min="1" max="5" name="rating" class="form-control" value="<?= htmlspecialchars($edit['rating'] ?? 5); ?>"></div>
<div class="col-md-3"><input type="file" name="image" class="form-control" accept="image/*"></div>
<div class="col-12"><button class="btn btn-dark"><?= $edit ? 'Update':'Add'; ?> Testimonial</button></div>
</form></div>
<table class="table table-bordered bg-white shadow-sm"><thead><tr><th>Name</th><th>Rating</th><th>Review</th><th>Actions</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><?= htmlspecialchars($r['name']); ?></td><td><?= (int)$r['rating']; ?>/5</td><td><?= htmlspecialchars($r['review']); ?></td><td><a class="btn btn-sm btn-primary" href="?edit=<?= $r['id']; ?>">Edit</a> <a data-confirm="Delete this testimonial?" class="btn btn-sm btn-danger" href="?delete=<?= $r['id']; ?>">Delete</a></td></tr><?php endforeach; ?>
</tbody></table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
