<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle = 'Admin Dashboard';
$pageHeading = 'Dashboard';
$activePage = 'dashboard';

$topbarActions = '
  <a href="' . ROOT . '/admin/add_user"><button class="btn btn-primary">Add User</button></a>
  <a href="' . ROOT . '/admin/add_student"><button class="btn btn-primary">Add Student</button></a>
  <a href="' . ROOT . '/admin/assign_students"><button class="btn btn-primary">Assign Students</button></a>
';

$stats          = $stats          ?? null;
$recentUsers    = $recentUsers    ?? [];
$recentStudents = $recentStudents ?? [];
 
$totalStaff = (int)($stats->total_teachers    ?? 0)
            + (int)($stats->total_therapists   ?? 0)
            + (int)($stats->total_nurses       ?? 0)
            + (int)($stats->total_boarding_staff ?? 0)
            + (int)($stats->total_security     ?? 0);

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

require 'C:/xampp1/htdocs/mysess_new/public/assets/css/admin.php';
?>

<!-- Stat Cards -->
<div class="stat-cards">
    <?php
    statCard($stats->total_students   ?? 0, 'Total Students', '#2563eb');
    statCard($totalStaff, 'Total Staff', '#2563eb');
    statCard($stats->active_medications ?? 0, 'Active Medications', '#2563eb');
    ?>
</div>

<!-- Role Breakdown -->
<div class="card" style="margin-bottom: 28px;">
    <div class="card-header">
        <h2>Staff Breakdown</h2>
    </div>
    <div class="card-body">
        <div class="role-breakdown">
            <?php
            $roles = [
                'Teachers'       => $stats->total_teachers      ?? 0,
                'Therapists'     => $stats->total_therapists     ?? 0,
                'Nurses'         => $stats->total_nurses         ?? 0,
                'Parents'        => $stats->total_parents        ?? 0,
                'Boarding Staff' => $stats->total_boarding_staff ?? 0,
                'Security'       => $stats->total_security       ?? 0,
            ];
            foreach ($roles as $label => $count): ?>
                <div class="role-item">
                    <span class="role-label"><?= esc($label) ?></span>
                    <span class="role-count"><?= (int)$count ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
 
<!-- Recent Tables -->
<div class="dashboard-grid">
 
    <!-- Recent Students -->
    <div class="card">
        <div class="card-header">
            <h2>Recent Students</h2>
            <a href="<?= ROOT ?>/admin/students"><button class="btn">View All</button></a>
        </div>
        <div class="card-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Diagnosis</th>
                        <th>Enrolled</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentStudents)): ?>
                        <tr><td colspan="4">No students found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentStudents as $student): ?>
                            <tr>
                                <td><?= esc($student->first_name . ' ' . $student->last_name) ?></td>
                                <td><?= esc($student->diagnosis ?? '—') ?></td>
                                <td><?= date('d M Y', strtotime($student->enrollment_date)) ?></td>
                                <td>
                                    <span class="status-badge status-<?= $student->is_active ? 'active' : 'inactive' ?>">
                                        <?= $student->is_active ? 'Active' : 'Archived' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
 
</div>
 
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>