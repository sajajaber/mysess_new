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
    <form method="POST" action="<?= ROOT ?>/boarding/add-log">

      <input type="hidden" name="student_id" value="<?= (int)$student->id ?>">

      <div class="form-group">
        <label for="log_date">Date <span style="color:#eb004e">*</span></label>
        <input type="date" id="log_date" name="log_date" value="<?= date('Y-m-d') ?>" required>
      </div>

      <div class="form-group">
        <label for="log_type">Log Type <span style="color:#eb004e">*</span></label>
        <?php
          renderSelect(
            'log_type',
            [
              'daily_activity' => 'Daily Activity',
              'behavior'       => 'Behavior',
              'meal'           => 'Meal',
              'sleep'          => 'Sleep',
              'other'          => 'Other',
            ],
            'daily_activity',
            ['id' => 'log_type', 'required' => true]
          );
        ?>
      </div>

      <div class="form-group">
        <label for="description">Description <span style="color:#eb004e">*</span></label>
        <textarea id="description" name="description" rows="3"
                  placeholder="What happened? Describe the activity, behavior, meal, etc." required></textarea>
      </div>

      <!-- Optional well-being fields -->
      <div class="form-group">
        <label for="mood_indicator">Mood</label>
        <?php
          renderSelect(
            'mood_indicator',
            [
              ''        => '— optional —',
              'happy'   => 'Happy',
              'calm'    => 'Calm',
              'anxious' => 'Anxious',
              'upset'   => 'Upset',
              'other'   => 'Other',
            ],
            '',
            ['id' => 'mood_indicator']
          );
        ?>
      </div>

      <div class="form-group">
        <label for="appetite_level">Appetite</label>
        <?php
          renderSelect(
            'appetite_level',
            [
              ''        => '— optional —',
              'good'    => 'Good',
              'fair'    => 'Fair',
              'poor'    => 'Poor',
              'refused' => 'Refused',
            ],
            '',
            ['id' => 'appetite_level']
          );
        ?>
      </div>

      <div class="form-group">
        <label for="sleep_quality">Sleep Quality</label>
        <?php
          renderSelect(
            'sleep_quality',
            [
              ''     => '— optional —',
              'good' => 'Good',
              'fair' => 'Fair',
              'poor' => 'Poor',
            ],
            '',
            ['id' => 'sleep_quality']
          );
        ?>
      </div>

      <div class="form-group">
        <label for="bedtime">Bedtime</label>
        <input type="time" id="bedtime" name="bedtime">
      </div>

      <div class="form-group">
        <label for="wakeup_time">Wake-up Time</label>
        <input type="time" id="wakeup_time" name="wakeup_time">
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Save Log</button>
        <a href="<?= ROOT ?>/boarding/student/<?= (int)$student->id ?>" class="btn">Cancel</a>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
