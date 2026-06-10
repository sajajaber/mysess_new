<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'Homework';
$pageHeading = 'Homework';
$activePage  = 'homework';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

?>

<?php
$data = $homework ?? [];

$headers = ['Student', 'Title', 'Description', 'Due Date', 'Assigned By'];

$renderRow = function ($row) {
  ob_start();
?>
  <tr>
    <td><?= esc(($row->student_first_name ?? '') . ' ' . ($row->student_last_name ?? '')) ?></td>
    <td><strong><?= esc($row->title) ?></strong></td>
    <td><?= esc($row->description ?: '—') ?></td>
    <td><?= date('d M Y', strtotime($row->due_date)) ?></td>
    <td><?= esc(trim(($row->teacher_first_name ?? '') . ' ' . ($row->teacher_last_name ?? '')) ?: '—') ?></td>
  </tr>
<?php
  return ob_get_clean();
};

$emptyMessage = 'No homework for your boarding students yet.';

require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
