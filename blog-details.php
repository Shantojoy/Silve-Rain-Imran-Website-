<?php
require_once __DIR__ . '/includes/db.php';
$slug=trim($_GET['slug'] ?? '');
$stmt=$pdo->prepare('SELECT b.*, bc.name category_name FROM blogs b LEFT JOIN blog_categories bc ON bc.id=b.category_id WHERE b.slug=? LIMIT 1');
$stmt->execute([$slug]);
$blog=$stmt->fetch();
if(!$blog){die('Blog not found');}
require_once __DIR__ . '/includes/header.php';
$shareUrl = urlencode((isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
?>
<div class="card page-card p-4">
    <h1><?= htmlspecialchars($blog['title']); ?></h1>
    <p class="text-muted small"><?= htmlspecialchars($blog['category_name'] ?? ''); ?> • <?= date('M d, Y', strtotime($blog['created_at'])); ?></p>
    <?php if($blog['featured_image']): ?><img src="uploads/products/<?= htmlspecialchars($blog['featured_image']); ?>" class="img-fluid rounded mb-3" alt="blog"><?php endif; ?>
    <div><?= $blog['content']; ?></div>
    <hr><h6>Share this post</h6>
    <a class="btn btn-sm btn-primary" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl; ?>">Facebook</a>
    <a class="btn btn-sm btn-success" target="_blank" href="https://wa.me/?text=<?= $shareUrl; ?>">WhatsApp</a>
    <a class="btn btn-sm btn-info" target="_blank" href="https://twitter.com/intent/tweet?url=<?= $shareUrl; ?>">Twitter</a>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
