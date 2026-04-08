<?php if ($showAdminShell ?? false): ?>
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
<?php if (!empty($setting['whatsapp_number'])): ?>
<a href="https://wa.me/<?= urlencode($setting['whatsapp_number']); ?>?text=<?= urlencode($setting['whatsapp_message'] ?? 'Hi'); ?>" class="whatsapp-float" target="_blank" title="Chat on WhatsApp">
    <i class="bi bi-whatsapp"></i>
</a>
<?php endif; ?>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script src="<?= (strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../' : ''); ?>assets/js/main.js"></script>
</body>
</html>
