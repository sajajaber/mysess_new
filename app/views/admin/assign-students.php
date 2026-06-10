<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle    = 'Assign Students';
$pageHeading  = 'Assign Students';
$activePage   = 'students';
$topbarActions = '
    <a href="' . ROOT . '/admin/students"><button class="btn btn-primary">← Back to Students</button></a>
';

require_once __DIR__ . '/../layouts/admin_header.php';
require_once __DIR__ . '/../components/alert.php';

$nurses           = $nurses           ?? [];
$teachers         = $teachers         ?? [];
$therapists       = $therapists       ?? [];
$boardingStaff    = $boardingStaff    ?? [];
$students         = $students         ?? [];
$boardingStudents = $boardingStudents ?? [];
$assignments      = $assignments      ?? [];
$diagnoses        = $diagnoses        ?? [];
?>

<!-- Tabs -->
<div class="section-tabs" style="margin-bottom: 20px;">
  <button class="section-tab active" data-target="sec-nurses">
    Nurses <span class="tab-count"><?= count($nurses) ?></span>
  </button>
  <button class="section-tab" data-target="sec-teachers">
    Teachers <span class="tab-count"><?= count($teachers) ?></span>
  </button>
  <button class="section-tab" data-target="sec-therapists">
    Therapists <span class="tab-count"><?= count($therapists) ?></span>
  </button>
  <button class="section-tab" data-target="sec-boarding">
    Boarding Staff <span class="tab-count"><?= count($boardingStaff) ?></span>
  </button>
</div>


<?php
function staffCard($staff, $assignments, $students, $role, $rootUrl, $diagnoses) {
    $assigned    = $assignments[$staff->id] ?? [];
    $assignedIds = array_map(fn($s) => $s->id, $assigned);

    // Build options excluding already-assigned students
    $studentOptions = '<option value="">— Select Student —</option>';
    foreach ($students as $s) {
        if (!in_array($s->id, $assignedIds)) {
            $studentOptions .= '<option value="' . $s->id . '">'
                . esc($s->first_name . ' ' . $s->last_name)
                . '</option>';
        }
    }

    ob_start();
?>
    <div class="assignment-card">
        <div class="assignment-card__header">
            <div>
                <div class="assignment-card__name"><?= esc($staff->first_name . ' ' . $staff->last_name) ?></div>
                <div class="assignment-card__email"><?= esc($staff->email) ?></div>
            </div>
            <span class="assignment-card__count"><?= count($assigned) ?> student<?= count($assigned) !== 1 ? 's' : '' ?></span>
        </div>

        <!-- Currently assigned -->
        <?php if (!empty($assigned)): ?>
            <div class="assignment-card__students">
                <?php foreach ($assigned as $student): ?>
                    <div class="assigned-student-row">
                        <span><?= esc($student->first_name . ' ' . $student->last_name) ?></span>
                        <form method="POST" action="<?= $rootUrl ?>/admin/assign_students" class="inline-form">
                            <input type="hidden" name="action"     value="remove">
                            <input type="hidden" name="student_id" value="<?= $student->id ?>">
                            <input type="hidden" name="user_id"    value="<?= $staff->id ?>">
                            <input type="hidden" name="role"       value="<?= $role ?>">
                            <button type="submit" class="btn-remove-assignment"
                                    onclick="return confirm('Remove this assignment?')" title="Remove">✕</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="assignment-card__empty">No students assigned yet.</div>
        <?php endif; ?>

        <!-- Add a single student -->
        <?php if (count($students) > count($assignedIds)): ?>
            <form method="POST" action="<?= $rootUrl ?>/admin/assign_students" class="assignment-add-form">
                <input type="hidden" name="action"  value="assign">
                <input type="hidden" name="user_id" value="<?= $staff->id ?>">
                <input type="hidden" name="role"    value="<?= $role ?>">
                <select name="student_id" required>
                    <?= $studentOptions ?>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">+ Assign</button>
            </form>
        <?php else: ?>
            <div class="assignment-card__empty">All students assigned.</div>
        <?php endif; ?>

        <!-- Assign a whole diagnosis group -->
        <?php if (!empty($diagnoses)): ?>
            <form method="POST" action="<?= $rootUrl ?>/admin/assign_students" class="assignment-add-form"
                  style="margin-top:8px;"
                  onsubmit="return confirm('Assign all students with this diagnosis?')">
                <input type="hidden" name="action"  value="assign_by_diagnosis">
                <input type="hidden" name="user_id" value="<?= $staff->id ?>">
                <input type="hidden" name="role"    value="<?= $role ?>">
                <select name="diagnosis" required>
                    <option value="">— By diagnosis —</option>
                    <?php foreach ($diagnoses as $d): ?>
                        <option value="<?= esc($d->diagnosis) ?>"><?= esc($d->diagnosis) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-sm">+ Assign group</button>
            </form>
        <?php endif; ?>

    </div>
<?php
    return ob_get_clean();
}
?>

<!-- Nurses -->
<div id="sec-nurses" class="section-panel active">
  <?php if (empty($nurses)): ?>
    <div class="empty-state">No nurses found.</div>
  <?php else: ?>
    <div class="assignment-grid">
      <?php foreach ($nurses as $staff): ?>
        <?= staffCard($staff, $assignments, $students, 'nurse', ROOT, $diagnoses) ?>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Teachers -->
<div id="sec-teachers" class="section-panel">
  <?php if (empty($teachers)): ?>
    <div class="empty-state">No teachers found.</div>
  <?php else: ?>
    <div class="assignment-grid">
      <?php foreach ($teachers as $staff): ?>
        <?= staffCard($staff, $assignments, $students, 'teacher', ROOT, $diagnoses) ?>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Therapists -->
<div id="sec-therapists" class="section-panel">
  <?php if (empty($therapists)): ?>
    <div class="empty-state">No therapists found.</div>
  <?php else: ?>
    <div class="assignment-grid">
      <?php foreach ($therapists as $staff): ?>
        <?= staffCard($staff, $assignments, $students, 'therapist', ROOT, $diagnoses) ?>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Boarding Staff (boarding students only) -->
<div id="sec-boarding" class="section-panel">
  <?php if (empty($boardingStaff)): ?>
    <div class="empty-state">No boarding staff found.</div>
  <?php elseif (empty($boardingStudents)): ?>
    <div class="empty-state">No boarding students yet. Mark students as boarding in the Add Student form.</div>
  <?php else: ?>
    <div class="assignment-grid">
      <?php foreach ($boardingStaff as $staff): ?>
        <?= staffCard($staff, $assignments, $boardingStudents, 'boarding_staff', ROOT, $diagnoses) ?>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<script src="<?= ROOT ?>/public/assets/js/display-student.js"></script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
