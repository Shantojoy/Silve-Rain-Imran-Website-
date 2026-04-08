<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
$category = !empty($_GET['category']) ? (int)$_GET['category'] : 0;
if ($category) {
    $stmt = $pdo->prepare('SELECT g.*, c.name category_name FROM gallery g LEFT JOIN categories c ON c.id=g.category_id WHERE g.category_id = ? ORDER BY g.created_at DESC');
    $stmt->execute([$category]);
} else {
    $stmt = $pdo->query('SELECT g.*, c.name category_name FROM gallery g LEFT JOIN categories c ON c.id=g.category_id ORDER BY g.created_at DESC');
}
$items = $stmt->fetchAll();
$categories = $pdo->query("SELECT id,name FROM categories WHERE type='gallery' ORDER BY name")->fetchAll();
?>
<h1 class="section-title">Project Gallery</h1>
<form class="row mb-4"><div class="col-md-4"><select name="category" class="form-select" onchange="this.form.submit()"><option value="">All Categories</option><?php foreach($categories as $cat): ?><option value="<?= $cat['id']; ?>" <?= $category===(int)$cat['id']?'selected':''; ?>><?= htmlspecialchars($cat['name']); ?></option><?php endforeach; ?></select></div></form>
<div class="row g-4"><?php foreach($items as $item): ?><div class="col-lg-6"><div class="card p-3 shadow-sm before-after"><h5><?= htmlspecialchars($item['title']); ?> <span class="badge bg-secondary"><?= htmlspecialchars($item['category_name'] ?? ''); ?></span></h5><p class="small text-muted mb-1"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($item['location'] ?? ''); ?></p><div class="mb-2"><?= $item['description'] ?? ''; ?></div><div class="row g-2"><div class="col-6"><small>Before</small><img src="uploads/gallery/<?= htmlspecialchars($item['before_image']); ?>" alt="before"></div><div class="col-6"><small>After</small><img src="uploads/gallery/<?= htmlspecialchars($item['after_image']); ?>" alt="after"></div></div><a class="btn btn-outline-dark btn-sm mt-3" href="gallery-details.php?id=<?= (int)$item['id']; ?>"><i class="bi bi-eye"></i> View Details</a></div></div><?php endforeach; ?></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
