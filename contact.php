<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$settings = $pdo->query('SELECT * FROM settings LIMIT 1')->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($name === '' || $email === '' || $message === '') {
            throw new RuntimeException('Name, email and message are required.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Please enter a valid email address.');
        }

        $stmt = $pdo->prepare('INSERT INTO leads (name, phone, email, service_type, message, status) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $phone ?: '-', $email, 'Contact Inquiry', $message, 'New']);
        addNotification($pdo, 'lead', 'New Contact Inquiry', 'Contact request from ' . $name . ($phone ? ' (' . $phone . ')' : ''), 'leads.php');

        setFlash('success', 'Thanks for contacting us! We will respond shortly.');
    } catch (Throwable $e) {
        setFlash('danger', $e->getMessage());
    }
    redirect('contact.php');
}

require_once __DIR__ . '/includes/header.php';
?>
<h1 class="section-title">Contact Us</h1>
<div class="row g-4"><div class="col-md-6"><form method="post" class="card p-4 shadow-sm"><input name="name" class="form-control mb-3" placeholder="Name *" required><input name="email" class="form-control mb-3" type="email" placeholder="Email *" required><input name="phone" class="form-control mb-3" placeholder="Phone"><textarea name="message" class="form-control mb-3" rows="4" placeholder="Message *" required></textarea><button class="btn btn-dark">Send Message</button></form></div><div class="col-md-6"><div class="card p-3 shadow-sm h-100"><h5>Business Info</h5><p><strong>Phone:</strong> <?= htmlspecialchars($settings['phone'] ?? ''); ?></p><p><strong>Email:</strong> <?= htmlspecialchars($settings['contact_email'] ?? ''); ?></p><p><strong>Address:</strong> <?= htmlspecialchars($settings['address'] ?? ''); ?></p><iframe class="w-100 rounded" height="280" src="https://maps.google.com/maps?q=New%20York&t=&z=13&ie=UTF8&iwloc=&output=embed"></iframe></div></div></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
