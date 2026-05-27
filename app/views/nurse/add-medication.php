<!-- Add Medication Modal -->
<?php
$modalId     = 'addMedicationModal';
$modalTitle  = 'Add Medication';
$modalAction = ROOT . '/nurse/add_medication';

// Build student options using object syntax
$studentOptions = array_reduce($students ?? [], function ($carry, $student) {
  return $carry . '<option value="' . $student->id . '">'
    . esc($student->first_name . ' ' . $student->last_name)
    . '</option>';
}, '');

$modalContent = '
<div class="form-group">
  <label>Student:</label>
  <select name="student_id" required>
    <option value="">Select Student</option>
    ' . $studentOptions . '
  </select>
</div>
<div class="form-row">
  <div class="form-group">
    <label>Medication Name:</label>
    <input type="text" name="name" placeholder="e.g., Ritalin" required>
  </div>
  <div class="form-group">
    <label>Dosage:</label>
    <input type="text" name="dosage" placeholder="e.g., 10mg" required>
  </div>
</div>
<div class="form-row">
  <div class="form-group">
    <label>Frequency:</label>
    <input type="text" name="frequency" placeholder="e.g., Twice daily" required>
  </div>
</div>
<div class="form-group">
  <label>Instructions/Notes:</label>
  <textarea name="instructions" rows="3" placeholder="Any special instructions..."></textarea>
</div>
';
$modalSubmitText = 'Add Medication';

require __DIR__ . '/../components/modal.php';
