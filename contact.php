<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
$settings = $pdo->query('SELECT * FROM settings LIMIT 1')->fetch();
if ($_SERVER['REQUEST_METHOD'] === 'POST') { setFlash('success', 'Thanks for contacting us!'); redirect('contact.php'); }
require_once __DIR__ . '/includes/header.php';
?>
<h1 class="section-title">Contact Us</h1>
<div class="row g-4"><div class="col-md-6"><form method="post" class="card p-4 shadow-sm"><input class="form-control mb-3" placeholder="Name" required><input class="form-control mb-3" type="email" placeholder="Email" required><textarea class="form-control mb-3" rows="4" placeholder="Message" required></textarea><button class="btn btn-dark">Send Message</button></form></div><div class="col-md-6"><div class="card p-3 shadow-sm h-100"><h5>Business Info</h5><p><strong>Phone:</strong> <?= htmlspecialchars($settings['phone'] ?? ''); ?></p><p><strong>Email:</strong> <?= htmlspecialchars($settings['contact_email'] ?? ''); ?></p><p><strong>Address:</strong> <?= htmlspecialchars($settings['address'] ?? ''); ?></p><iframe class="w-100 rounded" height="280" src="https://maps.google.com/maps?q=New%20York&t=&z=13&ie=UTF8&iwloc=&output=embed"></iframe></div></div></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
