<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'Attendance Review';
$pageHeading = 'Attendance Review';
$activePage  = 'attendance';

$topbarActions = '
  <a href="' . ROOT . '/admin/dashboard"><button class="btn btn-primary">Dashboard</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

$date    = $date    ?? date('Y-m-d');
$records = $records ?? [];
$noteMap = $noteMap ?? [];

$totalIn  = 0;
$totalOut = 0;
foreach ($records as $r) {
  if ($r->check_in_time)  { $totalIn++; }
  if ($r->check_out_time) { $totalOut++; }
}
?>

<div class="card" style="margin-bottom:14px;">
  <div class="card-body">
    <form method="GET" action="<?= ROOT ?>/admin/attendance" style="display:flex; gap:10px; align-items:flex-end;">
      <div class="form-group" style="margin:0;">
        <label for="date" style="display:block; font-size:12px; color:#64748b;">Pick a date</label>
        <input type="date" id="date" name="date" value="<?= esc($date) ?>" required>
      </div>
      <button type="submit" class="btn btn-primary">View</button>
    </form>
  </div>
</div>

<div style="display:flex; gap:12px; margin-bottom:14px; flex-wrap:wrap;">
  <div style="flex:1; min-width:140px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:14px; text-align:center;">
    <div style="font-size:28px; font-weight:700; color:#16a34a;"><?= $totalIn ?></div>
    <div style="color:#64748b; font-size:13px;">Checked In</div>
  </div>
  <div style="flex:1; min-width:140px; background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:14px; text-align:center;">
    <div style="font-size:28px; font-weight:700; color:#dc2626;"><?= $totalOut ?></div>
    <div style="color:#64748b; font-size:13px;">Checked Out</div>
  </div>
  <div style="flex:1; min-width:140px; background:#fef9c3; border:1px solid #fde68a; border-radius:10px; padding:14px; text-align:center;">
    <div style="font-size:28px; font-weight:700; color:#854d0e;"><?= count($noteMap) ?></div>
    <div style="color:#64748b; font-size:13px;">Notes from Security</div>
  </div>
</div>


<?php
$data    = $records ?? [];
$headers = ['Student', 'Diagnosis', 'Check In', 'Check Out', 'Note from Security'];

$renderRow = function ($row) use ($noteMap) { ob_start(); ?>
  <tr>
    <td><?= esc(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')) ?></td>
    <td><?= esc($row->diagnosis ?: '—') ?></td>
    <td>
      <?php if ($row->check_in_time): ?>
        <span style="color:#16a34a; font-weight:600;"><?= date('H:i', strtotime($row->check_in_time)) ?></span>
      <?php else: ?>
        <span style="color:#94a3b8;">—</span>
      <?php endif; ?>
    </td>
    <td>
      <?php if ($row->check_out_time): ?>
        <span style="color:#dc2626; font-weight:600;"><?= date('H:i', strtotime($row->check_out_time)) ?></span>
      <?php else: ?>
        <span style="color:#94a3b8;">—</span>
      <?php endif; ?>
    </td>
    <td>
      <?php $n = $noteMap[$row->student_id] ?? ''; ?>
      <?php if ($n !== ''): ?>
        <span style="background:#fef9c3; border:1px solid #fde68a; border-radius:6px;
                     padding:2px 8px; color:#854d0e;"><?= esc($n) ?></span>
      <?php else: ?>
        <span style="color:#94a3b8;">—</span>
      <?php endif; ?>
    </td>
  </tr>
<?php return ob_get_clean(); };

$emptyMessage = 'No check-ins or check-outs recorded on this date.';
require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
