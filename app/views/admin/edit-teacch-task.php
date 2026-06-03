<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle    = 'Edit TEACCH Task';
$pageHeading  = 'Edit TEACCH Task';
$activePage   = 'teacch tasks';
$topbarActions = '
    <a href="' . ROOT . '/admin/teacch-tasks"><button class="btn btn-primary">Back to TEACCH Tasks</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';
?>

<div class="card">
  <div class="card-header">
    <h2>Edit TEACCH Task</h2>
  </div>

  <div class="card-body">
    <form method="POST" action="<?= ROOT ?>/admin/edit-teacch-task/<?= (int)$entry->id ?>">

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
        <label for="title">Task Title <span style="color:#eb004e">*</span></label>
        <input type="text" id="title" name="title"
               value="<?= esc($entry->title) ?>" required>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="<?= ROOT ?>/admin/teacch-tasks" class="btn">Cancel</a>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
