<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = trim(isset($_POST['name']) ? $_POST['name'] : '');
        $price = (float)(isset($_POST['price']) ? $_POST['price'] : 0);
        $description = trim(isset($_POST['description']) ? $_POST['description'] : '');
        $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;

        if ($name === '' || $description === '' || $price < 0) {
            throw new RuntimeException('Please provide valid product name, price and description.');
        }

        $mainImage = uploadImage('main_image', __DIR__ . '/../uploads/products');
        $galleryImages = uploadMultipleImages('gallery_images', __DIR__ . '/../uploads/products');

        $slugBase = slugify($name);
        $slug = $slugBase;
        $counter = 1;
        while (true) {
            $check = $pdo->prepare('SELECT id FROM products WHERE slug = ? LIMIT 1');
            $check->execute([$slug]);
            if (!$check->fetch()) {
                break;
            }
            $slug = $slugBase . '-' . $counter++;
        }

        $stmt = $pdo->prepare('INSERT INTO products (name, slug, price, description, category_id, main_image, is_virtual) VALUES (?, ?, ?, ?, ?, ?, 1)');
        if (!$stmt->execute([$name, $slug, $price, $description, $categoryId, $mainImage])) {
            $errorInfo = $stmt->errorInfo();
            throw new RuntimeException('Insert failed: ' . implode(' | ', $errorInfo));
        }

        $productId = (int)$pdo->lastInsertId();
        foreach ($galleryImages as $image) {
            $imgStmt = $pdo->prepare('INSERT INTO product_images (product_id, image) VALUES (?, ?)');
            if (!$imgStmt->execute([$productId, $image])) {
                $errorInfo = $imgStmt->errorInfo();
                throw new RuntimeException('Gallery insert failed: ' . implode(' | ', $errorInfo));
            }
        }

        setFlash('success', 'Product added successfully.');
        redirect('products-list.php');
    } catch (Throwable $e) {
        setFlash('danger', $e->getMessage());
        redirect('product-add.php');
    }
}

$categories = $pdo->query("SELECT id, name FROM categories WHERE type='product' ORDER BY name")->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="section-title">Add Product</h1>
<div class="card p-3 shadow-sm">
    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Product Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" placeholder="Modern Grey Wallpaper" required>
            <?= helpText('Enter a clear product name for customers.'); ?>
        </div>

        <div class="col-md-2">
            <label class="form-label">Price (USD) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="price" class="form-control" placeholder="99.99" required>
            <?= helpText('Use valid numeric price, for example 49.99.'); ?>
        </div>

        <div class="col-md-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select">
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int)$cat['id']; ?>"><?= htmlspecialchars($cat['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Main Image</label>
            <input type="file" name="main_image" class="form-control" accept="image/jpeg,image/png">
            <?= helpText('JPG/PNG only, max 5MB.'); ?>
        </div>

        <div class="col-md-6">
            <label class="form-label">Gallery Images</label>
            <input type="file" name="gallery_images[]" class="form-control" accept="image/jpeg,image/png" multiple>
            <?= helpText('Optional: upload multiple extra images.'); ?>
        </div>

        <div class="col-12">
            <label class="form-label">Description <span class="text-danger">*</span></label>
            <textarea name="description" class="form-control editor" rows="4" placeholder="Write complete product details" required></textarea>
        </div>

        <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-dark"><i class="bi bi-save"></i> Save Product</button>
            <a href="products-list.php" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
