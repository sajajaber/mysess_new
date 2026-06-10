<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'Student Report';
$pageHeading = 'Generate Student Report';
$activePage  = 'semester report';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

$students = $students ?? [];
?>

<div class="card">
  <div class="card-header">
    <h2>Build a Student Report</h2>
  </div>

  <div class="card-body">

    <?php if (empty($students)): ?>
      <p>You have no assigned students to report on.</p>
      <a href="<?= ROOT ?>/therapist/students" class="btn btn-primary">My Students</a>

    <?php else: ?>
      <p>Pick a student to build their report.</p>

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
          <button type="submit" class="btn btn-primary">Generate Report</button>
        </div>

      </form>
    <?php endif; ?>

  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
