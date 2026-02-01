<section class="container">
    <h1><?= esc($pageHeading ?? 'Login'); ?></h1>
    
    <?php if (!empty($error)): ?>
        <div class="card" style="background-color: #fee; border-left: 4px solid #c33; padding: 1rem; margin-bottom: 1rem;">
            <p class="muted" style="color: #c33;"><?= esc($error); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="card" style="background-color: #efe; border-left: 4px solid #3c3; padding: 1rem; margin-bottom: 1rem;">
            <p class="muted" style="color: #3c3;"><?= esc($success); ?></p>
        </div>
    <?php endif; ?>
    
    <form class="card" method="post" action="">
        <input type="hidden" name="csrf_token" value="<?= esc($csrf_token ?? ''); ?>">
        
        <label>
            Email
            <input type="email" name="email" required placeholder="your@email.com">
        </label>
        
        <label>
            Password
            <input type="password" name="password" required placeholder="Enter your password">
        </label>
        
        <button type="submit">Sign In</button>
    </form>
    
    <p class="muted" style="margin-top: 1rem; text-align: center;">
        Don't have an account? <a href="<?= BASE_URL; ?>/register.php">Register here</a>
    </p>
</section>
