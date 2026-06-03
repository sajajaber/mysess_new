<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Add Progress Report';
$pageHeading = 'Add Progress Report';
$activePage  = 'progress reports';

$topbarActions = '
  <a href="' . ROOT . '/teacher/student/' . (int)$student->id . '"><button class="btn btn-primary">Back to Student</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';
?>

<div class="card">
  <div class="card-header">
    <h2>New Progress Report — <?= esc($student->first_name . ' ' . $student->last_name) ?></h2>
  </div>

  <div class="card-body">
    <form method="POST" action="<?= ROOT ?>/teacher/add-progress-report">

      <input type="hidden" name="student_id" value="<?= (int)$student->id ?>">

      <div class="form-group">
        <label for="reporting_period">Reporting Period <span style="color:#eb004e">*</span></label>
        <input type="text" id="reporting_period" name="reporting_period"
               placeholder="e.g. Term 1 2025" required>
      </div>

      <div class="form-group">
        <label for="summary">Summary <span style="color:#eb004e">*</span></label>
        <textarea id="summary" name="summary" rows="4"
                  placeholder="Summarize the student's progress this period" required></textarea>
      </div>

      <div class="form-group">
        <label for="rating">Rating <span style="color:#eb004e">*</span></label>
        <?php
          renderSelect(
            'rating',
            [
              'excellent'         => 'Excellent',
              'good'              => 'Good',
              'fair'              => 'Fair',
              'needs_improvement' => 'Needs Improvement',
            ],
            'good',
            ['id' => 'rating', 'required' => true]
          );
        ?>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Submit Report</button>
        <a href="<?= ROOT ?>/teacher/student/<?= (int)$student->id ?>" class="btn">Cancel</a>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
