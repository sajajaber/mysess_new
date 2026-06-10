<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'View Broadcast';
$pageHeading = 'Broadcast';
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

if (!isset($broadcast)) {
  echo "<div class='empty-state'>Broadcast not found.</div>";
  require_once __DIR__ . '/../layouts/footer.php';
  exit();
}

?>

<div class="card">

  <div class="card-header">
    <h2><?= esc($broadcast->subject) ?></h2>
  </div>

  <div class="card-body">

    <div style="margin-bottom:15px;">
      <strong>From:</strong>
      <?= esc($broadcast->sender_first . ' ' . $broadcast->sender_last) ?>
    </div>

    <div style="margin-bottom:15px;">
      <strong>Sent to:</strong>
      <?php foreach (explode(',', $broadcast->target_roles) as $role): ?>
        <span class="role-badge role-<?= esc(trim($role)) ?>">
          <?= ucfirst(str_replace('_', ' ', trim($role))) ?>
        </span>
      <?php endforeach; ?>
    </div>

    <div style="margin-bottom:15px;">
      <strong>Date:</strong>
      <?= date('d M Y, H:i', strtotime($broadcast->created_at)) ?>
    </div>

    <hr>

    <div style="white-space:pre-line; line-height:1.6;">
      <?= esc($broadcast->body) ?>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
