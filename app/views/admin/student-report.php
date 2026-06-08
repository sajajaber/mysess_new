<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$student = $student;

$studentName = trim($student->first_name . ' ' . $student->last_name) ?: 'Student';
$pageTitle   = $studentName . ' — Report';
$pageHeading = $studentName . ' — Student Report';
$activePage  = 'reports';

$topbarActions = '
  <a href="' . ROOT . '/admin/view_student/' . (int)$student->id . '"><button class="btn btn-primary">Back to Profile</button></a>
  <button class="btn" onclick="window.print()">Print / Save PDF</button>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

$goals      = $goals      ?? [];
$milestones = $milestones ?? [];
$progress   = $progress   ?? [];
$teacch     = $teacch     ?? [];
$sessions   = $sessions   ?? [];
$therapies  = $therapies  ?? [];
$observ     = $observ     ?? [];
$reports    = $reports    ?? [];
$homework   = $homework   ?? [];
$staff      = $staff      ?? [];

$milestonesByGoal = [];
foreach ($milestones as $m) {
  $milestonesByGoal[$m->goal_id][] = $m;
}

$latestScoreByGoal = [];
foreach ($progress as $p) {
  if (!isset($latestScoreByGoal[$p->goal_id])) {
    $latestScoreByGoal[$p->goal_id] = (int)$p->score;
  }
}

$goalsTotal = count($goals);
$goalsMet   = 0;
foreach ($goals as $g) {
  if (($g->status ?? '') === 'achieved') $goalsMet++;
}
$milestonesDone = 0;
foreach ($milestones as $m) {
  if ($m->is_achieved) $milestonesDone++;
}
$milestonesTotal = count($milestones);

function pctVal($n, $d) { $d = (int)$d; return $d > 0 ? (int)round(((int)$n / $d) * 100) : 0; }
$goalsMetPct       = pctVal($goalsMet, $goalsTotal);
$milestonesDonePct = pctVal($milestonesDone, $milestonesTotal);

$photoFile = $student->photo_url ?? '';
$photoUrl  = $photoFile ? ROOT . '/public/assets/uploads/' . $photoFile : '';
$initials  = strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1));
?>

