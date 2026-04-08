<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $img = $pdo->prepare('SELECT main_image FROM products WHERE id=?'); $img->execute([$id]); $prod = $img->fetch();
    $pdo->prepare('DELETE FROM products WHERE id=?')->execute([$id]);
    if (!empty($prod['main_image'])) deleteUploadedFile(__DIR__.'/../uploads/products/'.$prod['main_image']);
    setFlash('success', 'Product deleted.'); redirect('products.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $slugBase = slugify($name);
        $description = trim($_POST['description'] ?? '');
        $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        if (!$name || !$description || $price < 0) throw new RuntimeException('Valid name, price and description are required.');

        $mainImage = uploadImage('main_image', __DIR__.'/../uploads/products');
        $galleryImages = uploadMultipleImages('gallery_images', __DIR__.'/../uploads/products');


        // Generate unique SEO slug
        $slug = $slugBase;
        $counter = 1;
        while (true) {
            $q = $pdo->prepare('SELECT id FROM products WHERE slug = ?' . ($id ? ' AND id != ?' : '') . ' LIMIT 1');
            $params = $id ? [$slug, $id] : [$slug];
            $q->execute($params);
            if (!$q->fetch()) break;
            $slug = $slugBase . '-' . $counter++;
        }

        if ($id) {
            $cur = $pdo->prepare('SELECT main_image FROM products WHERE id=?'); $cur->execute([$id]); $old = $cur->fetch();
            $pdo->prepare('UPDATE products SET name=?,slug=?,price=?,description=?,category_id=?,main_image=? WHERE id=?')->execute([$name,$slug,$price,$description,$categoryId,$mainImage ?: $old['main_image'],$id]);
            if ($mainImage && !empty($old['main_image'])) {
                deleteUploadedFile(__DIR__.'/../uploads/products/'.$old['main_image']);
            }
            foreach ($galleryImages as $g) $pdo->prepare('INSERT INTO product_images (product_id,image) VALUES (?,?)')->execute([$id,$g]);
            setFlash('success', 'Product updated.');
        } else {
            if (!$mainImage) throw new RuntimeException('Main image is required when adding a new product.');
            $pdo->prepare('INSERT INTO products (name,slug,price,description,category_id,main_image,is_virtual) VALUES (?,?,?,?,?,?,1)')->execute([$name,$slug,$price,$description,$categoryId,$mainImage]);
            $productId = (int)$pdo->lastInsertId();
            foreach ($galleryImages as $g) $pdo->prepare('INSERT INTO product_images (product_id,image) VALUES (?,?)')->execute([$productId,$g]);
            setFlash('success', 'Product added.');
        }
    } catch (Throwable $e) { setFlash('danger', $e->getMessage()); }
    redirect('products.php');
}

if (isset($_GET['delete_image'])) {
    $id = (int)$_GET['delete_image'];
    $q = $pdo->prepare('SELECT image FROM product_images WHERE id=?'); $q->execute([$id]); $row=$q->fetch();
    if ($row) { $pdo->prepare('DELETE FROM product_images WHERE id=?')->execute([$id]); deleteUploadedFile(__DIR__.'/../uploads/products/'.$row['image']); }
    setFlash('success', 'Gallery image removed.'); redirect('products.php?edit='.(int)($_GET['edit'] ?? 0));
}

$search = trim($_GET['search'] ?? '');
$where = $search !== '' ? 'WHERE p.name LIKE ?' : '';
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM products p $where");
$countStmt->execute($search !== '' ? ["%$search%"] : []);
$pagination = paginate((int)$countStmt->fetchColumn(), 8);
$listStmt = $pdo->prepare("SELECT p.*, c.name category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id $where ORDER BY p.created_at DESC LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}");
$listStmt->execute($search !== '' ? ["%$search%"] : []);
$rows = $listStmt->fetchAll();

