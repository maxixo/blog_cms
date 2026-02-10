<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-key fa-3x text-primary mb-3"></i>
                        <h2 class="card-title h3">Forgot Password?</h2>
                        <p class="text-muted">No worries, we'll send you reset instructions</p>
                    </div>

                    <?php if ($error ?? ''): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo esc($error); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success ?? ''): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo esc($success); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!($success ?? '')): ?>
                        <form action="<?php echo htmlspecialchars(BASE_URL); ?>/forgot-password.php" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input 
                                        type="email" 
                                        class="form-control" 
                                        id="email" 
                                        name="email" 
                                        placeholder="Enter your email address"
                                        required
                                        value="<?php echo htmlspecialchars($email ?? ''); ?>"
                                    >
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                            </button>
                        </form>
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
                    For your security, the reset link will expire in <?php echo (int)($expiry_minutes ?? 60); ?> minutes.
                </p>
            </div>
        </div>
    </div>
</div>