<?php
if (!isset($modalId) || !isset($modalTitle) || !isset($modalAction)) {
    return;
}
?>
<div id="<?= esc($modalId) ?>" class="modal-overlay">
  <div class="modal-dialog">
    <div class="modal-header">
      <h2><?= esc($modalTitle) ?></h2>
      <button type="button" class="modal-close" onclick="closeModal('<?= esc($modalId) ?>')">×</button>
    </div>
    <form method="POST" action="<?= esc($modalAction) ?>" class="modal-form">
      <div class="modal-body">
        <?= $modalContent ?? '' ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('<?= esc($modalId) ?>')">Cancel</button>
        <button type="submit" class="btn btn-primary"><?= esc($modalSubmitText ?? 'Submit') ?></button>
      </div>
    </form>
  </div>
</div>
