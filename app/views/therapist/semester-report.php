<?php

require_once __DIR__ . '/../../core/config.php';

$student       = $student;
$semesterLabel = $semesterLabel ?? '';
$report        = $report        ?? [];
$sessions      = $sessions      ?? [];
$teacch        = $teacch        ?? [];
$therapistName = $therapistName ?? '';

$studentName = trim($student->first_name . ' ' . $student->last_name);

$pageTitle   = 'Semester Report';
$pageHeading = 'Semester Report';
$activePage  = 'sessions';
$topbarActions = '
  <a href="' . ROOT . '/therapist/semester-report"><button class="btn">Build Another</button></a>
  <button class="btn btn-primary" onclick="window.print()">Print / Save PDF</button>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';
function statusColor($status)
{
  if ($status === 'Met') {
    return '#16a34a';
  }
  if ($status === 'In Progress') {
    return '#d97706';
  }
  return '#dc2626';
}
function progressCircle($percent, $color, $size = 120)
{
  $percent = max(0, min(100, (int)$percent));
  $inner   = $size - 26; // hole size
?>
  <div style="width:<?= $size ?>px; height:<?= $size ?>px; border-radius:50%;
              background: conic-gradient(<?= $color ?> <?= $percent ?>%, #e2e8f0 <?= $percent ?>% 100%);
              display:flex; align-items:center; justify-content:center;">
    <div style="width:<?= $inner ?>px; height:<?= $inner ?>px; border-radius:50%; background:#fff;
                display:flex; align-items:center; justify-content:center;
                font-size:<?= (int)($size / 5) ?>px; font-weight:700; color:<?= $color ?>;">
      <?= $percent ?>%
    </div>
  </div>
<?php
}
$metCount = 0;
$inProgressCount = 0;
$notMetCount = 0;
$scoreSum = 0;
$scoreCount = 0;
foreach ($report as $block) {
  if ($block['status'] === 'Met') {
    $metCount++;
  } elseif ($block['status'] === 'In Progress') {
    $inProgressCount++;
  } else {
    $notMetCount++;
  }
  if ($block['current'] !== null) {
    $scoreSum += $block['current'];
    $scoreCount++;
  }
}
$overallPercent = $scoreCount ? (int)round($scoreSum / $scoreCount) : 0;
?>

<!-- The whole printable report lives in this card -->
<div class="card" id="report-sheet">
  <div class="card-body">

    <!-- Report header -->
    <div style="text-align:center; border-bottom:2px solid #4f46e5; padding-bottom:16px; margin-bottom:24px;">
      <h1 style="margin:0; font-size:24px;">MySESS Semester Progress Report</h1>
      <p style="margin:6px 0 0; color:#64748b;"><?= esc($semesterLabel) ?></p>
    </div>

    <!-- Student + report info -->
    <div style="display:flex; flex-wrap:wrap; gap:24px; margin-bottom:24px;">
      <div>
        <div style="color:#64748b; font-size:13px;">Student</div>
        <div style="font-weight:600; font-size:16px;"><?= esc($studentName) ?></div>
      </div>
      <div>
        <div style="color:#64748b; font-size:13px;">Diagnosis</div>
        <div style="font-weight:600; font-size:16px;"><?= esc($student->diagnosis ?: '—') ?></div>
      </div>
      <div>
        <div style="color:#64748b; font-size:13px;">Prepared by</div>
        <div style="font-weight:600; font-size:16px;"><?= esc($therapistName ?: '—') ?></div>
      </div>
      <div>
        <div style="color:#64748b; font-size:13px;">Generated</div>
        <div style="font-weight:600; font-size:16px;"><?= date('d M Y') ?></div>
      </div>
    </div>

    <!-- Overall snapshot: a big progress circle next to the status counts -->
    <div style="display:flex; gap:28px; align-items:center; flex-wrap:wrap; background:#f8fafc;
                border:1px solid #e2e8f0; border-radius:12px; padding:20px; margin-bottom:28px;">
      <div style="text-align:center;">
        <?php progressCircle($overallPercent, '#4f46e5', 130); ?>
        <div style="color:#64748b; font-size:13px; margin-top:8px;">Overall goal progress</div>
      </div>
      <div style="display:flex; gap:12px; flex-wrap:wrap; flex:1;">
        <div style="flex:1; min-width:110px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:14px; text-align:center;">
          <div style="font-size:28px; font-weight:700; color:#16a34a;"><?= $metCount ?></div>
          <div style="color:#64748b; font-size:13px;">Goals Met</div>
        </div>
        <div style="flex:1; min-width:110px; background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:14px; text-align:center;">
          <div style="font-size:28px; font-weight:700; color:#d97706;"><?= $inProgressCount ?></div>
          <div style="color:#64748b; font-size:13px;">In Progress</div>
        </div>
        <div style="flex:1; min-width:110px; background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:14px; text-align:center;">
          <div style="font-size:28px; font-weight:700; color:#dc2626;"><?= $notMetCount ?></div>
          <div style="color:#64748b; font-size:13px;">Not Met</div>
        </div>
      </div>
    </div>

    <!-- One block per goal -->
    <h2 style="font-size:18px; border-bottom:1px solid #e2e8f0; padding-bottom:8px;">IEP Goals &amp; Progress</h2>

    <?php if (empty($report)): ?>
      <p style="color:#64748b;">This student has no IEP goals yet.</p>
    <?php else: ?>
      <?php foreach ($report as $block): ?>
        <?php
          $goal     = $block['goal'];
          $baseline = $block['baseline'];
          $current  = $block['current'];
          $change   = $block['change'];
          $color    = statusColor($block['status']);
        ?>
        <div style="border:1px solid #e2e8f0; border-radius:10px; padding:16px; margin-bottom:16px; page-break-inside:avoid;
                    display:flex; gap:18px; align-items:flex-start;">

          <!-- Per-goal circular gauge of the current level -->
          <div style="flex-shrink:0; text-align:center;">
            <?php progressCircle($current === null ? 0 : $current, $color, 90); ?>
          </div>

          <div style="flex:1;">
            <!-- Goal heading: category + status badge -->
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px;">
              <div>
                <span style="background:#eef2ff; color:#4f46e5; font-size:12px; padding:2px 8px; border-radius:10px;">
                  <?= esc($goal->category ?: 'General') ?>
                </span>
                <div style="font-weight:600; margin-top:6px;"><?= esc($goal->goal_description) ?></div>
              </div>
              <span style="background:<?= $color ?>; color:#fff; font-size:12px; padding:4px 10px; border-radius:10px; white-space:nowrap;">
                <?= esc($block['status']) ?>
              </span>
            </div>

            <!-- Baseline -> current numbers -->
            <div style="display:flex; gap:24px; margin-top:14px; flex-wrap:wrap;">
              <div>
                <div style="color:#64748b; font-size:12px;">Baseline</div>
                <div style="font-weight:600;"><?= $baseline === null ? '—' : $baseline . '%' ?></div>
              </div>
              <div>
                <div style="color:#64748b; font-size:12px;">Current</div>
                <div style="font-weight:600;"><?= $current === null ? '—' : $current . '%' ?></div>
              </div>
              <div>
                <div style="color:#64748b; font-size:12px;">Change</div>
                <div style="font-weight:600; color:<?= ($change !== null && $change >= 0) ? '#16a34a' : '#dc2626' ?>;">
                  <?php if ($change === null): ?>
                    —
                  <?php else: ?>
                    <?= ($change >= 0 ? '+' : '') . $change ?> pts
                  <?php endif; ?>
                </div>
              </div>
              <div>
                <div style="color:#64748b; font-size:12px;">Data points</div>
                <div style="font-weight:600;"><?= (int)$block['entries'] ?></div>
              </div>
            </div>

            <!-- Milestones for this goal -->
            <?php if (!empty($block['milestones'])): ?>
              <div style="margin-top:14px;">
                <div style="color:#64748b; font-size:12px; margin-bottom:6px;">
                  Milestones (<?= (int)$block['milestoneDone'] ?> of <?= (int)$block['milestoneTotal'] ?> achieved)
                </div>
                <ul style="margin:0; padding-left:18px;">
                  <?php foreach ($block['milestones'] as $m): ?>
                    <li style="margin-bottom:3px;">
                      <?= $m->is_achieved ? '[done]' : '[ ]' ?>
                      <?= esc($m->description) ?>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
          </div>

        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- TEACCH structured-task progress -->
    <h2 style="font-size:18px; border-bottom:1px solid #e2e8f0; padding-bottom:8px; margin-top:28px;">TEACCH Progress</h2>

    <?php if (empty($teacch)): ?>
      <p style="color:#64748b;">No TEACCH schedules for this student.</p>
    <?php else: ?>
      <?php foreach ($teacch as $sched): ?>
        <div style="border:1px solid #e2e8f0; border-radius:10px; padding:16px; margin-bottom:16px; page-break-inside:avoid;
                    display:flex; gap:18px; align-items:flex-start;">

          <!-- Schedule-level circle (average independence of its tasks) -->
          <div style="flex-shrink:0; text-align:center;">
            <?php progressCircle($sched['percent'], '#0ea5e9', 90); ?>
          </div>

          <div style="flex:1;">
            <div style="font-weight:600;"><?= esc($sched['title']) ?></div>
            <div style="color:#64748b; font-size:12px; margin-bottom:10px;">
              <?= (int)$sched['rated'] ?> of <?= (int)$sched['total'] ?> tasks rated this semester
            </div>

            <?php if (empty($sched['tasks'])): ?>
              <div style="color:#64748b; font-size:13px;">No tasks in this schedule yet.</div>
            <?php else: ?>
              <table class="data-table">
                <thead>
                  <tr><th>#</th><th>Task</th><th>Latest Level</th><th>Ratings</th></tr>
                </thead>
                <tbody>
                  <?php foreach ($sched['tasks'] as $t): ?>
                    <?php
                      $levelLabels = [
                        'full_prompt'    => 'Full prompt',
                        'partial_prompt' => 'Partial prompt',
                        'independent'    => 'Independent',
                      ];
                      $levelText = $t['level'] ? $levelLabels[$t['level']] : 'Not rated';
                    ?>
                    <tr>
                      <td><?= (int)$t['order'] ?></td>
                      <td><?= esc($t['title']) ?></td>
                      <td><?= esc($levelText) ?></td>
                      <td><?= (int)$t['entries'] ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
          </div>

        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- Therapy sessions in this semester -->
    <h2 style="font-size:18px; border-bottom:1px solid #e2e8f0; padding-bottom:8px; margin-top:28px;">
      Therapy Sessions (<?= count($sessions) ?>)
    </h2>
    <?php if (empty($sessions)): ?>
      <p style="color:#64748b;">No therapy sessions recorded in this semester.</p>
    <?php else: ?>
      <table class="data-table">
        <thead>
          <tr><th>Date</th><th>Type</th><th>Status</th><th>Goal Addressed</th><th>Notes</th></tr>
        </thead>
        <tbody>
          <?php foreach ($sessions as $s): ?>
            <tr>
              <td><?= date('d M Y', strtotime($s->session_date)) ?></td>
              <td><?= esc($s->session_type) ?></td>
              <td><?= ucfirst(esc($s->status)) ?></td>
              <td><?= esc($s->goal_addressed ?: '—') ?></td>
              <td><?= esc($s->performance_notes ?: '—') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <!-- Signature line for the printed copy -->
    <div style="margin-top:40px; display:flex; justify-content:space-between; gap:40px;">
      <div style="flex:1; border-top:1px solid #94a3b8; padding-top:6px; color:#64748b; font-size:13px;">Therapist signature</div>
      <div style="flex:1; border-top:1px solid #94a3b8; padding-top:6px; color:#64748b; font-size:13px;">Parent signature</div>
    </div>

  </div>
</div>

<!-- When printing, hide the sidebar/topbar and keep the circles' colours -->
<style media="print">
  .sidebar, .topbar, #navigation { display: none !important; }
  .main { margin: 0 !important; }
  #report-sheet { box-shadow: none !important; border: none !important; }
  * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
