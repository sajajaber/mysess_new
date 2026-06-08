<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'Daily Logs';
$pageHeading = 'Daily Logs';
$activePage  = 'daily logs';

$topbarActions = '
  <a href="' . ROOT . '/boarding/students"><button class="btn btn-primary">Students</button></a>
';

require_once __DIR__ . '/../layouts/boarding_header.php';
require_once __DIR__ . '/../components/alert.php';

?>

<?php
$data = $logs ?? [];

$headers = ['Student', 'Date', 'Type', 'Description'];

$renderRow = function ($log) {
  ob_start();
?>
  <tr>
    <td><?= esc(($log->student_first_name ?? '') . ' ' . ($log->student_last_name ?? '')) ?></td>
    <td><?= date('d M Y', strtotime($log->log_date)) ?></td>
    <td><?= ucwords(str_replace('_', ' ', esc($log->log_type))) ?></td>
    <td><?= esc($log->description) ?></td>
  </tr>
<?php
  return ob_get_clean();
};

$emptyMessage = 'No daily logs yet.';

require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
