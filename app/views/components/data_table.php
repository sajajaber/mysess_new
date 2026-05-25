<?php
$rows = $data ?? ($recordsData ?? ($eventsData ?? ($medData ?? ($doseData ?? ($eventData ?? ($recordData ?? ($students ?? [])))))));
$headers = $headers ?? ($recordsHeaders ?? ($eventsHeaders ?? ($medHeaders ?? ($doseHeaders ?? ($eventHeaders ?? ($recordHeaders ?? []))))));
$renderRow = $renderRow ?? ($recordsRenderRow ?? ($eventsRenderRow ?? ($medRenderRow ?? ($doseRenderRow ?? ($eventRenderRow ?? ($recordRenderRow ?? null))))));
$action = $action ?? ($recordsAction ?? ($eventsAction ?? ($medAction ?? ($doseAction ?? ($eventAction ?? ($recordAction ?? ''))))));
$emptyMessage = $emptyMessage ?? ($recordsEmptyMessage ?? ($eventsEmptyMessage ?? ($medEmptyMessage ?? ($doseEmptyMessage ?? ($eventEmptyMessage ?? ($recordEmptyMessage ?? 'No records found yet.'))))));

$rows = is_array($rows) ? $rows : [];
?>
<div class="data-table-wrapper">
  <?php if (empty($rows)): ?>
    <div class="empty-state"><?= esc($emptyMessage) ?></div>
  <?php else: ?>
    <table class="data-table">
      <thead>
        <tr>
          <?php foreach ($headers as $header): ?>
            <th><?= esc($header) ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
          <?= $renderRow ? $renderRow($row) : '' ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <?php if (!empty($action)): ?>
    <div class="table-actions"><?= $action ?></div>
  <?php endif; ?>
</div>
