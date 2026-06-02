<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle    = 'Add Student';
$pageHeading  = 'Add Student';
$activePage   = 'students';
$topbarActions = '
    <a href="' . ROOT . '/admin/students"><button class="btn btn-primary">← Back to Students</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

require 'C:/xampp1/htdocs/mysess_new/public/assets/css/admin.php';

$parents = $parents ?? [];
$old     = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>

<div class="card">
  <div class="card-header">
    <h2>New Student</h2>
  </div>
  <div class="card-body">
    <form method="POST" action="<?= ROOT ?>/admin/add_student">

      <div class="form-row">
        <div class="form-group">
          <label for="first_name">First Name *</label>
          <input type="text" id="first_name" name="first_name" required
            value="<?= esc($old['first_name'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label for="last_name">Last Name *</label>
          <input type="text" id="last_name" name="last_name" required
            value="<?= esc($old['last_name'] ?? '') ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="date_of_birth">Date of Birth *</label>
          <input type="date" id="date_of_birth" name="date_of_birth" required
            value="<?= esc($old['date_of_birth'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label for="gender">Gender *</label>
          <select id="gender" name="gender" required>
            <option value="">Select Gender</option>
            <option value="male" <?= ($old['gender'] ?? '') === 'male'   ? 'selected' : '' ?>>Male</option>
            <option value="female" <?= ($old['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="enrollment_date">Enrollment Date *</label>
          <input type="date" id="enrollment_date" name="enrollment_date" required
            value="<?= esc($old['enrollment_date'] ?? date('Y-m-d')) ?>">
        </div>
        <div class="form-group">
          <label for="guardian_id">Parent / Guardian</label>
          <select id="guardian_id" name="guardian_id">
            <option value="">— None —</option>
            <?php foreach ($parents as $parent): ?>
              <option value="<?= $parent->id ?>"
                <?= ($old['guardian_id'] ?? '') == $parent->id ? 'selected' : '' ?>>
                <?= esc($parent->first_name . ' ' . $parent->last_name) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="diagnosis">Diagnosis</label>
        <textarea id="diagnosis" name="diagnosis" rows="3"><?= esc($old['diagnosis'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Add Student</button>
        <a href="<?= ROOT ?>/admin/students" class="btn">Cancel</a>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>