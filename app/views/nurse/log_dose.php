<?php
// LOAD CONFIG FIRST - BEFORE ANYTHING ELSE
require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'Log Dose';
$pageHeading = 'Log Dose';
$activePage  = 'medications';

$topbarActions = '
<a href="' . ROOT . '/nurse/all_medications"><button class="btn btn-primary">Back to Medications</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';
?>

<div class="card">
  <div class="card-header">
    <h2>Log Dose</h2>
  </div>
  <div class="card-body">

    <?php if (!$medication): ?>
      <p>Medication not found. <a href="<?= ROOT ?>/nurse/all_medications">Go back</a></p>
    <?php else: ?>

      <!-- Medication summary -->
      <div class="info-banner" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;margin-bottom:24px;">
        <strong><?= esc($medication->name) ?></strong>
        &nbsp;·&nbsp; <?= esc($medication->dosage) ?>
        &nbsp;·&nbsp; <?= esc($medication->frequency) ?>
        <?php if (!empty($medication->instructions)): ?>
          <br><small style="color:#666;margin-top:4px;display:block;"><?= esc($medication->instructions) ?></small>
        <?php endif; ?>
      </div>

      <form method="POST" action="<?= ROOT ?>/nurse/log_dose">
        <input type="hidden" name="medication_id" value="<?= (int)$medication->id ?>">
        <input type="hidden" name="student_id" value="<?= (int)$medication->student_id ?>">

        <div class="form-row">
          <div class="form-group">
            <label for="administered_at">Date &amp; Time Administered <span style="color:#eb004e">*</span></label>
            <input
              type="datetime-local"
              id="administered_at"
              name="administered_at"
              value="<?= date('Y-m-d\TH:i') ?>"
              required>
          </div>
        </div>

        <div class="form-group">
          <label for="notes">Notes</label>
          <textarea
            id="notes"
            name="notes"
            rows="3"
            placeholder="Any observations, reactions, or notes..."></textarea>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary">Save Log</button>
          <a href="<?= ROOT ?>/nurse/student/<?= (int)$medication->student_id ?>">
            <button type="button" class="btn btn-secondary">Cancel</button>
          </a>
        </div>
      </form>

    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>