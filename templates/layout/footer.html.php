    </main>
    <footer class="site-footer">
        <div class="container">
            <p>&copy; <?= date('Y'); ?> <?= esc(SITE_NAME); ?>. All rights reserved.</p>
        </div>
    </footer>
    <script src="<?= esc(ASSETS_URL); ?>/js/main.js"></script>
    <?php if (!empty($additionalJs)): ?>
        <?php foreach ($additionalJs as $js): ?>
            <script src="<?= esc($js); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
