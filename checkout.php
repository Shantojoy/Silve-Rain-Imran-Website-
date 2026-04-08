<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
$productId = (int)($_GET['product_id'] ?? ($_POST['product_id'] ?? 0));
$stmt = $pdo->prepare('SELECT * FROM products WHERE id=?');
$stmt->execute([$productId]);
$product = $stmt->fetch();
if (!$product) { die('Product not found'); }
$settings = $pdo->query('SELECT payment_instructions FROM settings LIMIT 1')->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $qty = max(1, (int)($_POST['quantity'] ?? 1));
        if (!$name || !$phone || !$email || !$address) throw new RuntimeException('All fields are required.');

        $total = $qty * (float)$product['price'];
        $pdo->beginTransaction();
        $pdo->prepare('INSERT INTO orders (name,phone,email,address,total_amount,status) VALUES (?,?,?,?,?,?)')->execute([$name,$phone,$email,$address,$total,'Pending']);
        $orderId = (int)$pdo->lastInsertId();
        $pdo->prepare('INSERT INTO order_items (order_id,product_id,quantity,price) VALUES (?,?,?,?)')->execute([$orderId,$productId,$qty,$product['price']]);
        $pdo->commit();

        sendTemplateEmail($pdo, 'new_order', $email, ['name'=>$name,'order_id'=>$orderId,'total'=>$total]);

        setFlash('success', 'Order placed successfully.');
        redirect('checkout.php?product_id='.$productId);
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        setFlash('danger', $e->getMessage());
        redirect('checkout.php?product_id='.$productId);
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<h1 class="section-title">Checkout (Cash on Delivery)</h1>
<div class="row g-4"><div class="col-lg-7"><div class="card p-3 shadow-sm"><h5><?= htmlspecialchars($product['name']); ?></h5><p>Price: <strong>$<?= number_format((float)$product['price'],2); ?></strong></p><form method="post" class="row g-3"><input type="hidden" name="product_id" value="<?= $productId; ?>"><div class="col-md-6"><input name="name" class="form-control" placeholder="Name" required></div><div class="col-md-6"><input name="phone" class="form-control" placeholder="Phone" required></div><div class="col-md-6"><input type="email" name="email" class="form-control" placeholder="Email" required></div><div class="col-md-6"><input type="number" min="1" name="quantity" class="form-control" value="1" required></div><div class="col-12"><textarea name="address" class="form-control" placeholder="Address" rows="3" required></textarea></div><div class="col-12"><button class="btn btn-dark">Place Order</button></div></form></div></div><div class="col-lg-5"><div class="card p-3 shadow-sm"><h6>Payment Instructions</h6><p class="mb-0"><?= nl2br(htmlspecialchars($settings['payment_instructions'] ?? 'Cash on Delivery')); ?></p></div></div></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
