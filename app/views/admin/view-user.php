<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$user = $user ?? null;
if (!$user) {
  header('Location: ' . ROOT . '/admin/users');
  exit();
}

$pageTitle    = esc($user->first_name . ' ' . $user->last_name);
$pageHeading  = 'User Profile';
$activePage   = 'users';
$topbarActions = '
    <a href="' . ROOT . '/admin/users"><button class="btn btn-primary">← Back to Users</button></a>
    <a href="' . ROOT . '/admin/edit_user/' . $user->id . '"><button class="btn btn-primary">✏️ Edit</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

$assignedStudents = $assignedStudents ?? [];

require 'C:/xampp1/htdocs/mysess_new/public/assets/css/admin.php';
?>

<!-- Profile Card -->
<div class="profile-card">
  <div class="profile-card__initials">
    <?= strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) ?>
  </div>

  <div class="profile-card__info">
    <h2 class="profile-card__name"><?= esc($user->first_name . ' ' . $user->last_name) ?></h2>
    <div class="profile-card__meta">
      <span class="profile-meta-item">
        <span class="profile-meta-label">Role</span>
        <span class="role-badge role-<?= esc($user->role) ?>">
          <?= ucfirst(str_replace('_', ' ', $user->role)) ?>
        </span>
      </span>
      <span class="profile-meta-divider">|</span>
      <span class="profile-meta-item">
        <span class="profile-meta-label">Email</span>
        <span class="profile-meta-value"><?= esc($user->email) ?></span>
      </span>
      <span class="profile-meta-divider">|</span>
      <span class="profile-meta-item">
        <span class="profile-meta-label">Phone</span>
        <span class="profile-meta-value"><?= esc($user->phone ?? '—') ?></span>
      </span>
      <span class="profile-meta-divider">|</span>
      <span class="profile-meta-item">
        <span class="profile-meta-label">Status</span>
        <span class="status-badge status-<?= $user->is_active ? 'active' : 'inactive' ?>">
          <?= $user->is_active ? 'Active' : 'Inactive' ?>
        </span>
      </span>
      <span class="profile-meta-divider">|</span>
      <span class="profile-meta-item">
        <span class="profile-meta-label">Member Since</span>
        <span class="profile-meta-value"><?= date('d M Y', strtotime($user->created_at)) ?></span>
      </span>
    </div>
  </div>
</div>

<!-- Assigned Students (only for nurse, teacher, therapist, parent) -->
<?php if (in_array($user->role, ['nurse', 'teacher', 'therapist', 'parent'])): ?>
  <div class="card">
    <div class="card-header">
      <h2>
        <?= $user->role === 'parent' ? 'Children' : 'Assigned Students' ?>
      </h2>
      <?php if ($user->role !== 'parent'): ?>
        <a href="<?= ROOT ?>/admin/assign_students">
          <button class="btn">Manage Assignments</button>
        </a>
      <?php endif; ?>
    </div>
    <div class="card-body">
      <?php if (empty($assignedStudents)): ?>
        <div class="empty-state">No students assigned.</div>
      <?php else: ?>
        <table class="data-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Diagnosis</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($assignedStudents as $student): ?>
              <tr>
                <td><?= esc($student->first_name . ' ' . $student->last_name) ?></td>
                <td><?= esc($student->diagnosis ?? '—') ?></td>
                <td>
                  <a href="<?= ROOT ?>/admin/view_student/<?= $student->id ?>"
                    class="btn-icon btn-view" title="View Profile">👤</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>