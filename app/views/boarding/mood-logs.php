<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'Mood Logs';
$pageHeading = 'Mood Logs';
$activePage  = 'mood';

$topbarActions = '
  <a href="' . ROOT . '/boarding/students"><button class="btn btn-primary">Students</button></a>
';

require_once __DIR__ . '/../layouts/boarding_header.php';
require_once __DIR__ . '/../components/alert.php';

$logs = $logs ?? [];
$colors = ['happy' => '#16a34a', 'calm' => '#0ea5e9', 'anxious' => '#d97706', 'upset' => '#dc2626', 'other' => '#64748b'];

$data    = $logs;
$headers = ['Student', 'Date', 'Mood', 'Notes'];
$renderRow = function ($l) use ($colors) { ob_start();
  $m = $l->mood_indicator;
  $color = $m && isset($colors[$m]) ? $colors[$m] : '#94a3b8';
?>
  <tr>
    <td><?= esc(($l->student_first_name ?? '') . ' ' . ($l->student_last_name ?? '')) ?></td>
    <td><?= date('d M Y', strtotime($l->log_date)) ?></td>
    <td>
      <?php if ($m): ?>
        <span style="background:<?= $color ?>; color:#fff; padding:2px 10px; border-radius:999px; font-size:12px;"><?= ucfirst(esc($m)) ?></span>
      <?php else: ?>
        <span class="muted">—</span>
      <?php endif; ?>
    </td>
    <td><?= esc($l->description) ?></td>
  </tr>
<?php return ob_get_clean(); };

$emptyMessage = 'No mood logs yet.';
require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
