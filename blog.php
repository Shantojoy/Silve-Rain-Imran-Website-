<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
$cat=(int)($_GET['category'] ?? 0);
if($cat){$stmt=$pdo->prepare('SELECT b.*, bc.name category_name FROM blogs b LEFT JOIN blog_categories bc ON bc.id=b.category_id WHERE b.category_id=? ORDER BY b.created_at DESC');$stmt->execute([$cat]);}
else{$stmt=$pdo->query('SELECT b.*, bc.name category_name FROM blogs b LEFT JOIN blog_categories bc ON bc.id=b.category_id ORDER BY b.created_at DESC');}
$blogs=$stmt->fetchAll();
$cats=$pdo->query('SELECT * FROM blog_categories ORDER BY name')->fetchAll();
?>
<h1 class="section-title">Our Blog</h1>
<form class="mb-4"><div class="row"><div class="col-md-4"><select class="form-select" name="category" onchange="this.form.submit()"><option value="">All categories</option><?php foreach($cats as $c): ?><option value="<?= $c['id']; ?>" <?= $cat===(int)$c['id']?'selected':''; ?>><?= htmlspecialchars($c['name']); ?></option><?php endforeach; ?></select></div></div></form>
<div class="row g-4"><?php foreach($blogs as $b): ?><div class="col-md-4"><div class="card h-100 shadow-sm"><?php if($b['featured_image']): ?><img src="uploads/products/<?= htmlspecialchars($b['featured_image']); ?>" class="card-img-top" alt="blog"><?php endif; ?><div class="card-body"><h5><?= htmlspecialchars($b['title']); ?></h5><p class="text-muted small"><?= htmlspecialchars($b['category_name'] ?? ''); ?> • <?= date('M d, Y', strtotime($b['created_at'])); ?></p><a class="btn btn-outline-dark btn-sm" href="blog-details.php?slug=<?= urlencode($b['slug']); ?>">Read More</a></div></div></div><?php endforeach; ?></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
