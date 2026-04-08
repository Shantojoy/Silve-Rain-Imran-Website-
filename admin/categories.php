<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = sanitize($_POST['name'] ?? '');
        $type = sanitize($_POST['type'] ?? '');
        if (!$name || !in_array($type, ['product','gallery','service'], true)) {
            throw new RuntimeException('Valid name and type are required.');
        }
        $pdo->prepare('INSERT INTO categories (name,type) VALUES (?,?)')->execute([$name,$type]);
        setFlash('success', 'Category added.');
    } catch (Throwable $e) {
        setFlash('danger', $e->getMessage());
    }
    redirect('categories.php');
}

if (isset($_GET['delete'])) {
    $pdo->prepare('DELETE FROM categories WHERE id = ?')->execute([(int)$_GET['delete']]);
    setFlash('success', 'Category deleted.');
    redirect('categories.php');
}

$rows = $pdo->query('SELECT * FROM categories ORDER BY type,name')->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Categories</h1>
<div class="card p-3 shadow-sm mb-3"><form method="post" class="row g-2"><div class="col-md-5"><input name="name" class="form-control" placeholder="Category name" required></div><div class="col-md-4"><select name="type" class="form-select" required><option value="product">Product</option><option value="gallery">Gallery</option><option value="service">Service</option></select></div><div class="col-md-3"><button class="btn btn-dark w-100">Add Category</button></div></form></div>
<div class="card shadow-sm"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>ID</th><th>Name</th><th>Type</th><th></th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= $r['id']; ?></td><td><?= htmlspecialchars($r['name']); ?></td><td><?= htmlspecialchars($r['type']); ?></td><td><a data-confirm="Delete this category?" href="?delete=<?= $r['id']; ?>" class="btn btn-sm btn-danger">Delete</a></td></tr><?php endforeach; ?></tbody></table></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
