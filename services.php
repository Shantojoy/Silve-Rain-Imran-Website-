<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
$services = $pdo->query('SELECT * FROM services ORDER BY created_at DESC')->fetchAll();
?>
<h1 class="section-title">Painting & Wallpaper Services</h1>
<div class="row g-4">
<?php foreach ($services as $service): ?>
    <div class="col-md-6">
        <div class="card h-100 shadow-sm">
            <img src="uploads/products/<?= htmlspecialchars($service['image'] ?: 'placeholder.jpg'); ?>" class="card-img-top" alt="service">
            <div class="card-body">
                <h4><?= htmlspecialchars($service['title']); ?></h4>
                <p><?= nl2br(htmlspecialchars($service['description'])); ?></p>
                <a href="quote.php?service=<?= urlencode($service['title']); ?>" class="btn btn-warning">Get Quote for this Service</a>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
