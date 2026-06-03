<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'My Students';
$pageHeading = 'My Students';
$activePage  = 'students';

$topbarActions = '
<a href="' . ROOT . '/therapist/dashboard"><button class="btn btn-primary">Dashboard</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
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
      <div class="student-info">
        <a href="<?= ROOT ?>/therapist/student/<?= $student->id ?>" class="student-name">
          <?= esc($student->first_name . ' ' . $student->last_name) ?>
        </a>
      </div>
    </td>
    <td><?= date('d-m-Y', strtotime($student->date_of_birth)) ?></td>
    <td><?= esc($student->diagnosis ?? '—') ?></td>
    <td class="actions">
      <a href="<?= ROOT ?>/therapist/student/<?= $student->id ?>" class="btn btn-sm btn-primary">Profile</a>
      <a href="<?= ROOT ?>/therapist/schedule-session?student_id=<?= $student->id ?>" class="btn btn-sm">Schedule Session</a>
    </td>
  </tr>
<?php
  return ob_get_clean();
};

$emptyMessage = 'No students assigned to you yet.';

require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
