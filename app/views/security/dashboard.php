<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Attendance';
$pageHeading = 'Attendance — ' . date('d M Y');
$activePage  = 'attendance';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

$students       = $students       ?? [];
$todayMap       = $todayMap       ?? [];
$noteMap        = $noteMap        ?? [];
$diagnoses      = $diagnoses      ?? [];
$checkInLocked  = $checkInLocked  ?? false;
$checkOutLocked = $checkOutLocked ?? false;
$inLockHour     = $inLockHour     ?? 9;
$outLockHour    = $outLockHour    ?? 16;
?>

<div class="stat-cards">
  <?php
    statCard($totalCount ?? 0, 'Total Students',    '#4f46e5');
    statCard($inCount    ?? 0, 'Checked In Today',  '#16a34a');
    statCard($outCount   ?? 0, 'Checked Out Today', '#dc2626');
  ?>
</div>


<?php if ($checkInLocked || $checkOutLocked): ?>
  <div class="alert" style="background:#fef9c3; border:1px solid #fde68a; color:#854d0e;
                            border-radius:10px; padding:10px 14px; margin-bottom:14px;">
    <?php if ($checkInLocked):  ?>Check-in is locked (after <?= sprintf('%02d', $inLockHour)  ?>:00). <?php endif; ?>
    <?php if ($checkOutLocked): ?>Check-out is locked (after <?= sprintf('%02d', $outLockHour) ?>:00).<?php endif; ?>
  </div>
<?php endif; ?>


<!-- Filters -->
<div class="card" style="margin-bottom:14px;">
  <div class="card-body" style="display:flex; gap:14px; flex-wrap:wrap; align-items:center;">

    <div class="form-group" style="margin:0; flex:1; min-width:220px;">
      <label for="filter-name" style="display:block; font-size:12px; color:#64748b;">Search by name</label>
      <input type="text" id="filter-name" placeholder="e.g. Sarah" oninput="filterAttendance()"
             style="width:100%; padding:7px 10px; border:1px solid #cbd5e1; border-radius:8px;">
    </div>

    <div class="form-group" style="margin:0; min-width:200px;">
      <label for="filter-diagnosis" style="display:block; font-size:12px; color:#64748b;">Diagnosis</label>
      <select id="filter-diagnosis" onchange="filterAttendance()"
              style="width:100%; padding:7px 10px; border:1px solid #cbd5e1; border-radius:8px;">
        <option value="">All diagnoses</option>
        <?php foreach ($diagnoses as $d): ?>
          <option value="<?= esc(strtolower($d)) ?>"><?= esc($d) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group" style="margin:0;">
      <label style="display:block; font-size:12px; color:#64748b;">Quick view</label>
      <label style="margin-right:10px; font-size:13px;">
        <input type="checkbox" id="filter-pending-in" onchange="filterAttendance()">
        Not checked in yet
      </label>
      <label style="font-size:13px;">
        <input type="checkbox" id="filter-pending-out" onchange="filterAttendance()">
        Not checked out yet
      </label>
    </div>

  </div>
</div>


<?php if (empty($students)): ?>
  <p>No active students to show.</p>
