<?php

$role = $_SESSION['role'];

$sidebarItems = [
  'nurse' => [
    'dashboard' => '/nurse/dashboard',
    'students' => '/nurse/students',
    'health-records' => '/nurse/health-records',
    'medications' => '/nurse/all_medications',
    'messages' => '/messages'
  ],
  'admin' => [
    'dashboard' => '/admin/dashboard',
    'users' => '/admin/users',
    'students' => '/admin/students',
    'goal bank' => '/admin/goal-bank',
    'teacch tasks' => '/admin/teacch-tasks',
    'sessions' => '/admin/sessions',
    'attendance' => '/admin/attendance',
    'reports' => '/admin/reports',
    'student reports' => '/admin/student_reports',
    'messages' => '/messages'
  ],
  'parent' => [
    'dashboard' => '/parent/dashboard',
    'my children' => '/parent/children',
    'reports' => '/parent/reports',
    'messages' => '/messages'
  ],
  'teacher' => [
    'dashboard' => '/teacher/dashboard',
    'students' => '/teacher/students',
    'iep goals' => '/teacher/iep-goals',
    'teacch' => '/teacher/teacch',
    'homework' => '/teacher/homework',
    'sessions' => '/teacher/sessions',
    'progress reports' => '/teacher/progress-reports',
    'semester report' => '/teacher/semester-report',
    'profile' => '/teacher/profile',
    'messages' => '/messages'
  ],
  'therapist' => [
    'dashboard' => '/therapist/dashboard',
    'students' => '/therapist/students',
    'iep goals' => '/therapist/iep-goals',
    'teacch' => '/therapist/teacch',
    'sessions' => '/therapist/sessions',
    'semester report' => '/therapist/semester-report',
    'profile' => '/therapist/profile',
    'messages' => '/messages'
  ],
  'boarding_staff' => [
    'dashboard' => '/boarding/dashboard',
    'students' => '/boarding/students',
    'sleep' => '/boarding/sleep-logs',
    'nutrition' => '/boarding/nutrition-logs',
    'mood' => '/boarding/mood-logs',
    'activity' => '/boarding/activity-logs',
    'homework' => '/boarding/homework',
    'messages' => '/messages'
  ],
  'security_guard' => [
    'attendance' => '/security/dashboard',
    'check ins' => '/security/checkins',
    'messages' => '/messages'
  ]
];

// Get menu for current role (only if role exists in array)
$currentMenu = isset($sidebarItems[$role]) ? $sidebarItems[$role] : [];

// Get user info from session
$user_name = $_SESSION['user_name'];
$userRole = ucfirst($role ?? '');

// Look up this user's profile picture (or fall back to their initials)
$currentUser = (new User())->getById($_SESSION['user_id'] ?? 0);
$sidebarPhotoFile = $currentUser->photo_url ?? '';
$sidebarPhotoPath = $sidebarPhotoFile ? ROOT . '/public/assets/uploads/' . $sidebarPhotoFile : '';
$sidebarInitials  = $currentUser
  ? strtoupper(substr($currentUser->first_name, 0, 1) . substr($currentUser->last_name, 0, 1))
  : '';
?>

<aside class='sidebar'>
  <div class='sidebar-logo'>
    MySESS
  </div>

  <hr>

  <div id="navigation">NAVIGATION</div>

  <div class='sidebar-menu'>
    <?php if (!empty($currentMenu)): ?>
      <?php foreach ($currentMenu as $label => $url): ?>
        <a href="<?= ROOT . $url ?>">
          <?= ucfirst($label) ?>
        </a>
      <?php endforeach; ?>
    <?php else: ?>
      <!-- No menu items for this role -->
    <?php endif; ?>
  </div>

  <hr>

  <div class='sidebar-bottom'>
    <div class='user-info'>
      <div class='sidebar-avatar'>
        <?php if ($sidebarPhotoPath): ?>
          <img src="<?= $sidebarPhotoPath ?>" alt="Profile picture" class="sidebar-avatar-img">
        <?php else: ?>
          <div class="sidebar-avatar-initials"><?= htmlspecialchars($sidebarInitials) ?></div>
        <?php endif; ?>
      </div>
      <div class='name'>
        <?= htmlspecialchars($user_name ?: '') ?>
      </div>
      <div class='role'>
        <?= htmlspecialchars($userRole) ?>
      </div>
    </div>
    <a href="<?= ROOT ?>/auth/logout" onclick="return confirm('Are you sure you want to logout?');" style="all:unset;">
      <button class="logout-btn">Logout</button>
    </a>
  </div>
</aside>

<style>
  /* Profile picture in the sidebar, centered above the name and role */
  .sidebar-avatar {
    display: flex;
    justify-content: center;
    margin-bottom: 8px;
  }

  /* Center the name and role under the picture */
  .sidebar-bottom .user-info {
    text-align: center;
  }

  .sidebar-avatar-img {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid #e2e8f0;
  }

  .sidebar-avatar-initials {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #4f46e5;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 600;
  }

  .logout-btn {
    all: unset;
    border: none;
    display: block;
    text-align: left;
    color: #eb004e;
    margin-top: 10px;
    padding: 8px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    transition: background 0.2s;
  }

  .logout-btn:hover {
    background: rgba(184, 184, 184, 0.3);
    color: #eb004e;
  }
</style>