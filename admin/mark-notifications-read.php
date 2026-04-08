<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
$pdo->exec('UPDATE notifications SET is_read = 1 WHERE is_read = 0');
setFlash('success', 'Notifications marked as read.');
redirect('dashboard.php');
