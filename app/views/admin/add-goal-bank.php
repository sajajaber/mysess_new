<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle    = 'Add Goal Bank Entry';
$pageHeading  = 'Add Goal Bank Entry';
$activePage   = 'goal bank';
$topbarActions = '
    <a href="' . ROOT . '/admin/goal-bank"><button class="btn btn-primary">Back to Goal Bank</button></a>
';

require_once __DIR__ . '/../layouts/admin_header.php';
require_once __DIR__ . '/../components/alert.php';

$categoryOptions = [];
foreach ($categories ?? [] as $value => $label) {
  $categoryOptions[$value] = [
    'label' => $label,
    'style' => [
      'Communication' => 'background:#ecfeff;color:#0f766e;',
      'Social'        => 'background:#f5f3ff;color:#6d28d9;',
      'Motor'         => 'background:#fff7ed;color:#c2410c;',
      'Academic'      => 'background:#eff6ff;color:#1d4ed8;',
      'Behavioral'    => 'background:#fef2f2;color:#b91c1c;',
      'Daily Living'  => 'background:#f0fdf4;color:#15803d;',
    ][$value] ?? '',
  ];
}
?>

<div class="card">
  <div class="card-header">
    <h2>New Goal Bank Entry</h2>
  </div>

  <div class="card-body">
    <form method="POST" action="<?= ROOT ?>/admin/add-goal-bank">

      <div class="form-group">
        <label for="category">Category <span style="color:#eb004e">*</span></label>
        <?php
          renderSelect(
            'category',
            $categoryOptions,
            '',
            ['id' => 'category', 'required' => true]
          );
        ?>
      </div>

      <div class="form-group">
        <label for="goal_text">Goal Text <span style="color:#eb004e">*</span></label>
        <textarea id="goal_text" name="goal_text" rows="3"
                  placeholder="e.g. Greet a classmate using words or signs" required></textarea>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Save Entry</button>
        <a href="<?= ROOT ?>/admin/goal-bank" class="btn">Cancel</a>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
