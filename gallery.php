<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
$category = $_GET['category'] ?? '';

if ($category) {
    $stmt = $pdo->prepare('SELECT * FROM gallery WHERE category = ? ORDER BY created_at DESC');
    $stmt->execute([$category]);
} else {
    $stmt = $pdo->query('SELECT * FROM gallery ORDER BY created_at DESC');
}
$items = $stmt->fetchAll();
$categories = $pdo->query('SELECT DISTINCT category FROM gallery ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);
?>
<h1 class="section-title">Project Gallery</h1>
<form class="row mb-4">
    <div class="col-md-4">
        <select name="category" class="form-select" onchange="this.form.submit()">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat); ?>" <?= $cat === $category ? 'selected' : ''; ?>><?= htmlspecialchars($cat); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</form>
<div class="row g-4">
<?php foreach ($items as $item): ?>
    <div class="col-lg-6">
        <div class="card p-3 shadow-sm before-after">
            <h5><?= htmlspecialchars($item['title']); ?> <span class="badge bg-secondary"><?= htmlspecialchars($item['category']); ?></span></h5>
            <div class="row g-2">
                <div class="col-6"><small>Before</small><img src="uploads/gallery/<?= htmlspecialchars($item['before_image']); ?>" alt="before"></div>
                <div class="col-6"><small>After</small><img src="uploads/gallery/<?= htmlspecialchars($item['after_image']); ?>" alt="after"></div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
