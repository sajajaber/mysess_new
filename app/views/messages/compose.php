<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle = 'Compose Message';
$pageHeading = 'Compose Message';
$activePage = 'messages';

$topbarActions = '
    <a href="' . ROOT . '/messages">
        <button class="btn btn-primary">← Back to Inbox</button>
    </a>
';

$_SESSION['role'] === 'admin'
  ? require_once __DIR__ . '/../layouts/admin_header.php'
  : require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

$recipients = $recipients ?? [];
$old = $old ?? [];
$preselect = $preselect ?? 0;

// Group recipients by role
$grouped = [];

foreach ($recipients as $recipient) {
  $grouped[$recipient->role][] = $recipient;
}

?>

<div class="card">

  <div class="card-header">
    <h2>New Message</h2>
  </div>

  <div class="card-body">

    <form method="POST" action="<?= ROOT ?>/messages/compose">

      <div class="form-group">
        <label for="receiver_id">To *</label>

        <select id="receiver_id" name="receiver_id" required>

          <option value="">
            — Select Recipient —
          </option>

          <?php foreach ($grouped as $role => $users): ?>

            <optgroup label="<?= ucfirst(str_replace('_', ' ', $role)) ?>">

              <?php foreach ($users as $user): ?>

                <option
                  value="<?= $user->id ?>"
                  <?= (($old['receiver_id'] ?? $preselect) == $user->id) ? 'selected' : '' ?>>
                  <?= esc($user->first_name . ' ' . $user->last_name) ?>
                </option>

              <?php endforeach; ?>

            </optgroup>

          <?php endforeach; ?>

        </select>

      </div>

      <div class="form-group">

        <label for="subject">Subject *</label>

        <input
          type="text"
          id="subject"
          name="subject"
          required
          value="<?= esc($old['subject'] ?? '') ?>">

      </div>

      <div class="form-group">

        <label for="body">Message *</label>

        <textarea
          id="body"
          name="body"
          rows="8"
          required><?= esc($old['body'] ?? '') ?></textarea>

      </div>

      <div class="form-group">

        <button type="submit" class="btn btn-primary">
          Send Message
        </button>

        <a href="<?= ROOT ?>/messages" class="btn">
          Cancel
        </a>

      </div>

    </form>

  </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>