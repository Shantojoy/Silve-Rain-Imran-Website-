<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
if ($id <= 0) {
    setFlash('danger', 'Invalid product ID.');
    redirect('products-list.php');
}

if (isset($_GET['delete_image'])) {
    $imgId = (int)$_GET['delete_image'];
    $q = $pdo->prepare('SELECT image FROM product_images WHERE id=? AND product_id=?');
    $q->execute([$imgId, $id]);
    $row = $q->fetch();

    if ($row) {
        $pdo->prepare('DELETE FROM product_images WHERE id=?')->execute([$imgId]);
        deleteUploadedFile(__DIR__ . '/../uploads/products/' . $row['image']);
        setFlash('success', 'Gallery image removed.');
    }

    redirect('product-edit.php?id=' . $id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = trim(isset($_POST['name']) ? $_POST['name'] : '');
        $price = (float)(isset($_POST['price']) ? $_POST['price'] : 0);
        $description = trim(isset($_POST['description']) ? $_POST['description'] : '');
        $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;

        if ($name === '' || $description === '' || $price < 0) {
            throw new RuntimeException('Please provide valid product name, price and description.');
        }

        $slugBase = slugify($name);
        $slug = $slugBase;
        $counter = 1;
        while (true) {
            $check = $pdo->prepare('SELECT id FROM products WHERE slug = ? AND id != ? LIMIT 1');
            $check->execute([$slug, $id]);
            if (!$check->fetch()) {
                break;
            }
            $slug = $slugBase . '-' . $counter++;
        }

        $newMainImage = uploadImage('main_image', __DIR__ . '/../uploads/products');
        $newGalleryImages = uploadMultipleImages('gallery_images', __DIR__ . '/../uploads/products');

        $oldStmt = $pdo->prepare('SELECT main_image FROM products WHERE id=?');
        $oldStmt->execute([$id]);
        $old = $oldStmt->fetch();
        if (!$old) {
            throw new RuntimeException('Product not found.');
        }

        $mainImageToSave = $newMainImage ? $newMainImage : $old['main_image'];

        $stmt = $pdo->prepare('UPDATE products SET name=?, slug=?, price=?, description=?, category_id=?, main_image=? WHERE id=?');
        if (!$stmt->execute([$name, $slug, $price, $description, $categoryId, $mainImageToSave, $id])) {
            $errorInfo = $stmt->errorInfo();
            throw new RuntimeException('Update failed: ' . implode(' | ', $errorInfo));
        }

        if ($newMainImage && !empty($old['main_image'])) {
            deleteUploadedFile(__DIR__ . '/../uploads/products/' . $old['main_image']);
        }

        foreach ($newGalleryImages as $image) {
            $imgStmt = $pdo->prepare('INSERT INTO product_images (product_id, image) VALUES (?, ?)');
            if (!$imgStmt->execute([$id, $image])) {
                $errorInfo = $imgStmt->errorInfo();
                throw new RuntimeException('Gallery insert failed: ' . implode(' | ', $errorInfo));
            }
        }

        setFlash('success', 'Product updated successfully.');
        redirect('products-list.php');
    } catch (Throwable $e) {
        setFlash('danger', $e->getMessage());
        redirect('product-edit.php?id=' . $id);
    }
}

$stmt = $pdo->prepare('SELECT * FROM products WHERE id=?');
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) {
    setFlash('danger', 'Product not found.');
    redirect('products-list.php');
}

$imgStmt = $pdo->prepare('SELECT * FROM product_images WHERE product_id=? ORDER BY id DESC');
$imgStmt->execute([$id]);
$galleryImages = $imgStmt->fetchAll();

$categories = $pdo->query("SELECT id, name FROM categories WHERE type='product' ORDER BY name")->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="section-title">Edit Product</h1>
<div class="card p-3 shadow-sm mb-3">
    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="id" value="<?= (int)$product['id']; ?>">

        <div class="col-md-4">
            <label class="form-label">Product Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" placeholder="Modern Grey Wallpaper" value="<?= htmlspecialchars($product['name']); ?>" required>
        </div>

        <div class="col-md-2">
            <label class="form-label">Price (USD) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($product['price']); ?>" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select">
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int)$cat['id']; ?>" <?= (int)$product['category_id'] === (int)$cat['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($cat['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Main Image</label>
            <input type="file" name="main_image" class="form-control" accept="image/jpeg,image/png">
            <?= helpText('Leave empty to keep current main image.'); ?>
        </div>

        <div class="col-md-6">
            <label class="form-label">Gallery Images</label>
            <input type="file" name="gallery_images[]" class="form-control" accept="image/jpeg,image/png" multiple>
            <?= helpText('Optional: upload additional gallery images.'); ?>
        </div>

        <div class="col-12">
            <label class="form-label">Description <span class="text-danger">*</span></label>
            <textarea name="description" class="form-control editor" rows="4" required><?= htmlspecialchars($product['description']); ?></textarea>
        </div>

        <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-dark"><i class="bi bi-save"></i> Update Product</button>
            <a href="products-list.php" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php if ($galleryImages): ?>
    <div class="card p-3 shadow-sm">
        <strong>Current Gallery Images</strong>
        <div class="d-flex flex-wrap gap-2 mt-2">
            <?php foreach ($galleryImages as $img): ?>
                <div>
                    <img src="../uploads/products/<?= htmlspecialchars($img['image']); ?>" width="80" alt="gallery">
                    <br>
                    <a class="text-danger small" href="?id=<?= (int)$product['id']; ?>&delete_image=<?= (int)$img['id']; ?>" data-confirm="Delete this gallery image?">remove</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
