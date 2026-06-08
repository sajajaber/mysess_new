<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Messages';
$pageHeading = 'Messages';
$activePage  = 'messages';

$topbarActions =
  '<a href="' . ROOT . '/messages/compose">
        <button class="btn btn-primary">✉️ Compose</button>
     </a>'
  .
  (
    $_SESSION['role'] === 'admin'
    ? '<a href="' . ROOT . '/messages/broadcast">
                <button class="btn btn-primary">📢 Broadcast</button>
           </a>'
    : ''
  );

  $_SESSION['role'] === 'admin'
    ? require_once __DIR__ . '/../layouts/admin_header.php'
    : require_once __DIR__ . '/../layouts/header.php';

require_once __DIR__ . '/../components/alert.php';

$inbox      = $inbox ?? [];
$sent       = $sent ?? [];
$broadcasts = $broadcasts ?? [];
$unread     = $unread ?? 0;

?>

<div class="section-tabs" style="margin-bottom:20px;">

  <button class="section-tab active" data-target="sec-inbox">
    Inbox

    <?php if ($unread > 0): ?>
      <span class="tab-count" style="background:#ef4444;color:white;">
        <?= $unread ?>
      </span>
    <?php endif; ?>
  </button>

  <button class="section-tab" data-target="sec-sent">
    Sent
    <span class="tab-count"><?= count($sent) ?></span>
  </button>

  <?php if (!empty($broadcasts)): ?>
    <button class="section-tab" data-target="sec-broadcasts">
      Broadcasts
      <span class="tab-count"><?= count($broadcasts) ?></span>
    </button>
  <?php endif; ?>

</div>


<!-- INBOX -->

<div id="sec-inbox" class="section-panel active">

  <div class="card">

    <div class="card-header">
      <h2>Inbox</h2>
    </div>

    <div class="card-body">

      <?php if (empty($inbox)): ?>

        <div class="empty-state">
          No messages yet.
        </div>

      <?php else: ?>

        <table class="data-table">

          <thead>
            <tr>
              <th>From</th>
              <th>Subject</th>
              <th>Date</th>
              <th style="width:80px;"></th>
            </tr>
          </thead>

          <tbody>

            <?php foreach ($inbox as $msg): ?>

              <tr class="<?= !$msg->is_read ? 'msg-unread' : '' ?>">

                <td>
                  <span class="role-badge role-<?= esc($msg->sender_role) ?>">
                    <?= ucfirst(str_replace('_', ' ', $msg->sender_role)) ?>
                  </span>

                  <?= esc($msg->sender_first . ' ' . $msg->sender_last) ?>
                </td>

                <td>
                  <?php if (!$msg->is_read): ?>
                    <strong><?= esc($msg->subject) ?></strong>
                  <?php else: ?>
                    <?= esc($msg->subject) ?>
                  <?php endif; ?>
                </td>

                <td>
                  <?= date('d M Y, H:i', strtotime($msg->created_at)) ?>
                </td>

                <td>
                  <a href="<?= ROOT ?>/messages/view_message/<?= $msg->id ?>"
                    class="btn btn-sm btn-primary">
                    View
                  </a>
                </td>

              </tr>

            <?php endforeach; ?>

          </tbody>

        </table>

      <?php endif; ?>

    </div>

  </div>

</div>


<!-- SENT -->

<div id="sec-sent" class="section-panel">

  <div class="card">

    <div class="card-header">
      <h2>Sent</h2>
    </div>

    <div class="card-body">

      <?php if (empty($sent)): ?>

        <div class="empty-state">
          No sent messages.
        </div>

      <?php else: ?>

        <table class="data-table">

          <thead>
            <tr>
              <th>To</th>
              <th>Subject</th>
              <th>Date</th>
              <th style="width:80px;"></th>
            </tr>
          </thead>

          <tbody>

            <?php foreach ($sent as $msg): ?>

              <tr>

                <td>
                  <span class="role-badge role-<?= esc($msg->receiver_role) ?>">
                    <?= ucfirst(str_replace('_', ' ', $msg->receiver_role)) ?>
                  </span>

                  <?= esc($msg->receiver_first . ' ' . $msg->receiver_last) ?>
                </td>

                <td>
                  <?= esc($msg->subject) ?>
                </td>

                <td>
                  <?= date('d M Y, H:i', strtotime($msg->created_at)) ?>
                </td>

                <td>
                  <a href="<?= ROOT ?>/messages/view_message/<?= $msg->id ?>"
                    class="btn btn-sm">
                    View
                  </a>
                </td>

              </tr>

            <?php endforeach; ?>

          </tbody>

        </table>

      <?php endif; ?>

    </div>

  </div>

</div>


<!-- BROADCASTS -->

<?php if (!empty($broadcasts)): ?>
  <div id="sec-broadcasts" class="section-panel">
    <div class="card">
      <div class="card-header">
        <h2>Broadcasts</h2>
      </div>
      <div class="card-body">

        <table class="data-table">
          <thead>
            <tr>
              <th>From</th>
              <th>Subject</th>
              <th>Target Roles</th>
              <th>Date</th>
              <th style="width:80px;"></th>
            </tr>
          </thead>

          <tbody>

            <?php foreach ($broadcasts as $bc): ?>

              <tr>

                <td>
                  <?= esc($bc->sender_first . ' ' . $bc->sender_last) ?>
                </td>

                <td>
                  <?= esc($bc->subject) ?>
                </td>

                <td>

                  <?php foreach (explode(',', $bc->target_roles) as $role): ?>

                    <span class="role-badge role-<?= esc(trim($role)) ?>">
                      <?= ucfirst(str_replace('_', ' ', trim($role))) ?>
                    </span>

                  <?php endforeach; ?>

                </td>

                <td>
                  <?= date('d M Y, H:i', strtotime($bc->created_at)) ?>
                </td>

                <td>
                  <a href="<?= ROOT ?>/messages/broadcast_view/<?= $bc->id ?>"
                    class="btn btn-sm">
                    View
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

      </div>
    </div>
  </div>

<?php endif; ?>

<script src="<?= ROOT ?>/public/assets/js/display-student.js"></script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>