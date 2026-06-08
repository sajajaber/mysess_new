<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Sessions';
$pageHeading = 'All Sessions';
$activePage  = 'sessions';

$topbarActions = '
  <a href="' . ROOT . '/admin/dashboard"><button class="btn btn-primary">← Back to Dashboard</button></a>
';

require_once __DIR__ . '/../layouts/admin_header.php';
require_once __DIR__ . '/../components/alert.php';

$classroomSessions = $classroomSessions ?? [];
$therapySessions   = $therapySessions ?? [];
?>

<div class="card" style="margin-bottom: 24px;">
  <div class="card-header">
    <h2>Classroom Sessions</h2>
    <span class="status-badge status-active"><?= count($classroomSessions) ?> records</span>
  </div>
  <div class="card-body">
    <?php
    $data = $classroomSessions;
    $headers = ['Student', 'Teacher', 'Date', 'Subject', 'Notes'];
    $renderRow = function ($session) {
      ob_start();
    ?>
      <tr>
        <td>
          <a href="<?= ROOT ?>/admin/view_student/<?= (int)$session->student_id ?>" class="student-name">
            <?= esc(($session->student_first_name ?? '') . ' ' . ($session->student_last_name ?? '')) ?>
          </a>
        </td>
        <td><?= esc(($session->teacher_first_name ?? '') . ' ' . ($session->teacher_last_name ?? '')) ?></td>
        <td><?= date('d M Y', strtotime($session->session_date)) ?></td>
        <td><strong><?= esc($session->subject ?? '—') ?></strong></td>
        <td><?= esc($session->notes ?? '—') ?></td>
      </tr>
    <?php
      return ob_get_clean();
    };

    $emptyMessage = 'No classroom sessions found.';
    require __DIR__ . '/../components/data_table.php';
    ?>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h2>Therapy Sessions</h2>
    <span class="status-badge status-active"><?= count($therapySessions) ?> records</span>
  </div>
  <div class="card-body">
    <?php
    $data = $therapySessions;
    $headers = ['Student', 'Therapist', 'Date', 'Type', 'Status', 'Goal Addressed'];
    $renderRow = function ($session) {
      ob_start();
    ?>
      <tr>
        <td>
          <a href="<?= ROOT ?>/admin/view_student/<?= (int)$session->student_id ?>" class="student-name">
            <?= esc(($session->student_first_name ?? '') . ' ' . ($session->student_last_name ?? '')) ?>
          </a>
        </td>
        <td><?= esc(($session->therapist_first_name ?? '') . ' ' . ($session->therapist_last_name ?? '')) ?></td>
        <td><?= date('d M Y', strtotime($session->session_date)) ?></td>
        <td><strong><?= esc($session->session_type ?? '—') ?></strong></td>
        <td><?= esc($session->status ?? '—') ?></td>
        <td><?= esc($session->goal_addressed ?? '—') ?></td>
      </tr>
    <?php
      return ob_get_clean();
    };

    $emptyMessage = 'No therapy sessions found.';
    require __DIR__ . '/../components/data_table.php';
    ?>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
