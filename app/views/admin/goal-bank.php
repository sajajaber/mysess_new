<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle    = 'Goal Bank';
$pageHeading  = 'IEP Goal Bank';
$activePage   = 'goal bank';
$topbarActions = '
    <a href="' . ROOT . '/admin/add-goal-bank"><button class="btn btn-primary">Add Goal Bank Entry</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

?>

<?php
$data = $entries ?? [];

$headers = ['Category', 'Goal Text', 'Status', 'Actions'];

$renderRow = function ($entry) {
    ob_start();
?>
    <tr>
        <td><?= esc($entry->category) ?></td>
        <td><?= esc($entry->goal_text) ?></td>
        <td>
            <span class="status-badge status-<?= $entry->is_active ? 'active' : 'inactive' ?>">
                <?= $entry->is_active ? 'Active' : 'Inactive' ?>
            </span>
        </td>
        <td class="actions">
            <a href="<?= ROOT ?>/admin/edit-goal-bank/<?= $entry->id ?>" class="btn btn-sm">Edit</a>
            <form method="POST" action="<?= ROOT ?>/admin/toggle-goal-bank" class="inline-form">
                <input type="hidden" name="id" value="<?= (int)$entry->id ?>">
                <input type="hidden" name="is_active" value="<?= $entry->is_active ? 0 : 1 ?>">
                <button type="submit" class="btn btn-sm">
                    <?= $entry->is_active ? 'Deactivate' : 'Activate' ?>
                </button>
            </form>
        </td>
    </tr>
<?php
    return ob_get_clean();
};

$emptyMessage = 'No goal-bank entries yet.';

require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
