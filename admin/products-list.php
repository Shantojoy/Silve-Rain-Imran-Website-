<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $img = $pdo->prepare('SELECT main_image FROM products WHERE id=?');
    $img->execute([$id]);
    $product = $img->fetch();

    $pdo->prepare('DELETE FROM products WHERE id=?')->execute([$id]);
    if (!empty($product['main_image'])) {
        deleteUploadedFile(__DIR__ . '/../uploads/products/' . $product['main_image']);
    }

    setFlash('success', 'Product deleted successfully.');
    redirect('products-list.php');
}

$search = trim(isset($_GET['search']) ? $_GET['search'] : '');
$where = $search !== '' ? 'WHERE p.name LIKE ?' : '';
$params = $search !== '' ? ["%$search%"] : [];

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM products p $where");
$countStmt->execute($params);
$pagination = paginate((int)$countStmt->fetchColumn(), 10);

$listStmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id $where ORDER BY p.created_at DESC LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}");
$listStmt->execute($params);
$rows = $listStmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="section-title mb-0">Products List</h1>
    <a href="product-add.php" class="btn btn-dark"><i class="bi bi-plus-circle"></i> Add Product</a>
</div>

<div class="card shadow-sm">
    <div class="card-body border-bottom">
        <form class="row g-2" method="GET">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search product name" value="<?= htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-dark"><i class="bi bi-search"></i> Search</button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Category</th>
                <th>Price</th>
                <th>Main Image</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['name']); ?></td>
                    <td><small><?= htmlspecialchars($r['slug']); ?></small></td>
                    <td><?= htmlspecialchars(isset($r['category_name']) ? $r['category_name'] : '-'); ?></td>
                    <td>$<?= number_format((float)$r['price'], 2); ?></td>
                    <td><?php if (!empty($r['main_image'])): ?><img src="../uploads/products/<?= htmlspecialchars($r['main_image']); ?>" width="70" alt="main"><?php endif; ?></td>
                    <td>
                        <a href="product-edit.php?id=<?= (int)$r['id']; ?>" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></a>
                        <a href="?delete=<?= (int)$r['id']; ?>" class="btn btn-sm btn-danger" data-confirm="Delete this product?"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card-body">
        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <a class="btn btn-sm <?= $i === $pagination['page'] ? 'btn-dark' : 'btn-outline-dark'; ?>" href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>"><?= $i; ?></a>
        <?php endfor; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
