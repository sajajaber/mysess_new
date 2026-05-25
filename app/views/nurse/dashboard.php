<?php
$pageTitle = 'Nurse Dashboard';
$pageHeading = 'Dashboard';
$activePage = 'dashboard';
$topbarActions = '<a href="' . ROOT . '/nurse" class="btn btn-secondary">View Students</a>';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';
?>

<!-- Statistics Cards -->
<div class="stats-grid">
  <?php
  $totalStudents = $totalStudents ?? 0;
  $allergyCount = $allergyCount ?? 0;
  $medicationCount = $medicationCount ?? 0;
  $recentHealthRecords = $recentHealthRecords ?? [];
  $recentHealthEvents = $recentHealthEvents ?? [];

  $stats = [
    ['value' => $totalStudents, 'label' => 'Assigned Students', 'class' => 'total-students-card', 'icon' => '👥'],
    ['value' => $allergyCount, 'label' => 'Students with Allergies', 'class' => 'allergies-card', 'icon' => '⚠️'],
    ['value' => $medicationCount, 'label' => 'Students on Medication', 'class' => 'medications-card', 'icon' => '💊']
  ];

  foreach ($stats as $stat):
    require __DIR__ . '/../components/stat_card.php';
  endforeach;
  ?>
</div>

<!-- Recent Health Records -->
<?php
$recordsData = $recentHealthRecords ?? [];
$recordsHeaders = ['Student Name', 'Type', 'Title', 'Description', 'Recorded By', 'Date'];
$recordsRenderRow = function ($record) {
  ob_start();
?>
  <tr>
    <td><?= esc($record->student_first_name . ' ' . $record->student_last_name) ?></td>
    <td><span class="badge badge-<?= $record->record_type ?>"><?= ucfirst(esc($record->record_type)) ?></span></td>
    <td><?= esc($record->title) ?></td>
    <td><?= esc(substr($record->description ?? '', 0, 50)) ?></td>
    <td><?= esc(($record->recorded_by_first_name ?? '') . ' ' . ($record->recorded_by_last_name ?? '')) ?></td>
    <td><?= date('d-m-Y', strtotime($record->recorded_at)) ?></td>
  </tr>
<?php
  return ob_get_clean();
};

$action = '<a href="' . ROOT . '/nurse" class="btn btn-sm btn-primary">View All Students</a>';

require __DIR__ . '/../components/data_table.php';
?>

<!-- Recent Health Events -->
<?php if (!empty($recentHealthEvents)):
  $eventsData = $recentHealthEvents;
  $eventsHeaders = ['Student', 'Severity', 'Description', 'Action Taken', 'Recorded At'];
  $eventsRenderRow = function ($event) {
    $severityClass = match ($event->severity) {
      'high' => 'severity-high',
      'medium' => 'severity-medium',
      'low' => 'severity-low',
      default => ''
    };
    ob_start();
?>
    <tr>
      <td><?= esc($event->student_first_name . ' ' . $event->student_last_name) ?></td>
      <td><span class="severity-badge <?= $severityClass ?>"><?= strtoupper(esc($event->severity)) ?></span></td>
      <td><?= esc(substr($event->description, 0, 60)) ?></td>
      <td><?= esc(substr($event->action_taken ?? '', 0, 50)) ?></td>
      <td><?= date('d-m-Y H:i', strtotime($event->recorded_at)) ?></td>
    </tr>
<?php
    return ob_get_clean();
  };

  require __DIR__ . '/../components/data_table.php';
endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>