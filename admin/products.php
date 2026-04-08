<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $old = $pdo->prepare('SELECT image FROM products WHERE id=?');
    $old->execute([$id]);
    $row = $old->fetch();
    $pdo->prepare('DELETE FROM products WHERE id=?')->execute([$id]);
    if (!empty($row['image'])) deleteUploadedFile(__DIR__ . '/../uploads/products/' . $row['image']);
    setFlash('success', 'Product deleted.');
    redirect('products.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = (int)($_POST['id'] ?? 0);
        $name = sanitize($_POST['name'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $description = sanitize($_POST['description'] ?? '');
        $category = sanitize($_POST['category'] ?? '');
        if (!$name || !$description || !$category) throw new RuntimeException('Name, description and category are required.');
        $newImage = uploadImage('image', __DIR__ . '/../uploads/products');

        if ($id) {
            $cur = $pdo->prepare('SELECT image FROM products WHERE id=?'); $cur->execute([$id]); $existing = $cur->fetch();
            $image = $newImage ?: ($existing['image'] ?? null);
            $stmt = $pdo->prepare('UPDATE products SET name=?,price=?,image=?,description=?,category=? WHERE id=?');
            $stmt->execute([$name,$price,$image,$description,$category,$id]);
            if ($newImage && !empty($existing['image'])) deleteUploadedFile(__DIR__ . '/../uploads/products/' . $existing['image']);
            setFlash('success', 'Product updated.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO products (name,price,image,description,category,is_virtual) VALUES (?,?,?,?,?,1)');
            $stmt->execute([$name,$price,$newImage,$description,$category]);
            setFlash('success', 'Product added.');
        }
    } catch (Throwable $e) {
        setFlash('danger', $e->getMessage());
    }
    redirect('products.php');
}

$edit = null;
if (isset($_GET['edit'])) { $stmt = $pdo->prepare('SELECT * FROM products WHERE id=?'); $stmt->execute([(int)$_GET['edit']]); $edit = $stmt->fetch(); }
$rows = $pdo->query('SELECT * FROM products ORDER BY created_at DESC')->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Manage Wallpaper Products</h1>
<div class="card p-3 shadow-sm mb-4">
<form method="post" enctype="multipart/form-data" class="row g-2">
<input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0); ?>">
<div class="col-md-3"><input name="name" class="form-control" placeholder="Name" value="<?= htmlspecialchars($edit['name'] ?? ''); ?>" required></div>
<div class="col-md-2"><input type="number" step="0.01" name="price" class="form-control" placeholder="Price" value="<?= htmlspecialchars($edit['price'] ?? ''); ?>" required></div>
<div class="col-md-2"><input name="category" class="form-control" placeholder="Category" value="<?= htmlspecialchars($edit['category'] ?? ''); ?>" required></div>
<div class="col-md-3"><input name="description" class="form-control" placeholder="Description" value="<?= htmlspecialchars($edit['description'] ?? ''); ?>" required></div>
<div class="col-md-2"><input type="file" name="image" class="form-control" accept="image/*"></div>
<div class="col-12"><button class="btn btn-dark"><?= $edit ? 'Update' : 'Add'; ?> Product</button></div>
</form></div>
<table class="table table-bordered bg-white shadow-sm"><thead><tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Image</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= $r['id']; ?></td><td><?= htmlspecialchars($r['name']); ?></td><td><?= htmlspecialchars($r['category']); ?></td><td>$<?= number_format((float)$r['price'],2); ?></td>
<td><?php if ($r['image']): ?><img src="../uploads/products/<?= htmlspecialchars($r['image']); ?>" width="70"><?php endif; ?></td>
<td><a class="btn btn-sm btn-primary" href="?edit=<?= $r['id']; ?>">Edit</a> <a data-confirm="Delete this product?" class="btn btn-sm btn-danger" href="?delete=<?= $r['id']; ?>">Delete</a></td></tr><?php endforeach; ?>
</tbody></table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
