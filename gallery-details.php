<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    echo '<div class="alert alert-danger">Invalid gallery item.</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$stmt = $pdo->prepare('SELECT g.*, c.name category_name FROM gallery g LEFT JOIN categories c ON c.id=g.category_id WHERE g.id=? LIMIT 1');
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    echo '<div class="alert alert-danger">Gallery item not found.</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$shareUrl = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item"><a href="gallery.php">Gallery</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($item['title']); ?></li>
    </ol>
</nav>

<div class="row g-4 align-items-start">
    <div class="col-lg-7">
        <div class="card page-card p-3">
        <div id="galleryDetailsSlider" class="carousel slide">
            <div class="carousel-inner rounded">
                <?php if (!empty($item['before_image'])): ?>
                    <div class="carousel-item active">
                        <img src="uploads/gallery/<?= htmlspecialchars($item['before_image']); ?>" class="d-block w-100" alt="Before image">
                    </div>
                <?php endif; ?>
                <?php if (!empty($item['after_image'])): ?>
                    <div class="carousel-item <?= empty($item['before_image']) ? 'active' : ''; ?>">
                        <img src="uploads/gallery/<?= htmlspecialchars($item['after_image']); ?>" class="d-block w-100" alt="After image">
                    </div>
                <?php endif; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#galleryDetailsSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#galleryDetailsSlider" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card page-card p-4">
            <h1 class="h3 mb-2"><?= htmlspecialchars($item['title']); ?></h1>
            <p class="mb-1"><span class="badge bg-secondary"><?= htmlspecialchars($item['category_name'] ?? 'Uncategorized'); ?></span></p>
            <p class="text-muted mb-3"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($item['location'] ?: 'Location not specified'); ?></p>
            <div class="mb-3"><?= $item['description'] ?: '<span class="text-muted">No project details provided.</span>'; ?></div>

            <h6>Share this project</h6>
            <div class="d-flex gap-2 flex-wrap">
                <a class="btn btn-outline-primary btn-sm" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($shareUrl); ?>"><i class="bi bi-facebook"></i> Facebook</a>
                <a class="btn btn-outline-info btn-sm" target="_blank" href="https://twitter.com/intent/tweet?url=<?= urlencode($shareUrl); ?>&text=<?= urlencode($item['title']); ?>"><i class="bi bi-twitter-x"></i> X</a>
                <a class="btn btn-outline-success btn-sm" target="_blank" href="https://wa.me/?text=<?= urlencode($item['title'] . ' - ' . $shareUrl); ?>"><i class="bi bi-whatsapp"></i> WhatsApp</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
