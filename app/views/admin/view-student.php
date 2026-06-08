<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$student = $student ?? null;
if (!$student) {
  header('Location: ' . ROOT . '/admin/students');
  exit();
}

$pageTitle    = esc($student->first_name . ' ' . $student->last_name);
$pageHeading  = 'Student Profile';
$activePage   = 'students';
$topbarActions = '
    <a href="' . ROOT . '/admin/students"><button class="btn btn-primary">← Back to Students</button></a>
    <a href="' . ROOT . '/admin/student_report/' . $student->id . '"><button class="btn btn-primary">Student Report</button></a>
    <a href="' . ROOT . '/admin/edit_student/' . $student->id . '"><button class="btn btn-primary">✏️ Edit</button></a>
';

require_once __DIR__ . '/../layouts/admin_header.php';
require_once __DIR__ . '/../components/alert.php';


$medications    = $medications    ?? [];
$medLogs        = $medLogs        ?? [];
$healthEvents   = $healthEvents   ?? [];
$healthRecords  = $healthRecords  ?? [];
$iepGoals       = $iepGoals       ?? [];
$goalProgress   = $goalProgress   ?? [];
$sessions       = $sessions       ?? [];
$teacchProgress = $teacchProgress ?? [];
$assignedStaff  = $assignedStaff  ?? [];
?>

<!-- Profile Card -->
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
      <span class="profile-meta-divider">|</span>
      <span class="profile-meta-item">
        <span class="profile-meta-label">Enrolled</span>
        <span class="profile-meta-value"><?= date('d M Y', strtotime($student->enrollment_date)) ?></span>
      </span>
      <?php if ($student->diagnosis): ?>
        <span class="profile-meta-divider">|</span>
        <span class="profile-meta-item">
          <span class="profile-meta-label">Diagnosis</span>
          <span class="profile-meta-value diagnosis-badge"><?= esc($student->diagnosis) ?></span>
        </span>
      <?php endif; ?>
      <span class="profile-meta-divider">|</span>
      <span class="profile-meta-item">
        <span class="profile-meta-label">Status</span>
        <span class="status-badge status-<?= $student->is_active ? 'active' : 'inactive' ?>">
          <?= $student->is_active ? 'Active' : 'Archived' ?>
        </span>
      </span>
    </div>
  </div>
  <div class="profile-card__actions">
    <a href="<?= ROOT ?>/admin/add_health_record?student_id=<?= $student->id ?>" class="btn btn-info">📝 Add Record</a>
    <?php if ($student->is_active): ?>
      <form method="POST" action="<?= ROOT ?>/admin/archive_student" class="inline-form"
        onsubmit="return confirm('Archive this student?')">
        <input type="hidden" name="student_id" value="<?= $student->id ?>">
        <button type="submit" class="btn btn-warning">📦 Archive</button>
      </form>
    <?php else: ?>
      <form method="POST" action="<?= ROOT ?>/admin/restore_student" class="inline-form"
        onsubmit="return confirm('Restore this student?')">
        <input type="hidden" name="student_id" value="<?= $student->id ?>">
        <button type="submit" class="btn btn-primary">♻️ Restore</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<!-- Tabs -->
<div class="section-tabs">
  <button class="section-tab active" data-target="sec-staff">Assigned Staff</button>
  <button class="section-tab" data-target="sec-health">Health</button>
  <button class="section-tab" data-target="sec-medications">Medications</button>
  <button class="section-tab" data-target="sec-iep">IEP Goals</button>
  <button class="section-tab" data-target="sec-sessions">Sessions</button>
  <button class="section-tab" data-target="sec-teacch">TEACCH</button>
</div>

<!-- ASSIGNED STAFF -->
<div id="sec-staff" class="section-panel active">
  <div class="card">
    <div class="card-header">
      <h2>Assigned Staff</h2>
      <a href="<?= ROOT ?>/admin/assign_students"><button class="btn">Manage</button></a>
    </div>
    <div class="card-body">
      <?php if (empty($assignedStaff)): ?>
        <div class="empty-state">No staff assigned to this student.</div>
      <?php else: ?>
        <table class="data-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Role</th>
              <th>Email</th>
              <th>Since</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($assignedStaff as $staff): ?>
              <tr>
                <td>
                  <a href="<?= ROOT ?>/admin/view_user/<?= $staff->id ?>" class="student-name">
                    <?= esc($staff->first_name . ' ' . $staff->last_name) ?>
                  </a>
                </td>
                <td><span class="role-badge role-<?= esc($staff->role) ?>"><?= ucfirst($staff->role_type) ?></span></td>
                <td><?= esc($staff->email) ?></td>
                <td><?= $staff->start_date ? date('d M Y', strtotime($staff->start_date)) : '—' ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- HEALTH -->
