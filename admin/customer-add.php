<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

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

        $pdo->prepare('INSERT INTO customers (name, phone, email, address) VALUES (?,?,?,?)')
            ->execute([$name, $phone, $email ?: null, $address ?: null]);

        setFlash('success', 'Customer added successfully.');
        redirect('customers.php');
    } catch (Throwable $e) {
        setFlash('danger', $e->getMessage());
        redirect('customer-add.php');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">Add Customer</h1>
<div class="card p-3 shadow-sm">
    <form method="post" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Phone <span class="text-danger">*</span></label>
            <input type="text" name="phone" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control">
        </div>
        <div class="col-12 d-flex gap-2">
            <button class="btn btn-dark"><i class="bi bi-save"></i> Save Customer</button>
            <a href="customers.php" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
