<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) {
    setFlash('danger', 'Invalid customer id.');
    redirect('customers.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if ($name === '' || $phone === '') {
            throw new RuntimeException('Customer name and phone are required.');
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Please enter a valid email address.');
        }

        $pdo->prepare('UPDATE customers SET name=?, phone=?, email=?, address=? WHERE id=?')
            ->execute([$name, $phone, $email ?: null, $address ?: null, $id]);

        setFlash('success', 'Customer updated successfully.');
        redirect('customers.php');
    } catch (Throwable $e) {
        setFlash('danger', $e->getMessage());
        redirect('customer-edit.php?id=' . $id);
    }
}

$stmt = $pdo->prepare('SELECT * FROM customers WHERE id=?');
$stmt->execute([$id]);
$customer = $stmt->fetch();
if (!$customer) {
    setFlash('danger', 'Customer not found.');
    redirect('customers.php');
}

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Edit Customer</h1>
<div class="card p-3 shadow-sm">
    <form method="post" class="row g-3">
        <input type="hidden" name="id" value="<?= (int)$customer['id']; ?>">
        <div class="col-md-6">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($customer['name']); ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Phone <span class="text-danger">*</span></label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($customer['phone']); ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($customer['email'] ?? ''); ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($customer['address'] ?? ''); ?>">
        </div>
        <div class="col-12 d-flex gap-2">
            <button class="btn btn-dark"><i class="bi bi-save"></i> Update Customer</button>
            <a href="customers.php" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
