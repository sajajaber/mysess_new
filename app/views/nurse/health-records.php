<?php
// LOAD CONFIG FIRST - BEFORE ANYTHING ELSE
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle    = 'Health Records';
$pageHeading  = 'Health Records';
$activePage   = 'health-records';
$topbarActions = '<a href="' . ROOT . '/nurse/add_health_record"><button class="btn btn-primary">Add Record</button></a>';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

// All variables passed from NurseController::health_records()
$healthRecords = $healthRecords ?? [];
?>

<!-- Health Records Table -->
<?php
$tableData = [];
foreach ($healthRecords as $record) {
    $tableData[] = [
        ($record->student_first_name ?? '') . ' ' . ($record->student_last_name ?? ''),
        ucfirst($record->record_type ?? ''),
        $record->title ?? '',
        substr($record->description ?? '', 0, 50),
        ($record->recorded_by_first_name ?? '') . ' ' . ($record->recorded_by_last_name ?? ''),
        date('d-m-Y', strtotime($record->recorded_at ?? 'now'))
    ];
}

renderTable(
    'All Health Records',
    ['Student', 'Type', 'Title', 'Description', 'Recorded By', 'Date'],
    $tableData
);
?>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
