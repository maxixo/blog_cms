<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-envelope-open-text fa-3x text-primary mb-3"></i>
                        <h2 class="card-title h3">Verify Your Email</h2>
                        <p class="text-muted">We've sent a verification link to your email</p>
                    </div>

                    <?php if ($error ?? ''): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo esc($error); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success ?? ''): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo esc($success); ?>
                        </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <p class="text-muted">
                            <strong><?php echo esc($email ?? ''); ?></strong>
                        </p>
                        <p class="small text-muted">
                            Please check your email and click the verification link to activate your account. The link will expire in <?php echo (int)($expiry_hours ?? 24); ?> hours.
                        </p>
                    </div>

                    <form action="<?php echo htmlspecialchars(BASE_URL); ?>/resend-verification.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-redo me-2"></i>Resend Verification Email
                        </button>
                    </form>

                    <div class="text-center">
                        <a href="<?php echo htmlspecialchars(BASE_URL); ?>/login.php" class="text-muted">
                            <i class="fas fa-arrow-left me-2"></i>Back to Login
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-center mt-3">
                <p class="small text-muted mb-0">
                    <i class="fas fa-question-circle me-1"></i>
                    Didn't receive the email? Check your spam folder or request a new one above.
                </p>
            </div>
        </div>
    </div>
</div>