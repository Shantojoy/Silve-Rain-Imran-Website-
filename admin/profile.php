<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$adminId = (int)$_SESSION['admin_id'];
$stmt = $pdo->prepare('SELECT id, name, email FROM users WHERE id = ?');
$stmt->execute([$adminId]);
$user = $stmt->fetch();
if (!$user) {
    setFlash('danger', 'User not found.');
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($name === '' || $email === '') throw new RuntimeException('Name and email are required.');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Please enter a valid email address.');

        $check = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1');
        $check->execute([$email, $adminId]);
        if ($check->fetch()) throw new RuntimeException('This email is already used by another account.');

        if ($newPassword !== '') {
            if (strlen($newPassword) < 8) throw new RuntimeException('Password must be at least 8 characters.');
            if ($newPassword !== $confirmPassword) throw new RuntimeException('Password confirmation does not match.');
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $pdo->prepare('UPDATE users SET name=?, email=?, password=? WHERE id=?')->execute([$name,$email,$hash,$adminId]);
        } else {
            $pdo->prepare('UPDATE users SET name=?, email=? WHERE id=?')->execute([$name,$email,$adminId]);
        }

        $_SESSION['admin_name'] = $name;
        setFlash('success', 'Profile updated successfully.');
        redirect('profile.php');
    } catch (Throwable $e) {
        setFlash('danger', $e->getMessage());
        redirect('profile.php');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<h1 class="section-title">My Profile</h1>
<div class="card shadow-sm p-4">
    <form method="post" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']); ?>" placeholder="Enter your full name" required>
            <?= helpText('Use your real display name shown across the admin panel.'); ?>
        </div>
        <div class="col-md-6">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" placeholder="admin@company.com" required>
            <?= helpText('This email is used for login and notifications.'); ?>
        </div>
        <div class="col-md-6">
            <label class="form-label">New Password</label>
            <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current password">
            <?= helpText("Leave blank if you don't want to change your password."); ?>
        </div>
        <div class="col-md-6">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter new password">
            <?= helpText('Must match the new password field above.'); ?>
        </div>
        <div class="col-12">
            <button class="btn btn-dark"><i class="bi bi-save"></i> Update Profile</button>
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
