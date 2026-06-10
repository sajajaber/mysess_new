<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$student    = $student;
$team       = $team       ?? [];
$goals      = $goals      ?? [];
$milestones = $milestones ?? [];
$progress   = $progress   ?? [];
$schedules  = $schedules  ?? [];
$teacch     = $teacch     ?? [];
$therapy    = $therapy    ?? [];
$sessions   = $sessions   ?? [];
$observ     = $observ     ?? [];
$reports    = $reports    ?? [];
$homework   = $homework   ?? [];
$meds       = $meds       ?? [];
$medLogs    = $medLogs    ?? [];
$events     = $events     ?? [];
$records    = $records    ?? [];
$boarding   = $boarding   ?? [];
$checkins   = $checkins   ?? [];

$studentName = trim($student->first_name . ' ' . $student->last_name);
$pageTitle   = $studentName;
$pageHeading = $studentName;
$activePage  = 'children';

$topbarActions = '
  <a href="' . ROOT . '/parent/dashboard"><button class="btn btn-primary">Back to Dashboard</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

$photo    = $student->photo_url ?? '';
$photoUrl = $photo ? ROOT . '/public/assets/uploads/' . $photo : '';
$initials = strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1));

$milestonesByGoal = [];
foreach ($milestones as $m) {
  $milestonesByGoal[$m->goal_id][] = $m;
}
?>


<div class="profile-card">
  <?php if ($photoUrl): ?>
    <img src="<?= $photoUrl ?>" alt=""
         style="width:64px; height:64px; border-radius:50%; object-fit:cover; border:1px solid #e2e8f0;">
  <?php else: ?>
    <div class="profile-card__initials"><?= $initials ?></div>
  <?php endif; ?>

  <div class="profile-card__info">
    <h2 class="profile-card__name"><?= esc($studentName) ?></h2>
    <div class="profile-card__meta">
      <span class="profile-meta-item">
        <span class="profile-meta-label">Date of Birth</span>
        <span class="profile-meta-value"><?= date('d M Y', strtotime($student->date_of_birth)) ?></span>
      </span>
      <span class="profile-meta-divider">|</span>
      <span class="profile-meta-item">
        <span class="profile-meta-label">Gender</span>
        <span class="profile-meta-value"><?= ucfirst($student->gender ?? '') ?></span>
      </span>
      <?php if ($student->diagnosis): ?>
        <span class="profile-meta-divider">|</span>
        <span class="profile-meta-item">
          <span class="profile-meta-label">Diagnosis</span>
          <span class="profile-meta-value diagnosis-badge"><?= esc($student->diagnosis) ?></span>
        </span>
      <?php endif; ?>
    </div>
  </div>
</div>


<div class="section-tabs">
  <button class="section-tab active" data-target="p-overview">Overview</button>
  <button class="section-tab"        data-target="p-iep">IEP Goals</button>
  <button class="section-tab"        data-target="p-teacch">TEACCH</button>
  <button class="section-tab"        data-target="p-therapy">Therapy</button>
  <button class="section-tab"        data-target="p-classroom">Classroom</button>
  <button class="section-tab"        data-target="p-homework">Homework</button>
  <button class="section-tab"        data-target="p-health">Health</button>
  <button class="section-tab"        data-target="p-boarding">Boarding</button>
</div>


<div id="p-overview" class="section-panel active">
  <div class="card">
    <div class="card-header"><h2>Care Team</h2></div>
    <div class="card-body">
      <?php if (empty($team)): ?>
        <p class="muted">No staff are currently assigned.</p>
      <?php else: ?>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
          <?php foreach ($team as $u): ?>
            <div style="background:#eef2ff; border:1px solid #c7d2fe; border-radius:10px; padding:8px 12px; min-width:200px;">
              <div style="font-weight:600;"><?= esc($u->first_name . ' ' . $u->last_name) ?></div>
              <div class="muted text-sm">
                <?= ucfirst(str_replace('_', ' ', esc($u->role_type ?? $u->role ?? ''))) ?>
                <?php if ($u->email): ?> &nbsp;|&nbsp; <?= esc($u->email) ?> <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>


