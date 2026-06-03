<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle = 'Nurse Dashboard';
$pageHeading = 'Dashboard';
$activePage = 'dashboard';

$topbarActions = '
<a href="' . ROOT . '/nurse/students">
    <button class="btn btn-primary">View Students</button>
</a>';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

?>

<div class="stat-cards">
  <?php
    statCard($totalStudents ?? 0, 'Total Students', '#e1496a');
    statCard($medicationCount ?? 0, 'Total Medications', '#e1496a');
    statCard($studentsOnMeds ?? 0, 'Students on Medications', '#e1496a');
  ?>
</div>

<?php

$tableData = [];

foreach (($recentHealthRecords ?? []) as $record) {

    $tableData[] = [
        ($record->student_first_name ?? '') . ' ' . ($record->student_last_name ?? ''),
        ucfirst($record->record_type ?? ''),
        $record->title ?? '',
        substr($record->description ?? '', 0, 50),
        ($record->recorded_by_first_name ?? '') . ' ' . ($record->recorded_by_last_name ?? ''),
        !empty($record->recorded_at)
            ? date('d-m-Y', strtotime($record->recorded_at))
            : ''
    ];
}

renderTable(
    'Recent Health Records',
    ['Student', 'Type', 'Title', 'Description', 'Recorded By', 'Date'],
    $tableData,
    5
);

?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>