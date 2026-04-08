<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_name'] = $user['name'];
        setFlash('success', 'Welcome back, ' . $user['name']);
        redirect('dashboard.php');
    }

    setFlash('danger', 'Invalid email or password.');
    redirect('index.php');
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm p-4">
            <h3 class="mb-3">Admin Login</h3>
            <form method="post">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control mb-3" required>
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control mb-3" required>
                <button class="btn btn-dark w-100">Login</button>
            </form>
            <small class="text-muted mt-2 d-block">Default: admin@paintpro.com / Admin@123</small>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
