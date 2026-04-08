<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
$services = $pdo->query('SELECT s.*, c.name category_name FROM services s LEFT JOIN categories c ON c.id=s.category_id ORDER BY s.created_at DESC')->fetchAll();
?>
<h1 class="section-title">Our Services</h1>
<div class="row g-4"><?php foreach($services as $service): ?><div class="col-md-6"><div class="card h-100 shadow-sm"><img src="uploads/products/<?= htmlspecialchars($service['image'] ?: ''); ?>" class="card-img-top" alt="service"><div class="card-body"><h4><?= htmlspecialchars($service['title']); ?></h4><p class="text-muted"><?= htmlspecialchars($service['category_name'] ?? ''); ?></p><p><?= nl2br(htmlspecialchars($service['description'])); ?></p><a href="quote.php?service=<?= urlencode($service['title']); ?>" class="btn btn-warning">Get Quote</a></div></div></div><?php endforeach; ?></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
