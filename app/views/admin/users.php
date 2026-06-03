<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle    = 'Users';
$pageHeading  = 'All Users';
$activePage   = 'users';
$topbarActions = '
    <a href="' . ROOT . '/admin/add_user"><button class="btn btn-primary">+ Add User</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

require __DIR__ . '/../../../public/assets/css/admin.php';

$users = $users ?? [];

// Group users by role
$grouped = [];
foreach ($users as $user) {
  $grouped[$user->role][] = $user;
}

$roles = ['admin', 'teacher', 'therapist', 'nurse', 'parent', 'boarding_staff', 'security_guard'];
$activeTab = $_GET['role'] ?? 'all';
?>

<!-- Role Tabs -->
<div class="section-tabs" style="margin-bottom: 20px;">
  <button class="section-tab <?= $activeTab === 'all' ? 'active' : '' ?>"
    onclick="filterTab('all')">
    All <?= count($users) ?>
  </button>
  <?php foreach ($roles as $role): ?>
    <?php $count = count($grouped[$role] ?? []); ?>
    <?php if ($count > 0): ?>
      <button class="section-tab <?= $activeTab === $role ? 'active' : '' ?>"
        onclick="filterTab('<?= $role ?>')">
        <?= ucfirst(str_replace('_', ' ', $role)) ?> <?= $count ?>
      </button>
    <?php endif; ?>
  <?php endforeach; ?>
</div>

<!-- Users Table -->
<div class="card">
  <div class="card-header">
    <h2 id="table-heading">All Users</h2>
  </div>
  <div class="card-body">
    <table class="data-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Role</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="users-tbody">
        <?php if (empty($users)): ?>
          <tr>
            <td colspan="6">No users found.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($users as $user): ?>
            <tr class="user-row" data-role="<?= esc($user->role) ?>">
              <td>
                <a href="<?= ROOT ?>/admin/view_user/<?= $user->id ?>" class="student-name">
                  <?= esc($user->first_name . ' ' . $user->last_name) ?>
                </a>
              </td>
              <td><?= esc($user->email) ?></td>
              <td><?= esc($user->phone ?? '—') ?></td>
              <td>
                <span class="role-badge role-<?= esc($user->role) ?>">
                  <?= ucfirst(str_replace('_', ' ', $user->role)) ?>
                </span>
              </td>
              <td>
                <span class="status-badge status-<?= $user->is_active ? 'active' : 'inactive' ?>">
                  <?= $user->is_active ? 'Active' : 'Inactive' ?>
                </span>
              </td>
              <td class="actions">
                <a href="<?= ROOT ?>/admin/edit_user/<?= $user->id ?>"
                  class="btn-icon btn-view" title="Edit">✏️</a>

                <?php if ($user->is_active): ?>
                  <form method="POST" action="<?= ROOT ?>/admin/deactivate_user"
                    class="inline-form"
                    onsubmit="return confirm('Deactivate <?= esc($user->first_name) ?>?')">
                    <input type="hidden" name="user_id" value="<?= $user->id ?>">
                    <button type="submit" class="btn-icon btn-deactivate" title="Deactivate">🚫</button>
                  </form>
                <?php else: ?>
                  <form method="POST" action="<?= ROOT ?>/admin/activate_user"
                    class="inline-form"
                    onsubmit="return confirm('Activate <?= esc($user->first_name) ?>?')">
                    <input type="hidden" name="user_id" value="<?= $user->id ?>">
                    <button type="submit" class="btn-icon btn-log" title="Activate">✅</button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="<?= ROOT ?>/public/assets/js/users.js"></script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>