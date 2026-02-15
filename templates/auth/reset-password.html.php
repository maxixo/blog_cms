<section class="container">
    <h1><?= esc($pageHeading ?? 'Reset Password'); ?></h1>
    
    <p class="muted" style="margin-bottom: 1.5rem;">
        Create a new secure password for your account.
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
        <p class="muted" style="text-align: center;">
            <a href="<?= BASE_URL; ?>/login.php">Login with your new password</a>
        </p>
    <?php endif; ?>
    
    <?php if (empty($success) && !empty($token)): ?>
        <form class="card" method="post" action="">
            <input type="hidden" name="csrf_token" value="<?= esc($csrf_token ?? ''); ?>">
            <input type="hidden" name="token" value="<?= esc($token ?? ''); ?>">
            
            <label>
                New Password
                <input type="password" name="password" required minlength="6" placeholder="At least 6 characters">
            </label>
            
            <label>
                Confirm New Password
                <input type="password" name="password_confirm" required minlength="6" placeholder="Re-enter your password">
            </label>
            
            <button type="submit">Update Password</button>
        </form>
    <?php endif; ?>
    
    <p class="muted" style="margin-top: 1rem; text-align: center;">
        <i class="fas fa-shield-alt"></i>
        For your security, this reset link will expire in <?= esc((int)($expiry_minutes ?? 60)); ?> minutes.
    </p>
    
    <?php if (empty($success)): ?>
        <p class="muted" style="margin-top: 1rem; text-align: center;">
            <a href="<?= BASE_URL; ?>/login.php">Back to Login</a>
        </p>
    <?php endif; ?>
</section>
