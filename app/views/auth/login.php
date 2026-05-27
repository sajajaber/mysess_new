<?php require_once __DIR__ . '/../../core/config.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= ROOT ?>/public/assets/css/main.css">
</head>
<?php require __DIR__ . "/../../../public/assets/css/login.php" ?>
<body>

    <div class="login-wrapper">

        <div class="login-logo">
            <h1><?= APP_NAME ?></h1>
            <p>School Health &amp; Student Management</p>
        </div>

        <div class="login-card">
            <div class="login-card-header">
                <h2>Welcome back</h2>
                <p>Sign in to your account to continue</p>
            </div>
            <div class="login-card-body">

                <?php if (!empty($errors)): ?>
                    <ul class="error-list">
                        <?php foreach ($errors as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <form method="POST" action="<?= ROOT ?>/auth/login">

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="you@school.edu"
                            value="<?= esc($_POST['email'] ?? '') ?>" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn-login">Sign In</button>

                </form>

            </div>
        </div>

    </div>

</body>

</html>