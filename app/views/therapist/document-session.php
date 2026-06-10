<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Document Session';
$pageHeading = 'Document Session';
$activePage  = 'students';

$topbarActions = '
  <a href="' . ROOT . '/therapist/student/' . (int)$session->student_id . '"><button class="btn btn-primary">Back to Student</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

$goalOptions = ['' => '— none —'];
foreach (($iepGoals ?? []) as $goal) {
  $goalOptions[$goal->id] = $goal->goal_description;
}
?>

<div class="card">
  <div class="card-header">
    <h2>Document Session</h2>
  </div>

  <div class="card-body">

    <div class="info-banner" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;margin-bottom:24px;">
      <strong>Session:</strong> <?= esc($session->session_type) ?>
      &nbsp;·&nbsp; <?= date('d M Y', strtotime($session->session_date)) ?>
      <br><small style="color:#666;">Current status: <?= ucfirst(esc($session->status)) ?></small>
    </div>

    <form method="POST" action="<?= ROOT ?>/therapist/document-session">

      <input type="hidden" name="session_id"  value="<?= (int)$session->id ?>">
      <input type="hidden" name="student_id"  value="<?= (int)$session->student_id ?>">

      <div class="form-group">
        <label for="performance_notes">Performance Notes <span style="color:#eb004e">*</span></label>
        <textarea id="performance_notes" name="performance_notes" rows="4"
                  placeholder="How did the session go? What did the student do?" required></textarea>
      </div>

      <div class="form-group">
        <label for="goal_addressed_id">IEP Goal Addressed (optional)</label>
        <?php
          renderSelect(
            'goal_addressed_id',
            $goalOptions,
            '',
            ['id' => 'goal_addressed_id']
          );
        ?>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Save Documentation</button>
        <a href="<?= ROOT ?>/therapist/student/<?= (int)$session->student_id ?>" class="btn">Cancel</a>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
