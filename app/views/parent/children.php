<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'My Children';
$pageHeading = 'My Children';
$activePage  = 'children';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

$children = $children ?? [];

$data    = $children;
$headers = ['Name', 'Date of Birth', 'Diagnosis', ''];

$renderRow = function ($c) { ob_start(); ?>
  <tr>
    <td><?= esc($c->first_name . ' ' . $c->last_name) ?></td>
    <td><?= date('d M Y', strtotime($c->date_of_birth)) ?></td>
    <td><?= esc($c->diagnosis ?: '—') ?></td>
    <td><a class="btn btn-sm btn-primary" href="<?= ROOT ?>/parent/child/<?= (int)$c->id ?>">Open</a></td>
  </tr>
<?php return ob_get_clean(); };

$emptyMessage = 'No children are linked to your account yet.';
require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
