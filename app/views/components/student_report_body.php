<?php
// Shared printable student report body.
// Expects: $reportData (from StudentReport::build), $semesterLabel (optional string),
//          $preparedBy (optional string), $boardingStats (optional array|null)

$rd            = $reportData;
$student       = $rd['student'];
$goals         = $rd['goals']        ?? [];
$teacch        = $rd['teacch']       ?? [];
$sessions      = $rd['sessions']     ?? [];
$observations  = $rd['observations'] ?? [];
$therapies     = $rd['therapies']    ?? [];
$homework      = $rd['homework']     ?? [];
$staff         = $rd['staff']        ?? [];
$medications   = $rd['medications']  ?? [];
$healthRecords = $rd['healthRecords'] ?? [];
$healthEvents  = $rd['healthEvents'] ?? [];
$semesterLabel = $semesterLabel ?? '';
$preparedBy    = $preparedBy    ?? '';
$boardingStats = $boardingStats ?? null;

$studentName = trim($student->first_name . ' ' . $student->last_name);

$initials = strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1));
$photo    = $student->photo_url ?? '';
$photoUrl = $photo ? ROOT . '/public/assets/uploads/' . $photo : '';

if (!function_exists('srStatusColor')) {
  function srStatusColor($status)
  {
    if ($status === 'Met') return '#16a34a';
    if ($status === 'In Progress') return '#d97706';
    return '#dc2626';
  }
  function srCircle($percent, $color, $size = 110)
  {
    $percent = max(0, min(100, (int)$percent));
    $inner   = $size - 26;
    echo '<div style="width:' . $size . 'px;height:' . $size . 'px;border-radius:50%;'
       . 'background:conic-gradient(' . $color . ' ' . $percent . '%,#e2e8f0 ' . $percent . '% 100%);'
       . 'display:flex;align-items:center;justify-content:center;">'
       . '<div style="width:' . $inner . 'px;height:' . $inner . 'px;border-radius:50%;background:#fff;'
       . 'display:flex;align-items:center;justify-content:center;font-size:' . (int)($size / 5) . 'px;'
       . 'font-weight:700;color:' . $color . ';">' . $percent . '%</div></div>';
  }
}

$metCount = 0; $inProgressCount = 0; $notMetCount = 0; $scoreSum = 0; $scoreCount = 0;
foreach ($goals as $b) {
  if ($b['status'] === 'Met') $metCount++;
  elseif ($b['status'] === 'In Progress') $inProgressCount++;
  else $notMetCount++;
  if ($b['current'] !== null) { $scoreSum += $b['current']; $scoreCount++; }
}
$overallPercent = $scoreCount ? (int)round($scoreSum / $scoreCount) : 0;
?>

