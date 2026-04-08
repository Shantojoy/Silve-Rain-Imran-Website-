<?php $showAdminShell = strpos($_SERVER['PHP_SELF'], '/admin/') !== false && isset($_SESSION['admin_id']); ?>
<?php if ($showAdminShell): ?>
        </div>
    </main>
</div>
<?php else: ?>
</div>
<footer class="bg-dark text-light py-4 mt-5">
    <div class="container text-center">
        <p class="mb-1">&copy; <?= date('Y'); ?> <?= htmlspecialchars($siteName ?? 'PaintPro'); ?></p>
    </div>
</footer>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../' : ''); ?>assets/js/main.js"></script>
</body>
</html>
