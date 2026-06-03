<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Teacher Dashboard';
$pageHeading = 'Dashboard';
$activePage  = 'dashboard';

$topbarActions = '
<a href="' . ROOT . '/teacher/students">
    <button class="btn btn-primary">View Students</button>
</a>';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

?>

<div class="stat-cards">
  <?php
    statCard($studentCount ?? 0, 'Total Students', '#333');
    statCard($sessionCount ?? 0, 'Classroom Sessions', '#333');
    statCard($reportCount  ?? 0, 'Progress Reports', '#333');
  ?>
</div>

<?php

$tableData = [];

foreach (($recentSessions ?? []) as $session) {
    $tableData[] = [
        ($session->student_first_name ?? '') . ' ' . ($session->student_last_name ?? ''),
        $session->subject ?? '',
        !empty($session->session_date)
            ? date('d-m-Y', strtotime($session->session_date))
            : '',
    ];
}

renderTable(
    'Recent Classroom Sessions',
    ['Student', 'Subject', 'Date'],
    $tableData,
    5
);

?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
