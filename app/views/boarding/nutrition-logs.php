<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'Nutrition Logs';
$pageHeading = 'Nutrition Logs';
$activePage  = 'nutrition';

$topbarActions = '
  <a href="' . ROOT . '/boarding/students"><button class="btn btn-primary">Students</button></a>
';

require_once __DIR__ . '/../layouts/boarding_header.php';
require_once __DIR__ . '/../components/alert.php';

$logs = $logs ?? [];
$colors = ['good' => '#16a34a', 'fair' => '#d97706', 'poor' => '#dc2626', 'refused' => '#7f1d1d'];

$data    = $logs;
$headers = ['Student', 'Date', 'Appetite', 'Notes'];
$renderRow = function ($l) use ($colors) { ob_start();
  $a = $l->appetite_level;
  $color = $a && isset($colors[$a]) ? $colors[$a] : '#94a3b8';
?>
  <tr>
    <td><?= esc(($l->student_first_name ?? '') . ' ' . ($l->student_last_name ?? '')) ?></td>
    <td><?= date('d M Y', strtotime($l->log_date)) ?></td>
    <td>
      <?php if ($a): ?>
        <span style="background:<?= $color ?>; color:#fff; padding:2px 10px; border-radius:999px; font-size:12px;"><?= ucfirst(esc($a)) ?></span>
      <?php else: ?>
        <span class="muted">—</span>
      <?php endif; ?>
    </td>
    <td><?= esc($l->description) ?></td>
  </tr>
<?php return ob_get_clean(); };

$emptyMessage = 'No nutrition logs yet.';
require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
