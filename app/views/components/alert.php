<?php
// Success
if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?= esc($_SESSION['success']) ?>
    </div>
<?php unset($_SESSION['success']);
endif;

// Single error
if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-error">
        <?= esc($_SESSION['error']) ?>
    </div>
<?php unset($_SESSION['error']);
endif;

// Multiple errors
if (!empty($_SESSION['errors'])): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($_SESSION['errors'] as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php unset($_SESSION['errors']);
endif;
?>