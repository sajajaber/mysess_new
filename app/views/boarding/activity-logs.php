<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'Activity Logs';
$pageHeading = 'Activity Logs';
$activePage  = 'activity';

$topbarActions = '
  <a href="' . ROOT . '/boarding/students"><button class="btn btn-primary">Students</button></a>
';

require_once __DIR__ . '/../layouts/boarding_header.php';
require_once __DIR__ . '/../components/alert.php';

$logs = $logs ?? [];

$data    = $logs;
$headers = ['Student', 'Date', 'Description'];
$renderRow = function ($l) { ob_start(); ?>
  <tr>
    <td><?= esc(($l->student_first_name ?? '') . ' ' . ($l->student_last_name ?? '')) ?></td>
    <td><?= date('d M Y', strtotime($l->log_date)) ?></td>
    <td><?= esc($l->description) ?></td>
  </tr>
<?php return ob_get_clean(); };

$emptyMessage = 'No activity logs yet.';
require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
