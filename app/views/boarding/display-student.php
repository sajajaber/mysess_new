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

require_once __DIR__ . '/../layouts/boarding_header.php';
require_once __DIR__ . '/../components/alert.php';

$logs     = $logs     ?? [];
$checkins = $checkins ?? [];

$sleepLogs    = [];
$nutritionLogs= [];
$moodLogs     = [];
$activityLogs = [];
foreach ($logs as $l) {
  if ($l->log_type === 'sleep')          { $sleepLogs[]    = $l; }
  if ($l->log_type === 'meal')           { $nutritionLogs[]= $l; }
  if ($l->log_type === 'behavior')       { $moodLogs[]     = $l; }
  if ($l->log_type === 'daily_activity') { $activityLogs[] = $l; }
}
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
    <a href="<?= ROOT ?>/boarding/add-sleep?student_id=<?= (int)$student->id ?>"     class="btn btn-primary">Add Sleep</a>
    <a href="<?= ROOT ?>/boarding/add-nutrition?student_id=<?= (int)$student->id ?>" class="btn btn-info">Add Nutrition</a>
    <a href="<?= ROOT ?>/boarding/add-mood?student_id=<?= (int)$student->id ?>"      class="btn btn-warning">Add Mood</a>
    <a href="<?= ROOT ?>/boarding/add-activity?student_id=<?= (int)$student->id ?>"  class="btn">Add Activity</a>
    <a href="<?= ROOT ?>/boarding/add-checkin?student_id=<?= (int)$student->id ?>"   class="btn">Check In/Out</a>
  </div>
</div>


<div class="section-tabs">
  <button class="section-tab active" data-target="sec-sleep">Sleep</button>
  <button class="section-tab"        data-target="sec-nutrition">Nutrition</button>
  <button class="section-tab"        data-target="sec-mood">Mood</button>
  <button class="section-tab"        data-target="sec-activity">Activity</button>
  <button class="section-tab"        data-target="sec-checkins">Check In/Out</button>
</div>


<div id="sec-sleep" class="section-panel active">
<?php
  $sleepColors = ['good' => '#16a34a', 'fair' => '#d97706', 'poor' => '#dc2626'];
  $data    = $sleepLogs;
  $headers = ['Date', 'Quality', 'Bedtime', 'Wake-up', 'Notes'];
  $renderRow = function ($l) use ($sleepColors) { ob_start();
    $q = $l->sleep_quality;
    $color = $q && isset($sleepColors[$q]) ? $sleepColors[$q] : '#94a3b8';
  ?>
    <tr>
      <td><?= date('d M Y', strtotime($l->log_date)) ?></td>
      <td>
        <?php if ($q): ?>
          <span style="background:<?= $color ?>; color:#fff; padding:2px 10px; border-radius:999px; font-size:12px;"><?= ucfirst(esc($q)) ?></span>
        <?php else: ?>
          <span class="muted">—</span>
        <?php endif; ?>
      </td>
      <td><?= $l->bedtime     ? date('H:i', strtotime($l->bedtime))     : '—' ?></td>
      <td><?= $l->wakeup_time ? date('H:i', strtotime($l->wakeup_time)) : '—' ?></td>
      <td><?= esc($l->description) ?></td>
    </tr>
  <?php return ob_get_clean(); };
  $emptyMessage = 'No sleep logs yet.';
  require __DIR__ . '/../components/data_table.php';
?>
</div>


<div id="sec-nutrition" class="section-panel">
<?php
  $apColors = ['good' => '#16a34a', 'fair' => '#d97706', 'poor' => '#dc2626', 'refused' => '#7f1d1d'];
  $data    = $nutritionLogs;
  $headers = ['Date', 'Appetite', 'Notes'];
  $renderRow = function ($l) use ($apColors) { ob_start();
    $a = $l->appetite_level;
    $color = $a && isset($apColors[$a]) ? $apColors[$a] : '#94a3b8';
  ?>
    <tr>
      <td><?= date('d M Y', strtotime($l->log_date)) ?></td>
      <td>
        <?php if ($a): ?>
          <span style="background:<?= $color ?>; color:#fff; padding:2px 10px; border-radius:999px; font-size:12px;"><?= ucfirst(esc($a)) ?></span>
        <?php else: ?>
          <span class="muted">—</span>
        <?php endif; ?>
      </td>
      <td><?= esc($l->description) ?></td>
    </tr>
  <?php return ob_get_clean(); };
  $emptyMessage = 'No nutrition logs yet.';
  require __DIR__ . '/../components/data_table.php';
?>
</div>


<div id="sec-mood" class="section-panel">
<?php
  $moodColors = ['happy' => '#16a34a', 'calm' => '#0ea5e9', 'anxious' => '#d97706', 'upset' => '#dc2626', 'other' => '#64748b'];
  $data    = $moodLogs;
  $headers = ['Date', 'Mood', 'Notes'];
  $renderRow = function ($l) use ($moodColors) { ob_start();
    $m = $l->mood_indicator;
    $color = $m && isset($moodColors[$m]) ? $moodColors[$m] : '#94a3b8';
  ?>
    <tr>
      <td><?= date('d M Y', strtotime($l->log_date)) ?></td>
      <td>
        <?php if ($m): ?>
          <span style="background:<?= $color ?>; color:#fff; padding:2px 10px; border-radius:999px; font-size:12px;"><?= ucfirst(esc($m)) ?></span>
        <?php else: ?>
          <span class="muted">—</span>
        <?php endif; ?>
      </td>
      <td><?= esc($l->description) ?></td>
    </tr>
  <?php return ob_get_clean(); };
  $emptyMessage = 'No mood logs yet.';
  require __DIR__ . '/../components/data_table.php';
?>
</div>


<div id="sec-activity" class="section-panel">
<?php
  $data    = $activityLogs;
  $headers = ['Date', 'Description'];
  $renderRow = function ($l) { ob_start(); ?>
    <tr>
      <td><?= date('d M Y', strtotime($l->log_date)) ?></td>
      <td><?= esc($l->description) ?></td>
    </tr>
  <?php return ob_get_clean(); };
  $emptyMessage = 'No activity logs yet.';
  require __DIR__ . '/../components/data_table.php';
?>
</div>


<div id="sec-checkins" class="section-panel">
<?php
  $data    = $checkins;
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
