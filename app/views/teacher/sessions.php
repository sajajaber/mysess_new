<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'Sessions';
$pageHeading = 'Classroom Sessions';
$activePage  = 'sessions';

$topbarActions = '
  <a href="' . ROOT . '/teacher/students"><button class="btn btn-primary">My Students</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

?>

<?php
$data = $sessions ?? [];

$headers = ['Student', 'Date', 'Subject', 'Notes'];

$renderRow = function ($session) {
  ob_start();
?>
  <tr>
    <td>
      <a href="<?= ROOT ?>/teacher/student/<?= (int)$session->student_id ?>" class="student-name">
        <?= esc(($session->student_first_name ?? '') . ' ' . ($session->student_last_name ?? '')) ?>
      </a>
    </td>
    <td><?= date('d M Y', strtotime($session->session_date)) ?></td>
    <td><strong><?= esc($session->subject) ?></strong></td>
    <td><?= esc($session->notes ?? '—') ?></td>
  </tr>
<?php
  return ob_get_clean();
};

$emptyMessage = 'No classroom sessions recorded yet.';

require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
