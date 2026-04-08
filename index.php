<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$services = $pdo->query('SELECT * FROM services ORDER BY created_at DESC LIMIT 3')->fetchAll();
$gallery = $pdo->query('SELECT * FROM gallery ORDER BY created_at DESC LIMIT 4')->fetchAll();
$testimonials = $pdo->query('SELECT * FROM testimonials ORDER BY created_at DESC LIMIT 3')->fetchAll();
?>

<div id="heroCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
    <div class="carousel-inner rounded-4 shadow">
        <?php $slides = ['assets/images/slide1.jpg', 'assets/images/slide2.jpg', 'assets/images/slide3.jpg']; ?>
        <?php foreach ($slides as $i => $slide): ?>
            <div class="carousel-item <?= $i === 0 ? 'active' : ''; ?>">
                <div class="hero-slide" style="background-image:url('<?= $slide; ?>');">
                    <div class="hero-overlay d-flex align-items-center">
                        <div class="p-5">
                            <h1 class="display-5 fw-bold">Transform Your Walls With Experts</h1>
                            <p>Premium painting and designer wallpaper solutions for homes and offices.</p>
                            <a href="quote.php" class="btn btn-warning btn-lg me-2">Get Quote</a>
                            <a href="shop.php" class="btn btn-outline-light btn-lg">Request Design</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<h2 class="section-title">Our Services</h2>
<div class="row g-4 mb-5">
    <?php foreach ($services as $service): ?>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <img src="uploads/products/<?= htmlspecialchars($service['image'] ?: 'placeholder.jpg'); ?>" class="card-img-top" alt="service">
                <div class="card-body">
                    <h5><?= htmlspecialchars($service['title']); ?></h5>
                    <p><?= htmlspecialchars(substr($service['description'], 0, 120)); ?>...</p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<h2 class="section-title">Recent Projects</h2>
<div class="row g-3 mb-5">
    <?php foreach ($gallery as $item): ?>
        <div class="col-md-3"><img class="w-100 rounded shadow-sm" src="uploads/gallery/<?= htmlspecialchars($item['after_image']); ?>" alt="project"></div>
    <?php endforeach; ?>
</div>

<h2 class="section-title">Client Testimonials</h2>
<div class="row g-4">
    <?php foreach ($testimonials as $t): ?>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm"><div class="card-body">
                <h6><?= htmlspecialchars($t['name']); ?></h6>
                <div class="text-warning mb-2"><?= str_repeat('★', (int)$t['rating']); ?></div>
                <p><?= htmlspecialchars($t['review']); ?></p>
            </div></div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
