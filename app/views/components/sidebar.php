<?php if (!empty($activePage)): ?>
<nav class="sidebar-nav">
  <ul>
    <li class="<?= ($activePage === 'dashboard') ? 'active' : '' ?>"><a href="<?= ROOT ?>/nurse/dashboard">Dashboard</a></li>
    <li class="<?= ($activePage === 'students') ? 'active' : '' ?>"><a href="<?= ROOT ?>/nurse">Students</a></li>
    <li class="<?= ($activePage === 'medications') ? 'active' : '' ?>"><a href="<?= ROOT ?>/nurse/all_medications">Medications</a></li>
    <li><a href="<?= ROOT ?>/auth/logout">Logout</a></li>
  </ul>
</nav>
<?php endif; ?>
