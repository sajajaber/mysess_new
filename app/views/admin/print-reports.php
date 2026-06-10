<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$bundle     = $bundle     ?? [];
$preparedBy = $preparedBy ?? '';

$pageTitle   = 'Print Reports';
$pageHeading = 'Print Reports';
$activePage  = 'student reports';

$topbarActions = '
  <a href="' . ROOT . '/admin/student_reports"><button class="btn btn-primary">Back</button></a>
  <button class="btn" onclick="window.print()">Print / Save PDF</button>
';

require_once __DIR__ . '/../layouts/admin_header.php';

if (empty($bundle)) {
  echo "<div class='empty-state'>No students to print.</div>";
  require_once __DIR__ . '/../layouts/footer.php';
  exit();
}

foreach ($bundle as $i => $item) {
  $reportData    = $item['reportData'];
  $boardingStats = $item['boardingStats'];
  if ($i > 0) {
    echo '<div style="page-break-before:always;"></div>';
  }
  require __DIR__ . '/../components/student_report_body.php';
}

require_once __DIR__ . '/../layouts/footer.php';
