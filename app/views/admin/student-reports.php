<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Student Reports';
$pageHeading = 'Student Reports';
$activePage  = 'student reports';

require_once __DIR__ . '/../layouts/admin_header.php';
require_once __DIR__ . '/../components/alert.php';

$students  = $students  ?? [];
$diagnoses = $diagnoses ?? [];
$name      = $name      ?? '';
$diagnosis = $diagnosis ?? '';

$printQuery = http_build_query(['name' => $name, 'diagnosis' => $diagnosis]);
?>

<div class="card">
  <div class="card-header"><h2>Find Students</h2></div>
  <div class="card-body">
    <form method="GET" action="<?= ROOT ?>/admin/student_reports">
      <div class="form-row">
        <div class="form-group">
          <label for="name">Search by name</label>
          <input type="text" id="name" name="name" value="" placeholder="First or last name"
                 autocomplete="off"
                 oninput="document.getElementById('diagnosis').value = '';">
        </div>
        <div class="form-group">
          <label for="diagnosis">Filter by diagnosis</label>
          <select id="diagnosis" name="diagnosis"
                  onchange="document.getElementById('name').value = '';">
            <option value="">All diagnoses</option>
            <?php foreach ($diagnoses as $d): ?>
              <option value="<?= esc($d->diagnosis) ?>" <?= $diagnosis === $d->diagnosis ? 'selected' : '' ?>>
                <?= esc($d->diagnosis) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="<?= ROOT ?>/admin/student_reports" class="btn">Reset</a>
      </div>
    </form>
  </div>
</div>


<div class="card" style="margin-top:16px;">
  <div class="card-header">
    <h2><?= count($students) ?> Student<?= count($students) !== 1 ? 's' : '' ?></h2>
    <?php if (!empty($students)): ?>
      <a href="<?= ROOT ?>/admin/print_reports?<?= $printQuery ?>" target="_blank">
        <button class="btn">Print All Shown</button>
      </a>
    <?php endif; ?>
  </div>
  <div class="card-body">

    <?php if (empty($students)): ?>
      <div class="empty-state">No students match your search.</div>
    <?php else: ?>

      <form method="POST" action="<?= ROOT ?>/admin/share_all">
        <div style="margin-bottom:10px; display:flex; gap:8px;">
          <button type="button" class="btn btn-sm" onclick="toggleAllReports(true)">Select all</button>
          <button type="button" class="btn btn-sm" onclick="toggleAllReports(false)">Clear</button>
          <button type="submit" class="btn btn-sm btn-primary">Share selected with parents</button>
        </div>

        <table class="data-table">
          <thead>
            <tr>
              <th style="width:40px;"></th>
              <th>Name</th>
              <th>Diagnosis</th>
              <th>Boarding</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($students as $s): ?>
              <tr>
                <td><input type="checkbox" class="reportCheck" name="student_ids[]" value="<?= (int)$s->id ?>"></td>
                <td><?= esc($s->first_name . ' ' . $s->last_name) ?></td>
                <td><?= esc($s->diagnosis ?: '—') ?></td>
                <td><?= !empty($s->is_boarding) ? 'Yes' : 'No' ?></td>
                <td class="actions">
                  <a href="<?= ROOT ?>/admin/student_report/<?= (int)$s->id ?>" class="btn btn-sm btn-primary">Open</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </form>

    <?php endif; ?>

  </div>
</div>

<script>
  function toggleAllReports(on) {
    document.querySelectorAll('.reportCheck').forEach(function (c) { c.checked = on; });
  }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
