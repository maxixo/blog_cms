    </main>
    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-col">
                <h3>About</h3>
                <p class="muted"><?= esc(SITE_DESCRIPTION); ?></p>
            </div>
            <div class="footer-col">
                <h3>Explore</h3>
                <ul class="footer-links">
                    <li><a href="<?= esc(BASE_URL); ?>/posts.php">Latest posts</a></li>
                    <li><a href="<?= esc(BASE_URL); ?>/category.php">Categories</a></li>
                    <li><a href="<?= esc(BASE_URL); ?>/about.php">About</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Connect</h3>
                <ul class="footer-links footer-social">
                    <li><a href="https://twitter.com">Twitter</a></li>
                    <li><a href="https://github.com">GitHub</a></li>
                    <li><a href="https://www.linkedin.com">LinkedIn</a></li>
                </ul>
                <p class="muted">
                    <a href="mailto:<?= esc(SITE_EMAIL); ?>"><?= esc(SITE_EMAIL); ?></a>
                </p>
            </div>
        </div>
        <div class="container footer-bottom">
            <p>&copy; <?= date('Y'); ?> <?= esc(SITE_NAME); ?>. All rights reserved.</p>
            <div class="footer-meta">
                <a href="<?= esc(BASE_URL); ?>/rss.php">RSS</a>
                <span class="footer-divider">|</span>
                <a href="<?= esc(BASE_URL); ?>/sitemap.xml.php">Sitemap</a>
            </div>
        </div>
    </footer>
    <button class="back-to-top" type="button" aria-label="Back to top" aria-hidden="true">
        Back to top
    </button>
    <script src="<?= esc(ASSETS_URL); ?>/js/main.js"></script>
    <?php if (!empty($additionalJs)): ?>
        <?php foreach ($additionalJs as $js): ?>
            <script src="<?= esc($js); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
