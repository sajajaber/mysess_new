<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Add Nutrition Log';
$pageHeading = 'Add Nutrition Log';
$activePage  = 'nutrition';

$topbarActions = '
  <a href="' . ROOT . '/boarding/student/' . (int)$student->id . '"><button class="btn btn-primary">Back to Student</button></a>
';

require_once __DIR__ . '/../layouts/boarding_header.php';
require_once __DIR__ . '/../components/alert.php';
?>

<div class="card">
  <div class="card-header">
    <h2>New Nutrition Log — <?= esc($student->first_name . ' ' . $student->last_name) ?></h2>
  </div>

  <div class="card-body">
    <form method="POST" action="<?= ROOT ?>/boarding/add-nutrition">
      <input type="hidden" name="student_id" value="<?= (int)$student->id ?>">

      <div class="form-group">
        <label for="log_date">Date <span style="color:#eb004e">*</span></label>
        <input type="date" id="log_date" name="log_date" value="<?= date('Y-m-d') ?>" required>
      </div>

      <div class="form-group">
        <label for="appetite_level">Appetite level</label>
        <?php
          renderSelect(
            'appetite_level',
            [
              ''        => '— not tracked —',
              'good'    => 'Good — finished most of the meal',
              'fair'    => 'Fair — ate some',
              'poor'    => 'Poor — barely ate',
              'refused' => 'Refused — would not eat',
            ],
            '',
            ['id' => 'appetite_level']
          );
        ?>
      </div>

      <div class="form-group">
        <label for="description">Meal notes <span style="color:#eb004e">*</span></label>
        <textarea id="description" name="description" rows="3" placeholder="What was the meal, what they ate, etc." required></textarea>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Save Nutrition Log</button>
        <a href="<?= ROOT ?>/boarding/student/<?= (int)$student->id ?>" class="btn">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
