<section class="container">
    <h1><?= esc($pageHeading ?? 'Profile'); ?></h1>
    <p><?= esc($pageDescription ?? ''); ?></p>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= esc($error); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= esc($success); ?>
        </div>
    <?php endif; ?>

    <div class="card" style="margin-top: 2rem;">
        <div class="card-header">
            <h2><i class="fas fa-lock me-2"></i>Change Password</h2>
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">
                Use this form to update your account password. A confirmation email will be sent to your registered email address.
            </p>
            
            <form method="post" action="">
                <input type="hidden" name="csrf_token" value="<?= esc($csrf_token ?? generateCsrfToken()); ?>">

                <div class="form-group">
                    <label for="current_password">Current Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        <input type="password" id="current_password" name="current_password" required placeholder="Enter current password">
                        <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        <input type="password" id="new_password" name="new_password" required minlength="6" placeholder="At least 6 characters">
                        <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small class="form-text text-muted">
                        Minimum 6 characters. For better security, use a mix of letters, numbers, and special characters.
                    </small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6" placeholder="Re-enter new password">
                        <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Password Requirements:</strong>
                    <ul class="mb-0 mt-2">
                        <li>At least 6 characters long</li>
                        <li>Recommended: Mix of uppercase, lowercase, numbers, and symbols</li>
                        <li>Don't use personal information or common words</li>
                    </ul>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Password
                </button>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility for current password
    const toggleCurrentPassword = document.getElementById('toggleCurrentPassword');
    if (toggleCurrentPassword) {
        toggleCurrentPassword.addEventListener('click', function() {
            const password = document.getElementById('current_password');
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
    
    // Toggle password visibility for new password
    const toggleNewPassword = document.getElementById('toggleNewPassword');
    if (toggleNewPassword) {
        toggleNewPassword.addEventListener('click', function() {
            const password = document.getElementById('new_password');
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
    
    // Toggle password visibility for confirm password
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    if (toggleConfirmPassword) {
        toggleConfirmPassword.addEventListener('click', function() {
            const password = document.getElementById('confirm_password');
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
});
</script>
