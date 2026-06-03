<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Goal Detail';
$pageHeading = 'IEP Goal';
$activePage  = 'students';

$topbarActions = '
  <a href="' . ROOT . '/therapist/student/' . (int)$goal->student_id . '"><button class="btn btn-primary">Back to Student</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';
$milestones   = $milestones ?? [];
$totalCount   = count($milestones);
$achievedCount = 0;
foreach ($milestones as $m) {
  if ($m->is_achieved) {
    $achievedCount++;
  }
}
$milestonePercent = $totalCount > 0 ? round($achievedCount / $totalCount * 100) : 0;
$latestScore = isset($latestScore) && $latestScore !== null ? (int)$latestScore : 0;
?>

<!-- Goal info -->
<div class="card">
  <div class="card-header">
    <h2><?= esc($goal->goal_description) ?></h2>
  </div>
  <div class="card-body">
    <p><strong>Category:</strong> <?= esc($goal->category ?? '—') ?></p>
    <p><strong>Status:</strong> <?= ucfirst(esc($goal->status)) ?></p>
    <p><strong>Target Date:</strong> <?= date('d M Y', strtotime($goal->target_date)) ?></p>
  </div>
</div>

<!-- Milestones -->
<div class="card">
  <div class="card-header">
    <h2>Milestones</h2>
  </div>
  <div class="card-body">

    <p><strong><?= $achievedCount ?> of <?= $totalCount ?> achieved</strong></p>

    <!-- Progress bar: grey track, filled part is the achieved percentage -->
    <div style="background:#e2e8f0;border-radius:10px;height:18px;width:100%;overflow:hidden;">
      <div style="background:#4f46e5;height:100%;width:<?= (int)$milestonePercent ?>%;"></div>
    </div>
    <small><?= (int)$milestonePercent ?>%</small>

    <?php if (empty($milestones)): ?>
      <p style="margin-top:16px;">No milestones yet.</p>
    <?php else: ?>
      <table class="data-table" style="margin-top:16px;">
        <thead>
          <tr>
            <th>Milestone</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($milestones as $m): ?>
            <tr>
              <td><?= esc($m->description) ?></td>
              <td><?= $m->is_achieved ? 'Achieved' : 'Not yet' ?></td>
              <td>
                <form method="POST" action="<?= ROOT ?>/therapist/toggle-milestone" class="inline-form">
                  <input type="hidden" name="milestone_id" value="<?= (int)$m->id ?>">
                  <input type="hidden" name="is_achieved" value="<?= $m->is_achieved ? 0 : 1 ?>">
                  <button type="submit" class="btn btn-sm">
                    <?= $m->is_achieved ? 'Mark Not Achieved' : 'Mark Achieved' ?>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <!-- Add a milestone -->
    <form method="POST" action="<?= ROOT ?>/therapist/add-milestone" style="margin-top:16px;">
      <input type="hidden" name="goal_id" value="<?= (int)$goal->id ?>">
      <div class="form-group">
        <label for="description">New Milestone</label>
        <input type="text" id="description" name="description"
               placeholder="e.g. Says the first word of the sentence" required>
      </div>
      <button type="submit" class="btn btn-primary">Add Milestone</button>
    </form>

  </div>
</div>

<!-- Progress -->
<div class="card">
  <div class="card-header">
    <h2>Progress</h2>
  </div>
  <div class="card-body">

    <p><strong>Latest score:</strong> <?= $latestScore ?>%</p>

    <!-- Latest-score progress bar -->
    <div style="background:#e2e8f0;border-radius:10px;height:18px;width:100%;overflow:hidden;">
      <div style="background:#16a34a;height:100%;width:<?= (int)$latestScore ?>%;"></div>
    </div>

    <!-- Score history bar chart: one bar per entry, height = score percent -->
    <h3 style="margin-top:20px;">Score History</h3>
    <?php if (empty($progressChart)): ?>
      <p>No progress recorded yet.</p>
    <?php else: ?>
      <div style="display:flex;align-items:flex-end;gap:8px;height:120px;border-bottom:1px solid #e2e8f0;margin-top:8px;">
        <?php foreach ($progressChart as $entry): ?>
          <div style="display:flex;flex-direction:column;align-items:center;">
            <div title="Score: <?= (int)$entry->score ?>"
                 style="background:#16a34a;width:24px;height:<?= (int)$entry->score ?>%;border-radius:4px 4px 0 0;"></div>
            <small style="margin-top:4px;"><?= date('d M', strtotime($entry->recorded_at)) ?></small>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- Progress history table, newest first -->
    <?php if (!empty($progressList)): ?>
      <table class="data-table" style="margin-top:20px;">
        <thead>
          <tr>
            <th>Score</th>
            <th>Notes</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($progressList as $entry): ?>
            <tr>
              <td><?= (int)$entry->score ?>%</td>
              <td><?= esc($entry->notes ?? '—') ?></td>
              <td><?= date('d M Y', strtotime($entry->recorded_at)) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <!-- Record a new progress score -->
    <form method="POST" action="<?= ROOT ?>/therapist/record-progress" style="margin-top:16px;">
      <input type="hidden" name="goal_id" value="<?= (int)$goal->id ?>">
      <div class="form-group">
        <label for="score">New Score (0-100) <span style="color:#eb004e">*</span></label>
        <input type="number" id="score" name="score" min="0" max="100" required>
      </div>
      <div class="form-group">
        <label for="notes">Note</label>
        <textarea id="notes" name="notes" rows="2"
                  placeholder="Optional note about this progress"></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Record Progress</button>
    </form>

  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
