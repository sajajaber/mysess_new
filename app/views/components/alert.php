<?php
$alertMessage = null;
$alertType = 'info';

if (!empty($errors) && is_array($errors)) {
    $alertMessage = implode(' ', array_map('esc', $errors));
    $alertType = 'danger';
} elseif (!empty($_SESSION['login_error'])) {
    $alertMessage = $_SESSION['login_error'];
    $alertType = 'danger';
    unset($_SESSION['login_error']);
} elseif (!empty($_SESSION['medication_success'])) {
    $alertMessage = $_SESSION['medication_success'];
    $alertType = 'success';
    unset($_SESSION['medication_success']);
} elseif (!empty($_SESSION['medication_error'])) {
    $alertMessage = $_SESSION['medication_error'];
    $alertType = 'danger';
    unset($_SESSION['medication_error']);
}

if ($alertMessage): ?>
<div class="alert alert-<?= esc($alertType) ?>">
  <?= $alertMessage ?>
</div>
<?php endif; ?>
