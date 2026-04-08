<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$id = (int)($_GET['id'] ?? 0);
$slug = trim($_GET['slug'] ?? '');
if ($slug !== '') {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE slug=? LIMIT 1');
    $stmt->execute([$slug]);
} else {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id=? LIMIT 1');
    $stmt->execute([$id]);
}
$product = $stmt->fetch();
if (!$product) {
    echo '<div class="alert alert-danger">Product not found.</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$images = [];
if (!empty($product['main_image'])) {
    $images[] = $product['main_image'];
}
$imgStmt = $pdo->prepare('SELECT image FROM product_images WHERE product_id=? ORDER BY id DESC');
$imgStmt->execute([(int)$product['id']]);
$images = array_merge($images, $imgStmt->fetchAll(PDO::FETCH_COLUMN));
?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
        <li class="breadcrumb-item"><a href="product-details.php?slug=<?= urlencode($product['slug']); ?>"><?= htmlspecialchars($product['name']); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page">Product Gallery</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Product Gallery: <?= htmlspecialchars($product['name']); ?></h1>
    <a class="btn btn-sm btn-outline-dark" href="product-details.php?slug=<?= urlencode($product['slug']); ?>">Back to Product</a>
</div>

<div class="row g-3">
    <?php foreach ($images as $img): ?>
        <div class="col-md-4 col-lg-3">
            <a href="uploads/products/<?= htmlspecialchars($img); ?>" target="_blank" class="d-block">
                <img class="w-100 rounded shadow-sm" src="uploads/products/<?= htmlspecialchars($img); ?>" alt="Product image">
            </a>
        </div>
    <?php endforeach; ?>
</div>

<?php if (!$images): ?>
    <div class="alert alert-info mt-3">No product images available yet.</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
