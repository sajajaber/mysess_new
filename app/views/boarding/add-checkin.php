<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Check In / Out';
$pageHeading = 'Check In / Out';
$activePage  = 'students';

$topbarActions = '
  <a href="' . ROOT . '/boarding/student/' . (int)$student->id . '"><button class="btn btn-primary">Back to Student</button></a>
';

require_once __DIR__ . '/../layouts/boarding_header.php';
require_once __DIR__ . '/../components/alert.php';
?>

<div class="card">
  <div class="card-header">
    <h2>Check In / Out — <?= esc($student->first_name . ' ' . $student->last_name) ?></h2>
  </div>

  <div class="card-body">
    <form method="POST" action="<?= ROOT ?>/boarding/add-checkin">

      <input type="hidden" name="student_id" value="<?= (int)$student->id ?>">

      <div class="form-group">
        <label for="check_type">Type <span style="color:#eb004e">*</span></label>
        <?php
          renderSelect(
            'check_type',
            [
              'check_in'  => 'Check In',
              'check_out' => 'Check Out',
            ],
            'check_in',
            ['id' => 'check_type', 'required' => true]
          );
        ?>
      </div>

      <div class="form-group">
        <label for="check_time">Time <span style="color:#eb004e">*</span></label>
        <input type="datetime-local" id="check_time" name="check_time"
               value="<?= date('Y-m-d\TH:i') ?>" required>
      </div>

      <div class="form-group">
        <label for="notes">Notes</label>
        <textarea id="notes" name="notes" rows="2" placeholder="Any notes (optional)"></textarea>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="<?= ROOT ?>/boarding/student/<?= (int)$student->id ?>" class="btn">Cancel</a>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