<div id="report-sheet" class="card">
  <div class="card-body">


    <!-- Header -->
    <div style="display:flex; gap:18px; align-items:center; border-bottom:2px solid #4f46e5; padding-bottom:14px; margin-bottom:18px;">
      <?php if ($photoUrl): ?>
        <img src="<?= $photoUrl ?>" alt=""
             style="width:80px; height:80px; border-radius:50%; object-fit:cover; border:1px solid #e2e8f0;">
      <?php else: ?>
        <div style="width:80px; height:80px; border-radius:50%; background:#4f46e5;
                    color:#fff; display:flex; align-items:center; justify-content:center;
                    font-size:26px; font-weight:700;"><?= $initials ?></div>
      <?php endif; ?>

      <div style="flex:1;">
        <h1 style="margin:0; font-size:22px;"><?= esc($studentName) ?></h1>
        <div style="color:#64748b; font-size:13px; margin-top:4px;">
          DOB <?= date('d M Y', strtotime($student->date_of_birth)) ?>
          &nbsp;|&nbsp; <?= ucfirst($student->gender ?? '') ?>
          <?php if ($student->diagnosis): ?>
            &nbsp;|&nbsp; <?= esc($student->diagnosis) ?>
          <?php endif; ?>
          <?php if (!empty($student->enrollment_date)): ?>
            &nbsp;|&nbsp; Enrolled <?= date('d M Y', strtotime($student->enrollment_date)) ?>
          <?php endif; ?>
        </div>
        <?php if (!empty($student->guardian_first_name) || !empty($student->guardian_last_name)): ?>
          <div style="color:#64748b; font-size:13px; margin-top:2px;">
            Guardian:
            <?= esc(trim(($student->guardian_first_name ?? '') . ' ' . ($student->guardian_last_name ?? ''))) ?>
          </div>
        <?php endif; ?>
      </div>
    </div>


    <!-- Snapshot circles -->
    <div style="display:flex; gap:24px; flex-wrap:wrap; align-items:center;
                background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px;
                padding:18px; margin-bottom:22px;">

      <div style="text-align:center;">
        <div style="width:120px; height:120px; border-radius:50%;
                    background: conic-gradient(#16a34a <?= $goalsMetPct ?>%, #e2e8f0 <?= $goalsMetPct ?>% 100%);
                    display:flex; align-items:center; justify-content:center;">
          <div style="width:96px; height:96px; border-radius:50%; background:#fff;
                      display:flex; align-items:center; justify-content:center;
                      font-size:22px; font-weight:700; color:#16a34a;">
            <?= $goalsMetPct ?>%
          </div>
        </div>
        <div style="color:#64748b; font-size:13px; margin-top:6px;">Goals Met</div>
      </div>

      <div style="text-align:center;">
        <div style="width:120px; height:120px; border-radius:50%;
                    background: conic-gradient(#0ea5e9 <?= $milestonesDonePct ?>%, #e2e8f0 <?= $milestonesDonePct ?>% 100%);
                    display:flex; align-items:center; justify-content:center;">
          <div style="width:96px; height:96px; border-radius:50%; background:#fff;
                      display:flex; align-items:center; justify-content:center;
                      font-size:22px; font-weight:700; color:#0ea5e9;">
            <?= $milestonesDonePct ?>%
          </div>
        </div>
        <div style="color:#64748b; font-size:13px; margin-top:6px;">Milestones Achieved</div>
      </div>

      <div style="flex:1; min-width:220px; display:flex; flex-direction:column; gap:8px;">
        <div style="display:flex; justify-content:space-between; font-size:13px;">
          <span>Total IEP goals</span><span style="font-weight:600;"><?= $goalsTotal ?></span>
        </div>
        <div style="display:flex; justify-content:space-between; font-size:13px;">
          <span>Total milestones</span><span style="font-weight:600;"><?= $milestonesTotal ?></span>
        </div>
        <div style="display:flex; justify-content:space-between; font-size:13px;">
          <span>Classroom sessions</span><span style="font-weight:600;"><?= count($sessions) ?></span>
        </div>
        <div style="display:flex; justify-content:space-between; font-size:13px;">
          <span>Therapy sessions</span><span style="font-weight:600;"><?= count($therapies) ?></span>
        </div>
        <div style="display:flex; justify-content:space-between; font-size:13px;">
          <span>Homework assigned</span><span style="font-weight:600;"><?= count($homework) ?></span>
        </div>
      </div>

    </div>


    <!-- Care team -->
    <h2 style="font-size:18px; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-top:24px;">Care Team</h2>
    <?php if (empty($staff)): ?>
      <p style="color:#64748b;">No staff currently assigned.</p>
    <?php else: ?>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <?php foreach ($staff as $u): ?>
          <div style="background:#eef2ff; border:1px solid #c7d2fe; border-radius:10px; padding:8px 12px; min-width:200px;">
            <div style="font-weight:600;">
              <?= esc(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')) ?>
            </div>
            <div style="color:#475569; font-size:12px;">
              <?= ucfirst(str_replace('_', ' ', esc($u->role_type ?? $u->role ?? ''))) ?>
              <?php if (!empty($u->email)): ?> &nbsp;|&nbsp; <?= esc($u->email) ?> <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>


    <!-- IEP goals -->
    <h2 style="font-size:18px; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-top:24px;">IEP Goals</h2>
    <?php if (empty($goals)): ?>
      <p style="color:#64748b;">No IEP goals on file.</p>
    <?php else: ?>
      <?php foreach ($goals as $g):
        $latest    = $latestScoreByGoal[$g->id] ?? null;
        $statusKey = $g->status ?? 'active';
        $statusLbl = $statusKey === 'achieved'    ? 'Met'
                    : ($statusKey === 'discontinued' ? 'Discontinued' : 'In Progress');
        $statusClr = $statusKey === 'achieved'    ? '#16a34a'
                    : ($statusKey === 'discontinued' ? '#64748b' : '#d97706');
        $goalMs    = $milestonesByGoal[$g->id] ?? [];
        $msDone    = 0;
        foreach ($goalMs as $m) { if ($m->is_achieved) $msDone++; }
      ?>
        <div style="border:1px solid #e2e8f0; border-radius:10px; padding:14px; margin-bottom:12px; page-break-inside:avoid;">
          <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
            <div>
              <span style="background:#eef2ff; color:#4f46e5; font-size:12px; padding:2px 8px; border-radius:10px;">
                <?= esc($g->category ?: 'General') ?>
              </span>
              <div style="font-weight:600; margin-top:6px;"><?= esc($g->goal_text ?? '') ?></div>
              <?php if (!empty($g->target_date)): ?>
                <div style="color:#64748b; font-size:12px; margin-top:3px;">
                  Target date: <?= date('d M Y', strtotime($g->target_date)) ?>
                </div>
              <?php endif; ?>
            </div>
            <span style="background:<?= $statusClr ?>; color:#fff; font-size:12px; padding:4px 10px; border-radius:10px; white-space:nowrap;">
              <?= $statusLbl ?>
            </span>
          </div>

          <div style="display:flex; gap:18px; margin-top:10px; flex-wrap:wrap; font-size:13px;">
            <div>
              <span style="color:#64748b;">Latest score</span>
              <span style="font-weight:600; margin-left:6px;">
                <?= $latest === null ? '—' : $latest . '%' ?>
              </span>
            </div>
            <?php if (!empty($goalMs)): ?>
              <div>
                <span style="color:#64748b;">Milestones</span>
                <span style="font-weight:600; margin-left:6px;"><?= $msDone ?> of <?= count($goalMs) ?> achieved</span>
              </div>
            <?php endif; ?>
          </div>

          <?php if ($latest !== null): ?>
            <div style="margin-top:10px; background:#e2e8f0; border-radius:6px; height:10px; overflow:hidden;">
              <div style="background:<?= $statusClr ?>; height:10px; width:<?= max(0, min(100, (int)$latest)) ?>%;"></div>
            </div>
          <?php endif; ?>

          <?php if (!empty($goalMs)): ?>
            <ul style="margin:10px 0 0; padding-left:18px; font-size:13px;">
              <?php foreach ($goalMs as $m): ?>
                <li><?= $m->is_achieved ? '[done] ' : '[ ] ' ?><?= esc($m->description) ?></li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>


    <!-- TEACCH progress -->
    <h2 style="font-size:18px; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-top:24px;">TEACCH Progress</h2>
    <?php if (empty($teacch)): ?>
      <p style="color:#64748b;">No TEACCH progress recorded.</p>
    <?php else: ?>
      <table class="data-table">
        <thead><tr><th>Task</th><th>Independence</th><th>Date</th></tr></thead>
        <tbody>
          <?php foreach ($teacch as $t):
            $level = $t->independence_level;
            $label = ['full_prompt' => 'Full prompt', 'partial_prompt' => 'Partial prompt', 'independent' => 'Independent'][$level] ?? '—';
            $color = $level === 'independent' ? '#16a34a' : ($level === 'partial_prompt' ? '#d97706' : '#dc2626');
          ?>
            <tr>
              <td><?= esc($t->task_title ?? '—') ?></td>
              <td><span style="color:<?= $color ?>; font-weight:600;"><?= $label ?></span></td>
              <td><?= date('d M Y', strtotime($t->session_date)) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>


    <!-- Therapy sessions -->
    <h2 style="font-size:18px; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-top:24px;">Therapy Sessions</h2>
    <?php if (empty($therapies)): ?>
      <p style="color:#64748b;">No therapy sessions recorded.</p>
    <?php else: ?>
      <table class="data-table">
        <thead><tr><th>Date</th><th>Type</th><th>Status</th><th>Therapist</th><th>Goal Addressed</th><th>Notes</th></tr></thead>
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


    <!-- Classroom sessions -->
    <h2 style="font-size:18px; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-top:24px;">Classroom Sessions</h2>
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
    <h2 style="font-size:18px; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-top:24px;">Academic Observations</h2>
    <?php if (empty($observ)): ?>
      <p style="color:#64748b;">No observations recorded.</p>
    <?php else: ?>
      <ul style="padding-left:18px;">
        <?php foreach ($observ as $o): ?>
          <li style="margin-bottom:6px;">
            <span style="color:#64748b; font-size:12px;">
              <?= date('d M Y', strtotime($o->created_at)) ?> by
              <?= esc(($o->teacher_first ?? '') . ' ' . ($o->teacher_last ?? '')) ?>:
            </span>
            <?= esc($o->observation) ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>


    <!-- Progress reports written by teachers -->
    <h2 style="font-size:18px; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-top:24px;">Progress Reports</h2>
    <?php if (empty($reports)): ?>
      <p style="color:#64748b;">No progress reports written yet.</p>
    <?php else: ?>
      <?php foreach ($reports as $r): ?>
        <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px; margin-bottom:10px; page-break-inside:avoid;">
          <div style="display:flex; justify-content:space-between; font-size:12px; color:#64748b;">
            <span><?= esc($r->reporting_period) ?> &nbsp;|&nbsp;
                  <?= esc(($r->teacher_first ?? '') . ' ' . ($r->teacher_last ?? '')) ?></span>
            <span><?= date('d M Y', strtotime($r->created_at)) ?></span>
          </div>
          <div style="margin-top:6px; font-weight:600;">Rating: <?= ucfirst(esc($r->rating)) ?></div>
          <div style="margin-top:4px;"><?= esc($r->summary) ?></div>
        </div>
      <?php endforeach; ?>
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