<div id="p-iep" class="section-panel">
  <?php if (empty($goals)): ?>
    <div class="empty-state">No IEP goals on file yet.</div>
  <?php else: ?>
    <?php foreach ($goals as $g):
      $latest    = $progress[$g->id] ?? null;
      $statusKey = $g->status ?? 'active';
      $statusLbl = $statusKey === 'achieved' ? 'Met'
                  : ($statusKey === 'discontinued' ? 'Discontinued' : 'In Progress');
      $statusClr = $statusKey === 'achieved' ? '#16a34a'
                  : ($statusKey === 'discontinued' ? '#64748b' : '#d97706');
      $goalMs    = $milestonesByGoal[$g->id] ?? [];
      $msDone    = 0;
      foreach ($goalMs as $m) { if ($m->is_achieved) $msDone++; }
    ?>
      <div style="border:1px solid #e2e8f0; border-radius:10px; padding:14px; margin-bottom:12px;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
          <div>
            <span style="background:#eef2ff; color:#4f46e5; font-size:12px; padding:2px 8px; border-radius:10px;">
              <?= esc($g->category ?: 'General') ?>
            </span>
            <div style="font-weight:600; margin-top:6px;"><?= esc($g->goal_text) ?></div>
            <?php if ($g->target_date): ?>
              <div class="muted text-sm" style="margin-top:3px;">
                Target date: <?= date('d M Y', strtotime($g->target_date)) ?>
              </div>
            <?php endif; ?>
          </div>
          <span style="background:<?= $statusClr ?>; color:#fff; font-size:12px; padding:4px 10px; border-radius:10px; white-space:nowrap;">
            <?= $statusLbl ?>
          </span>
        </div>

        <?php if ($latest !== null): ?>
          <div style="margin-top:10px; display:flex; gap:18px; align-items:center;">
            <div class="muted text-sm">Latest score</div>
            <div style="font-weight:700;"><?= $latest ?>%</div>
            <div style="flex:1; background:#e2e8f0; border-radius:6px; height:10px; overflow:hidden;">
              <div style="background:<?= $statusClr ?>; height:10px; width:<?= max(0, min(100, $latest)) ?>%;"></div>
            </div>
          </div>
        <?php endif; ?>

        <?php if (!empty($goalMs)): ?>
          <div style="margin-top:12px;">
            <div class="muted text-sm" style="margin-bottom:6px;">
              Milestones (<?= $msDone ?> of <?= count($goalMs) ?> achieved)
            </div>
            <ul style="margin:0; padding-left:18px;">
              <?php foreach ($goalMs as $m): ?>
                <li><?= $m->is_achieved ? '[done] ' : '[ ] ' ?><?= esc($m->description) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>


<div id="p-teacch" class="section-panel">

  <?php if (empty($schedules)): ?>
    <div class="empty-state">No TEACCH schedules yet.</div>
  <?php else: ?>
    <div class="card">
      <div class="card-header"><h2>Visual Schedules</h2></div>
      <div class="card-body">
        <ul style="padding-left:18px;">
          <?php foreach ($schedules as $s): ?>
            <li><?= esc($s->title) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  <?php endif; ?>

  <div class="card" style="margin-top:14px;">
    <div class="card-header"><h2>Recent Independence Ratings</h2></div>
    <div class="card-body">
      <?php
        $levelLabels = ['full_prompt' => 'Full prompt', 'partial_prompt' => 'Partial prompt', 'independent' => 'Independent'];
        $levelColors = ['full_prompt' => '#dc2626',     'partial_prompt' => '#d97706',         'independent' => '#16a34a'];
        $data    = $teacch;
        $headers = ['Date', 'Task', 'Independence'];
        $renderRow = function ($t) use ($levelLabels, $levelColors) { ob_start();
          $lvl = $t->independence_level;
          $lbl = $levelLabels[$lvl] ?? '—';
          $col = $levelColors[$lvl] ?? '#94a3b8';
        ?>
          <tr>
            <td><?= date('d M Y', strtotime($t->session_date)) ?></td>
            <td><?= esc($t->task_title ?? '—') ?></td>
            <td>
              <span style="background:<?= $col ?>; color:#fff; padding:2px 10px; border-radius:999px; font-size:12px;">
                <?= esc($lbl) ?>
              </span>
            </td>
          </tr>
        <?php return ob_get_clean(); };
        $emptyMessage = 'No TEACCH ratings yet.';
        require __DIR__ . '/../components/data_table.php';
      ?>
    </div>
  </div>
</div>


<div id="p-therapy" class="section-panel">
  <div class="card">
    <div class="card-header"><h2>Therapy Sessions</h2></div>
    <div class="card-body">
      <?php
        $data    = $therapy;
        $headers = ['Date', 'Type', 'Status', 'Therapist', 'Goal Addressed', 'Notes'];
        $renderRow = function ($s) { ob_start(); ?>
          <tr>
            <td><?= date('d M Y', strtotime($s->session_date)) ?></td>
            <td><?= esc($s->session_type) ?></td>
            <td><?= ucfirst(esc($s->status ?? '')) ?></td>
            <td><?= esc(($s->therapist_first ?? '') . ' ' . ($s->therapist_last ?? '')) ?></td>
            <td><?= esc($s->goal_addressed ?: '—') ?></td>
            <td><?= esc($s->performance_notes ?: '—') ?></td>
          </tr>
        <?php return ob_get_clean(); };
        $emptyMessage = 'No therapy sessions yet.';
        require __DIR__ . '/../components/data_table.php';
      ?>
    </div>
  </div>
