<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'Progress Reports';
$pageHeading = 'Progress Reports';
$activePage  = 'progress reports';

$topbarActions = '
  <a href="' . ROOT . '/teacher/semester-report"><button class="btn btn-primary">Generate Semester Report</button></a>
  <a href="' . ROOT . '/teacher/students"><button class="btn">My Students</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

?>

<?php
$data = $reports ?? [];

$headers = ['Student', 'Period', 'Rating', 'Summary', 'Date'];

$renderRow = function ($report) {
  ob_start();
?>
  <tr>
    <td><?= esc(($report->student_first_name ?? '') . ' ' . ($report->student_last_name ?? '')) ?></td>
    <td><?= esc($report->reporting_period) ?></td>
    <td><?= ucfirst(esc($report->rating)) ?></td>
    <td><?= esc($report->summary) ?></td>
    <td><?= date('d M Y', strtotime($report->created_at)) ?></td>
  </tr>
<?php
  return ob_get_clean();
};

$emptyMessage = 'You have not submitted any progress reports yet.';

require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
