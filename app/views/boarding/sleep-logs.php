<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'Sleep Logs';
$pageHeading = 'Sleep Logs';
$activePage  = 'sleep';

$topbarActions = '
  <a href="' . ROOT . '/boarding/students"><button class="btn btn-primary">Students</button></a>
';

require_once __DIR__ . '/../layouts/boarding_header.php';
require_once __DIR__ . '/../components/alert.php';

$logs = $logs ?? [];
$colors = ['good' => '#16a34a', 'fair' => '#d97706', 'poor' => '#dc2626'];

$data    = $logs;
$headers = ['Student', 'Date', 'Quality', 'Bedtime', 'Wake-up', 'Notes'];
$renderRow = function ($l) use ($colors) { ob_start();
  $q = $l->sleep_quality;
  $color = $q && isset($colors[$q]) ? $colors[$q] : '#94a3b8';
?>
  <tr>
    <td><?= esc(($l->student_first_name ?? '') . ' ' . ($l->student_last_name ?? '')) ?></td>
    <td><?= date('d M Y', strtotime($l->log_date)) ?></td>
    <td>
      <?php if ($q): ?>
        <span style="background:<?= $color ?>; color:#fff; padding:2px 10px; border-radius:999px; font-size:12px;"><?= ucfirst(esc($q)) ?></span>
      <?php else: ?>
        <span class="muted">—</span>
      <?php endif; ?>
    </td>
    <td><?= $l->bedtime     ? date('H:i', strtotime($l->bedtime))     : '—' ?></td>
    <td><?= $l->wakeup_time ? date('H:i', strtotime($l->wakeup_time)) : '—' ?></td>
    <td><?= esc($l->description) ?></td>
  </tr>
<?php return ob_get_clean(); };

$emptyMessage = 'No sleep logs yet.';
require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
