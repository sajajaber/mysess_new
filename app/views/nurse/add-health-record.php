<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle    = 'Add Health Record';
$pageHeading  = 'Add Health Record';
$activePage   = 'health-records';
$topbarActions = '<a href="' . ROOT . '/nurse/health-records">
<button class="btn btn-primary">← Back to Records</button></a>';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

// Passed from NurseController::add_health_record()
$students = $students ?? [];
$record   = $record   ?? new stdClass();
$isEdit   = isset($record->id);
?>

<div class="card">
  <div class="card-header">
    <h2><?= $isEdit ? 'Edit Health Record' : 'New Health Record' ?></h2>
  </div>

  <div class="card-body">
    <form method="POST" action="<?= ROOT ?>/nurse/add_health_record">

      <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= $record->id ?>">
      <?php endif; ?>

      <div class="form-group">
        <label for="student_id">Student *</label>
        <select id="student_id" name="student_id" required>
          <option value="">Select Student</option>

          <?php foreach ($students as $s): ?>
            <option value="<?= $s->id ?>"
              <?= (($record->student_id ?? null) == $s->id) ? 'selected' : '' ?>>
              <?= esc($s->first_name . ' ' . $s->last_name) ?>
            </option>
          <?php endforeach; ?>

        </select>
      </div>

      <div class="form-group">
        <label for="record_type">Record Type *</label>

        <?php
        renderSelect(
          'record_type',
          [
            'checkup'     => 'Checkup',
            'medication'  => 'Medication',
            'injury'      => 'Injury',
            'illness'     => 'Illness',
            'vaccination' => 'Vaccination',
            'other'       => 'Other'
          ],
          $record->record_type ?? '',
          ['id' => 'record_type', 'required' => true]
        );
        ?>
      </div>

      <div class="form-group">
        <label for="title">Title *</label>
        <input type="text" id="title" name="title" required
          value="<?= esc($record->title ?? '') ?>">
      </div>

      <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"><?= esc($record->description ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label for="recorded_at">Date *</label>
        <input type="date" id="recorded_at" name="recorded_at" required
          value="<?= esc($record->recorded_at ?? date('Y-m-d')) ?>">
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Save Record</button>
        <a href="<?= ROOT ?>/nurse/health-records" class="btn">Cancel</a>
      </div>

    </form>
  </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>