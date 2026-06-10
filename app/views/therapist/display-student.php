<?php

require_once __DIR__ . '/../../core/config.php';

$student = $student ?? (object) [
  'id'            => 0,
  'first_name'    => '',
  'last_name'     => '',
  'date_of_birth' => '',
  'gender'        => '',
  'diagnosis'     => ''
];

$pageTitle   = trim($student->first_name . ' ' . $student->last_name) ?: 'Student Profile';
$pageHeading = trim($student->first_name . ' ' . $student->last_name) ?: 'Student Profile';
$activePage  = 'students';

$topbarActions = '
  <a href="' . ROOT . '/therapist/students"><button class="btn btn-primary">Back to Students</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';
?>

<!-- Student header card -->
<div class="profile-card">
  <div class="profile-card__initials">
    <?= strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) ?>
  </div>

  <div class="profile-card__info">
    <h2 class="profile-card__name"><?= esc($student->first_name . ' ' . $student->last_name) ?></h2>
    <div class="profile-card__meta">
      <span class="profile-meta-item">
        <span class="profile-meta-label">Date of Birth</span>
        <span class="profile-meta-value"><?= date('d M Y', strtotime($student->date_of_birth)) ?></span>
      </span>
      <span class="profile-meta-divider">|</span>
      <span class="profile-meta-item">
        <span class="profile-meta-label">Gender</span>
        <span class="profile-meta-value"><?= ucfirst($student->gender) ?></span>
      </span>
      <?php if ($student->diagnosis): ?>
        <span class="profile-meta-divider">|</span>
        <span class="profile-meta-item">
          <span class="profile-meta-label">Diagnosis</span>
          <span class="profile-meta-value diagnosis-badge"><?= esc($student->diagnosis) ?></span>
        </span>
      <?php endif; ?>
    </div>
  </div>

  <div class="profile-card__actions">
    <a href="<?= ROOT ?>/therapist/schedule-session?student_id=<?= $student->id ?>" class="btn btn-primary">Schedule Session</a>
    <a href="<?= ROOT ?>/therapist/add-iep-goal?student_id=<?= $student->id ?>" class="btn btn-warning">Add IEP Goal</a>
  </div>
</div>

<!-- Section tabs -->
<div class="section-tabs">
  <button class="section-tab active" data-target="sec-sessions">Therapy Sessions</button>
  <button class="section-tab" data-target="sec-iep">IEP Goals &amp; Progress</button>
  <button class="section-tab" data-target="sec-teacch">TEACCH</button>
</div>

<!-- Therapy Sessions -->
<div id="sec-sessions" class="section-panel active">
<?php
$data    = $sessions ?? [];
$headers = ['Date', 'Type', 'Status', 'Performance Notes', 'Goal Addressed', 'Actions'];
$renderRow = function ($session) { ob_start(); ?>
  <tr>
    <td><?= date('d M Y', strtotime($session->session_date)) ?></td>
    <td><strong><?= esc($session->session_type) ?></strong></td>
    <td><?= ucfirst(esc($session->status)) ?></td>
    <td><?= esc($session->performance_notes ?? '—') ?></td>
    <td><?= esc($session->goal_addressed ?? '—') ?></td>
    <td class="actions">
      <?php if ($session->status === 'scheduled'): ?>
        <a href="<?= ROOT ?>/therapist/document-session?session_id=<?= $session->id ?>" class="btn btn-sm btn-primary">Document</a>
      <?php endif; ?>
    </td>
  </tr>
<?php return ob_get_clean(); };
$emptyMessage = 'No therapy sessions yet.';
require __DIR__ . '/../components/data_table.php';
?>
</div>

<!-- IEP Goals & Progress -->
<div id="sec-iep" class="section-panel">

<?php
$data    = $iepGoals ?? [];
$headers = ['Goal', 'Status', 'Actions'];
$renderRow = function ($goal) { ob_start(); ?>
  <tr>
    <td><?= esc($goal->goal_description) ?></td>
    <td><?= ucfirst(esc($goal->status)) ?></td>
    <td class="actions">
      <a href="<?= ROOT ?>/therapist/goal/<?= $goal->id ?>" class="btn btn-sm btn-primary">Open Goal</a>
    </td>
  </tr>
<?php return ob_get_clean(); };
$emptyMessage = 'No IEP goals yet.';
require __DIR__ . '/../components/data_table.php';
?>
</div>

<div id="sec-teacch" class="section-panel">
<?php
$data    = $schedules ?? [];
$headers = ['Schedule', 'Actions'];
$renderRow = function ($schedule) { ob_start(); ?>
  <tr>
    <td><?= esc($schedule->title) ?></td>
    <td class="actions">
      <a href="<?= ROOT ?>/therapist/schedule/<?= $schedule->id ?>" class="btn btn-sm btn-primary">Open Schedule</a>
    </td>
  </tr>
<?php return ob_get_clean(); };
$emptyMessage = 'No schedules yet. Create one below.';
require __DIR__ . '/../components/data_table.php';
?>

  <!-- Create a new schedule for this student -->
  <form method="POST" action="<?= ROOT ?>/therapist/add-schedule" style="margin-top:16px;">
    <input type="hidden" name="student_id" value="<?= (int)$student->id ?>">
    <div class="form-group">
      <label for="schedule_title">New Schedule Title <span style="color:#eb004e">*</span></label>
      <input type="text" id="schedule_title" name="title" placeholder="e.g. Morning Routine" required>
    </div>
    <button type="submit" class="btn btn-primary">Create Schedule</button>
  </form>
</div>

<script src="<?= ROOT ?>/public/assets/js/display-student.js"></script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
