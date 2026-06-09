<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Parent Dashboard';
$pageHeading = 'Dashboard';
$activePage  = 'dashboard';

$topbarActions = '
<a href="' . ROOT . '/parent/academic-records">
  <button class="btn btn-primary">View Academic Records</button>
</a>';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

?>

<div class="stat-cards">
  <?php
    statCard($studentCount ?? 0, 'Student Profiles', '#2c7be5');
    statCard($upcomingChecks ?? 0, 'Upcoming Check-ins', '#16a34a');
    statCard($activeGoals ?? 0, 'Active Goals', '#f59e0b');
  ?>
</div>

<?php

$recentUpdates = $recentUpdates ?? [];
$updateRows = [];

foreach ($recentUpdates as $update) {
  $updateRows[] = [
    $update[0] ?? '',
    $update[1] ?? '',
    $update[2] ?? '',
  ];
}

renderTable(
  'Recent Updates',
  ['Area', 'Staff', 'Note'],
  $updateRows,
  5
);

?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>