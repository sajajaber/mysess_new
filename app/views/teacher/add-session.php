<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'Add Classroom Session';
$pageHeading = 'Add Classroom Session';
$activePage  = 'students';

$topbarActions = '
  <a href="' . ROOT . '/teacher/student/' . (int)$student->id . '"><button class="btn btn-primary">Back to Student</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';
?>

<div class="card">
  <div class="card-header">
    <h2>New Classroom Session — <?= esc($student->first_name . ' ' . $student->last_name) ?></h2>
  </div>

  <div class="card-body">
    <form method="POST" action="<?= ROOT ?>/teacher/add-session">

      <input type="hidden" name="student_id" value="<?= (int)$student->id ?>">

      <div class="form-group">
        <label for="session_date">Session Date <span style="color:#eb004e">*</span></label>
        <input type="date" id="session_date" name="session_date"
               value="<?= date('Y-m-d') ?>" required>
      </div>

      <div class="form-group">
        <label for="subject">Subject <span style="color:#eb004e">*</span></label>
        <input type="text" id="subject" name="subject"
               placeholder="e.g. Reading, Math, Art" required>
      </div>

      <div class="form-group">
        <label for="notes">Notes</label>
        <textarea id="notes" name="notes" rows="3"
                  placeholder="What did you cover in this session? (optional)"></textarea>
      </div>

      <div class="form-group">
        <label for="observation">Academic Observation (optional)</label>
        <textarea id="observation" name="observation" rows="3"
                  placeholder="Anything you noticed about the student today? (optional)"></textarea>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Save Session</button>
        <a href="<?= ROOT ?>/teacher/student/<?= (int)$student->id ?>" class="btn">Cancel</a>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
