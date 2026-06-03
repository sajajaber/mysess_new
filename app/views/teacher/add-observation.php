<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'Add Observation';
$pageHeading = 'Add Observation';
$activePage  = 'students';

$topbarActions = '
  <a href="' . ROOT . '/teacher/student/' . (int)$student->id . '"><button class="btn btn-primary">Back to Student</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';
?>

<div class="card">
  <div class="card-header">
    <h2>New Observation — <?= esc($student->first_name . ' ' . $student->last_name) ?></h2>
  </div>

  <div class="card-body">
    <form method="POST" action="<?= ROOT ?>/teacher/add-observation">

      <input type="hidden" name="student_id" value="<?= (int)$student->id ?>">

      <div class="form-group">
        <label for="observation">Observation <span style="color:#eb004e">*</span></label>
        <textarea id="observation" name="observation" rows="4"
                  placeholder="What did you notice about this student?" required></textarea>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Save Observation</button>
        <a href="<?= ROOT ?>/teacher/student/<?= (int)$student->id ?>" class="btn">Cancel</a>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
