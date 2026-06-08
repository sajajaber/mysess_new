<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Daily Logs';
$pageHeading = 'Daily Logs';
$activePage  = 'daily logs';

$topbarActions = '
  <a href="' . ROOT . '/boarding/students"><button class="btn btn-primary">Students</button></a>
';

require_once __DIR__ . '/../layouts/boarding_header.php';
require_once __DIR__ . '/../components/alert.php';

$logs = $logs ?? [];
?>


<div class="card" style="margin-bottom:14px;">
  <div class="card-body" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">

    <span class="muted text-sm">Show:</span>

    <button class="btn btn-sm log-filter active" data-filter="all">All</button>
    <button class="btn btn-sm log-filter"        data-filter="sleep">Sleep</button>
    <button class="btn btn-sm log-filter"        data-filter="meal">Nutrition</button>
    <button class="btn btn-sm log-filter"        data-filter="behavior">Mood</button>
    <button class="btn btn-sm log-filter"        data-filter="daily_activity">Activity</button>

  </div>
</div>


<?php
$typeColors = [
  'sleep'          => '#B4A7D6',  // lavender
  'meal'           => '#E2DFA2',  // pale yellow
  'behavior'       => '#A1BE95',  // sage
  'daily_activity' => '#92AAC7',  // soft blue
  'other'          => '#cbd5e1',
];

$data    = $logs;
$headers = ['Student', 'Date', 'Type', 'Description'];

$renderRow = function ($log) use ($typeColors) {
  ob_start();
  $type = $log->log_type ?? 'other';
  $dot  = $typeColors[$type] ?? '#cbd5e1';
?>
  <tr class="log-row" data-type="<?= esc($type) ?>">
    <td><?= esc(($log->student_first_name ?? '') . ' ' . ($log->student_last_name ?? '')) ?></td>
    <td><?= date('d M Y', strtotime($log->log_date)) ?></td>
    <td>
      <span style="display:inline-block; width:10px; height:10px; border-radius:50%;
                   background:<?= $dot ?>; margin-right:6px;"></span>
      <?= ucwords(str_replace('_', ' ', esc($type))) ?>
    </td>
    <td><?= esc($log->description) ?></td>
  </tr>
<?php
  return ob_get_clean();
};

$emptyMessage = 'No daily logs yet.';

require __DIR__ . '/../components/data_table.php';
?>


<script>
  (function () {
    var buttons = document.querySelectorAll('.log-filter');
    var rows    = document.querySelectorAll('.log-row');

    function applyFilter(value) {
      rows.forEach(function (r) {
        if (value === 'all' || r.dataset.type === value) {
          r.style.display = '';
        } else {
          r.style.display = 'none';
        }
      });
    }

    buttons.forEach(function (b) {
      b.addEventListener('click', function () {
        buttons.forEach(function (x) { x.classList.remove('active'); });
        b.classList.add('active');
        applyFilter(b.dataset.filter);
      });
    });
  })();
</script>


<style>
  .log-filter.active { background: var(--module-main, #4f46e5); color:#fff; border-color: var(--module-main, #4f46e5); }
</style>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
