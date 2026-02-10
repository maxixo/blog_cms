<section class="container">
    <h1><?= esc($pageHeading ?? 'Verify your email'); ?></h1>

    <p class="muted">We sent a verification link to your email address. Please click the link to activate your account.</p>

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

    <div id="resend-message"></div>

    <form id="resend-verification-form" class="card" method="post" action="<?= esc(BASE_URL); ?>/resend-verification.php">
        <input type="hidden" name="csrf_token" value="<?= esc($csrf_token ?? ''); ?>">

        <label>
            Email
            <input type="email" name="email" required placeholder="you@example.com" value="<?= esc($email ?? ''); ?>">
        </label>

        <button type="submit">Resend verification email</button>
    </form>

    <p class="muted" style="margin-top: 1rem;">
        Having trouble? Contact support at <a href="mailto:<?= esc(SITE_EMAIL); ?>"><?= esc(SITE_EMAIL); ?></a>.
    </p>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('resend-verification-form');
    var messageBox = document.getElementById('resend-message');
    if (!form || !messageBox) {
        return;
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        messageBox.innerHTML = '';

        var formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                var isSuccess = data && data.success;
                var text = data && data.message ? data.message : 'Request completed.';
                var color = isSuccess ? '#3c3' : '#c33';
                var background = isSuccess ? '#efe' : '#fee';
                messageBox.innerHTML = '<div class="card" style="background-color: ' + background + '; border-left: 4px solid ' + color + '; padding: 1rem; margin-bottom: 1rem;"><p class="muted" style="color: ' + color + ';">' + text + '</p></div>';

                if (data && data.csrf_token) {
                    var tokenInput = form.querySelector('input[name="csrf_token"]');
                    if (tokenInput) {
                        tokenInput.value = data.csrf_token;
                    }
                }
            })
            .catch(function () {
                messageBox.innerHTML = '<div class="card" style="background-color: #fee; border-left: 4px solid #c33; padding: 1rem; margin-bottom: 1rem;"><p class="muted" style="color: #c33;">Unable to send request. Please try again.</p></div>';
            });
    });
});
</script>
