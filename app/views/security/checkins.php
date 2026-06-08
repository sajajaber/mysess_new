<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'Check-ins by Day';
$pageHeading = 'Check-ins by Day';
$activePage  = 'check ins';

$topbarActions = '
  <a href="' . ROOT . '/security/dashboard"><button class="btn btn-primary">Back to Attendance</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

$date    = $date    ?? date('Y-m-d');
$records = $records ?? [];
$noteMap = $noteMap ?? [];
?>

<div class="card" style="margin-bottom:14px;">
  <div class="card-body">
    <form method="GET" action="<?= ROOT ?>/security/checkins" style="display:flex; gap:10px; align-items:flex-end;">
      <div class="form-group" style="margin:0;">
        <label for="date" style="display:block; font-size:12px; color:#64748b;">Pick a date</label>
        <input type="date" id="date" name="date" value="<?= esc($date) ?>" required>
      </div>
      <button type="submit" class="btn btn-primary">View</button>
    </form>
  </div>
</div>


<?php
$data    = $records ?? [];
$headers = ['Student', 'Diagnosis', 'Check In', 'Check Out', 'Note'];

$renderRow = function ($row) use ($noteMap) { ob_start(); ?>
  <tr>
    <td><?= esc(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')) ?></td>
    <td><?= esc($row->diagnosis ?: '—') ?></td>
    <td>
      <?php if ($row->check_in_time): ?>
        <span style="color:#16a34a; font-weight:600;"><?= date('H:i', strtotime($row->check_in_time)) ?></span>
      <?php else: ?>
        <span style="color:#94a3b8;">—</span>
      <?php endif; ?>
    </td>
    <td>
      <?php if ($row->check_out_time): ?>
        <span style="color:#dc2626; font-weight:600;"><?= date('H:i', strtotime($row->check_out_time)) ?></span>
      <?php else: ?>
        <span style="color:#94a3b8;">—</span>
      <?php endif; ?>
    </td>
    <td><?= esc($noteMap[$row->student_id] ?? '—') ?></td>
  </tr>
<?php return ob_get_clean(); };

$emptyMessage = 'No check-ins or check-outs recorded on this date.';
require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
