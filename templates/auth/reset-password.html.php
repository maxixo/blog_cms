<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-lock fa-3x text-primary mb-3"></i>
                        <h2 class="card-title h3">Set New Password</h2>
                        <p class="text-muted">Create a secure password for your account</p>
                    </div>

                    <?php if ($error ?? ''): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo esc($error); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success ?? ''): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo esc($success); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!($success ?? '') && ($token ?? '')): ?>
                        <form action="<?php echo htmlspecialchars(BASE_URL); ?>/reset-password.php" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token ?? ''); ?>">
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-key"></i>
                                    </span>
                                    <input 
                                        type="password" 
                                        class="form-control" 
                                        id="password" 
                                        name="password" 
                                        placeholder="Enter new password"
                                        required
                                        minlength="6"
                                    >
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">Minimum 6 characters</div>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-key"></i>
                                    </span>
                                    <input 
                                        type="password" 
                                        class="form-control" 
                                        id="password_confirm" 
                                        name="password_confirm" 
                                        placeholder="Confirm new password"
                                        required
                                        minlength="6"
                                    >
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="alert alert-info" role="alert">
                                <small>
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Password Tips:</strong>
                                    <ul class="mb-0 mt-1">
                                        <li>Use at least 8 characters</li>
                                        <li>Include uppercase and lowercase letters</li>
                                        <li>Add numbers and special characters</li>
                                        <li>Avoid using personal information</li>
                                    </ul>
                                </small>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-save me-2"></i>Update Password
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if ($success ?? ''): ?>
                        <div class="text-center">
                            <a href="<?php echo htmlspecialchars(BASE_URL); ?>/login.php" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Login with New Password
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="text-center">
                        <a href="<?php echo htmlspecialchars(BASE_URL); ?>/login.php" class="text-muted">
                            <i class="fas fa-arrow-left me-2"></i>Back to Login
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-center mt-3">
                <p class="small text-muted mb-0">
                    <i class="fas fa-shield-alt me-1"></i>
                    For your security, this reset link will expire in <?php echo (int)($expiry_minutes ?? 60); ?> minutes.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }
    
    if (toggleConfirmPassword) {
        toggleConfirmPassword.addEventListener('click', function() {
            const passwordConfirm = document.getElementById('password_confirm');
            const icon = this.querySelector('i');
            
            if (passwordConfirm.type === 'password') {
                passwordConfirm.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordConfirm.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }
});
</script>