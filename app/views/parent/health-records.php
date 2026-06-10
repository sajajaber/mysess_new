<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Parent Health Records';
$pageHeading = 'Health Records';
$activePage  = 'health';

$topbarActions = '
<a href="' . ROOT . '/parent/dashboard">
  <button class="btn btn-primary">Back to Dashboard</button>
</a>';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

?>

<div class="stat-cards">
  <?php
    statCard($medicationCount ?? 0, 'Medication Entries', '#e1496a');
    statCard($checkInCount ?? 0, 'Health Check-ins', '#22c55e');
    statCard('0', 'Open Alerts', '#f59e0b');
  ?>
</div>

<?php

$supportNotes = $supportNotes ?? [];
$healthRows = [];

foreach ($supportNotes as $note) {
  $healthRows[] = [
    $note[0] ?? '',
    $note[1] ?? '',
    $note[2] ?? '',
  ];
}

renderTable(
  'Recent Health Notes',
  ['Date', 'Category', 'Summary'],
  $healthRows,
  5
);

?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>