<?php else: ?>

  <div class="attendance-grid" id="attendance-grid">
    <?php foreach ($students as $s):
      $today    = $todayMap[$s->id] ?? ['check_in' => null, 'check_out' => null];
      $hasIn    = !empty($today['check_in']);
      $hasOut   = !empty($today['check_out']);
      $note     = $noteMap[$s->id] ?? '';
      $photo    = $s->photo_url ?? '';
      $photoUrl = $photo ? ROOT . '/public/assets/uploads/' . $photo : '';
      $initials = strtoupper(substr($s->first_name, 0, 1) . substr($s->last_name, 0, 1));
      $fullName = trim($s->first_name . ' ' . $s->last_name);
      $diagAttr = strtolower($s->diagnosis ?? '');
    ?>

      <div class="attendance-card"
           data-name="<?= esc(strtolower($fullName)) ?>"
           data-diagnosis="<?= esc($diagAttr) ?>"
           data-has-in="<?= $hasIn ? '1' : '0' ?>"
           data-has-out="<?= $hasOut ? '1' : '0' ?>"
           style="background:#fff; border:1px solid #e2e8f0; border-radius:12px;
                  padding:14px; margin-bottom:10px;">

        <div style="display:flex; gap:14px; align-items:center;">
          <div style="flex-shrink:0;">
            <?php if ($photoUrl): ?>
              <img src="<?= $photoUrl ?>" alt=""
                   style="width:56px; height:56px; border-radius:50%; object-fit:cover; border:1px solid #e2e8f0;">
            <?php else: ?>
              <div style="width:56px; height:56px; border-radius:50%; background:#4f46e5;
                          color:#fff; display:flex; align-items:center; justify-content:center;
                          font-size:18px; font-weight:600;"><?= $initials ?></div>
            <?php endif; ?>
          </div>

          <div style="flex:1;">
            <div style="font-weight:600; font-size:15px;"><?= esc($fullName) ?></div>
            <div style="color:#64748b; font-size:13px; margin-top:2px;">
              <?= $s->diagnosis ? esc($s->diagnosis) : 'No diagnosis on file' ?>
              &nbsp;|&nbsp;
              <?= ucfirst($s->gender ?? '') ?>
              &nbsp;|&nbsp;
              DOB <?= date('d M Y', strtotime($s->date_of_birth)) ?>
            </div>
            <?php if ($note !== ''): ?>
              <div style="color:#92400e; background:#fef9c3; border:1px solid #fde68a;
                          border-radius:8px; padding:6px 10px; margin-top:6px; font-size:13px;">
                <strong>Note:</strong> <?= esc($note) ?>
              </div>
            <?php endif; ?>
          </div>

          <button type="button" onclick="toggleNotes(<?= (int)$s->id ?>)"
                  style="background:#fff; color:#475569; border:1px solid #cbd5e1;
                         border-radius:8px; padding:7px 12px; font-size:13px; cursor:pointer;">
            <?= $note !== '' ? 'Edit note' : 'Add note' ?>
          </button>
        </div>


        <!-- The note has its OWN form and OWN Save button.
             It posts to /security/save-note and is sent to admin straight away. -->
        <form method="POST" action="<?= ROOT ?>/security/save-note"
              id="notes-form-<?= (int)$s->id ?>"
              style="display:none; margin-top:10px;">
          <input type="hidden" name="student_id" value="<?= (int)$s->id ?>">
          <textarea name="note" rows="2"
                    placeholder="e.g. Picked up early by guardian, late bus, etc."
                    style="width:100%; padding:7px 10px; border:1px solid #cbd5e1; border-radius:8px;"><?= esc($note) ?></textarea>
          <div style="margin-top:6px; display:flex; gap:8px; align-items:center;">
            <button type="submit" class="btn btn-primary" style="font-size:13px;">Save &amp; send to admin</button>
            <span style="color:#64748b; font-size:12px;">The admin will see this on the Attendance review page.</span>
          </div>
        </form>


        <!-- Toggle buttons: NOT locked by clicking, only by current time -->
        <div style="display:flex; gap:10px; margin-top:10px; justify-content:flex-end;">

          <form method="POST" action="<?= ROOT ?>/security/toggle" style="margin:0;">
            <input type="hidden" name="student_id" value="<?= (int)$s->id ?>">
            <input type="hidden" name="check_type" value="check_in">
            <?php if ($checkInLocked): ?>
              <button type="button" disabled
                      style="background:#cbd5e1; color:#475569; border:none; border-radius:8px;
                             padding:8px 14px; font-size:13px; cursor:not-allowed;">
                <?php if ($hasIn): ?>
                  Locked · <?= date('H:i', strtotime($today['check_in'])) ?>
                <?php else: ?>
                  Check-in locked
                <?php endif; ?>
              </button>
            <?php elseif ($hasIn): ?>
              <button type="submit"
                      style="background:#16a34a; color:#fff; border:1.5px solid #16a34a;
                             border-radius:8px; padding:8px 14px; font-size:13px; cursor:pointer;"
                      title="Click to update the time">
                Checked In · <?= date('H:i', strtotime($today['check_in'])) ?>
              </button>
            <?php else: ?>
              <button type="submit"
                      style="background:#fff; color:#16a34a; border:1.5px solid #16a34a;
                             border-radius:8px; padding:8px 14px; font-size:13px; cursor:pointer;">
                Check In
              </button>
            <?php endif; ?>
          </form>

          <form method="POST" action="<?= ROOT ?>/security/toggle" style="margin:0;">
            <input type="hidden" name="student_id" value="<?= (int)$s->id ?>">
            <input type="hidden" name="check_type" value="check_out">
            <?php if ($checkOutLocked): ?>
              <button type="button" disabled
                      style="background:#cbd5e1; color:#475569; border:none; border-radius:8px;
                             padding:8px 14px; font-size:13px; cursor:not-allowed;">
                <?php if ($hasOut): ?>
                  Locked · <?= date('H:i', strtotime($today['check_out'])) ?>
                <?php else: ?>
                  Check-out locked
                <?php endif; ?>
              </button>
            <?php elseif ($hasOut): ?>
              <button type="submit"
                      style="background:#dc2626; color:#fff; border:1.5px solid #dc2626;
                             border-radius:8px; padding:8px 14px; font-size:13px; cursor:pointer;"
                      title="Click to update the time">
                Checked Out · <?= date('H:i', strtotime($today['check_out'])) ?>
              </button>
            <?php else: ?>
              <button type="submit"
                      style="background:#fff; color:#dc2626; border:1.5px solid #dc2626;
                             border-radius:8px; padding:8px 14px; font-size:13px; cursor:pointer;">
                Check Out
              </button>
            <?php endif; ?>
          </form>

        </div>
      </div>

    <?php endforeach; ?>
  </div>

<?php endif; ?>


<script>
  function toggleNotes(id) {
    var f = document.getElementById('notes-form-' + id);
    if (!f) return;
    f.style.display = (f.style.display === 'none' || f.style.display === '') ? 'block' : 'none';
  }

  function filterAttendance() {
    var name        = (document.getElementById('filter-name').value || '').toLowerCase().trim();
    var diagnosis   = document.getElementById('filter-diagnosis').value;
    var pendingIn   = document.getElementById('filter-pending-in').checked;
    var pendingOut  = document.getElementById('filter-pending-out').checked;

    var cards = document.querySelectorAll('.attendance-card');
    cards.forEach(function (card) {
      var matchesName = !name || card.dataset.name.indexOf(name) !== -1;
      var matchesDiag = !diagnosis || card.dataset.diagnosis === diagnosis;
      var matchesIn   = !pendingIn  || card.dataset.hasIn  === '0';
      var matchesOut  = !pendingOut || card.dataset.hasOut === '0';

      card.style.display = (matchesName && matchesDiag && matchesIn && matchesOut) ? '' : 'none';
    });
  }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
