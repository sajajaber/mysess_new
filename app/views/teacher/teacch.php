<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'TEACCH Schedules';
$pageHeading = 'TEACCH Schedules';
$activePage  = 'teacch';

$topbarActions = '
  <a href="' . ROOT . '/teacher/students"><button class="btn btn-primary">My Students</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

?>

<?php
$data = $schedules ?? [];

$headers = ['Student', 'Schedule', 'Created', 'Actions'];

$renderRow = function ($schedule) {
  ob_start();
?>
  <tr>
    <td><?= esc(($schedule->student_first_name ?? '') . ' ' . ($schedule->student_last_name ?? '')) ?></td>
    <td><?= esc($schedule->title) ?></td>
    <td><?= date('d M Y', strtotime($schedule->created_at)) ?></td>
    <td class="actions">
      <a href="<?= ROOT ?>/teacher/schedule/<?= (int)$schedule->id ?>" class="btn btn-sm btn-primary">Open Schedule</a>
    </td>
  </tr>
<?php
  return ob_get_clean();
};

$emptyMessage = 'No TEACCH schedules yet. Open a student profile to create one.';

require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
