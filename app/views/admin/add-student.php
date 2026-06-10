<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle    = 'Add Student';
$pageHeading  = 'Add Student';
$activePage   = 'students';
$topbarActions = '
    <a href="' . ROOT . '/admin/students"><button class="btn btn-primary">← Back to Students</button></a>
';

require_once __DIR__ . '/../layouts/admin_header.php';
require_once __DIR__ . '/../components/alert.php';

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
        <label for="diagnosis_select">Diagnosis</label>
        <?php
          $diagOptions = diagnosisOptions();
          $oldDiag     = $old['diagnosis'] ?? '';
          $isPreset    = in_array($oldDiag, $diagOptions, true);
        ?>
        <select id="diagnosis_select" name="diagnosis_select"
                onchange="document.getElementById('diagnosis_other_wrap').style.display = (this.value === '__other__') ? 'block' : 'none';">
          <option value="">— Select Diagnosis —</option>
          <?php foreach ($diagOptions as $d): ?>
            <option value="<?= esc($d) ?>" <?= ($isPreset && $oldDiag === $d) ? 'selected' : '' ?>>
              <?= esc($d) ?>
            </option>
          <?php endforeach; ?>
          <option value="__other__" <?= ($oldDiag !== '' && !$isPreset) ? 'selected' : '' ?>>Other (type below)</option>
        </select>

        <div id="diagnosis_other_wrap" style="margin-top:8px; display:<?= ($oldDiag !== '' && !$isPreset) ? 'block' : 'none' ?>;">
          <input type="text" id="diagnosis_other" name="diagnosis_other"
                 placeholder="Type the diagnosis"
                 value="<?= esc(!$isPreset ? $oldDiag : '') ?>">
        </div>
      </div>

      <div class="form-group">
        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
          <input type="checkbox" name="is_boarding" value="1"
                 <?= !empty($old['is_boarding']) ? 'checked' : '' ?>>
          This student is a boarding (residential) student
        </label>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Add Student</button>
        <a href="<?= ROOT ?>/admin/students" class="btn">Cancel</a>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>