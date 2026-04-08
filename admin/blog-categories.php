<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = trim($_POST['name'] ?? '');
        if ($name === '') throw new RuntimeException('Category name is required.');
        $slugBase = slugify($name); $slug = $slugBase; $i = 1;
        while (true) { $q = $pdo->prepare('SELECT id FROM blog_categories WHERE slug=? LIMIT 1'); $q->execute([$slug]); if (!$q->fetch()) break; $slug = $slugBase.'-'.$i++; }
        $pdo->prepare('INSERT INTO blog_categories (name,slug) VALUES (?,?)')->execute([$name,$slug]);
        setFlash('success', 'Blog category added.');
    } catch (Throwable $e) { setFlash('danger', $e->getMessage()); }
    redirect('blog-categories.php');
}

if (isset($_GET['delete'])) {
    $pdo->prepare('DELETE FROM blog_categories WHERE id=?')->execute([(int)$_GET['delete']]);
    setFlash('success','Category deleted.');
    redirect('blog-categories.php');
}

$rows = $pdo->query('SELECT * FROM blog_categories ORDER BY created_at DESC')->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Blog Categories</h1>
<div class="card p-3 shadow-sm mb-3"><form method="post" class="row g-2"><div class="col-md-8"><label class="form-label">Category Name <span class="text-danger">*</span></label><input name="name" class="form-control" placeholder="Interior Design Tips" required><?= helpText('Use short and clear category names for better filtering.'); ?></div><div class="col-md-4 d-flex align-items-end"><button class="btn btn-dark w-100"><i class="bi bi-plus-circle"></i> Add Category</button></div></form></div>
<div class="card shadow-sm"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Name</th><th>Slug</th><th>Action</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= htmlspecialchars($r['name']); ?></td><td><?= htmlspecialchars($r['slug']); ?></td><td><a class="btn btn-sm btn-danger" data-confirm="Delete category?" href="?delete=<?= $r['id']; ?>"><i class="bi bi-trash"></i></a></td></tr><?php endforeach; ?></tbody></table></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
