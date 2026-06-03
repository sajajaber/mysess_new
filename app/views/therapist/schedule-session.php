<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Schedule Therapy Session';
$pageHeading = 'Schedule Therapy Session';
$activePage  = 'students';

$topbarActions = '
  <a href="' . ROOT . '/therapist/student/' . (int)$student->id . '"><button class="btn btn-primary">Back to Student</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';
?>

<div class="card">
  <div class="card-header">
    <h2>New Therapy Session — <?= esc($student->first_name . ' ' . $student->last_name) ?></h2>
  </div>

  <div class="card-body">
    <form method="POST" action="<?= ROOT ?>/therapist/schedule-session">

      <input type="hidden" name="student_id" value="<?= (int)$student->id ?>">

      <div class="form-group">
        <label for="session_date">Session Date <span style="color:#eb004e">*</span></label>
        <input type="date" id="session_date" name="session_date"
               value="<?= date('Y-m-d') ?>" required>
      </div>

      <div class="form-group">
        <label for="session_type">Session Type <span style="color:#eb004e">*</span></label>
        <?php
          renderSelect(
            'session_type',
            [
              'Speech'       => 'Speech',
              'Occupational' => 'Occupational',
              'Behavioral'   => 'Behavioral',
              'Physical'     => 'Physical',
            ],
            'Speech',
            ['id' => 'session_type', 'required' => true]
          );
        ?>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Schedule Session</button>
        <a href="<?= ROOT ?>/therapist/student/<?= (int)$student->id ?>" class="btn">Cancel</a>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
