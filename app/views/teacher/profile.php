<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'My Profile';
$pageHeading = 'My Profile';
$activePage  = 'profile';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';
$photoFile = $user->photo_url ?? '';
$photoPath = $photoFile ? ROOT . '/public/assets/uploads/' . $photoFile : '';
?>

<div class="card">
  <div class="card-header">
    <h2>My Profile</h2>
  </div>

  <div class="card-body">

    <!-- Current picture (or initials when there is none) -->
    <div style="margin-bottom:20px;">
      <?php if ($photoPath): ?>
        <img src="<?= $photoPath ?>" alt="Profile picture"
             style="width:120px;height:120px;border-radius:50%;object-fit:cover;border:1px solid #e2e8f0;">
      <?php else: ?>
        <div style="width:120px;height:120px;border-radius:50%;background:#4f46e5;color:#fff;display:flex;align-items:center;justify-content:center;font-size:40px;">
          <?= strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- enctype is required so the file actually uploads -->
    <form method="POST" action="<?= ROOT ?>/teacher/profile" enctype="multipart/form-data">

      <div class="form-group">
        <label for="first_name">First Name <span style="color:#eb004e">*</span></label>
        <input type="text" id="first_name" name="first_name"
               value="<?= esc($user->first_name) ?>" required>
      </div>

      <div class="form-group">
        <label for="last_name">Last Name <span style="color:#eb004e">*</span></label>
        <input type="text" id="last_name" name="last_name"
               value="<?= esc($user->last_name) ?>" required>
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email"
               value="<?= esc($user->email) ?>" readonly
               style="background:#f1f5f9; cursor:not-allowed;">
        <small style="color:#666;display:block;margin-top:4px;">Email is managed by the admin and cannot be changed here.</small>
      </div>

      <div class="form-group">
        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone"
               value="<?= esc($user->phone ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Role</label>
        <!-- Role is shown for info only and cannot be changed here -->
        <input type="text" value="<?= ucfirst(esc($user->role)) ?>" disabled>
      </div>

      <div class="form-group">
        <label for="photo">Profile Picture</label>
        <input type="file" id="photo" name="photo" accept="image/*">
        <small style="color:#666;display:block;margin-top:4px;">JPG, PNG, or GIF. Leave empty to keep your current picture.</small>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Save Profile</button>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
