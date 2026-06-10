<?php

require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$reportData    = $reportData;
$student       = $reportData['student'];
$boardingStats = $boardingStats ?? null;
$semesterLabel = $semesterLabel ?? '';
$preparedBy    = $preparedBy    ?? '';

$studentName = trim($student->first_name . ' ' . $student->last_name) ?: 'Student';
$pageTitle   = $studentName . ' — Report';
$pageHeading = 'Student Report';
$activePage  = 'children';

$topbarActions = '
  <a href="' . ROOT . '/parent/child/' . (int)$student->id . '"><button class="btn btn-primary">Back to Profile</button></a>
  <button class="btn" onclick="window.print()">Print / Save PDF</button>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

require __DIR__ . '/../components/student_report_body.php';

require_once __DIR__ . '/../layouts/footer.php';
