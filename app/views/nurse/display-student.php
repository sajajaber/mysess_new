<?php

// LOAD CONFIG FIRST - BEFORE ANYTHING ELSE
require_once __DIR__ . '/../../core/config.php';

$student = $student ?? (object) [
  'id'            => 0,
  'first_name'    => '',
  'last_name'     => '',
  'photo_url'     => '',
  'date_of_birth' => '',
  'gender'        => '',
  'diagnosis'     => ''
];
$pageTitle    = trim($student->first_name . ' ' . $student->last_name) ?: 'Health Profile';
$pageHeading  = trim($student->first_name . ' ' . $student->last_name) ?: 'Student Profile';
$activePage   = 'students';
$topbarActions = '
    <a href="' . ROOT . '/nurse"><button class="btn btn-primary">← Back to Students</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';
?>

<!-- Student Profile Card -->
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
    <a href="<?= ROOT ?>/nurse/add_medication?student_id=<?= $student->id ?>" class="btn btn-primary">💊 Add Medication</a>
    <a href="<?= ROOT ?>/nurse/log_health_event?student_id=<?= $student->id ?>" class="btn btn-warning">⚠️ Log Event</a>
    <a href="<?= ROOT ?>/nurse/add_health_record?student_id=<?= $student->id ?>" class="btn btn-info">📝 Add Record</a>
  </div>
</div>

<!-- Section tabs -->
<div class="section-tabs">
  <button class="section-tab active" data-target="sec-medications">Medications</button>
  <button class="section-tab" data-target="sec-dose-log">Dose Log</button>
  <button class="section-tab" data-target="sec-events">Health Events</button>
  <button class="section-tab" data-target="sec-records">Health Records</button>
</div>

<!-- MEDICATIONS -->
<div id="sec-medications" class="section-panel active">
<?php
$data        = $medications ?? [];
$headers     = ['Medication', 'Dosage', 'Frequency', 'Instructions', 'Actions'];
$tableTitle  = 'Medications';
$tableAction = '<a href="' . ROOT . '/nurse/add_medication?student_id=' . $student->id . '" class="btn btn-sm btn-primary">+ Add Medication</a>';
$renderRow   = function ($med) use ($student) { ob_start(); ?>
  <tr>
    <td><strong><?= esc($med->name) ?></strong></td>
    <td><?= esc($med->dosage) ?></td>
    <td><?= esc($med->frequency) ?></td>
    <td><?= esc($med->instructions ?? '—') ?></td>
    <td class="actions">
      <a href="<?= ROOT ?>/nurse/log_dose?med_id=<?= $med->id ?>" class="btn-icon btn-log" title="Log Dose">💊</a>
      <form method="POST" action="<?= ROOT ?>/nurse/deactivate_medication" class="inline-form" onsubmit="return confirm('Deactivate this medication?')">
        <input type="hidden" name="medication_id" value="<?= $med->id ?>">
        <input type="hidden" name="student_id" value="<?= $student->id ?>">
        <button type="submit" class="btn-icon btn-deactivate" title="Deactivate">❌</button>
      </form>
    </td>
  </tr>
<?php return ob_get_clean(); };
$emptyMessage = 'No active medications.';
require __DIR__ . '/../components/data_table.php';
?>
</div>

<!-- DOSE LOG -->
<div id="sec-dose-log" class="section-panel">
<?php
$data        = $medLogs ?? [];
$headers     = ['Medication', 'Dosage', 'Administered At', 'Notes', 'Given By'];
$tableTitle  = 'Dose Log';
$tableAction = null;
$renderRow   = function ($log) { ob_start(); ?>
  <tr>
    <td><strong><?= esc($log->medication_name) ?></strong></td>
    <td><?= esc($log->dosage) ?></td>
    <td><?= date('d M Y, H:i', strtotime($log->administered_at)) ?></td>
    <td><?= esc($log->notes ?? '—') ?></td>
    <td><?= esc($log->administered_by_name ?? 'Nurse') ?></td>
  </tr>
<?php return ob_get_clean(); };
$emptyMessage = 'No doses logged yet.';
require __DIR__ . '/../components/data_table.php';
?>
</div>

<!-- HEALTH EVENTS -->
<div id="sec-events" class="section-panel">
<?php
$data        = $healthEvents ?? [];
$headers     = ['Severity', 'Description', 'Action Taken', 'Recorded At'];
$tableTitle  = 'Health Events';
$tableAction = '<a href="' . ROOT . '/nurse/log_health_event?student_id=' . $student->id . '" class="btn btn-sm btn-warning">+ Log Event</a>';
$renderRow   = function ($event) {
  $cls = match($event->severity) { 'high' => 'sev-high', 'medium' => 'sev-medium', 'low' => 'sev-low', default => '' };
  ob_start(); ?>
  <tr>
    <td><span class="severity-badge <?= $cls ?>"><?= strtoupper(esc($event->severity)) ?></span></td>
    <td><?= esc($event->description) ?></td>
    <td><?= esc($event->action_taken ?? '—') ?></td>
    <td><?= date('d M Y, H:i', strtotime($event->recorded_at)) ?></td>
  </tr>
<?php return ob_get_clean(); };
$emptyMessage = 'No health events recorded.';
require __DIR__ . '/../components/data_table.php';
?>
</div>

<!-- HEALTH RECORDS -->
<div id="sec-records" class="section-panel">
<?php
$data        = $healthRecords ?? [];
$headers     = ['Type', 'Title', 'Description', 'Date'];
$tableTitle  = 'Health Records';
$tableAction = '<a href="' . ROOT . '/nurse/add_health_record?student_id=' . $student->id . '" class="btn btn-sm btn-info">+ Add Record</a>';
$renderRow   = function ($record) { ob_start(); ?>
  <tr>
    <td><span class="record-type-badge record-type-<?= esc($record->record_type) ?>"><?= ucfirst(esc($record->record_type)) ?></span></td>
    <td><?= esc($record->title) ?></td>
    <td><?= esc($record->description ?? '—') ?></td>
    <td><?= date('d M Y', strtotime($record->recorded_at)) ?></td>
  </tr>
<?php return ob_get_clean(); };
$emptyMessage = 'No health records on file.';
require __DIR__ . '/../components/data_table.php';
?>
</div>

<script src="<?= ROOT ?>/public/assets/js/display-student.js"></script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>