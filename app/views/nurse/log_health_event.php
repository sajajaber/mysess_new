<?php
// LOAD CONFIG FIRST - BEFORE ANYTHING ELSE
require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'Log Health Event';
$pageHeading = 'Log Health Event';
$activePage  = 'students';

$topbarActions = $student
  ? '<a href="' . ROOT . '/nurse/student/' . (int)$student->id . '"><button class="btn btn-primary">Back to Student</button></a>'
  : '<a href="' . ROOT . '/nurse/students"><button class="btn btn-primary">Back to Students</button></a>';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';
?>

<div class="card">
  <div class="card-header">
    <h2>Log Health Event<?= $student ? ' — ' . esc($student->first_name . ' ' . $student->last_name) : '' ?></h2>
  </div>
  <div class="card-body">

    <?php if (!$student): ?>
      <p>Student not found. <a href="<?= ROOT ?>/nurse/students">Go back</a></p>
    <?php else: ?>

      <form method="POST" action="<?= ROOT ?>/nurse/log_health_event">
        <input type="hidden" name="student_id" value="<?= (int)$student->id ?>">

        <div class="form-group">
          <label for="description">Description <span style="color:#eb004e">*</span></label>
          <textarea
            id="description"
            name="description"
            rows="3"
            placeholder="What happened? Describe the health event..."
            required></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="severity">Severity <span style="color:#eb004e">*</span></label>
            <select id="severity" name="severity" required>
              <option value="">Select severity</option>
              <option value="low">🟢 Low</option>
              <option value="medium">🟡 Medium</option>
              <option value="high">🔴 High</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="action_taken">Action Taken</label>
          <textarea
            id="action_taken"
            name="action_taken"
            rows="3"
            placeholder="What action was taken in response? (optional)"></textarea>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary">Save Event</button>
          <a href="<?= ROOT ?>/nurse/student/<?= (int)$student->id ?>">
            <button type="button" class="btn btn-secondary">Cancel</button>
          </a>
        </div>
      </form>

    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>