$categories = $pdo->query("SELECT id,name FROM categories WHERE type='product' ORDER BY name")->fetchAll();
$edit = null; $editImages=[];
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id=?'); $stmt->execute([(int)$_GET['edit']]); $edit = $stmt->fetch();
    if ($edit) { $im = $pdo->prepare('SELECT * FROM product_images WHERE product_id=? ORDER BY id DESC'); $im->execute([$edit['id']]); $editImages = $im->fetchAll(); }
}

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Products</h1>
<div class="card p-3 shadow-sm mb-3">
    <h6 class="mb-3">Product Form</h6>
    <form method="post" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0); ?>">
        <div class="col-md-4">
            <label class="form-label">Product Name <span class="text-danger">*</span></label>
            <input name="name" class="form-control" placeholder="Modern Grey Wallpaper" value="<?= htmlspecialchars($edit['name'] ?? ''); ?>" required>
            <?= helpText('Enter a clear product name (e.g., Modern Grey Wallpaper).'); ?>
        </div>
        <div class="col-md-2">
            <label class="form-label">Price (USD) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="price" class="form-control" placeholder="99.99" value="<?= htmlspecialchars($edit['price'] ?? ''); ?>" required>
            <?= helpText('Price is per design package in USD.'); ?>
        </div>
        <div class="col-md-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select"><option value="">Category</option><?php foreach($categories as $cat): ?><option value="<?= $cat['id']; ?>" <?= (int)($edit['category_id'] ?? 0)===(int)$cat['id']?'selected':''; ?>><?= htmlspecialchars($cat['name']); ?></option><?php endforeach; ?></select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Main Image</label>
            <input type="file" name="main_image" class="form-control" accept="image/jpeg,image/png">
            <?= helpText('Upload JPG/PNG, max 5MB. This appears on listing pages.'); ?>
        </div>
        <div class="col-md-6">
            <label class="form-label">Gallery Images</label>
            <input type="file" name="gallery_images[]" class="form-control" multiple accept="image/jpeg,image/png">
            <?= helpText('You can upload multiple additional preview images.'); ?>
        </div>
        <div class="col-12">
            <label class="form-label">Description <span class="text-danger">*</span></label>
            <textarea name="description" class="form-control editor" rows="3" placeholder="Write full product details" required><?= htmlspecialchars($edit['description'] ?? ''); ?></textarea>
        </div>
        <div class="col-12"><button class="btn btn-dark"><i class="bi bi-save"></i> <?= $edit ? 'Update':'Add'; ?> Product</button></div>
    </form>
</div>
<?php if ($editImages): ?><div class="card p-3 shadow-sm mb-3"><strong>Current Gallery Images</strong><div class="d-flex flex-wrap gap-2 mt-2"><?php foreach($editImages as $img): ?><div><img src="../uploads/products/<?= htmlspecialchars($img['image']); ?>" width="80"><br><a class="text-danger small" href="?edit=<?= (int)$edit['id']; ?>&delete_image=<?= $img['id']; ?>">remove</a></div><?php endforeach; ?></div></div><?php endif; ?>
<div class="card shadow-sm"><div class="card-body border-bottom"><form class="row g-2"><div class="col-md-4"><input class="form-control" name="search" placeholder="Search product" value="<?= htmlspecialchars($search); ?>"></div><div class="col-md-2"><button class="btn btn-outline-dark"><i class="bi bi-search"></i> Search</button></div></form></div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Name</th><th>Slug</th><th>Category</th><th>Price</th><th>Main Image</th><th></th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= htmlspecialchars($r['name']); ?></td><td><small><?= htmlspecialchars($r['slug']); ?></small></td><td><?= htmlspecialchars($r['category_name'] ?? '-'); ?></td><td>$<?= number_format((float)$r['price'],2); ?></td><td><?php if($r['main_image']): ?><img src="../uploads/products/<?= htmlspecialchars($r['main_image']); ?>" width="70"><?php endif; ?></td><td><a class="btn btn-sm btn-primary" href="?edit=<?= $r['id']; ?>" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil"></i></a> <a class="btn btn-sm btn-danger" data-confirm="Delete this product?" href="?delete=<?= $r['id']; ?>" data-bs-toggle="tooltip" title="Delete"><i class="bi bi-trash"></i></a></td></tr><?php endforeach; ?></tbody></table></div><div class="card-body"><?php for($i=1;$i<=$pagination['total_pages'];$i++): ?><a class="btn btn-sm <?= $i===$pagination['page'] ? 'btn-dark' : 'btn-outline-dark'; ?>" href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>"><?= $i; ?></a> <?php endfor; ?></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
