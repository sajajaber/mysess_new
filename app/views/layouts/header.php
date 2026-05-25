<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($pageTitle ?? APP_NAME) ?></title>
  <link rel="stylesheet" href="<?= ROOT ?>/assets/css/main.css">
  <link rel="stylesheet" href="<?= ROOT ?>/assets/css/topbar.css">
  <link rel="stylesheet" href="<?= ROOT ?>/assets/css/sidebar.css">
</head>
<body>
  <div class="app-shell">
    <?php require __DIR__ . '/../components/sidebar.php'; ?>
    <header class="app-header">
      <div class="brand">
        <a href="<?= ROOT ?>"><?= esc(APP_NAME) ?></a>
      </div>
      <div class="header-center">
        <h1><?= esc($pageHeading ?? $pageTitle ?? APP_NAME) ?></h1>
      </div>
      <div class="header-actions">
        <?= $topbarActions ?? '' ?>
      </div>
    </header>

    <main class="app-content">
