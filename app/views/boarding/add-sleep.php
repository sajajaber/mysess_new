<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Add Sleep Log';
$pageHeading = 'Add Sleep Log';
$activePage  = 'sleep';

$topbarActions = '
  <a href="' . ROOT . '/boarding/student/' . (int)$student->id . '"><button class="btn btn-primary">Back to Student</button></a>
';

require_once __DIR__ . '/../layouts/boarding_header.php';
require_once __DIR__ . '/../components/alert.php';
?>

<div class="card">
  <div class="card-header">
    <h2>New Sleep Log — <?= esc($student->first_name . ' ' . $student->last_name) ?></h2>
  </div>

  <div class="card-body">
    <form method="POST" action="<?= ROOT ?>/boarding/add-sleep">
      <input type="hidden" name="student_id" value="<?= (int)$student->id ?>">

      <div class="form-group">
        <label for="log_date">Date <span style="color:#eb004e">*</span></label>
        <input type="date" id="log_date" name="log_date" value="<?= date('Y-m-d') ?>" required>
      </div>

      <div class="form-group">
        <label for="sleep_quality">Sleep quality</label>
        <?php
          renderSelect(
            'sleep_quality',
            ['' => '— not tracked —', 'good' => 'Good', 'fair' => 'Fair', 'poor' => 'Poor'],
            '',
            ['id' => 'sleep_quality']
          );
        ?>
      </div>

      <div class="form-group">
        <label for="bedtime">Bedtime</label>
        <input type="time" id="bedtime" name="bedtime">
      </div>

      <div class="form-group">
        <label for="wakeup_time">Wake-up time</label>
        <input type="time" id="wakeup_time" name="wakeup_time">
      </div>

      <div class="form-group">
        <label for="description">Notes <span style="color:#eb004e">*</span></label>
        <textarea id="description" name="description" rows="3" placeholder="Anything about the night (e.g. woke up twice, slept through, etc.)" required></textarea>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Save Sleep Log</button>
        <a href="<?= ROOT ?>/boarding/student/<?= (int)$student->id ?>" class="btn">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
