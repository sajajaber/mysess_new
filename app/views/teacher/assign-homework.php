<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'Assign Homework';
$pageHeading = 'Assign Homework';
$activePage  = 'homework';

$topbarActions = '
  <a href="' . ROOT . '/teacher/homework"><button class="btn btn-primary">Back to My Homework</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

$students = $students ?? [];
?>

<div class="card">
  <div class="card-header">
    <h2>New Homework</h2>
  </div>

  <div class="card-body">

    <?php if (empty($students)): ?>
      <!-- No students assigned yet, so there is nobody to give homework to -->
      <p>You have no assigned students to give homework to.</p>
      <a href="<?= ROOT ?>/teacher/students" class="btn btn-primary">My Students</a>

    <?php else: ?>
      <form method="POST" action="<?= ROOT ?>/teacher/assign-homework">

        <div class="form-group">
          <label for="title">Title <span style="color:#eb004e">*</span></label>
          <input type="text" id="title" name="title" placeholder="e.g. Read page 12" required>
        </div>

        <div class="form-group">
          <label for="description">Description</label>
          <textarea id="description" name="description" rows="3"
                    placeholder="Any extra details (optional)"></textarea>
        </div>

        <div class="form-group">
          <label for="due_date">Due Date <span style="color:#eb004e">*</span></label>
          <input type="date" id="due_date" name="due_date" value="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="form-group">
          <label for="target">Assign To <span style="color:#eb004e">*</span></label>
          <select id="target" name="target" required>
            <option value="class">Whole class (all my students)</option>
            <?php foreach ($students as $student): ?>
              <option value="<?= (int)$student->id ?>">
                <?= esc($student->first_name . ' ' . $student->last_name) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <button type="submit" class="btn btn-primary">Assign Homework</button>
          <a href="<?= ROOT ?>/teacher/homework" class="btn">Cancel</a>
        </div>

      </form>
    <?php endif; ?>

  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
