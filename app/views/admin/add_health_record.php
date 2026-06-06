<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$student = $student ?? null;
if (!$student) {
  header('Location: ' . ROOT . '/admin/students');
  exit();
}

$pageTitle    = 'Add Health Record';
$pageHeading  = 'Add Health Record';
$activePage   = 'students';
$topbarActions = '
    <a href="' . ROOT . '/admin/view_student/' . $student->id . '"><button class="btn btn-primary">← Back to Profile</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';
?>

<div class="card">
  <div class="card-header">
    <h2>New Health Record — <?= esc($student->first_name . ' ' . $student->last_name) ?></h2>
  </div>
  <div class="card-body">
    <form method="POST" action="<?= ROOT ?>/admin/add_health_record">

      <input type="hidden" name="student_id" value="<?= $student->id ?>">

      <div class="form-group">
        <label for="record_type">Record Type *</label>
        <?php renderSelect(
          'record_type',
          [
            'checkup'     => 'Checkup',
            'medication'  => 'Medication',
            'injury'      => 'Injury',
            'illness'     => 'Illness',
            'vaccination' => 'Vaccination',
            'other'       => 'Other',
          ],
          '',
          ['id' => 'record_type', 'required' => true]
        ); ?>
      </div>

      <div class="form-group">
        <label for="title">Title *</label>
        <input type="text" id="title" name="title" required>
      </div>

      <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"></textarea>
      </div>

      <div class="form-group">
        <label for="recorded_at">Date *</label>
        <input type="date" id="recorded_at" name="recorded_at" required
          value="<?= date('Y-m-d') ?>">
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Save Record</button>
        <a href="<?= ROOT ?>/admin/view_student/<?= $student->id ?>" class="btn">Cancel</a>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>