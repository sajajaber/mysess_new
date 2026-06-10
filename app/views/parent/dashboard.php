<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Parent Dashboard';
$pageHeading = 'Welcome';
$activePage  = 'dashboard';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

$children = $children ?? [];
?>


<div class="card">
  <div class="card-header"><h2>My Children</h2></div>
  <div class="card-body">

    <?php if (empty($children)): ?>
      <p class="muted">No children are linked to your account yet. Please contact the school admin.</p>
    <?php else: ?>

      <p class="muted text-sm" style="margin-bottom:14px;">
        Click a child to see all their school activity.
      </p>

      <div style="display:flex; gap:14px; flex-wrap:wrap;">
        <?php foreach ($children as $c):
          $photo    = $c->photo_url ?? '';
          $photoUrl = $photo ? ROOT . '/public/assets/uploads/' . $photo : '';
          $initials = strtoupper(substr($c->first_name, 0, 1) . substr($c->last_name, 0, 1));
        ?>
          <a href="<?= ROOT ?>/parent/child/<?= (int)$c->id ?>"
             style="text-decoration:none; color:inherit; flex:1; min-width:240px; max-width:320px;">

            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px;
                        padding:16px; display:flex; gap:14px; align-items:center;
                        transition:transform 0.15s, box-shadow 0.15s;"
                 onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 14px rgba(15,23,42,0.08)';"
                 onmouseout="this.style.transform=''; this.style.boxShadow='';">

              <?php if ($photoUrl): ?>
                <img src="<?= $photoUrl ?>" alt=""
                     style="width:64px; height:64px; border-radius:50%; object-fit:cover; border:1px solid #e2e8f0;">
              <?php else: ?>
                <div style="width:64px; height:64px; border-radius:50%;
                            background:var(--module-main, #4f46e5); color:#fff;
                            display:flex; align-items:center; justify-content:center;
                            font-size:20px; font-weight:700;"><?= $initials ?></div>
              <?php endif; ?>

              <div>
                <div style="font-weight:600; font-size:16px;">
                  <?= esc($c->first_name . ' ' . $c->last_name) ?>
                </div>
                <div class="muted text-sm" style="margin-top:3px;">
                  DOB <?= date('d M Y', strtotime($c->date_of_birth)) ?>
                  <?php if ($c->diagnosis): ?>
                    <br><?= esc($c->diagnosis) ?>
                  <?php endif; ?>
                </div>
              </div>

            </div>
          </a>
        <?php endforeach; ?>
      </div>

    <?php endif; ?>

  </div>
</div>


<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
