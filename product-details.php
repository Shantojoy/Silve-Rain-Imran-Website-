<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT p.*, c.name category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.id=?');
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) { echo '<div class="alert alert-danger">Product not found.</div>'; require_once __DIR__ . '/includes/footer.php'; exit; }
$imagesStmt = $pdo->prepare('SELECT image FROM product_images WHERE product_id=?');
$imagesStmt->execute([$id]);
$galleryImages = $imagesStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<div class="row g-4">
    <div class="col-lg-6">
        <div id="productSlider" class="carousel slide"><div class="carousel-inner rounded shadow-sm">
            <div class="carousel-item active"><img src="uploads/products/<?= htmlspecialchars($product['main_image']); ?>" class="d-block w-100" alt="main"></div>
            <?php foreach ($galleryImages as $img): ?><div class="carousel-item"><img src="uploads/products/<?= htmlspecialchars($img); ?>" class="d-block w-100" alt="gallery"></div><?php endforeach; ?>
        </div><button class="carousel-control-prev" type="button" data-bs-target="#productSlider" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button><button class="carousel-control-next" type="button" data-bs-target="#productSlider" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button></div>
    </div>
    <div class="col-lg-6">
        <h2><?= htmlspecialchars($product['name']); ?></h2>
        <p class="text-muted">Category: <?= htmlspecialchars($product['category_name'] ?? ''); ?></p>
        <h4 class="mb-3">$<?= number_format((float)$product['price'],2); ?></h4>
        <p><?= nl2br(htmlspecialchars($product['description'])); ?></p>
        <a href="quote.php?product_id=<?= $product['id']; ?>" class="btn btn-warning me-2">Request This Wallpaper</a>
        <a href="checkout.php?product_id=<?= $product['id']; ?>" class="btn btn-dark">Order Now (COD)</a>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
