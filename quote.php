<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $roomSize = trim($_POST['room_size'] ?? '');
        $serviceType = trim($_POST['service_type'] ?? '');
        $productId = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null;

        if ($name === '' || $phone === '' || $email === '' || $message === '') {
            throw new RuntimeException('Please fill all required fields.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Please enter a valid email.');
        }

        $imageName = uploadImage('image', __DIR__ . '/uploads/leads');

        $sql = 'INSERT INTO leads (name,phone,email,product_id,service_type,message,room_size,image) VALUES (?,?,?,?,?,?,?,?)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $phone, $email, $productId, $serviceType ?: null, $message, $roomSize ?: null, $imageName]);

        setFlash('success', 'Your lead request has been submitted successfully.');
        redirect('quote.php');
    } catch (Throwable $e) {
        setFlash('danger', $e->getMessage());
        redirect('quote.php');
    }
}

$products = $pdo->query('SELECT id,name FROM products ORDER BY name')->fetchAll();
require_once __DIR__ . '/includes/header.php';
?>
<h1 class="section-title">Get a Quote</h1>
<div class="card p-4 shadow-sm">
<form method="POST" enctype="multipart/form-data" class="row g-3">
<div class="col-md-6"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Phone *</label><input type="text" name="phone" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Room Size</label><input type="text" name="room_size" class="form-control"></div>
<div class="col-md-6"><label class="form-label">Service Type</label><input type="text" name="service_type" class="form-control" value="<?= htmlspecialchars($_GET['service'] ?? ''); ?>"></div>
<div class="col-md-6"><label class="form-label">Select Product</label><select name="product_id" class="form-select"><option value="">Optional</option><?php foreach($products as $p): ?><option value="<?= $p['id']; ?>" <?= isset($_GET['product_id']) && (int)$_GET['product_id']===(int)$p['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($p['name']); ?></option><?php endforeach; ?></select></div>
<div class="col-12"><label class="form-label">Message *</label><textarea name="message" class="form-control" rows="4" required></textarea></div>
<div class="col-12"><label class="form-label">Upload Room Image</label><input type="file" name="image" class="form-control" accept="image/jpeg,image/png"></div>
<div class="col-12"><button type="submit" class="btn btn-warning">Submit Lead</button></div>
</form>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
