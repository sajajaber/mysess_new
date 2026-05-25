<?php
if (!isset($stat) || !is_array($stat)) {
    return;
}
?>
<div class="stat-card <?= esc($stat['class'] ?? '') ?>">
  <div class="stat-card-icon"><?= $stat['icon'] ?? '' ?></div>
  <div class="stat-card-body">
    <div class="stat-card-value"><?= esc($stat['value'] ?? '0') ?></div>
    <div class="stat-card-label"><?= esc($stat['label'] ?? '') ?></div>
  </div>
</div>
