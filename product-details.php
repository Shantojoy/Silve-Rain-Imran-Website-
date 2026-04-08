<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) {
    echo '<div class="alert alert-danger">Product not found.</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
?>
<div class="row g-4 align-items-center">
    <div class="col-md-6"><img src="uploads/products/<?= htmlspecialchars($product['image']); ?>" class="img-fluid rounded shadow" alt="product"></div>
    <div class="col-md-6">
        <h1><?= htmlspecialchars($product['name']); ?></h1>
        <p class="lead">$<?= number_format((float)$product['price'], 2); ?></p>
        <p><?= nl2br(htmlspecialchars($product['description'])); ?></p>
        <a href="quote.php?product_id=<?= (int)$product['id']; ?>" class="btn btn-warning me-2">Request This Wallpaper</a>
        <a href="quote.php" class="btn btn-outline-dark">Get Quote</a>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
