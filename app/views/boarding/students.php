<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'Boarding Students';
$pageHeading = 'Boarding Students';
$activePage  = 'students';

$topbarActions = '
  <a href="' . ROOT . '/boarding/dashboard"><button class="btn btn-primary">Dashboard</button></a>
';

require_once __DIR__ . '/../layouts/boarding_header.php';
require_once __DIR__ . '/../components/alert.php';

?>

<?php
$data = $students ?? [];

$headers = ['Name', 'Date of Birth', 'Diagnosis', 'Actions'];

$renderRow = function ($student) {
  ob_start();
?>
  <tr>
    <td>
      <a href="<?= ROOT ?>/boarding/student/<?= (int)$student->id ?>" class="student-name">
        <?= esc($student->first_name . ' ' . $student->last_name) ?>
      </a>
    </td>
    <td><?= date('d-m-Y', strtotime($student->date_of_birth)) ?></td>
    <td><?= esc($student->diagnosis ?? '—') ?></td>
    <td class="actions">
      <a href="<?= ROOT ?>/boarding/student/<?= (int)$student->id ?>" class="btn btn-sm btn-primary">Profile</a>
      <a href="<?= ROOT ?>/boarding/add-log?student_id=<?= (int)$student->id ?>" class="btn btn-sm">Add Log</a>
      <a href="<?= ROOT ?>/boarding/add-checkin?student_id=<?= (int)$student->id ?>" class="btn btn-sm">Check In/Out</a>
    </td>
  </tr>
<?php
  return ob_get_clean();
};

$emptyMessage = 'No students found.';

require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
