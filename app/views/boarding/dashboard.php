<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Boarding Dashboard';
$pageHeading = 'Dashboard';
$activePage  = 'dashboard';

$topbarActions = '
  <a href="' . ROOT . '/boarding/students"><button class="btn btn-primary">View Students</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';
?>

<div class="stat-cards">
  <?php
    statCard($studentCount ?? 0, 'Boarding Students', '#4f46e5');
    statCard($logsToday ?? 0, 'Logs Today', '#16a34a');
  ?>
</div>

<!-- Recent daily logs -->
<div id="sec-logs">
<?php
$data    = $recentLogs ?? [];
$headers = ['Student', 'Date', 'Type', 'Description'];
$renderRow = function ($log) { ob_start(); ?>
  <tr>
    <td><?= esc(($log->student_first_name ?? '') . ' ' . ($log->student_last_name ?? '')) ?></td>
    <td><?= date('d M Y', strtotime($log->log_date)) ?></td>
    <td><?= ucwords(str_replace('_', ' ', esc($log->log_type))) ?></td>
    <td><?= esc($log->description) ?></td>
  </tr>
<?php return ob_get_clean(); };
$emptyMessage = 'No daily logs yet.';
require __DIR__ . '/../components/data_table.php';
?>
</div>

<!-- Recent check-ins / check-outs -->
<div id="sec-checkins" style="margin-top:24px;">
<?php
$data    = $recentCheck ?? [];
$headers = ['Student', 'Type', 'Time'];
$renderRow = function ($c) { ob_start(); ?>
  <tr>
    <td><?= esc(($c->student_first_name ?? '') . ' ' . ($c->student_last_name ?? '')) ?></td>
    <td><?= $c->check_type === 'check_in' ? 'Check In' : 'Check Out' ?></td>
    <td><?= date('d M Y H:i', strtotime($c->check_time)) ?></td>
  </tr>
<?php return ob_get_clean(); };
$emptyMessage = 'No check-ins yet.';
require __DIR__ . '/../components/data_table.php';
?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
