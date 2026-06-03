<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'My Homework';
$pageHeading = 'My Homework';
$activePage  = 'homework';

$topbarActions = '
  <a href="' . ROOT . '/teacher/assign-homework"><button class="btn btn-primary">Assign Homework</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

?>

<?php
$data = $homework ?? [];

$headers = ['Student', 'Title', 'Due Date'];

$renderRow = function ($row) {
  ob_start();
?>
  <tr>
    <td><?= esc(($row->student_first_name ?? '') . ' ' . ($row->student_last_name ?? '')) ?></td>
    <td><strong><?= esc($row->title) ?></strong></td>
    <td><?= date('d M Y', strtotime($row->due_date)) ?></td>
  </tr>
<?php
  return ob_get_clean();
};

$emptyMessage = 'You have not assigned any homework yet.';

require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
