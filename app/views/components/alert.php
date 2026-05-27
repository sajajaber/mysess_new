<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible">
        <?= $_SESSION['success']; ?>
        <button class="close-btn" onclick="this.parentElement.remove()">&times;</button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-error alert-dismissible">
        <?= $_SESSION['error']; ?>
        <button class="close-btn" onclick="this.parentElement.remove()">&times;</button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['medication_success'])): ?>
    <div class="alert alert-success alert-dismissible">
        <?= $_SESSION['medication_success']; ?>
        <button class="close-btn" onclick="this.parentElement.remove()">&times;</button>
    </div>
    <?php unset($_SESSION['medication_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['medication_error'])): ?>
    <div class="alert alert-error alert-dismissible">
        <?= $_SESSION['medication_error']; ?>
        <button class="close-btn" onclick="this.parentElement.remove()">&times;</button>
    </div>
    <?php unset($_SESSION['medication_error']); ?>
<?php endif; ?>