</div>


<div id="p-classroom" class="section-panel">

  <div class="card">
    <div class="card-header"><h2>Classroom Sessions</h2></div>
    <div class="card-body">
      <?php
        $data    = $sessions;
        $headers = ['Date', 'Subject', 'Teacher', 'Notes'];
        $renderRow = function ($s) { ob_start(); ?>
          <tr>
            <td><?= date('d M Y', strtotime($s->session_date)) ?></td>
            <td><?= esc($s->subject) ?></td>
            <td><?= esc(($s->teacher_first ?? '') . ' ' . ($s->teacher_last ?? '')) ?></td>
            <td><?= esc($s->notes ?: '—') ?></td>
          </tr>
        <?php return ob_get_clean(); };
        $emptyMessage = 'No classroom sessions yet.';
        require __DIR__ . '/../components/data_table.php';
      ?>
    </div>
  </div>

  <div class="card" style="margin-top:14px;">
    <div class="card-header"><h2>Observations</h2></div>
    <div class="card-body">
      <?php if (empty($observ)): ?>
        <div class="empty-state">No observations recorded yet.</div>
      <?php else: ?>
        <ul style="padding-left:18px;">
          <?php foreach ($observ as $o): ?>
            <li style="margin-bottom:6px;">
              <span class="muted text-sm">
                <?= date('d M Y', strtotime($o->created_at)) ?> by
                <?= esc(($o->teacher_first ?? '') . ' ' . ($o->teacher_last ?? '')) ?>:
              </span>
              <?= esc($o->observation) ?>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>

  <div class="card" style="margin-top:14px;">
    <div class="card-header"><h2>Progress Reports</h2></div>
    <div class="card-body">
      <?php if (empty($reports)): ?>
        <div class="empty-state">No progress reports written yet.</div>
      <?php else: ?>
        <?php foreach ($reports as $r): ?>
          <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px; margin-bottom:10px;">
            <div class="muted text-sm" style="display:flex; justify-content:space-between;">
              <span><?= esc($r->reporting_period) ?> &nbsp;|&nbsp;
                    <?= esc(($r->teacher_first ?? '') . ' ' . ($r->teacher_last ?? '')) ?></span>
              <span><?= date('d M Y', strtotime($r->created_at)) ?></span>
            </div>
            <div style="margin-top:6px; font-weight:600;">Rating: <?= ucfirst(esc($r->rating)) ?></div>
            <div style="margin-top:4px;"><?= esc($r->summary) ?></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>


<div id="p-homework" class="section-panel">
  <div class="card">
    <div class="card-header"><h2>Homework</h2></div>
    <div class="card-body">
      <?php
        $data    = $homework;
        $headers = ['Title', 'Description', 'Due Date', 'Assigned by'];
        $renderRow = function ($h) { ob_start(); ?>
          <tr>
            <td><strong><?= esc($h->title) ?></strong></td>
            <td><?= esc($h->description ?: '—') ?></td>
            <td><?= date('d M Y', strtotime($h->due_date)) ?></td>
            <td><?= esc(($h->teacher_first ?? '') . ' ' . ($h->teacher_last ?? '')) ?></td>
          </tr>
        <?php return ob_get_clean(); };
        $emptyMessage = 'No homework assigned yet.';
        require __DIR__ . '/../components/data_table.php';
      ?>
    </div>
  </div>
</div>


