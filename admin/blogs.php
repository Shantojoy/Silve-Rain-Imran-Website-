<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

if (isset($_GET['delete'])) {
    $id=(int)$_GET['delete'];
    $q=$pdo->prepare('SELECT featured_image FROM blogs WHERE id=?');$q->execute([$id]);$row=$q->fetch();
    $pdo->prepare('DELETE FROM blogs WHERE id=?')->execute([$id]);
    if(!empty($row['featured_image'])) deleteUploadedFile(__DIR__.'/../uploads/products/'.$row['featured_image']);
    setFlash('success','Blog deleted.');redirect('blogs.php');
}

$search=trim($_GET['search'] ?? '');
$where=$search!==''?'WHERE b.title LIKE ?':'';
$params=$search!==''?["%$search%"]:[];
$c=$pdo->prepare("SELECT COUNT(*) FROM blogs b $where");$c->execute($params);
$pg=paginate((int)$c->fetchColumn(),10);
$stmt=$pdo->prepare("SELECT b.*, bc.name category_name FROM blogs b LEFT JOIN blog_categories bc ON bc.id=b.category_id $where ORDER BY b.created_at DESC LIMIT {$pg['per_page']} OFFSET {$pg['offset']}");
$stmt->execute($params);$rows=$stmt->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">All Blogs</h1>
<div class="d-flex justify-content-between mb-3"><form class="d-flex gap-2"><input class="form-control" name="search" value="<?= htmlspecialchars($search); ?>" placeholder="Search blogs"><button class="btn btn-outline-dark"><i class="bi bi-search"></i></button></form><a class="btn btn-dark" href="blog-edit.php"><i class="bi bi-plus-circle"></i> Add New Blog</a></div>
<div class="card shadow-sm"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Title</th><th>Category</th><th>Date</th><th>Action</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= htmlspecialchars($r['title']); ?></td><td><?= htmlspecialchars($r['category_name'] ?? '-'); ?></td><td><?= htmlspecialchars($r['created_at']); ?></td><td><a class="btn btn-sm btn-primary" href="blog-edit.php?id=<?= $r['id']; ?>"><i class="bi bi-pencil"></i></a> <a class="btn btn-sm btn-danger" data-confirm="Delete blog?" href="?delete=<?= $r['id']; ?>"><i class="bi bi-trash"></i></a></td></tr><?php endforeach; ?></tbody></table></div><div class="card-body"><?php for($i=1;$i<=$pg['total_pages'];$i++): ?><a class="btn btn-sm <?= $i===$pg['page']?'btn-dark':'btn-outline-dark'; ?>" href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>"><?= $i; ?></a> <?php endfor; ?></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
