<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'View Message';
$pageHeading = 'Message';
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

if (!isset($message)) {
  echo "<div class='empty-state'>Message not found.</div>";
  require_once __DIR__ . '/../layouts/footer.php';
  exit();
}

?>

<div class="card">

  <div class="card-header">
    <h2><?= esc($message->subject) ?></h2>
  </div>

  <div class="card-body">

    <div style="margin-bottom:15px;">
      <strong>From:</strong>
      <?= esc($message->sender_first . ' ' . $message->sender_last) ?>
      <span class="role-badge role-<?= esc($message->sender_role) ?>">
        <?= ucfirst(str_replace('_', ' ', $message->sender_role)) ?>
      </span>
    </div>

    <div style="margin-bottom:15px;">
      <strong>To:</strong>
      <?= esc($message->receiver_first . ' ' . $message->receiver_last) ?>
      <span class="role-badge role-<?= esc($message->receiver_role) ?>">
        <?= ucfirst(str_replace('_', ' ', $message->receiver_role)) ?>
      </span>
    </div>

    <div style="margin-bottom:15px;">
      <strong>Date:</strong>
      <?= date('d M Y, H:i', strtotime($message->created_at)) ?>
    </div>

    <hr>

    <div style="white-space:pre-line; line-height:1.6;">
      <?= esc($message->body) ?>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>