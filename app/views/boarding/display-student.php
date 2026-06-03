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
  <a href="' . ROOT . '/boarding/students"><button class="btn btn-primary">Back to Students</button></a>
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
    <a href="<?= ROOT ?>/boarding/add-log?student_id=<?= (int)$student->id ?>" class="btn btn-primary">Add Log</a>
    <a href="<?= ROOT ?>/boarding/add-checkin?student_id=<?= (int)$student->id ?>" class="btn btn-info">Check In/Out</a>
  </div>
</div>

<!-- Section tabs -->
<div class="section-tabs">
  <button class="section-tab active" data-target="sec-logs">Daily Logs</button>
  <button class="section-tab" data-target="sec-checkins">Check In/Out</button>
</div>

<!-- Daily Logs -->
<div id="sec-logs" class="section-panel active">
<?php
$data    = $logs ?? [];
$headers = ['Date', 'Type', 'Description', 'Mood', 'Appetite', 'Sleep'];
$renderRow = function ($log) { ob_start(); ?>
  <tr>
    <td><?= date('d M Y', strtotime($log->log_date)) ?></td>
    <td><?= ucwords(str_replace('_', ' ', esc($log->log_type))) ?></td>
    <td><?= esc($log->description) ?></td>
    <td><?= $log->mood_indicator ? ucfirst(esc($log->mood_indicator)) : '—' ?></td>
    <td><?= $log->appetite_level ? ucfirst(esc($log->appetite_level)) : '—' ?></td>
    <td><?= $log->sleep_quality ? ucfirst(esc($log->sleep_quality)) : '—' ?></td>
  </tr>
<?php return ob_get_clean(); };
$emptyMessage = 'No daily logs yet for this student.';
require __DIR__ . '/../components/data_table.php';
?>
</div>

<!-- Check In/Out -->
<div id="sec-checkins" class="section-panel">
<?php
$data    = $checkins ?? [];
$headers = ['Type', 'Time', 'Notes'];
$renderRow = function ($c) { ob_start(); ?>
  <tr>
    <td><?= $c->check_type === 'check_in' ? 'Check In' : 'Check Out' ?></td>
    <td><?= date('d M Y H:i', strtotime($c->check_time)) ?></td>
    <td><?= esc($c->notes ?: '—') ?></td>
  </tr>
<?php return ob_get_clean(); };
$emptyMessage = 'No check-ins or check-outs yet.';
require __DIR__ . '/../components/data_table.php';
?>
</div>

<script src="<?= ROOT ?>/public/assets/js/display-student.js"></script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
