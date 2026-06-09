<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle   = 'Parent Academic Records';
$pageHeading = 'Academic Records';
$activePage  = 'academic';

$topbarActions = '
<a href="' . ROOT . '/parent/health-records">
  <button class="btn btn-primary">View Health Records</button>
</a>';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';


?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>