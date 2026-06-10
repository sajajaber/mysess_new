<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$reportData    = $reportData;
$student       = $reportData['student'];
$semesterLabel = $semesterLabel ?? '';
$preparedBy    = $preparedBy    ?? '';
$boardingStats = $boardingStats ?? null;

$pageTitle   = 'Student Report';
$pageHeading = 'Student Report';
$activePage  = 'semester report';

$topbarActions = '
  <a href="' . ROOT . '/teacher/semester-report"><button class="btn">Build Another</button></a>
  <button class="btn btn-primary" onclick="window.print()">Print / Save PDF</button>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

require __DIR__ . '/../components/student_report_body.php';

require_once __DIR__ . '/../layouts/footer.php';
