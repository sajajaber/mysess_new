<?php

require_once __DIR__ . '/../../core/config.php';

$pageTitle   = 'Reports';
$pageHeading = 'Reports';
$activePage  = 'reports';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

$shared = $shared ?? [];

$data    = $shared;
$headers = ['Child', 'Shared On', ''];

$renderRow = function ($r) { ob_start(); ?>
  <tr>
    <td><?= esc($r->first_name . ' ' . $r->last_name) ?></td>
    <td><?= date('d M Y', strtotime($r->created_at)) ?></td>
    <td><a class="btn btn-sm btn-primary" href="<?= ROOT ?>/parent/report/<?= (int)$r->student_id ?>">Open</a></td>
  </tr>
<?php return ob_get_clean(); };

$emptyMessage = 'No reports have been shared with you yet.';
require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
