<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Broadcast';
$pageHeading = 'Create Broadcast';
$activePage  = 'messages';

$topbarActions = '
    <a href="' . ROOT . '/messages">
        <button class="btn btn-primary">← Back</button>
    </a>
';

$_SESSION['role'] === 'admin'
  ? require_once __DIR__ . '/../layouts/admin_header.php'
  : require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

$allRoles = $allRoles ?? [];
$old      = $old ?? [];

$selectedRoles = $old['target_roles'] ?? [];

?>

<div class="card">

  <div class="card-header">
    <h2>New Broadcast</h2>
  </div>

  <div class="card-body">

    <form method="POST" action="<?= ROOT ?>/messages/broadcast">

      <div class="form-group">
        <label>Target Roles *</label>

        <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:10px;">

          <?php foreach ($allRoles as $role): ?>

            <label style="display:flex;align-items:center;gap:6px;">
              <input type="checkbox"
                name="target_roles[]"
                value="<?= $role ?>"
                <?= in_array($role, $selectedRoles) ? 'checked' : '' ?>>

              <span class="role-badge role-<?= esc($role) ?>">
                <?= ucfirst(str_replace('_', ' ', $role)) ?>
              </span>
            </label>

          <?php endforeach; ?>

        </div>
      </div>

      <div class="form-group">
        <label>Subject *</label>
        <input type="text"
          name="subject"
          required
          value="<?= esc($old['subject'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Message *</label>
        <textarea name="body" rows="8" required><?= esc($old['body'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Send Broadcast</button>
        <a href="<?= ROOT ?>/messages" class="btn">Cancel</a>
      </div>

    </form>

  </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>