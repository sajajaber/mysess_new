<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Add Daily Log';
$pageHeading = 'Add Daily Log';
$activePage  = 'students';

$topbarActions = '
  <a href="' . ROOT . '/boarding/student/' . (int)$student->id . '"><button class="btn btn-primary">Back to Student</button></a>
';

require_once __DIR__ . '/../layouts/boarding_header.php';
require_once __DIR__ . '/../components/alert.php';
?>

<div class="card">
  <div class="card-header">
    <h2>New Daily Log — <?= esc($student->first_name . ' ' . $student->last_name) ?></h2>
  </div>

  <div class="card-body">

    <p class="muted text-sm" style="margin-bottom:14px;">
      Pick what kind of thing you are logging. Then fill the matching section below.
      Anything you leave blank just stays empty, no problem.
    </p>


    <form method="POST" action="<?= ROOT ?>/boarding/add-log">

      <input type="hidden" name="student_id" value="<?= (int)$student->id ?>">


      <!-- top row: date + type, the always-required bits -->
      <div style="display:flex; gap:14px; flex-wrap:wrap; margin-bottom:18px;">

        <div class="form-group" style="flex:1; min-width:180px;">
          <label for="log_date">Date <span style="color:#eb004e">*</span></label>
          <input type="date" id="log_date" name="log_date" value="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="form-group" style="flex:1; min-width:200px;">
          <label for="log_type">Log Type <span style="color:#eb004e">*</span></label>
          <?php
            renderSelect(
              'log_type',
              [
                'sleep'          => 'Sleep',
                'meal'           => 'Nutrition / Meal',
                'behavior'       => 'Mood / Behavior',
                'daily_activity' => 'Daily Activity',
                'other'          => 'Other',
              ],
              'sleep',
              ['id' => 'log_type', 'required' => true]
            );
          ?>
        </div>

      </div>


      <!-- the main description, always required so we know what happened -->
      <div class="form-group">
        <label for="description">What happened? <span style="color:#eb004e">*</span></label>
        <textarea id="description" name="description" rows="3"
                  placeholder="A short note about today (e.g. slept well, ate lunch fully, was excited at art time...)" required></textarea>
      </div>


      <!-- ===== SLEEP SECTION ===== -->
      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-left:4px solid #B4A7D6;
                  border-radius:10px; padding:14px; margin-bottom:14px;">
        <h3 style="margin:0 0 4px; font-size:16px;">Sleep</h3>
        <p class="muted text-sm" style="margin:0 0 12px;">How well did they sleep, when did they go to bed and wake up.</p>

        <div style="display:flex; gap:12px; flex-wrap:wrap;">

          <div class="form-group" style="flex:1; min-width:180px; margin-bottom:0;">
            <label for="sleep_quality">Sleep quality</label>
            <?php
              renderSelect(
                'sleep_quality',
                [
                  ''     => '— not tracked —',
                  'good' => 'Good',
                  'fair' => 'Fair',
                  'poor' => 'Poor',
                ],
                '',
                ['id' => 'sleep_quality']
              );
            ?>
          </div>

          <div class="form-group" style="flex:1; min-width:140px; margin-bottom:0;">
            <label for="bedtime">Bedtime</label>
            <input type="time" id="bedtime" name="bedtime">
          </div>

          <div class="form-group" style="flex:1; min-width:140px; margin-bottom:0;">
            <label for="wakeup_time">Wake-up time</label>
            <input type="time" id="wakeup_time" name="wakeup_time">
          </div>

        </div>
      </div>


      <!-- ===== NUTRITION SECTION ===== -->
      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-left:4px solid #E2DFA2;
                  border-radius:10px; padding:14px; margin-bottom:14px;">
        <h3 style="margin:0 0 4px; font-size:16px;">Nutrition</h3>
        <p class="muted text-sm" style="margin:0 0 12px;">How they ate today. The description above can hold the meal details.</p>

        <div class="form-group" style="margin-bottom:0;">
          <label for="appetite_level">Appetite level</label>
          <?php
            renderSelect(
              'appetite_level',
              [
                ''        => '— not tracked —',
                'good'    => 'Good — finished most of the meal',
                'fair'    => 'Fair — ate some',
                'poor'    => 'Poor — barely ate',
                'refused' => 'Refused — would not eat',
              ],
              '',
              ['id' => 'appetite_level']
            );
          ?>
        </div>
      </div>


      <!-- ===== MOOD SECTION ===== -->
      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-left:4px solid #A1BE95;
                  border-radius:10px; padding:14px; margin-bottom:14px;">
        <h3 style="margin:0 0 4px; font-size:16px;">Mood</h3>
        <p class="muted text-sm" style="margin:0 0 12px;">How they felt today overall. Pick the closest one or leave it blank.</p>

        <!-- Friendly mood pills: clicking one ticks the matching radio -->
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
                         border:1.5px solid #cbd5e1; color:#64748b;
                         font-size:13px; transition:all 0.15s;"
                  data-color="#94a3b8">
              Not tracked
            </span>
          </label>
        </div>
      </div>


      <div class="form-group" style="margin-top:18px;">
        <button type="submit" class="btn btn-primary">Save Log</button>
        <a href="<?= ROOT ?>/boarding/student/<?= (int)$student->id ?>" class="btn">Cancel</a>
      </div>

    </form>

  </div>
</div>


<script>
  // simple mood pill picker: clicking a pill fills it in and unfills the others
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
      lbl.addEventListener('click', function () {
        // give the radio a tick before we re-paint
        setTimeout(paint, 0);
      });
    });

    paint();
  })();
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
