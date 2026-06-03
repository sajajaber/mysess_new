<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle    = 'Edit Goal Bank Entry';
$pageHeading  = 'Edit Goal Bank Entry';
$activePage   = 'goal bank';
$topbarActions = '
    <a href="' . ROOT . '/admin/goal-bank"><button class="btn btn-primary">Back to Goal Bank</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';
?>

<div class="card">
  <div class="card-header">
    <h2>Edit Goal Bank Entry</h2>
  </div>

  <div class="card-body">
    <form method="POST" action="<?= ROOT ?>/admin/edit-goal-bank/<?= (int)$entry->id ?>">

      <div class="form-group">
        <label for="category">Category <span style="color:#eb004e">*</span></label>
        <?php
          renderSelect(
            'category',
            $categories,
            $entry->category,
            ['id' => 'category', 'required' => true]
          );
        ?>
      </div>

      <div class="form-group">
        <label for="goal_text">Goal Text <span style="color:#eb004e">*</span></label>
        <textarea id="goal_text" name="goal_text" rows="3" required><?= esc($entry->goal_text) ?></textarea>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="<?= ROOT ?>/admin/goal-bank" class="btn">Cancel</a>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
