<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';

$category = !empty($_GET['category']) ? (int)$_GET['category'] : 0;
$search = trim($_GET['search'] ?? '');

$sql = 'SELECT p.*, c.name category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE 1';
$params = [];
if ($category) { $sql .= ' AND p.category_id = ?'; $params[] = $category; }
if ($search !== '') { $sql .= ' AND p.name LIKE ?'; $params[] = "%$search%"; }
$sql .= ' ORDER BY p.created_at DESC';
$stmt = $pdo->prepare($sql); $stmt->execute($params); $products = $stmt->fetchAll();
$categories = $pdo->query("SELECT id,name FROM categories WHERE type='product' ORDER BY name")->fetchAll();
?>
<h1 class="section-title">Wallpaper Designs</h1>
<form class="row g-2 mb-4">
    <div class="col-md-4"><input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search); ?>" placeholder="Search products"></div>
    <div class="col-md-4"><select name="category" class="form-select"><option value="">All categories</option><?php foreach($categories as $cat): ?><option value="<?= $cat['id']; ?>" <?= $category===(int)$cat['id']?'selected':''; ?>><?= htmlspecialchars($cat['name']); ?></option><?php endforeach; ?></select></div>
    <div class="col-md-2"><button class="btn btn-dark w-100">Filter</button></div>
</form>
<div class="row g-4"><?php foreach($products as $p): ?><div class="col-md-4"><div class="card h-100 shadow-sm"><img src="uploads/products/<?= htmlspecialchars($p['main_image'] ?: ''); ?>" class="card-img-top" alt="product"><div class="card-body"><h5><?= htmlspecialchars($p['name']); ?></h5><p class="text-muted mb-1"><?= htmlspecialchars($p['category_name'] ?? ''); ?></p><p class="fw-bold">$<?= number_format((float)$p['price'],2); ?></p><a href="product-details.php?slug=<?= urlencode($p['slug']); ?>" class="btn btn-warning">View Details</a></div></div></div><?php endforeach; ?></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
