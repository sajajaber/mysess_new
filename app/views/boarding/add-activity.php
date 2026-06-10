<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Add Activity Log';
$pageHeading = 'Add Activity Log';
$activePage  = 'activity';

$topbarActions = '
  <a href="' . ROOT . '/boarding/student/' . (int)$student->id . '"><button class="btn btn-primary">Back to Student</button></a>
';

require_once __DIR__ . '/../layouts/boarding_header.php';
require_once __DIR__ . '/../components/alert.php';
?>

<div class="card">
  <div class="card-header">
    <h2>New Activity Log — <?= esc($student->first_name . ' ' . $student->last_name) ?></h2>
  </div>

  <div class="card-body">
    <form method="POST" action="<?= ROOT ?>/boarding/add-activity">
      <input type="hidden" name="student_id" value="<?= (int)$student->id ?>">

      <div class="form-group">
        <label for="log_date">Date <span style="color:#eb004e">*</span></label>
        <input type="date" id="log_date" name="log_date" value="<?= date('Y-m-d') ?>" required>
      </div>

      <div class="form-group">
        <label for="description">What happened <span style="color:#eb004e">*</span></label>
        <textarea id="description" name="description" rows="4" placeholder="Activities, games, outings, anything worth noting" required></textarea>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Save Activity Log</button>
        <a href="<?= ROOT ?>/boarding/student/<?= (int)$student->id ?>" class="btn">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
