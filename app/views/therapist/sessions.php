<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'Sessions';
$pageHeading = 'Therapy Sessions';
$activePage  = 'sessions';

$topbarActions = '
  <a href="' . ROOT . '/therapist/students"><button class="btn btn-primary">My Students</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

?>

<?php
$data = $sessions ?? [];

$headers = ['Student', 'Date', 'Type', 'Status', 'Goal Addressed'];

$renderRow = function ($session) {
  ob_start();
?>
  <tr>
    <td>
      <a href="<?= ROOT ?>/therapist/student/<?= (int)$session->student_id ?>" class="student-name">
        <?= esc(($session->student_first_name ?? '') . ' ' . ($session->student_last_name ?? '')) ?>
      </a>
    </td>
    <td><?= date('d M Y', strtotime($session->session_date)) ?></td>
    <td><strong><?= esc($session->session_type) ?></strong></td>
    <td><?= ucfirst(esc($session->status)) ?></td>
    <td><?= esc($session->goal_addressed ?? '—') ?></td>
  </tr>
<?php
  return ob_get_clean();
};

$emptyMessage = 'No therapy sessions recorded yet.';

require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