<div id="p-health" class="section-panel">

  <div class="card">
    <div class="card-header"><h2>Active Medications</h2></div>
    <div class="card-body">
      <?php
        $data    = $meds;
        $headers = ['Name', 'Dosage', 'Frequency', 'Instructions'];
        $renderRow = function ($m) { ob_start(); ?>
          <tr>
            <td><strong><?= esc($m->name) ?></strong></td>
            <td><?= esc($m->dosage) ?></td>
            <td><?= esc($m->frequency) ?></td>
            <td><?= esc($m->instructions ?: '—') ?></td>
          </tr>
        <?php return ob_get_clean(); };
        $emptyMessage = 'No active medications.';
        require __DIR__ . '/../components/data_table.php';
      ?>
    </div>
  </div>

  <div class="card" style="margin-top:14px;">
    <div class="card-header"><h2>Recent Doses</h2></div>
    <div class="card-body">
      <?php
        $data    = $medLogs;
        $headers = ['Medication', 'Given at', 'Given by', 'Notes'];
        $renderRow = function ($l) { ob_start(); ?>
          <tr>
            <td><?= esc($l->med_name) ?></td>
            <td><?= date('d M Y H:i', strtotime($l->administered_at)) ?></td>
            <td><?= esc(($l->by_first ?? '') . ' ' . ($l->by_last ?? '')) ?></td>
            <td><?= esc($l->notes ?: '—') ?></td>
          </tr>
        <?php return ob_get_clean(); };
        $emptyMessage = 'No recent doses recorded.';
        require __DIR__ . '/../components/data_table.php';
      ?>
    </div>
  </div>

  <div class="card" style="margin-top:14px;">
    <div class="card-header"><h2>Health Events</h2></div>
    <div class="card-body">
      <?php
        $sevColors = ['low' => '#16a34a', 'medium' => '#d97706', 'high' => '#dc2626'];
        $data    = $events;
        $headers = ['Date', 'Severity', 'Description', 'Action Taken'];
        $renderRow = function ($e) use ($sevColors) { ob_start();
          $sev = $e->severity;
          $col = $sev && isset($sevColors[$sev]) ? $sevColors[$sev] : '#94a3b8';
        ?>
          <tr>
            <td><?= date('d M Y', strtotime($e->recorded_at)) ?></td>
            <td>
              <span style="background:<?= $col ?>; color:#fff; padding:2px 10px; border-radius:999px; font-size:12px;">
                <?= ucfirst(esc($sev)) ?>
              </span>
            </td>
            <td><?= esc($e->description) ?></td>
            <td><?= esc($e->action_taken ?: '—') ?></td>
          </tr>
        <?php return ob_get_clean(); };
        $emptyMessage = 'No health events recorded.';
        require __DIR__ . '/../components/data_table.php';
      ?>
    </div>
  </div>

  <div class="card" style="margin-top:14px;">
    <div class="card-header"><h2>Health Records</h2></div>
    <div class="card-body">
      <?php
        $data    = $records;
        $headers = ['Date', 'Type', 'Title', 'Description'];
        $renderRow = function ($r) { ob_start(); ?>
          <tr>
            <td><?= date('d M Y', strtotime($r->recorded_at)) ?></td>
            <td><?= ucwords(str_replace('_', ' ', esc($r->record_type))) ?></td>
            <td><?= esc($r->title) ?></td>
            <td><?= esc($r->description ?: '—') ?></td>
          </tr>
        <?php return ob_get_clean(); };
        $emptyMessage = 'No health records on file.';
        require __DIR__ . '/../components/data_table.php';
      ?>
    </div>
  </div>
</div>


<div id="p-boarding" class="section-panel">

  <?php if (empty($student->is_boarding)): ?>
    <div class="empty-state">This student is not a boarding student.</div>
  <?php else: ?>

  <div class="card">
    <div class="card-header"><h2>Daily Logs</h2></div>
    <div class="card-body">
      <?php
        $data    = $boarding;
        $headers = ['Date', 'Type', 'Description', 'Mood', 'Appetite', 'Sleep'];
        $renderRow = function ($l) { ob_start(); ?>
          <tr>
            <td><?= date('d M Y', strtotime($l->log_date)) ?></td>
            <td><?= ucwords(str_replace('_', ' ', esc($l->log_type))) ?></td>
            <td><?= esc($l->description) ?></td>
            <td><?= $l->mood_indicator ? ucfirst(esc($l->mood_indicator)) : '—' ?></td>
            <td><?= $l->appetite_level ? ucfirst(esc($l->appetite_level)) : '—' ?></td>
            <td><?= $l->sleep_quality ? ucfirst(esc($l->sleep_quality)) : '—' ?></td>
          </tr>
        <?php return ob_get_clean(); };
        $emptyMessage = 'No boarding logs yet.';
        require __DIR__ . '/../components/data_table.php';
      ?>
    </div>
  </div>

  <div class="card" style="margin-top:14px;">
    <div class="card-header"><h2>Check Ins / Outs</h2></div>
    <div class="card-body">
      <?php
        $data    = $checkins;
        $headers = ['Type', 'Time', 'Notes'];
        $renderRow = function ($c) { ob_start(); ?>
          <tr>
            <td><?= $c->check_type === 'check_in' ? 'Check In' : 'Check Out' ?></td>
            <td><?= date('d M Y H:i', strtotime($c->check_time)) ?></td>
            <td><?= esc($c->notes ?: '—') ?></td>
          </tr>
        <?php return ob_get_clean(); };
        $emptyMessage = 'No check-ins recorded.';
        require __DIR__ . '/../components/data_table.php';
      ?>
    </div>
  </div>

  <?php endif; ?>
</div>


<script src="<?= ROOT ?>/public/assets/js/display-student.js"></script>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
