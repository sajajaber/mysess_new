<?php
require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'My Students';
$pageHeading = 'My Students';
$activePage  = 'students';

$topbarActions = '
<a href="' . ROOT . '/nurse/dashboard"><button class="btn btn-primary">Dashboard</button></a>
<a href="' . ROOT . '/nurse/all_medications"><button class="btn btn-primary">Medications</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

?>

<?php
$data = $students ?? [];
$headers = ['Name', 'Date of Birth', 'Diagnosis', 'Actions'];
$renderRow = function ($student) {
  ob_start();
  $fullName = esc($student->first_name . ' ' . $student->last_name);
?>
  <tr>
    <td>
      <div class="student-info">
        <div class="student-avatar-small"></div>
        <a href="<?= ROOT ?>/nurse/student/<?= $student->id ?>" class="student-name">
          <?= $fullName ?>
        </a>
      </div>
    </td>
    <td><?= date('d-m-Y', strtotime($student->date_of_birth)) ?></td>
    <td><?= esc($student->diagnosis ?? '—') ?></td>
    <td class="actions">
      <a href="<?= ROOT ?>/nurse/student/<?= $student->id ?>" class="btn-icon btn-view" title="View Profile">👤</a>
      <button type="button"
              class="btn-icon btn-add"
              title="Add Medication"
              onclick="openAddMed(<?= (int)$student->id ?>, '<?= addslashes($fullName) ?>')">💊</button>
      <a href="<?= ROOT ?>/nurse/log_health_event?student_id=<?= $student->id ?>" class="btn-icon btn-event" title="Log Event">⚠️</a>
    </td>
  </tr>
<?php
  return ob_get_clean();
};

$emptyMessage = 'No students assigned to you yet.';
require __DIR__ . '/../components/data_table.php';
?>


<div id="addMedicationModal" class="overlay">
  <div class="modal">
    <div class="modal-header">
      <h2>Add Medication</h2>
      <button type="button" class="close-btn" onclick="closeModal('addMedicationModal')">×</button>
    </div>
    <form method="POST" action="<?= ROOT ?>/nurse/add_medication" class="modal-form">
      <div class="modal-body">
        <input type="hidden" name="student_id" id="addMedStudentId" value="">

        <div class="form-group">
          <label>For:</label>
          <div id="addMedStudentName" style="font-weight:600;"></div>
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
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" onclick="closeModal('addMedicationModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Add Medication</button>
      </div>
    </form>
  </div>
</div>


<script>
  function openAddMed(studentId, studentName) {
    document.getElementById('addMedStudentId').value   = studentId;
    document.getElementById('addMedStudentName').textContent = studentName;
    openModal('addMedicationModal');
  }
</script>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
