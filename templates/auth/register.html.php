<section class="container">
    <h1><?= esc($pageHeading ?? 'Register'); ?></h1>
    <?php if (!empty($error)): ?>
        <div class="card">
            <p class="muted"><?= esc($error); ?></p>
        </div>
    <?php endif; ?>
    <form class="card" method="post" action="">
        <label>
            Name
            <input type="text" name="name" required>
        </label>
        <label>
            Email
            <input type="email" name="email" required>
        </label>
        <label>
            Password
            <input type="password" name="password" required>
        </label>
        <button type="submit">Create Account</button>
    </form>
</section>
