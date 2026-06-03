<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'Semester Report';
$pageHeading = 'Generate Semester Report';
$activePage  = 'sessions';

$topbarActions = '
  <a href="' . ROOT . '/therapist/sessions"><button class="btn btn-primary">Sessions</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

$students  = $students  ?? [];
$semesters = $semesters ?? [];
?>

<div class="card">
  <div class="card-header">
    <h2>Build a Semester Report</h2>
  </div>

  <div class="card-body">

    <?php if (empty($students)): ?>
      <p>You have no assigned students to report on.</p>
      <a href="<?= ROOT ?>/therapist/students" class="btn btn-primary">My Students</a>

    <?php else: ?>
      <p>Pick a student and a semester. The report is built automatically from their IEP goals, progress scores, milestones, therapy sessions, and TEACCH progress.</p>

      <!-- GET so the choices land in the URL and the report can be bookmarked or printed -->
      <form method="GET" action="<?= ROOT ?>/therapist/semester-report">

        <div class="form-group">
          <label for="student_id">Student <span style="color:#eb004e">*</span></label>
          <select id="student_id" name="student_id" required>
            <option value="">— choose a student —</option>
            <?php foreach ($students as $student): ?>
              <option value="<?= (int)$student->id ?>">
                <?= esc($student->first_name . ' ' . $student->last_name) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="semester">Semester <span style="color:#eb004e">*</span></label>
          <select id="semester" name="semester" required>
            <option value="">— choose a semester —</option>
            <?php foreach ($semesters as $key => $label): ?>
              <option value="<?= esc($key) ?>"><?= esc($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <button type="submit" class="btn btn-primary">Generate Report</button>
        </div>

      </form>
    <?php endif; ?>

  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
