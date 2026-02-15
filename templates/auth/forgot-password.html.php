<section class="container">
    <h1><?= esc($pageHeading ?? 'Forgot Password'); ?></h1>
    
    <p class="muted" style="margin-bottom: 1.5rem;">
        Enter your email address and we'll send you a link to reset your password.
    </p>
    
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
    
    <?php if (empty($success)): ?>
        <form class="card" method="post" action="">
            <input type="hidden" name="csrf_token" value="<?= esc($csrf_token ?? ''); ?>">
            
            <label>
                Email Address
                <input type="email" name="email" required placeholder="your@email.com">
            </label>
            
            <button type="submit">Send Reset Link</button>
        </form>
    <?php endif; ?>
    
    <p class="muted" style="margin-top: 1rem; text-align: center;">
        <i class="fas fa-shield-alt"></i>
        For your security, the reset link will expire in <?= esc((int)($expiry_minutes ?? 60)); ?> minutes.
    </p>
    
    <p class="muted" style="margin-top: 1rem; text-align: center;">
        Remember your password? <a href="<?= BASE_URL; ?>/login.php">Back to Login</a>
    </p>
</section>
