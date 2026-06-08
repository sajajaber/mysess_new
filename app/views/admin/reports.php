<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'School Reports';
$pageHeading = 'School Reports';
$activePage  = 'reports';

$topbarActions = '
  <a href="' . ROOT . '/admin/dashboard"><button class="btn btn-primary">Dashboard</button></a>
  <button class="btn" onclick="window.print()">Print / Save PDF</button>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

$summary    = $summary    ?? null;
$byDiag     = $byDiag     ?? [];
$byGender   = $byGender   ?? [];
$goalsByCat = $goalsByCat ?? [];
$recentProg = $recentProg ?? [];
$topStaff   = $topStaff   ?? [];
$monthly    = $monthly    ?? [];

function pct($num, $den) {
  $den = (int)$den;
  if ($den <= 0) return 0;
  return (int)round(((int)$num / $den) * 100);
}

function bar($label, $count, $max, $color = '#4f46e5') {
  $w = $max > 0 ? (int)round(($count / $max) * 100) : 0;
  ?>
  <div style="margin-bottom:10px;">
    <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:4px;">
      <span><?= esc($label) ?></span>
      <span style="color:#64748b; font-weight:600;"><?= (int)$count ?></span>
    </div>
    <div style="background:#e2e8f0; border-radius:6px; height:10px; overflow:hidden;">
      <div style="background:<?= $color ?>; height:10px; width:<?= $w ?>%;"></div>
    </div>
  </div>
  <?php
}

$goalsTotal     = (int)($summary->total_goals       ?? 0);
$goalsMet       = (int)($summary->goals_met         ?? 0);
$goalsActive    = (int)($summary->goals_active      ?? 0);
$goalsDisc      = (int)($summary->goals_discontinued ?? 0);
$goalsMetPct    = pct($goalsMet, $goalsTotal);

$milestonesTotal = (int)($summary->total_milestones      ?? 0);
$milestonesDone  = (int)($summary->milestones_achieved   ?? 0);
$milestonesPct   = pct($milestonesDone, $milestonesTotal);

$diagMax  = 0; foreach ($byDiag     as $d) { if ((int)$d->total > $diagMax)  $diagMax  = (int)$d->total; }
$catMax   = 0; foreach ($goalsByCat as $c) { if ((int)$c->total > $catMax)   $catMax   = (int)$c->total; }
$monthMax = 0; foreach ($monthly    as $m) { $tot = (int)$m->class_count + (int)$m->therapy_count; if ($tot > $monthMax) $monthMax = $tot; }
?>

