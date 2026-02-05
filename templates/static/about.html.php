<section class="about-hero">
    <div class="container">
        <div class="card about-hero-card">
            <p class="about-eyebrow">About</p>
            <h1><?= esc(SITE_NAME); ?></h1>
            <p class="about-hero-tagline"><?= esc(SITE_TAGLINE); ?></p>
            <p class="about-hero-copy">
                Welcome to <?= esc(SITE_NAME); ?>, a blog CMS built for thoughtful publishing.
                <?= esc(SITE_DESCRIPTION); ?> We share practical guidance on content management,
                SEO, and editorial workflow so writers and editors can ship with confidence.
            </p>
            <div class="about-hero-actions">
                <a class="button-link" href="<?= esc(BASE_URL); ?>/posts.php">Explore posts</a>
                <a class="button-link" href="<?= esc(BASE_URL); ?>/category.php">Browse categories</a>
            </div>
        </div>
    </div>
</section>

<section class="container about-section">
    <div class="card about-mission">
        <div class="about-section-header">
            <h2>Mission Statement</h2>
            <p class="muted">What the blog stands for.</p>
        </div>
        <p>
            Our mission is to make publishing calm, clear, and sustainable for modern teams.
            We publish tutorials, product notes, and commentary on writing craft, content strategy,
            and community building.
        </p>
        <p>
            This space is for creators, editors, and small teams who want reliable, search-friendly
            knowledge they can put into action.
        </p>
    </div>
</section>

<section class="container about-section">
    <div class="about-section-header">
        <h2>Key Features</h2>
        <p class="muted">Highlights that keep readers engaged.</p>
    </div>
    <div class="about-features-grid">
        <article class="card about-feature-card">
            <span class="feature-icon" aria-hidden="true">QC</span>
            <h3>Quality Content</h3>
            <p class="muted">Editorially reviewed posts with clear structure and useful takeaways.</p>
        </article>
        <article class="card about-feature-card">
            <span class="feature-icon" aria-hidden="true">RD</span>
            <h3>Responsive Design</h3>
            <p class="muted">A clean reading experience that stays fast on every screen size.</p>
        </article>
        <article class="card about-feature-card">
            <span class="feature-icon" aria-hidden="true">AC</span>
            <h3>Active Community</h3>
            <p class="muted">Thoughtful comments and feedback loops that keep ideas improving.</p>
        </article>
        <article class="card about-feature-card">
            <span class="feature-icon" aria-hidden="true">RU</span>
            <h3>Regular Updates</h3>
            <p class="muted">Fresh posts and updates that track publishing trends and tools.</p>
        </article>
    </div>
</section>

<section class="container about-section">
    <div class="about-section-header">
        <h2>Statistics</h2>
        <p class="muted">Live snapshot from the database.</p>
    </div>
    <div class="about-stats-grid">
        <div class="card about-stat-card">
            <p class="about-stat-value"><?= esc(number_format((int) ($aboutStats['posts'] ?? 0))); ?></p>
            <p class="about-stat-label">Published posts</p>
        </div>
        <div class="card about-stat-card">
            <p class="about-stat-value"><?= esc(number_format((int) ($aboutStats['categories'] ?? 0))); ?></p>
            <p class="about-stat-label">Categories</p>
        </div>
        <div class="card about-stat-card">
            <p class="about-stat-value"><?= esc(number_format((int) ($aboutStats['users'] ?? 0))); ?></p>
            <p class="about-stat-label">Registered users</p>
        </div>
        <div class="card about-stat-card">
            <p class="about-stat-value"><?= esc(number_format((int) ($aboutStats['comments'] ?? 0))); ?></p>
            <p class="about-stat-label">Approved comments</p>
        </div>
    </div>
</section>

<section class="container about-section">
    <div class="card about-detail">
        <div class="about-section-header">
            <h2>About the Blog and Author</h2>
            <p class="muted">How it started, how it grows.</p>
        </div>
        <p>
            What started as a small internal tool now supports a growing editorial hub. The blog is
            written by a tight author team and guest contributors who care about accuracy,
            accessibility, and respectful discussion.
        </p>
        <p>
            We value clarity over noise, practical SEO over hype, and community feedback that makes
            every article stronger.
        </p>
        <div class="about-values">
            <span class="tag tag-weight-1">Clarity first</span>
            <span class="tag tag-weight-2">Respectful community</span>
            <span class="tag tag-weight-3">Practical SEO</span>
            <span class="tag tag-weight-4">Reliable updates</span>
        </div>
    </div>
</section>

<section class="container about-section">
    <div class="card about-contact">
        <div class="about-section-header">
            <h2>Contact</h2>
            <p class="muted">Stay in touch with the team.</p>
        </div>
        <div class="about-contact-grid">
            <div>
                <p>Have an idea, question, or collaboration request? Reach out any time.</p>
                <p>
                    <a href="mailto:<?= esc(SITE_EMAIL); ?>"><?= esc(SITE_EMAIL); ?></a>
                </p>
            </div>
            <div>
                <p class="muted">Follow along</p>
                <div class="about-social">
                    <a class="about-social-link" href="https://facebook.com" target="_blank" rel="noopener noreferrer">
                        <span class="social-icon" aria-hidden="true">FB</span>
                        <span>Facebook</span>
                    </a>
                    <a class="about-social-link" href="https://twitter.com" target="_blank" rel="noopener noreferrer">
                        <span class="social-icon" aria-hidden="true">TW</span>
                        <span>Twitter</span>
                    </a>
                    <a class="about-social-link" href="https://instagram.com" target="_blank" rel="noopener noreferrer">
                        <span class="social-icon" aria-hidden="true">IG</span>
                        <span>Instagram</span>
                    </a>
                    <a class="about-social-link" href="https://linkedin.com" target="_blank" rel="noopener noreferrer">
                        <span class="social-icon" aria-hidden="true">IN</span>
                        <span>LinkedIn</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