<div class="card" id="report-sheet">
  <div class="card-body">

    <!-- Header -->
    <div style="display:flex; gap:18px; align-items:center; border-bottom:2px solid #4f46e5; padding-bottom:16px; margin-bottom:20px;">
      <?php if ($photoUrl): ?>
        <img src="<?= $photoUrl ?>" alt="" style="width:74px;height:74px;border-radius:50%;object-fit:cover;border:1px solid #e2e8f0;">
      <?php else: ?>
        <div style="width:74px;height:74px;border-radius:50%;background:#4f46e5;color:#fff;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;"><?= $initials ?></div>
      <?php endif; ?>
      <div style="flex:1;">
        <h1 style="margin:0; font-size:22px;"><?= esc($studentName) ?> — Student Report</h1>
        <div style="color:#64748b; font-size:13px; margin-top:4px;">
          <?php if ($semesterLabel): ?><?= esc($semesterLabel) ?> &nbsp;|&nbsp; <?php endif; ?>
          DOB <?= date('d M Y', strtotime($student->date_of_birth)) ?>
          <?php if ($student->diagnosis): ?> &nbsp;|&nbsp; <?= esc($student->diagnosis) ?><?php endif; ?>
          <?php if ($preparedBy): ?> &nbsp;|&nbsp; Prepared by <?= esc($preparedBy) ?><?php endif; ?>
          &nbsp;|&nbsp; <?= date('d M Y') ?>
        </div>
      </div>
    </div>

    <!-- Overall snapshot -->
    <div style="display:flex; gap:28px; align-items:center; flex-wrap:wrap; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:20px; margin-bottom:24px;">
      <div style="text-align:center;">
        <?php srCircle($overallPercent, '#4f46e5', 130); ?>
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

    <!-- Care team -->
    <h2 style="font-size:18px; border-bottom:1px solid #e2e8f0; padding-bottom:6px;">Care Team</h2>
    <?php if (empty($staff)): ?>
      <p style="color:#64748b;">No staff currently assigned.</p>
    <?php else: ?>
      <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:8px;">
        <?php foreach ($staff as $u): ?>
          <div style="background:#eef2ff; border:1px solid #c7d2fe; border-radius:10px; padding:8px 12px; min-width:200px;">
            <div style="font-weight:600;"><?= esc(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')) ?></div>
            <div style="color:#475569; font-size:12px;">
              <?= ucfirst(str_replace('_', ' ', esc($u->role_type ?? $u->role ?? ''))) ?>
              <?php if (!empty($u->email)): ?> &nbsp;|&nbsp; <?= esc($u->email) ?><?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- IEP goals -->
    <h2 style="font-size:18px; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-top:24px;">IEP Goals &amp; Progress</h2>
    <?php if (empty($goals)): ?>
      <p style="color:#64748b;">No IEP goals on file.</p>
    <?php else: ?>
      <?php foreach ($goals as $b):
        $goal = $b['goal']; $color = srStatusColor($b['status']); ?>
        <div style="border:1px solid #e2e8f0; border-radius:10px; padding:16px; margin-bottom:14px; page-break-inside:avoid; display:flex; gap:18px; align-items:flex-start;">
          <div style="flex-shrink:0; text-align:center;">
            <?php srCircle($b['current'] === null ? 0 : $b['current'], $color, 90); ?>
          </div>
          <div style="flex:1;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px;">
              <div>
                <span style="background:#eef2ff; color:#4f46e5; font-size:12px; padding:2px 8px; border-radius:10px;"><?= esc($goal->category ?: 'General') ?></span>
                <div style="font-weight:600; margin-top:6px;"><?= esc($goal->goal_text) ?></div>
              </div>
              <span style="background:<?= $color ?>; color:#fff; font-size:12px; padding:4px 10px; border-radius:10px; white-space:nowrap;"><?= esc($b['status']) ?></span>
            </div>
            <div style="display:flex; gap:24px; margin-top:14px; flex-wrap:wrap; font-size:13px;">
              <div><div style="color:#64748b; font-size:12px;">Baseline</div><div style="font-weight:600;"><?= $b['baseline'] === null ? '—' : $b['baseline'] . '%' ?></div></div>
              <div><div style="color:#64748b; font-size:12px;">Current</div><div style="font-weight:600;"><?= $b['current'] === null ? '—' : $b['current'] . '%' ?></div></div>
              <div><div style="color:#64748b; font-size:12px;">Change</div>
                <div style="font-weight:600; color:<?= ($b['change'] !== null && $b['change'] >= 0) ? '#16a34a' : '#dc2626' ?>;">
                  <?= $b['change'] === null ? '—' : (($b['change'] >= 0 ? '+' : '') . $b['change'] . ' pts') ?>
                </div>
              </div>
              <div><div style="color:#64748b; font-size:12px;">Data points</div><div style="font-weight:600;"><?= (int)$b['entries'] ?></div></div>
            </div>
            <?php if (!empty($b['milestones'])): ?>
              <div style="margin-top:14px;">
                <div style="color:#64748b; font-size:12px; margin-bottom:6px;">Milestones (<?= (int)$b['milestoneDone'] ?> of <?= (int)$b['milestoneTotal'] ?> achieved)</div>
                <ul style="margin:0; padding-left:18px;">
                  <?php foreach ($b['milestones'] as $m): ?>
                    <li style="margin-bottom:3px;"><?= $m->is_achieved ? '[done]' : '[ ]' ?> <?= esc($m->description) ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- TEACCH -->
    <h2 style="font-size:18px; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-top:24px;">TEACCH Progress</h2>
    <?php if (empty($teacch)): ?>
      <p style="color:#64748b;">No TEACCH schedules for this student.</p>
    <?php else: ?>
      <?php foreach ($teacch as $sched): ?>
        <div style="border:1px solid #e2e8f0; border-radius:10px; padding:16px; margin-bottom:14px; page-break-inside:avoid; display:flex; gap:18px; align-items:flex-start;">
          <div style="flex-shrink:0; text-align:center;"><?php srCircle($sched['percent'], '#0ea5e9', 90); ?></div>
          <div style="flex:1;">
            <div style="font-weight:600;"><?= esc($sched['title']) ?></div>
            <div style="color:#64748b; font-size:12px; margin-bottom:10px;"><?= (int)$sched['rated'] ?> of <?= (int)$sched['total'] ?> tasks rated</div>
            <?php if (!empty($sched['tasks'])): ?>
              <table class="data-table">
                <thead><tr><th>#</th><th>Task</th><th>Latest Level</th><th>Ratings</th></tr></thead>
                <tbody>
                  <?php foreach ($sched['tasks'] as $t):
                    $labels = ['full_prompt' => 'Full prompt', 'partial_prompt' => 'Partial prompt', 'independent' => 'Independent'];
                    $lvl = $t['level'] ? $labels[$t['level']] : 'Not rated'; ?>
                    <tr><td><?= (int)$t['order'] ?></td><td><?= esc($t['title']) ?></td><td><?= esc($lvl) ?></td><td><?= (int)$t['entries'] ?></td></tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- Therapy -->
    <h2 style="font-size:18px; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-top:24px;">Therapy Sessions</h2>
    <?php if (empty($therapies)): ?>
      <p style="color:#64748b;">No therapy sessions recorded.</p>
    <?php else: ?>
      <table class="data-table">
        <thead><tr><th>Date</th><th>Type</th><th>Status</th><th>Therapist</th><th>Goal</th><th>Notes</th></tr></thead>
        <tbody>
          <?php foreach ($therapies as $s): ?>
            <tr>
              <td><?= date('d M Y', strtotime($s->session_date)) ?></td>
              <td><?= esc($s->session_type) ?></td>
              <td><?= ucfirst(esc($s->status ?? '')) ?></td>
              <td><?= esc(($s->therapist_first ?? '') . ' ' . ($s->therapist_last ?? '')) ?></td>
              <td><?= esc($s->goal_addressed ?: '—') ?></td>
              <td><?= esc($s->performance_notes ?: '—') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <!-- Classroom -->
    <h2 style="font-size:18px; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-top:24px;">Classroom Sessions (<?= count($sessions) ?>)</h2>
    <?php if (empty($sessions)): ?>
      <p style="color:#64748b;">No classroom sessions recorded.</p>
    <?php else: ?>
      <table class="data-table">
        <thead><tr><th>Date</th><th>Subject</th><th>Teacher</th><th>Notes</th></tr></thead>
        <tbody>
          <?php foreach ($sessions as $s): ?>
            <tr>
              <td><?= date('d M Y', strtotime($s->session_date)) ?></td>
              <td><?= esc($s->subject) ?></td>
              <td><?= esc(($s->teacher_first ?? '') . ' ' . ($s->teacher_last ?? '')) ?></td>
              <td><?= esc($s->notes ?: '—') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <!-- Observations -->
    <h2 style="font-size:18px; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-top:24px;">Academic Observations (<?= count($observations) ?>)</h2>
    <?php if (empty($observations)): ?>
      <p style="color:#64748b;">No observations recorded.</p>
    <?php else: ?>
      <ul style="padding-left:18px;">
        <?php foreach ($observations as $o): ?>
          <li style="margin-bottom:6px;">
            <span style="color:#64748b; font-size:12px;"><?= date('d M Y', strtotime($o->created_at)) ?>
              <?php if (!empty($o->teacher_first)): ?>by <?= esc($o->teacher_first . ' ' . $o->teacher_last) ?><?php endif; ?>:</span>
            <?= esc($o->observation) ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <!-- Homework -->
    <h2 style="font-size:18px; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-top:24px;">Homework</h2>
    <?php if (empty($homework)): ?>
      <p style="color:#64748b;">No homework assigned.</p>
    <?php else: ?>
      <table class="data-table">
        <thead><tr><th>Title</th><th>Description</th><th>Due</th><th>Assigned by</th></tr></thead>
        <tbody>
          <?php foreach ($homework as $h): ?>
            <tr>
              <td><strong><?= esc($h->title) ?></strong></td>
              <td><?= esc($h->description ?: '—') ?></td>
              <td><?= date('d M Y', strtotime($h->due_date)) ?></td>
              <td><?= esc(($h->teacher_first ?? '') . ' ' . ($h->teacher_last ?? '')) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <!-- Health: medications -->
    <h2 style="font-size:18px; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-top:24px;">Active Medications</h2>
    <?php if (empty($medications)): ?>
      <p style="color:#64748b;">No active medications.</p>
    <?php else: ?>
      <table class="data-table">
        <thead><tr><th>Name</th><th>Dosage</th><th>Frequency</th><th>Instructions</th></tr></thead>
        <tbody>
          <?php foreach ($medications as $m): ?>
            <tr>
              <td><strong><?= esc($m->name) ?></strong></td>
              <td><?= esc($m->dosage) ?></td>
              <td><?= esc($m->frequency) ?></td>
              <td><?= esc($m->instructions ?: '—') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <!-- Health: events -->
    <h2 style="font-size:18px; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-top:24px;">Health Events</h2>
    <?php if (empty($healthEvents)): ?>
      <p style="color:#64748b;">No health events recorded.</p>
    <?php else: ?>
      <table class="data-table">
        <thead><tr><th>Date</th><th>Severity</th><th>Description</th><th>Action Taken</th></tr></thead>
        <tbody>
          <?php
            $sevColors = ['low' => '#16a34a', 'medium' => '#d97706', 'high' => '#dc2626'];
            foreach ($healthEvents as $e):
              $col = $sevColors[$e->severity] ?? '#94a3b8'; ?>
            <tr>
              <td><?= date('d M Y', strtotime($e->recorded_at)) ?></td>
              <td><span style="background:<?= $col ?>; color:#fff; padding:2px 10px; border-radius:999px; font-size:12px;"><?= ucfirst(esc($e->severity)) ?></span></td>
              <td><?= esc($e->description) ?></td>
              <td><?= esc($e->action_taken ?: '—') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <!-- Health: records -->
    <h2 style="font-size:18px; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-top:24px;">Medical Records</h2>
    <?php if (empty($healthRecords)): ?>
      <p style="color:#64748b;">No medical records on file.</p>
    <?php else: ?>
      <table class="data-table">
        <thead><tr><th>Date</th><th>Type</th><th>Title</th><th>Description</th></tr></thead>
        <tbody>
          <?php foreach ($healthRecords as $r): ?>
            <tr>
              <td><?= date('d M Y', strtotime($r->recorded_at)) ?></td>
              <td><?= ucwords(str_replace('_', ' ', esc($r->record_type))) ?></td>
              <td><?= esc($r->title) ?></td>
              <td><?= esc($r->description ?: '—') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <!-- Boarding summary (only if provided) -->
    <?php if ($boardingStats !== null): ?>
      <h2 style="font-size:18px; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-top:24px;">Boarding Summary</h2>
      <?php
        $bs = $boardingStats;
        $renderBreakdown = function ($title, $map) {
          $total = array_sum($map);
          ob_start(); ?>
          <div style="flex:1; min-width:220px;">
            <div style="font-weight:600; margin-bottom:6px;"><?= esc($title) ?></div>
            <?php if ($total === 0): ?>
              <div style="color:#64748b; font-size:13px;">No data.</div>
            <?php else: ?>
              <?php foreach ($map as $label => $count): $pct = (int)round($count / $total * 100); ?>
                <div style="font-size:13px; margin-bottom:6px;">
                  <div style="display:flex; justify-content:space-between;">
                    <span><?= ucfirst(str_replace('_', ' ', esc($label))) ?></span>
                    <span style="font-weight:600;"><?= $pct ?>% (<?= $count ?>)</span>
                  </div>
                  <div style="background:#e2e8f0; border-radius:6px; height:8px; overflow:hidden; margin-top:2px;">
                    <div style="background:#92AAC7; height:8px; width:<?= $pct ?>%;"></div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        <?php return ob_get_clean();
        };
      ?>
      <div style="display:flex; gap:24px; flex-wrap:wrap; align-items:flex-start; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px;">
        <div style="flex:1; min-width:200px; display:flex; flex-direction:column; gap:8px;">
          <div style="display:flex; justify-content:space-between; font-size:13px;"><span>Avg sleep duration</span><span style="font-weight:600;"><?= $bs['avg_sleep_hours'] !== null ? $bs['avg_sleep_hours'] . ' hrs' : '—' ?></span></div>
          <div style="display:flex; justify-content:space-between; font-size:13px;"><span>Avg bedtime</span><span style="font-weight:600;"><?= $bs['avg_bedtime'] ?? '—' ?></span></div>
          <div style="display:flex; justify-content:space-between; font-size:13px;"><span>Avg wakeup</span><span style="font-weight:600;"><?= $bs['avg_wakeup'] ?? '—' ?></span></div>
          <div style="display:flex; justify-content:space-between; font-size:13px;"><span>Sleep logs</span><span style="font-weight:600;"><?= (int)$bs['sleep_count'] ?></span></div>
          <div style="display:flex; justify-content:space-between; font-size:13px;"><span>Total boarding logs</span><span style="font-weight:600;"><?= (int)$bs['total_logs'] ?></span></div>
        </div>
        <?= $renderBreakdown('Sleep quality', $bs['sleep_quality']) ?>
        <?= $renderBreakdown('Mood', $bs['mood']) ?>
        <?= $renderBreakdown('Appetite', $bs['appetite']) ?>
      </div>
    <?php endif; ?>

    <!-- Signatures -->
    <div style="margin-top:36px; display:flex; justify-content:space-between; gap:36px;">
      <div style="flex:1; border-top:1px solid #94a3b8; padding-top:6px; color:#64748b; font-size:13px;">School signature</div>
      <div style="flex:1; border-top:1px solid #94a3b8; padding-top:6px; color:#64748b; font-size:13px;">Parent signature</div>
    </div>

  </div>
</div>

<style media="print">
  .sidebar, .topbar, #navigation { display: none !important; }
  .main { margin: 0 !important; }
  #report-sheet { box-shadow: none !important; border: none !important; }
  * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
</style>