<div id="sec-health" class="section-panel">
  <!-- Health Records -->
  <?php
  $data      = $healthRecords;
  $headers   = ['Type', 'Title', 'Description', 'Date'];
  $tableTitle  = 'Health Records';
  $tableAction = null;
  $renderRow   = function ($record) {
    ob_start(); ?>
    <tr>
      <td><span class="record-type-badge record-type-<?= esc($record->record_type) ?>"><?= ucfirst(esc($record->record_type)) ?></span></td>
      <td><?= esc($record->title) ?></td>
      <td><?= esc($record->description ?? '—') ?></td>
      <td><?= date('d M Y', strtotime($record->recorded_at)) ?></td>
    </tr>
  <?php return ob_get_clean();
  };
  $emptyMessage = 'No health records on file.';
  require __DIR__ . '/../components/data_table.php';
  ?>

  <!-- Health Events -->
  <?php
  $data        = $healthEvents;
  $headers     = ['Severity', 'Description', 'Action Taken', 'Recorded At'];
  $tableTitle  = 'Health Events';
  $tableAction = null;
  $renderRow   = function ($event) {
    $cls = match ($event->severity) {
      'high' => 'sev-high',
      'medium' => 'sev-medium',
      'low' => 'sev-low',
      default => ''
    };
    ob_start(); ?>
    <tr>
      <td><span class="severity-badge <?= $cls ?>"><?= strtoupper(esc($event->severity)) ?></span></td>
      <td><?= esc($event->description) ?></td>
      <td><?= esc($event->action_taken ?? '—') ?></td>
      <td><?= date('d M Y, H:i', strtotime($event->recorded_at)) ?></td>
    </tr>
  <?php return ob_get_clean();
  };
  $emptyMessage = 'No health events recorded.';
  require __DIR__ . '/../components/data_table.php';
  ?>
</div>

<!-- MEDICATIONS -->
<div id="sec-medications" class="section-panel">
  <?php
  $data        = $medications;
  $headers     = ['Medication', 'Dosage', 'Frequency', 'Instructions'];
  $tableTitle  = 'Active Medications';
  $tableAction = null;
  $renderRow   = function ($med) {
    ob_start(); ?>
    <tr>
      <td><strong><?= esc($med->name) ?></strong></td>
      <td><?= esc($med->dosage) ?></td>
      <td><?= esc($med->frequency) ?></td>
      <td><?= esc($med->instructions ?? '—') ?></td>
    </tr>
  <?php return ob_get_clean();
  };
  $emptyMessage = 'No active medications.';
  require __DIR__ . '/../components/data_table.php';
  ?>

  <?php
  $data      = $medLogs;
  $headers   = ['Medication', 'Dosage', 'Administered At', 'Notes'];
  $tableTitle  = 'Dose Log';
  $tableAction = null;
  $renderRow   = function ($log) {
    ob_start(); ?>
    <tr>
      <td><strong><?= esc($log->medication_name) ?></strong></td>
      <td><?= esc($log->dosage) ?></td>
      <td><?= date('d M Y, H:i', strtotime($log->administered_at)) ?></td>
      <td><?= esc($log->notes ?? '—') ?></td>
    </tr>
  <?php return ob_get_clean();
  };
  $emptyMessage = 'No doses logged yet.';
  require __DIR__ . '/../components/data_table.php';
  ?>
</div>

<!-- IEP GOALS -->
<div id="sec-iep" class="section-panel">
<!-- fatima -->
</div>

<!-- SESSIONS -->
<div id="sec-sessions" class="section-panel">
<!-- fatima -->
</div>

<!-- TEACCH  -->
<div id="sec-teacch" class="section-panel">
<!-- fatima -->
</div>

<script src="<?= ROOT ?>/public/assets/js/display-student.js"></script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>