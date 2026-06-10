<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Add Mood Log';
$pageHeading = 'Add Mood Log';
$activePage  = 'mood';

$topbarActions = '
  <a href="' . ROOT . '/boarding/student/' . (int)$student->id . '"><button class="btn btn-primary">Back to Student</button></a>
';

require_once __DIR__ . '/../layouts/boarding_header.php';
require_once __DIR__ . '/../components/alert.php';
?>

<div class="card">
  <div class="card-header">
    <h2>New Mood Log — <?= esc($student->first_name . ' ' . $student->last_name) ?></h2>
  </div>

  <div class="card-body">
    <form method="POST" action="<?= ROOT ?>/boarding/add-mood">
      <input type="hidden" name="student_id" value="<?= (int)$student->id ?>">

      <div class="form-group">
        <label for="log_date">Date <span style="color:#eb004e">*</span></label>
        <input type="date" id="log_date" name="log_date" value="<?= date('Y-m-d') ?>" required>
      </div>

      <div class="form-group">
        <label>Mood</label>
        <div id="mood-pills" style="display:flex; gap:8px; flex-wrap:wrap;">
          <?php
            $moods = [
              'happy'   => ['label' => 'Happy',   'color' => '#16a34a'],
              'calm'    => ['label' => 'Calm',    'color' => '#0ea5e9'],
              'anxious' => ['label' => 'Anxious', 'color' => '#d97706'],
              'upset'   => ['label' => 'Upset',   'color' => '#dc2626'],
              'other'   => ['label' => 'Other',   'color' => '#64748b'],
            ];
            foreach ($moods as $key => $m): ?>
              <label style="cursor:pointer;">
                <input type="radio" name="mood_indicator" value="<?= esc($key) ?>" style="display:none;">
                <span class="mood-pill"
                      style="display:inline-block; padding:6px 14px; border-radius:999px;
                             border:1.5px solid <?= $m['color'] ?>; color:<?= $m['color'] ?>;
                             font-size:13px; transition:all 0.15s;"
                      data-color="<?= $m['color'] ?>">
                  <?= esc($m['label']) ?>
                </span>
              </label>
          <?php endforeach; ?>
          <label style="cursor:pointer;">
            <input type="radio" name="mood_indicator" value="" style="display:none;" checked>
            <span class="mood-pill"
                  style="display:inline-block; padding:6px 14px; border-radius:999px;
                         border:1.5px solid #cbd5e1; color:#64748b; font-size:13px;
                         transition:all 0.15s;"
                  data-color="#94a3b8">
              Not tracked
            </span>
          </label>
        </div>
      </div>

      <div class="form-group">
        <label for="description">Notes <span style="color:#eb004e">*</span></label>
        <textarea id="description" name="description" rows="3" placeholder="Anything about how they felt today" required></textarea>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Save Mood Log</button>
        <a href="<?= ROOT ?>/boarding/student/<?= (int)$student->id ?>" class="btn">Cancel</a>
      </div>
    </form>
  </div>
</div>

<script>
  (function () {
    var pills = document.querySelectorAll('#mood-pills label');
    function paint() {
      pills.forEach(function (lbl) {
        var radio = lbl.querySelector('input[type=radio]');
        var pill  = lbl.querySelector('.mood-pill');
        var color = pill.getAttribute('data-color');
        if (radio.checked) {
          pill.style.background = color;
          pill.style.color      = '#fff';
        } else {
          pill.style.background = '';
          pill.style.color      = color;
        }
      });
    }
    pills.forEach(function (lbl) {
      lbl.addEventListener('click', function () { setTimeout(paint, 0); });
    });
    paint();
  })();
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
