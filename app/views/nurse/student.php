<?php
$student = $student ?? (object) [
  'id' => 0,
  'first_name' => '',
  'last_name' => '',
  'photo_url' => '',
  'date_of_birth' => '',
  'gender' => '',
  'diagnosis' => ''
];
$pageTitle = trim($student->first_name . ' ' . $student->last_name) ?: 'Health Profile';
$pageHeading = trim($student->first_name . ' ' . $student->last_name) ?: 'Student Profile';
$activePage = 'students';
$topbarActions = '
    <a href="' . ROOT . '/nurse" class="btn">← Back to Students</a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';
?>

<!-- Student Info Card -->
<div class="profile-header">
  <div class="profile-avatar">
    <?php if ($student->photo_url): ?>
      <img src="<?= ROOT . '/' . $student->photo_url ?>" alt="Student photo">
    <?php else: ?>
      <div class="avatar-placeholder"><?= strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) ?></div>
    <?php endif; ?>
  </div>
  <div class="profile-info">
    <h2><?= esc($student->first_name . ' ' . $student->last_name) ?></h2>
    <div class="profile-details">
      <div class="detail-item">
        <span class="detail-label">Date of Birth:</span>
        <span class="detail-value"><?= date('d-m-Y', strtotime($student->date_of_birth)) ?></span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Gender:</span>
        <span class="detail-value"><?= ucfirst($student->gender) ?></span>
      </div>
      <?php if ($student->diagnosis): ?>
        <div class="detail-item">
          <span class="detail-label">Diagnosis:</span>
          <span class="detail-value"><?= esc($student->diagnosis) ?></span>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
  <a href="<?= ROOT ?>/nurse/add_medication?student_id=<?= $student->id ?>" class="btn btn-primary">+ Add Medication</a>
  <a href="<?= ROOT ?>/nurse/log_health_event?student_id=<?= $student->id ?>" class="btn btn-warning">⚠️ Log Health Event</a>
  <a href="<?= ROOT ?>/nurse/add_health_record?student_id=<?= $student->id ?>" class="btn btn-info">📝 Add Health Record</a>
</div>

<!-- Medications Section -->
<?php
$medData = $medications ?? [];
$medHeaders = ['Medication', 'Dosage', 'Frequency', 'Instructions', 'Actions'];
$medRenderRow = function ($med) use ($student) {
  ob_start();
?>
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
<?php
  return ob_get_clean();
};

$medAction = '<a href="' . ROOT . '/nurse/add_medication?student_id=' . $student->id . '" class="btn btn-sm btn-primary">+ Add Medication</a>';
$medEmptyMessage = 'No active medications.';

require __DIR__ . '/../components/data_table.php';
?>

<!-- Dose Log Section -->
<?php
$doseData = $medLogs ?? [];
$doseHeaders = ['Medication', 'Dosage', 'Administered At', 'Notes', 'Given By'];
$doseRenderRow = function ($log) {
  ob_start();
?>
  <tr>
    <td><strong><?= esc($log->medication_name) ?></strong></td>
    <td><?= esc($log->dosage) ?></td>
    <td><?= date('d-m-Y H:i', strtotime($log->administered_at)) ?></td>
    <td><?= esc($log->notes ?? '—') ?></td>
    <td><?= esc($log->administered_by_name ?? 'Nurse') ?></td>
  </tr>
<?php
  return ob_get_clean();
};

$doseEmptyMessage = 'No doses logged yet.';

require __DIR__ . '/../components/data_table.php';
?>

<!-- Health Events Section -->
<?php
$eventData = $healthEvents ?? [];
$eventHeaders = ['Severity', 'Description', 'Action Taken', 'Recorded At'];
$eventRenderRow = function ($event) {
  $severityClass = match ($event->severity) {
    'high' => 'severity-high',
    'medium' => 'severity-medium',
    'low' => 'severity-low',
    default => ''
  };
  ob_start();
?>
  <tr>
    <td><span class="severity-badge <?= $severityClass ?>"><?= strtoupper(esc($event->severity)) ?></span></td>
    <td><?= esc($event->description) ?></td>
    <td><?= esc($event->action_taken ?? '—') ?></td>
    <td><?= date('d-m-Y H:i', strtotime($event->recorded_at)) ?></td>
  </tr>
<?php
  return ob_get_clean();
};

$eventAction = '<a href="' . ROOT . '/nurse/log_health_event?student_id=' . $student->id . '" class="btn btn-sm btn-warning">+ Log Event</a>';
$eventEmptyMessage = 'No health events recorded.';

require __DIR__ . '/../components/data_table.php';
?>

<!-- Health Records Section -->
<?php
$recordData = $healthRecords ?? [];
$recordHeaders = ['Type', 'Title', 'Description', 'Date'];
$recordRenderRow = function ($record) {
  ob_start();
?>
  <tr>
    <td><span class="badge badge-<?= $record->record_type ?>"><?= ucfirst(esc($record->record_type)) ?></span></td>
    <td><?= esc($record->title) ?></td>
    <td><?= esc($record->description ?? '—') ?></td>
    <td><?= date('d-m-Y', strtotime($record->recorded_at)) ?></td>
  </tr>
<?php
  return ob_get_clean();
};

$recordAction = '<a href="' . ROOT . '/nurse/add_health_record?student_id=' . $student->id . '" class="btn btn-sm btn-info">+ Add Record</a>';
$recordEmptyMessage = 'No health records on file.';

require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>