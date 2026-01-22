<?php
if (!defined('APP_STARTED')) {
    http_response_code(403);
    exit('Forbidden');
}
?>
</main>
<footer class="site-footer">
    <div class="container">
        <p>&copy; <?= date('Y'); ?> <?= esc(SITE_NAME); ?>. All rights reserved.</p>
    </div>
</footer>
<script src="<?= esc(BASE_URL); ?>/assets/js/main.js"></script>
</body>
</html>
