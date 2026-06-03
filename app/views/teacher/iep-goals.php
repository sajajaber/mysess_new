<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'IEP Goals';
$pageHeading = 'IEP Goals';
$activePage  = 'iep goals';

$topbarActions = '
  <a href="' . ROOT . '/teacher/students"><button class="btn btn-primary">My Students</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

?>

<?php
$data = $goals ?? [];

$headers = ['Student', 'Goal', 'Category', 'Status', 'Actions'];

$renderRow = function ($goal) {
  ob_start();
?>
  <tr>
    <td><?= esc(($goal->student_first_name ?? '') . ' ' . ($goal->student_last_name ?? '')) ?></td>
    <td><?= esc($goal->goal_description) ?></td>
    <td><?= esc($goal->category ?: 'General') ?></td>
    <td><?= ucfirst(esc($goal->status)) ?></td>
    <td class="actions">
      <a href="<?= ROOT ?>/teacher/goal/<?= (int)$goal->id ?>" class="btn btn-sm btn-primary">Open Goal</a>
    </td>
  </tr>
<?php
  return ob_get_clean();
};

$emptyMessage = 'No IEP goals yet. Open a student profile to add one.';

require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
