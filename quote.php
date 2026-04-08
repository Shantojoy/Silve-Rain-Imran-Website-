<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = sanitize($_POST['name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $message = sanitize($_POST['message'] ?? '');
        $roomSize = sanitize($_POST['room_size'] ?? '');
        $serviceType = sanitize($_POST['service_type'] ?? '');
        $productId = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null;

        if (!$name || !$phone || !$email || !$message) {
            throw new RuntimeException('Please fill in all required fields.');
        }

        $imageName = uploadImage('image', __DIR__ . '/uploads/leads');

        $stmt = $pdo->prepare('INSERT INTO leads (name, phone, email, product_id, service_type, message, room_size, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $phone, $email, $productId, $serviceType ?: null, $message, $roomSize ?: null, $imageName]);

        setFlash('success', 'Quote request submitted successfully. Our team will contact you soon.');
        redirect('quote.php');
    } catch (Throwable $e) {
        setFlash('danger', $e->getMessage());
        redirect('quote.php');
    }
}

require_once __DIR__ . '/includes/header.php';
$products = $pdo->query('SELECT id,name FROM products ORDER BY name')->fetchAll();
?>
<h1 class="section-title">Request a Quote</h1>
<form method="post" enctype="multipart/form-data" class="card shadow-sm p-4">
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Name *</label><input name="name" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Phone *</label><input name="phone" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Room Size</label><input name="room_size" class="form-control" placeholder="e.g. 14x12 ft"></div>
        <div class="col-md-6"><label class="form-label">Service Type</label><input name="service_type" class="form-control" value="<?= htmlspecialchars($_GET['service'] ?? ''); ?>"></div>
        <div class="col-md-6"><label class="form-label">Wallpaper Design</label>
            <select name="product_id" class="form-select">
                <option value="">Select (optional)</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= $p['id']; ?>" <?= isset($_GET['product_id']) && (int)$_GET['product_id'] === (int)$p['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($p['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12"><label class="form-label">Message *</label><textarea name="message" class="form-control" rows="4" required></textarea></div>
        <div class="col-12"><label class="form-label">Upload Room Image (optional)</label><input type="file" name="image" class="form-control" accept="image/png,image/jpeg,image/webp"></div>
        <div class="col-12"><button class="btn btn-warning">Submit Request</button></div>
    </div>
</form>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
