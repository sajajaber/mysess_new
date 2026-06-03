<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Add IEP Goal';
$pageHeading = 'Add IEP Goal';
$activePage  = 'students';

$topbarActions = '
  <a href="' . ROOT . '/teacher/student/' . (int)$student->id . '"><button class="btn btn-primary">Back to Student</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';
?>

<div class="card">
  <div class="card-header">
    <h2>New IEP Goal — <?= esc($student->first_name . ' ' . $student->last_name) ?></h2>
  </div>

  <div class="card-body">
    <form method="POST" action="<?= ROOT ?>/teacher/add-iep-goal">

      <input type="hidden" name="student_id" value="<?= (int)$student->id ?>">

      <div class="form-group">
        <label for="category">Category <span style="color:#eb004e">*</span></label>
        <!-- When the category changes, refill the goal bank picker below -->
        <?php
          renderSelect(
            'category',
            [
              'Communication' => 'Communication',
              'Social'        => 'Social',
              'Motor'         => 'Motor',
              'Academic'      => 'Academic',
              'Behavioral'    => 'Behavioral',
              'Daily Living'  => 'Daily Living',
            ],
            'Communication',
            ['id' => 'category', 'required' => true, 'onchange' => 'fillBankPicker()']
          );
        ?>
      </div>

      <div class="form-group">
        <label for="bank_pick">Choose from goal bank (optional)</label>
        <!-- used js to match the chosen category. -->
        <select id="bank_pick" onchange="document.getElementById('goal_description').value = this.value;">
          <option value="">— choose from goal bank (optional) —</option>
        </select>
      </div>

      <script>
        var bankGoals = [
          <?php foreach (($bankEntries ?? []) as $entry): ?>
          { category: "<?= esc($entry->category) ?>", text: "<?= esc(addslashes($entry->goal_text)) ?>" },
          <?php endforeach; ?>
        ];
        function fillBankPicker() {
          var chosenCategory = document.getElementById('category').value;
          var picker = document.getElementById('bank_pick');
          picker.innerHTML = '<option value="">— choose from goal bank (optional) —</option>';
          for (var i = 0; i < bankGoals.length; i++) {
            if (bankGoals[i].category === chosenCategory) {
              var option = document.createElement('option');
              option.value = bankGoals[i].text;
              option.textContent = bankGoals[i].text;
              picker.appendChild(option);
            }
          }
        }
        fillBankPicker();
      </script>

      <div class="form-group">
        <label for="goal_description">Goal Description <span style="color:#eb004e">*</span></label>
        <textarea id="goal_description" name="goal_description" rows="3"
                  placeholder="e.g. Read a full sentence out loud without help" required></textarea>
      </div>

      <div class="form-group">
        <label for="target_date">Target Date <span style="color:#eb004e">*</span></label>
        <input type="date" id="target_date" name="target_date"
               value="<?= date('Y-m-d') ?>" required>
      </div>

      <div class="form-group">
        <label for="status">Status</label>
        <?php
          renderSelect(
            'status',
            [
              'active'       => 'Active',
              'achieved'     => 'Achieved',
              'discontinued' => 'Discontinued',
            ],
            'active',
            ['id' => 'status']
          );
        ?>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Save Goal</button>
        <a href="<?= ROOT ?>/teacher/student/<?= (int)$student->id ?>" class="btn">Cancel</a>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
