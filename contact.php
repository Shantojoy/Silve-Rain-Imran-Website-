<?php
require_once __DIR__ . '/includes/auth.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    setFlash('success', 'Thanks for contacting us! We will get back to you soon.');
    redirect('contact.php');
}
require_once __DIR__ . '/includes/header.php';
?>
<h1 class="section-title">Contact Us</h1>
<div class="row g-4">
    <div class="col-md-6">
        <form method="post" class="card p-4 shadow-sm">
            <input class="form-control mb-3" placeholder="Your Name" required>
            <input type="email" class="form-control mb-3" placeholder="Email" required>
            <textarea class="form-control mb-3" rows="4" placeholder="Message" required></textarea>
            <button class="btn btn-dark">Send Message</button>
        </form>
    </div>
    <div class="col-md-6">
        <div class="card p-3 shadow-sm h-100">
            <h5>Business Info</h5>
            <p><strong>Phone:</strong> +1 (555) 321-9988</p>
            <p><strong>Email:</strong> hello@paintpro.com</p>
            <p><strong>Address:</strong> 245 Design Street, New York, USA</p>
            <iframe class="w-100 rounded" height="320" src="https://maps.google.com/maps?q=New%20York&t=&z=13&ie=UTF8&iwloc=&output=embed"></iframe>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
