<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle    = 'TEACCH Tasks';
$pageHeading  = 'TEACCH Task Bank';
$activePage   = 'teacch tasks';
$topbarActions = '
    <a href="' . ROOT . '/admin/add-teacch-task"><button class="btn btn-primary">Add TEACCH Task</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

?>

<?php
$data = $entries ?? [];

$headers = ['Category', 'Title', 'Status', 'Actions'];

$renderRow = function ($entry) {
    ob_start();
?>
    <tr>
        <td><?= esc($entry->category) ?></td>
        <td><?= esc($entry->title) ?></td>
        <td>
            <span class="status-badge status-<?= $entry->is_active ? 'active' : 'inactive' ?>">
                <?= $entry->is_active ? 'Active' : 'Inactive' ?>
            </span>
        </td>
        <td class="actions">
            <a href="<?= ROOT ?>/admin/edit-teacch-task/<?= $entry->id ?>" class="btn btn-sm">Edit</a>
            <form method="POST" action="<?= ROOT ?>/admin/toggle-teacch-task" class="inline-form">
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

$emptyMessage = 'No task-bank entries yet.';

require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
