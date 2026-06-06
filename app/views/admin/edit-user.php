<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$user = $user ?? null;
if (!$user) {
  header('Location: ' . ROOT . '/admin/users');
  exit();
}

$pageTitle    = 'Edit User';
$pageHeading  = 'Edit User';
$activePage   = 'users';
$topbarActions = '
    <a href="' . ROOT . '/admin/view_user/' . $user->id . '"><button class="btn btn-primary">← Back to Profile</button></a>
';

require_once __DIR__ . '/../layouts/admin_header.php';
require_once __DIR__ . '/../components/alert.php';
?>
<?php if (!empty($_SESSION['errors'])): ?>
    <?php foreach ($_SESSION['errors'] as $e): ?>
        <p style="color:red"><?= $e ?></p>
    <?php endforeach; ?>
    <?php unset($_SESSION['errors']); ?>
<?php endif; ?>
<?php

$roles = $roles ?? [];
$old   = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

// old values override user values on validation errors
$val = function ($field) use ($old, $user) {
  return $old[$field] ?? $user->$field ?? '';
};
?>

<div class="card">
  <div class="card-header">
    <h2>Edit — <?= esc($user->first_name . ' ' . $user->last_name) ?></h2>
  </div>
  <div class="card-body">
    <form method="POST" action="<?= ROOT ?>/admin/edit_user/<?= $user->id ?>">

      <div class="form-row">
        <div class="form-group">
          <label for="first_name">First Name *</label>
          <input type="text" id="first_name" name="first_name" required
            value="<?= esc($val('first_name')) ?>">
        </div>
        <div class="form-group">
          <label for="last_name">Last Name *</label>
          <input type="text" id="last_name" name="last_name" required
            value="<?= esc($val('last_name')) ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="email">Email *</label>
          <input type="email" id="email" name="email" required
            value="<?= esc($val('email')) ?>">
        </div>
        <div class="form-group">
          <label for="phone">Phone</label>
          <input type="text" id="phone" name="phone"
            value="<?= esc($val('phone')) ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="role">Role *</label>
          <select id="role" name="role" required>
            <option value="">Select Role</option>
            <?php foreach ($roles as $role): ?>
              <option value="<?= esc($role) ?>"
                <?= $val('role') === $role ? 'selected' : '' ?>>
                <?= ucfirst(str_replace('_', ' ', $role)) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Password section — leave blank to keep current password -->
      <div class="form-section-label">Change Password <span>— leave blank to keep current</span></div>

      <div class="form-row">
        <div class="form-group">
          <label for="password">New Password</label>
          <input type="password" id="password" name="password"
            minlength="6" placeholder="Min. 6 characters">
        </div>
        <div class="form-group">
          <label for="password_confirm">Confirm New Password</label>
          <input type="password" id="password_confirm" name="password_confirm"
            minlength="6" placeholder="Retype new password">
        </div>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="<?= ROOT ?>/app/views/admin/view-user/<?= $user->id ?>" class="btn">Cancel</a>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>