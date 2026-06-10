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
  <a href="' . ROOT . '/teacher/students"><button class="btn btn-primary">Back to Students</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';
?>


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
    <a href="<?= ROOT ?>/teacher/add-session?student_id=<?= $student->id ?>" class="btn btn-primary">Add Session</a>
    <a href="<?= ROOT ?>/teacher/add-observation?student_id=<?= $student->id ?>" class="btn btn-info">Add Observation</a>
    <a href="<?= ROOT ?>/teacher/add-iep-goal?student_id=<?= $student->id ?>" class="btn btn-warning">Add IEP Goal</a>
    <a href="<?= ROOT ?>/teacher/add-progress-report?student_id=<?= $student->id ?>" class="btn btn-info">Add Report</a>
  </div>
</div>


<div class="section-tabs">
  <button class="section-tab active" data-target="sec-sessions">Classroom Sessions</button>
  <button class="section-tab" data-target="sec-observations">Academic Observations</button>
  <button class="section-tab" data-target="sec-iep">IEP Goals &amp; Progress</button>
  <button class="section-tab" data-target="sec-reports">Progress Reports</button>
  <button class="section-tab" data-target="sec-teacch">TEACCH</button>
  <button class="section-tab" data-target="sec-homework">Homework</button>
</div>


<div id="sec-sessions" class="section-panel active">
<?php
$data    = $sessions ?? [];
$headers = ['Date', 'Subject', 'Notes'];
$renderRow = function ($session) { ob_start(); ?>
  <tr>
    <td><?= date('d M Y', strtotime($session->session_date)) ?></td>
    <td><strong><?= esc($session->subject) ?></strong></td>
    <td><?= esc($session->notes ?? '—') ?></td>
  </tr>
<?php return ob_get_clean(); };
$emptyMessage = 'No classroom sessions yet.';
require __DIR__ . '/../components/data_table.php';
?>
</div>


<div id="sec-observations" class="section-panel">
<?php
$data    = $observations ?? [];
$headers = ['Observation', 'Date'];
$renderRow = function ($obs) { ob_start(); ?>
  <tr>
    <td><?= esc($obs->observation) ?></td>
    <td><?= date('d M Y', strtotime($obs->created_at)) ?></td>
  </tr>
<?php return ob_get_clean(); };
$emptyMessage = 'No academic observations yet.';
require __DIR__ . '/../components/data_table.php';
?>
</div>


<div id="sec-iep" class="section-panel">

<?php
$data    = $iepGoals ?? [];
$headers = ['Goal', 'Status', 'Actions'];
$renderRow = function ($goal) { ob_start(); ?>
  <tr>
    <td><?= esc($goal->goal_description) ?></td>
    <td><?= ucfirst(esc($goal->status)) ?></td>
    <td class="actions">
      <a href="<?= ROOT ?>/teacher/goal/<?= $goal->id ?>" class="btn btn-sm btn-primary">Open Goal</a>
    </td>
  </tr>
<?php return ob_get_clean(); };
$emptyMessage = 'No IEP goals yet.';
require __DIR__ . '/../components/data_table.php';
?>
</div>

<div id="sec-reports" class="section-panel">
<?php
$data    = $reports ?? [];
$headers = ['Period', 'Rating', 'Summary', 'Date'];
$renderRow = function ($report) { ob_start(); ?>
  <tr>
    <td><?= esc($report->reporting_period) ?></td>
    <td><?= ucfirst(esc($report->rating)) ?></td>
    <td><?= esc($report->summary) ?></td>
    <td><?= date('d M Y', strtotime($report->created_at)) ?></td>
  </tr>
<?php return ob_get_clean(); };
$emptyMessage = 'No progress reports yet.';
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
      <a href="<?= ROOT ?>/teacher/schedule/<?= $schedule->id ?>" class="btn btn-sm btn-primary">Open Schedule</a>
    </td>
  </tr>
<?php return ob_get_clean(); };
$emptyMessage = 'No schedules yet. Create one below.';
require __DIR__ . '/../components/data_table.php';
?>


  <form method="POST" action="<?= ROOT ?>/teacher/add-schedule" style="margin-top:16px;">
    <input type="hidden" name="student_id" value="<?= (int)$student->id ?>">
    <div class="form-group">
      <label for="schedule_title">New Schedule Title <span style="color:#eb004e">*</span></label>
      <input type="text" id="schedule_title" name="title" placeholder="e.g. Morning Routine" required>
    </div>
    <button type="submit" class="btn btn-primary">Create Schedule</button>
  </form>
</div>


<div id="sec-homework" class="section-panel">
<?php
$data    = $homework ?? [];
$headers = ['Title', 'Description', 'Due Date'];
$renderRow = function ($row) { ob_start(); ?>
  <tr>
    <td><strong><?= esc($row->title) ?></strong></td>
    <td><?= esc($row->description ?: '—') ?></td>
    <td><?= date('d M Y', strtotime($row->due_date)) ?></td>
  </tr>
<?php return ob_get_clean(); };
$emptyMessage = 'No homework yet for this student.';
require __DIR__ . '/../components/data_table.php';
?>


  <a href="<?= ROOT ?>/teacher/assign-homework" class="btn btn-primary" style="margin-top:16px;">Assign Homework</a>
</div>

<script src="<?= ROOT ?>/public/assets/js/display-student.js"></script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
