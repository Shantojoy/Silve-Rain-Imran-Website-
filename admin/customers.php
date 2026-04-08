<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$search = trim($_GET['search'] ?? '');

$where = $search !== '' ? 'WHERE name LIKE ? OR phone LIKE ? OR email LIKE ?' : '';
$params = $search !== '' ? ["%$search%","%$search%","%$search%"] : [];

// Count total
$c = $pdo->prepare("SELECT COUNT(*) FROM customers $where");
$c->execute($params);
$pg = paginate((int)$c->fetchColumn(), 12);

// Fetch data
$stmt = $pdo->prepare("SELECT * FROM customers $where ORDER BY created_at DESC LIMIT {$pg['per_page']} OFFSET {$pg['offset']}");
$stmt->execute($params);
$rows = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="section-title">Customers</h1>

<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success">Customer added successfully!</div>
<?php endif; ?>

<div class="card shadow-sm">

    <!-- HEADER (SEARCH + ADD BUTTON) -->
    <div class="card-body border-bottom">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

            <!-- Search -->
            <form class="d-flex gap-2">
                <input 
                    name="search" 
                    class="form-control" 
                    placeholder="Search customers (name, phone, email)" 
                    value="<?= htmlspecialchars($search); ?>"
                >
                <button class="btn btn-outline-dark">
                    <i class="bi bi-search"></i>
                </button>
            </form>

            <!-- Add Customer -->
            <a href="customer-add.php" class="btn btn-dark">
                <i class="bi bi-plus-lg"></i> Add Customer
            </a>

        </div>
    </div>

    <!-- TABLE -->
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th width="120">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($rows)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            No customers found
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($rows as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['name']); ?></td>
                            <td><?= htmlspecialchars($r['phone']); ?></td>
                            <td><?= htmlspecialchars($r['email']); ?></td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="customer-view.php?id=<?= $r['id']; ?>">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <div class="card-body">
        <?php for($i = 1; $i <= $pg['total_pages']; $i++): ?>
            <a 
                class="btn btn-sm <?= $pg['page'] === $i ? 'btn-dark' : 'btn-outline-dark'; ?>" 
                href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>"
            >
                <?= $i; ?>
            </a>
        <?php endfor; ?>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
