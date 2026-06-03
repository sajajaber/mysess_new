<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$student = $student ?? null;
if (!$student) {
  header('Location: ' . ROOT . '/admin/students');
  exit();
}

$pageTitle    = 'Edit Student';
$pageHeading  = 'Edit Student';
$activePage   = 'students';
$topbarActions = '
    <a href="' . ROOT . '/admin/view_student/' . $student->id . '"><button class="btn btn-primary">← Back to Profile</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

require __DIR__ . '/../../../public/assets/css/admin.php';

$parents = $parents ?? [];
$old     = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

// old values override student values on validation errors
$val = function ($field) use ($old, $student) {
  return $old[$field] ?? $student->$field ?? '';
};
?>

<div class="card">
  <div class="card-header">
    <h2>Edit — <?= esc($student->first_name . ' ' . $student->last_name) ?></h2>
  </div>
  <div class="card-body">
    <form method="POST" action="<?= ROOT ?>/admin/edit_student/<?= $student->id ?>">

      <div class="form-row">
        <div class="form-group">
          <label for="first_name">First Name *</label>
          <input type="text" id="first_name" name="first_name" required
            value="<?= esc($val('first_name')) ?>">
        </div>
        <div class="form-group">
          <label for="last_name">Last Name *</label>
          <input type="text" id="last_name" name="last_name" required
            value="<?= esc($val('last_name')) ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="date_of_birth">Date of Birth *</label>
          <input type="date" id="date_of_birth" name="date_of_birth" required
            value="<?= esc($val('date_of_birth')) ?>">
        </div>
        <div class="form-group">
          <label for="gender">Gender *</label>
          <select id="gender" name="gender" required>
            <option value="">Select Gender</option>
            <option value="male" <?= $val('gender') === 'male'   ? 'selected' : '' ?>>Male</option>
            <option value="female" <?= $val('gender') === 'female' ? 'selected' : '' ?>>Female</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="enrollment_date">Enrollment Date *</label>
          <input type="date" id="enrollment_date" name="enrollment_date" required
            value="<?= esc($val('enrollment_date')) ?>">
        </div>
        <div class="form-group">
          <label for="guardian_id">Parent / Guardian</label>
          <select id="guardian_id" name="guardian_id">
            <option value="">— None —</option>
            <?php foreach ($parents as $parent): ?>
              <option value="<?= $parent->id ?>"
                <?= $val('guardian_id') == $parent->id ? 'selected' : '' ?>>
                <?= esc($parent->first_name . ' ' . $parent->last_name) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="diagnosis">Diagnosis</label>
        <textarea id="diagnosis" name="diagnosis" rows="3"><?= esc($val('diagnosis')) ?></textarea>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="<?= ROOT ?>/app/views/admin/view-student/<?= $student->id ?>" class="btn">Cancel</a>
      </div>

      

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>