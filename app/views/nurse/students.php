<?php
// LOAD CONFIG FIRST - BEFORE ANYTHING ELSE
require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'My Students';
$pageHeading = 'My Students';
$activePage  = 'students';

$topbarActions = '
<a href="' . ROOT . '/nurse/dashboard"><button class="btn btn-primary">Dashboard</button></a>
<a href="' . ROOT . '/nurse/all_medications"><button class="btn btn-primary">Medications</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

?>

<!-- Students Table -->
<?php
$data    = $students ?? [];
$headers = ['Name', 'Date of Birth', 'Diagnosis', 'Actions'];
$renderRow = function ($student) {
  ob_start();
?>
  <tr>
    <td>
      <div class="student-info">
        <div class="student-avatar-small">
          <!-- if we want to put a student photo, the code should be here, but we will forget about it for now -->
        </div>
        <a href="<?= ROOT ?>/nurse/student/<?= $student->id ?>" class="student-name">
          <?= esc($student->first_name . ' ' . $student->last_name) ?>
        </a>
      </div>
    </td>
    <td><?= date('d-m-Y', strtotime($student->date_of_birth)) ?></td>
    <td><?= esc($student->diagnosis ?? '—') ?></td>
    <td class="actions">
      <a href="<?= ROOT ?>/nurse/student/<?= $student->id ?>" class="btn-icon btn-view" title="View Profile">👤</a>
      <a href="<?= ROOT ?>/nurse/add_medication?student_id=<?= $student->id ?>" class="btn-icon btn-add" title="Add Medication">💊</a>
      <a href="<?= ROOT ?>/nurse/log_health_event?student_id=<?= $student->id ?>" class="btn-icon btn-event" title="Log Event">⚠️</a>
    </td>
  </tr>
<?php
  return ob_get_clean();
};

$emptyMessage = 'No students assigned to you yet.';

require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>