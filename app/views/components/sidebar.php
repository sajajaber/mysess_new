<?php

$role = $_SESSION['role'];

$sidebarItems = [
  'nurse' => [
    'dashboard' => '/nurse/dashboard',
    'students' => '/nurse/students',
    'health-records' => '/nurse/health-records',
    'medications' => '/nurse/all_medications',
    'messages' => '/nurse/messages'
  ],
  'admin' => [
    'dashboard' => '/admin/dashboard',
    'users' => '/admin/users',
    'students' => '/admin/students',
    'sessions' => '/admin/sessions',
    'reports' => '/admin/reports',
    'messages' => '/admin/messages'
  ],
  'parent' => [
    'dashboard' => '/parent/dashboard',
    'academic' => '/parent/academic-records',
    'health' => '/parent/health-records',
    'messages' => '/parent/messages'
  ],
  'teacher' => [
    'dashboard' => '/teacher/dashboard',
    'students' => '/teacher/students',
    'sessions' => '/teacher/sessions',
    'progress reports' => '/teacher/progress-reports',
    'messages' => '/teacher/messages'
  ],
  'therapist' => [
    'dashboard' => '/therapist/dashboard',
    'students' => '/therapist/students',
    'iep goals' => '/therapist/iep-goals',
    'sessions' => '/therapist/sessions',
    'messages' => '/therapist/messages'
  ],
  'boarding-staff' => [
    'dashboard' => '/boarding-staff/dashboard',
    'students' => '/boarding-staff/students',
    'daily logs' => '/boarding-staff/daily-logs',
    'activities' => '/boarding-staff/activities'
  ]
];

// Get menu for current role (only if role exists in array)
$currentMenu = isset($sidebarItems[$role]) ? $sidebarItems[$role] : [];

// Get user info from session
$user_name = $_SESSION['user_name'];
$userRole = ucfirst($role ?? '');
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