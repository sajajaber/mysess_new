<?php

// LOAD CONFIG FIRST - BEFORE ANYTHING ELSE
require_once __DIR__ . '/../../core/config.php';

$pageTitle    = 'Student Medications';
$pageHeading  = 'Student Medications';
$activePage   = 'medications';

$topbarActions = '<button class="btn btn-primary" onclick="openModal(\'addMedicationModal\')">Add Medication</button>';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

?>

<!-- Medications Table -->
<?php
$data    = $medications ?? [];
$headers = ['Student', 'Medication', 'Dosage', 'Frequency', 'Instructions', 'Status', 'Actions'];
$renderRow = function ($med) {
  ob_start(); // Start output buffering to capture the generated HTML for this row, this is important to avoid issues with nested loops and variable scope when generating table rows
?>
  <tr>
    <td>
      <a href="<?= ROOT ?>/nurse/student/<?= $med->student_id ?>" class="student-link">
        <?= esc($med->first_name . ' ' . $med->last_name) ?>
      </a>
    </td>
    <td><strong><?= esc($med->name) ?></strong></td>
    <td><?= esc($med->dosage) ?></td>
    <td><?= esc($med->frequency) ?></td>
    <td><?= esc($med->instructions ?? '—') ?></td>
    <td>
      <span class="status-badge status-<?= $med->is_active ? 'active' : 'inactive' ?>">
        <?= $med->is_active ? 'Active' : 'Inactive' ?>
      </span>
    </td>
    <td class="actions">
      <a href="<?= ROOT ?>/nurse/log_dose?med_id=<?= $med->id ?>" class="btn-icon btn-log" title="Log Dose">💊</a>
      <?php if ($med->is_active): ?>
        <form method="POST" action="<?= ROOT ?>/nurse/deactivate_medication" class="inline-form" onsubmit="return confirm('Deactivate this medication?')">
          <input type="hidden" name="medication_id" value="<?= $med->id ?>">
          <input type="hidden" name="student_id" value="<?= $med->student_id ?>">
          <button type="submit" class="btn-icon btn-deactivate" title="Deactivate">❌</button>
        </form>
      <?php endif; ?>
    </td>
  </tr>
<?php
  return ob_get_clean();
};

$emptyMessage = 'No medication records found for your students.';

require __DIR__ . '/../components/data_table.php';
?>

<?php
require __DIR__ . '/add-medication.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>