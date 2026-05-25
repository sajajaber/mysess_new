<?php
$pageTitle = 'Login';
$pageHeading = 'Sign in to MySESS';
$activePage = 'login';
$topbarActions = '';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';
?>
<div class="auth-panel">
    <div class="auth-card">
        <h2><?= esc($pageHeading) ?></h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= ROOT ?>/auth/login" class="auth-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Sign In</button>
            </div>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
