<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle    = 'Add User';
$pageHeading  = 'Add User';
$activePage   = 'users';
$topbarActions = '
    <a href="' . ROOT . '/admin/users"><button class="btn btn-primary">← Back to Users</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

require 'C:/xampp1/htdocs/mysess_new/public/assets/css/admin.php';

$roles = $roles ?? [];
$old   = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>

<div class="card">
  <div class="card-header">
    <h2>New User</h2>
  </div>
  <div class="card-body">
    <form method="POST" action="<?= ROOT ?>/admin/add_user">

      <div class="form-row">
        <div class="form-group">
          <label for="first_name">First Name *</label>
          <input type="text" id="first_name" name="first_name" required
            value="<?= esc($old['first_name'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label for="last_name">Last Name *</label>
          <input type="text" id="last_name" name="last_name" required
            value="<?= esc($old['last_name'] ?? '') ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="email">Email *</label>
          <input type="email" id="email" name="email" required
            value="<?= esc($old['email'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label for="phone">Phone</label>
          <input type="text" id="phone" name="phone"
            value="<?= esc($old['phone'] ?? '') ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="role">Role *</label>
          <select id="role" name="role" required>
            <option value="">Select Role</option>
            <?php foreach ($roles as $role): ?>
              <option value="<?= esc($role) ?>"
                <?= ($old['role'] ?? '') === $role ? 'selected' : '' ?>>
                <?= ucfirst(str_replace('_', ' ', $role)) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="password">Password *</label>
          <input type="password" id="password" name="password" required
            minlength="6" placeholder="Min. 6 characters">
        </div>
        <div class="form-group">
          <label for="password_confirm">Confirm Password *</label>
          <input type="password" id="password_confirm" name="password_confirm" required
            minlength="6" placeholder="Retype password">
        </div>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Add User</button>
        <a href="<?= ROOT ?>/admin/users" class="btn">Cancel</a>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>