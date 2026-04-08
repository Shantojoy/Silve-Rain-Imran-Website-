<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
$category = $_GET['category'] ?? '';
$search = trim($_GET['search'] ?? '');

$sql = 'SELECT * FROM products WHERE 1';
$params = [];
if ($category) {
    $sql .= ' AND category = ?';
    $params[] = $category;
}
if ($search !== '') {
    $sql .= ' AND name LIKE ?';
    $params[] = "%$search%";
}
$sql .= ' ORDER BY created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
$categories = $pdo->query('SELECT DISTINCT category FROM products ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);
?>
<h1 class="section-title">Wallpaper Design Shop</h1>
<form class="row g-2 mb-4">
    <div class="col-md-4"><input type="text" name="search" class="form-control" placeholder="Search design" value="<?= htmlspecialchars($search); ?>"></div>
    <div class="col-md-4"><select name="category" class="form-select"><option value="">All Categories</option><?php foreach ($categories as $cat): ?><option value="<?= htmlspecialchars($cat); ?>" <?= $cat === $category ? 'selected' : ''; ?>><?= htmlspecialchars($cat); ?></option><?php endforeach; ?></select></div>
    <div class="col-md-2"><button class="btn btn-dark w-100">Filter</button></div>
</form>
<div class="row g-4">
<?php foreach ($products as $p): ?>
    <div class="col-md-4">
        <div class="card h-100 shadow-sm">
            <img src="uploads/products/<?= htmlspecialchars($p['image']); ?>" class="card-img-top" alt="product">
            <div class="card-body">
                <h5><?= htmlspecialchars($p['name']); ?></h5>
                <p class="text-muted">Category: <?= htmlspecialchars($p['category']); ?></p>
                <p class="fw-bold">$<?= number_format((float)$p['price'], 2); ?></p>
                <a href="product-details.php?id=<?= (int)$p['id']; ?>" class="btn btn-warning">View Design</a>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
