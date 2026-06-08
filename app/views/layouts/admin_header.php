<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'MySESS - Nurse Dashboard' ?></title>

  <link rel="stylesheet" href="/../mysess_new/public/assets/css/main.css">
  <link rel="stylesheet" href="/../mysess_new/public/assets/css/sidebar.css">
  <link rel="stylesheet" href="/../mysess_new/public/assets/css/topbar.css">
  <link rel="stylesheet" href="/../mysess_new/public/assets/css/display-student.css">
  <link rel="stylesheet" href="/../mysess_new/public/assets/css/admin.css">
</head>

<body>

  <!-- Sidebar Component -->
  <?php require_once __DIR__ . '/../components/sidebar.php'; ?>

  <main class="main">
    <div class="topbar">
      <div class="topbar-left">
        <h1><?= $pageHeading ?? 'Dashboard' ?></h1>
        <p id="current-date">
          <!-- Reference: https://www.w3schools.com/Js/js_dates.asp -->
          <script>
            const date = new Date();
            const options = {
              weekday: 'long',
              year: 'numeric',
              month: 'long',
              day: 'numeric'
            };
            document.write(date.toLocaleDateString("en-GB", options));
          </script>
        </p>
      </div>
      <?php if (isset($topbarActions)): ?>
        <div class="topbar-right">
          <?= $topbarActions ?>
        </div>
      <?php endif; ?>
    </div>