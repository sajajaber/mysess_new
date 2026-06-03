<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Visual Schedule';
$pageHeading = 'Visual Schedule';
$activePage  = 'students';

$topbarActions = '
  <a href="' . ROOT . '/teacher/student/' . (int)$schedule->student_id . '"><button class="btn btn-primary">Back to Student</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';
function independenceBadge($level)
{
  $map = [
    'full_prompt'    => ['Full Prompt',    '#dc2626'],  // red: most support needed
    'partial_prompt' => ['Partial Prompt', '#d97706'],  // amber: some support
    'independent'    => ['Independent',    '#16a34a'],  // green: no support
  ];

  if (!$level || !isset($map[$level])) {
    $label = 'Not rated yet';
    $color = '#94a3b8';
  } else {
    $label = $map[$level][0];
    $color = $map[$level][1];
  }
?>
  <span style="display:inline-block;padding:2px 10px;border-radius:10px;
               background:<?= $color ?>;color:#fff;font-size:12px;font-weight:600;">
    <?= esc($label) ?>
  </span>
<?php
}
$levelOptions = [
  'full_prompt'    => 'Full Prompt',
  'partial_prompt' => 'Partial Prompt',
  'independent'    => 'Independent',
];
?>

<div class="card">
  <div class="card-header">
    <h2><?= esc($schedule->title) ?></h2>
  </div>
  <div class="card-body">

    <?php if (empty($tasks)): ?>
      <p>No tasks on this schedule yet. Add the first one below.</p>
    <?php endif; ?>

    <?php foreach ($tasks as $task): ?>
      <div class="card" style="margin-bottom:16px;">
        <div class="card-body">

          <h3>
            <?= (int)$task->task_order ?>. <?= esc($task->title) ?>
            <?php if (!empty($task->visual_cue_url)): ?>
              <a href="<?= esc($task->visual_cue_url) ?>" target="_blank" style="font-size:13px;">(visual cue)</a>
            <?php endif; ?>
          </h3>

          <p>
            Latest:
            <?php independenceBadge($task->latest->independence_level ?? null); ?>
          </p>

          <!-- Rating history, newest first -->
          <?php if (!empty($task->history)): ?>
            <table class="data-table">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Level</th>
                  <th>Note</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($task->history as $row): ?>
                  <tr>
                    <td><?= date('d M Y', strtotime($row->session_date)) ?></td>
                    <td><?= esc($levelOptions[$row->independence_level] ?? $row->independence_level) ?></td>
                    <td><?= esc($row->notes ?? '—') ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>

          <!-- Rate this task -->
          <form method="POST" action="<?= ROOT ?>/teacher/rate-independence" style="margin-top:12px;">
            <input type="hidden" name="task_id" value="<?= (int)$task->id ?>">

            <div class="form-group">
              <label>Session Date <span style="color:#eb004e">*</span></label>
              <input type="date" name="session_date" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
              <label>Independence Level <span style="color:#eb004e">*</span></label>
              <?php renderSelect('independence_level', $levelOptions, 'full_prompt', ['required' => true]); ?>
            </div>

            <div class="form-group">
              <label>Note</label>
              <textarea name="notes" rows="2" placeholder="Optional note about this rating"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Rate Independence</button>
          </form>

        </div>
      </div>
    <?php endforeach; ?>

  </div>
</div>

<!-- Add a task to this schedule -->
<div class="card">
  <div class="card-header">
    <h2>Add Task</h2>
  </div>
  <div class="card-body">
    <form method="POST" action="<?= ROOT ?>/teacher/add-task">
      <input type="hidden" name="schedule_id" value="<?= (int)$schedule->id ?>">

      <div class="form-group">
        <label for="task_order">Order <span style="color:#eb004e">*</span></label>
        <input type="number" id="task_order" name="task_order" min="1" value="<?= (int)$nextOrder ?>" required>
      </div>

      <div class="form-group">
        <label for="bank_category">Filter task bank by category</label>
        <?php
          renderSelect(
            'bank_category',
            [
              'Self-Care'         => 'Self-Care',
              'Daily Living'      => 'Daily Living',
              'Classroom Routine' => 'Classroom Routine',
              'Play/Leisure'      => 'Play/Leisure',
              'Vocational'        => 'Vocational',
              'Communication'     => 'Communication',
            ],
            '',
            ['id' => 'bank_category', 'onchange' => 'fillTaskPicker()']
          );
        ?>
      </div>

      <div class="form-group">
        <label for="bank_pick">Choose from task bank (optional)</label>
        <select id="bank_pick" onchange="document.getElementById('title').value = this.value;">
          <option value="">— choose from task bank (optional) —</option>
        </select>
      </div>

      <script>
        var bankTasks = [
          <?php foreach (($bankEntries ?? []) as $entry): ?>
          { category: "<?= esc($entry->category) ?>", title: "<?= esc(addslashes($entry->title)) ?>" },
          <?php endforeach; ?>
        ];
        function fillTaskPicker() {
          var chosenCategory = document.getElementById('bank_category').value;
          var picker = document.getElementById('bank_pick');
          picker.innerHTML = '<option value="">— choose from task bank (optional) —</option>';
          for (var i = 0; i < bankTasks.length; i++) {
            if (bankTasks[i].category === chosenCategory) {
              var option = document.createElement('option');
              option.value = bankTasks[i].title;
              option.textContent = bankTasks[i].title;
              picker.appendChild(option);
            }
          }
        }
        fillTaskPicker();
      </script>

      <div class="form-group">
        <label for="title">Task Title <span style="color:#eb004e">*</span></label>
        <input type="text" id="title" name="title" placeholder="e.g. Wash hands" required>
      </div>

      <div class="form-group">
        <label for="visual_cue_url">Visual Cue URL (optional)</label>
        <input type="text" id="visual_cue_url" name="visual_cue_url"
               placeholder="Optional link or path to a picture symbol">
      </div>

      <button type="submit" class="btn btn-primary">Add Task</button>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
