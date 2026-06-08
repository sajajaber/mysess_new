<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle    = 'Edit TEACCH Task';
$pageHeading  = 'Edit TEACCH Task';
$activePage   = 'teacch tasks';
$topbarActions = '
    <a href="' . ROOT . '/admin/teacch-tasks"><button class="btn btn-primary">Back to TEACCH Tasks</button></a>
';

require_once __DIR__ . '/../layouts/admin_header.php';
require_once __DIR__ . '/../components/alert.php';

$entry = $entry ?? (object) [
  'id'       => 0,
  'category' => '',
  'title'    => '',
];

$categories = $categories ?? [
  'Self-Care'         => 'Self-Care',
  'Daily Living'      => 'Daily Living',
  'Classroom Routine' => 'Classroom Routine',
  'Play/Leisure'      => 'Play/Leisure',
  'Vocational'        => 'Vocational',
  'Communication'     => 'Communication',
];

$categoryColors = [
  'Self-Care'         => 'background:#ecfeff;color:#0f766e;',
  'Daily Living'      => 'background:#f0fdf4;color:#15803d;',
  'Classroom Routine' => 'background:#eff6ff;color:#1d4ed8;',
  'Play/Leisure'      => 'background:#f5f3ff;color:#6d28d9;',
  'Vocational'        => 'background:#fff7ed;color:#c2410c;',
  'Communication'     => 'background:#fef2f2;color:#b91c1c;',
];
?>

<div class="card">
  <div class="card-header">
    <h2>Edit TEACCH Task</h2>
  </div>
  <div class="card-body">
    <form method="POST" action="<?= ROOT ?>/admin/edit_teacch_task/<?= (int)$entry->id ?>">

      <div class="form-group">
        <label for="category">Category <span style="color:#eb004e">*</span></label>
        <?php renderSelect(
          'category',
          $categories,
          $entry->category,
          ['id' => 'category', 'required' => true]
        ); ?>

        <div id="category-badge">
          <?php var_dump($entry->category, isset($categoryColors[$entry->category])); ?>
        </div>
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

<script>
  const categoryColors = <?= json_encode($categoryColors) ?>;

  document.getElementById('category').addEventListener('change', function() {
    const badge = document.getElementById('category-badge');
    const val = this.value;
    const style = categoryColors[val] ?? '';

    if (val && style) {
      badge.innerHTML = '<span style="' + style + ' padding:3px 12px; border-radius:20px; font-size:13px; font-weight:600;">' + val + '</span>';
    } else {
      badge.innerHTML = '';
    }
  });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>