<div id="report-sheet">

  <!-- Top stat cards -->
  <div class="stat-cards">
    <?php
      statCard($summary->active_students       ?? 0, 'Active Students',     '#2563eb');
      statCard($summary->total_staff           ?? 0, 'Active Staff',        '#7c3aed');
      statCard($summary->total_goals           ?? 0, 'IEP Goals',           '#16a34a');
      statCard($summary->total_classroom_sessions ?? 0, 'Classroom Sessions', '#f59e0b');
      statCard($summary->total_therapy_sessions   ?? 0, 'Therapy Sessions',   '#0ea5e9');
    ?>
  </div>


  <!-- Goal achievement: percentage circles + breakdown -->
  <div class="card" style="margin-top:18px;">
    <div class="card-header"><h2>Goal &amp; Milestone Progress</h2></div>
    <div class="card-body">
      <div style="display:flex; gap:24px; flex-wrap:wrap; align-items:center;">

        <div style="text-align:center;">
          <div style="width:130px; height:130px; border-radius:50%;
                      background: conic-gradient(#16a34a <?= $goalsMetPct ?>%, #e2e8f0 <?= $goalsMetPct ?>% 100%);
                      display:flex; align-items:center; justify-content:center;">
            <div style="width:104px; height:104px; border-radius:50%; background:#fff;
                        display:flex; align-items:center; justify-content:center;
                        font-size:24px; font-weight:700; color:#16a34a;">
              <?= $goalsMetPct ?>%
            </div>
          </div>
          <div style="color:#64748b; font-size:13px; margin-top:8px;">Goals Met</div>
        </div>

        <div style="text-align:center;">
          <div style="width:130px; height:130px; border-radius:50%;
                      background: conic-gradient(#0ea5e9 <?= $milestonesPct ?>%, #e2e8f0 <?= $milestonesPct ?>% 100%);
                      display:flex; align-items:center; justify-content:center;">
            <div style="width:104px; height:104px; border-radius:50%; background:#fff;
                        display:flex; align-items:center; justify-content:center;
                        font-size:24px; font-weight:700; color:#0ea5e9;">
              <?= $milestonesPct ?>%
            </div>
          </div>
          <div style="color:#64748b; font-size:13px; margin-top:8px;">Milestones Achieved</div>
        </div>

        <div style="flex:1; min-width:240px;">
          <?php
            bar('Goals Met',          $goalsMet,    max($goalsTotal, 1), '#16a34a');
            bar('Goals In Progress',  $goalsActive, max($goalsTotal, 1), '#d97706');
            bar('Discontinued',       $goalsDisc,   max($goalsTotal, 1), '#dc2626');
            bar('Milestones Done',    $milestonesDone, max($milestonesTotal, 1), '#0ea5e9');
          ?>
        </div>

      </div>
    </div>
  </div>


  <!-- Two-column: Diagnoses + Goals by Category -->
  <div style="display:flex; gap:16px; margin-top:18px; flex-wrap:wrap;">

    <div class="card" style="flex:1; min-width:300px;">
      <div class="card-header"><h2>Active Students by Diagnosis</h2></div>
      <div class="card-body">
        <?php if (empty($byDiag)): ?>
          <p style="color:#64748b;">No diagnosis data yet.</p>
        <?php else: ?>
          <?php foreach ($byDiag as $d): ?>
            <?php bar($d->diagnosis, $d->total, $diagMax, '#7c3aed'); ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <div class="card" style="flex:1; min-width:300px;">
      <div class="card-header"><h2>IEP Goals by Category</h2></div>
      <div class="card-body">
        <?php if (empty($goalsByCat)): ?>
          <p style="color:#64748b;">No goals yet.</p>
        <?php else: ?>
          <?php foreach ($goalsByCat as $c):
            $line = $c->category . ' (' . (int)$c->achieved . ' of ' . (int)$c->total . ' met)';
            bar($line, $c->total, $catMax, '#16a34a');
          endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

  </div>


  <!-- Monthly sessions trend -->
  <div class="card" style="margin-top:18px;">
    <div class="card-header"><h2>Sessions per Month (last 6 months)</h2></div>
    <div class="card-body">
      <?php if (empty($monthly)): ?>
        <p style="color:#64748b;">No sessions recorded in the last 6 months.</p>
      <?php else: ?>
        <div style="display:flex; gap:14px; align-items:flex-end; height:180px; padding:6px 4px;">
          <?php foreach ($monthly as $m):
            $classCount   = (int)$m->class_count;
            $therapyCount = (int)$m->therapy_count;
            $classH   = $monthMax ? (int)round(($classCount   / $monthMax) * 150) : 0;
            $therapyH = $monthMax ? (int)round(($therapyCount / $monthMax) * 150) : 0;
            $monthLbl = date('M Y', strtotime($m->ym . '-01'));
          ?>
            <div style="flex:1; text-align:center;">
              <div style="display:flex; gap:3px; justify-content:center; align-items:flex-end; height:155px;">
                <div title="Classroom: <?= $classCount ?>"
                     style="width:14px; height:<?= $classH ?>px; background:#f59e0b; border-radius:3px 3px 0 0;"></div>
                <div title="Therapy: <?= $therapyCount ?>"
                     style="width:14px; height:<?= $therapyH ?>px; background:#0ea5e9; border-radius:3px 3px 0 0;"></div>
              </div>
              <div style="font-size:11px; color:#64748b; margin-top:6px;"><?= esc($monthLbl) ?></div>
              <div style="font-size:11px; color:#64748b;"><?= ($classCount + $therapyCount) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
        <div style="display:flex; gap:18px; margin-top:10px; font-size:12px; color:#64748b;">
          <span><span style="display:inline-block; width:10px; height:10px; background:#f59e0b; border-radius:2px;"></span> Classroom</span>
          <span><span style="display:inline-block; width:10px; height:10px; background:#0ea5e9; border-radius:2px;"></span> Therapy</span>
        </div>
      <?php endif; ?>
    </div>
  </div>


  <!-- Health, attendance & homework strip -->
  <div style="display:flex; gap:12px; margin-top:18px; flex-wrap:wrap;">

    <div style="flex:1; min-width:160px; background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:14px; text-align:center;">
      <div style="font-size:24px; font-weight:700; color:#dc2626;"><?= (int)($summary->high_severity_events ?? 0) ?></div>
      <div style="color:#64748b; font-size:13px;">High-severity health events</div>
    </div>

    <div style="flex:1; min-width:160px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:14px; text-align:center;">
      <div style="font-size:24px; font-weight:700; color:#16a34a;"><?= (int)($summary->checked_in_today ?? 0) ?></div>
      <div style="color:#64748b; font-size:13px;">Checked in today</div>
    </div>

    <div style="flex:1; min-width:160px; background:#fef9c3; border:1px solid #fde68a; border-radius:10px; padding:14px; text-align:center;">
      <div style="font-size:24px; font-weight:700; color:#854d0e;"><?= (int)($summary->checked_out_today ?? 0) ?></div>
      <div style="color:#64748b; font-size:13px;">Checked out today</div>
    </div>

    <div style="flex:1; min-width:160px; background:#eef2ff; border:1px solid #c7d2fe; border-radius:10px; padding:14px; text-align:center;">
      <div style="font-size:24px; font-weight:700; color:#4f46e5;"><?= (int)($summary->total_homework ?? 0) ?></div>
      <div style="color:#64748b; font-size:13px;">Homework assigned</div>
    </div>

    <div style="flex:1; min-width:160px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px; text-align:center;">
      <div style="font-size:24px; font-weight:700; color:#475569;"><?= (int)($summary->students_on_meds ?? 0) ?></div>
      <div style="color:#64748b; font-size:13px;">Students on medication</div>
    </div>

  </div>


  <!-- Two-column lists: Top staff + Recent progress -->
  <div style="display:flex; gap:16px; margin-top:18px; flex-wrap:wrap;">

    <div class="card" style="flex:1; min-width:300px;">
      <div class="card-header"><h2>Top Staff by Sessions</h2></div>
      <div class="card-body">
        <?php if (empty($topStaff)): ?>
          <p style="color:#64748b;">No sessions logged yet.</p>
        <?php else: ?>
          <table class="data-table">
            <thead><tr><th>Staff</th><th>Role</th><th style="text-align:right;">Sessions</th></tr></thead>
            <tbody>
              <?php foreach ($topStaff as $u): ?>
                <tr>
                  <td><?= esc($u->first_name . ' ' . $u->last_name) ?></td>
                  <td><?= ucfirst(str_replace('_', ' ', esc($u->role))) ?></td>
                  <td style="text-align:right; font-weight:600;"><?= (int)$u->total ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>

    <div class="card" style="flex:1; min-width:300px;">
      <div class="card-header"><h2>Recent Progress Scores</h2></div>
      <div class="card-body">
        <?php if (empty($recentProg)): ?>
          <p style="color:#64748b;">No progress recorded yet.</p>
        <?php else: ?>
          <table class="data-table">
            <thead><tr><th>Student</th><th>Goal</th><th style="text-align:right;">Score</th><th>When</th></tr></thead>
            <tbody>
              <?php foreach ($recentProg as $p):
                $score = (int)$p->score;
                $color = $score >= 80 ? '#16a34a' : ($score > 0 ? '#d97706' : '#dc2626');
              ?>
                <tr>
                  <td><?= esc($p->student_first_name . ' ' . $p->student_last_name) ?></td>
                  <td><?= esc(mb_strimwidth($p->goal_text ?? '', 0, 50, '…')) ?></td>
                  <td style="text-align:right; font-weight:700; color:<?= $color ?>;"><?= $score ?>%</td>
                  <td><?= date('d M Y', strtotime($p->recorded_at)) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>

  </div>


</div>


<style media="print">
  .sidebar, .topbar, #navigation { display: none !important; }
  .main { margin: 0 !important; }
  #report-sheet { box-shadow: none !important; }
  * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
