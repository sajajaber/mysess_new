<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/functions.php';

$pageTitle    = 'Students';
$pageHeading  = 'All Students';
$activePage   = 'students';
$topbarActions = '
    <a href="' . ROOT . '/admin/add_student"><button class="btn btn-primary">Add Student</button></a>
';

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../components/alert.php';

$students     = $students ?? [];
$showArchived = isset($_GET['archived']);

require 'C:/xampp1/htdocs/mysess_new/public/assets/css/admin.php';
?>

<!-- Toggle -->
<div class='table-filters'>
    <?php if ($showArchived): ?>
        <a href='<?= ROOT ?>/admin/students''><span class='show-archived'>Show Active Only</span></a>
        <span class='filter-badge'>showing archived students</span>
    <?php else: ?>
        <a href='<?= ROOT ?>/admin/students?archived=1'><span class='show-archived'>Show Archived</span></a>
    <?php endif;
    ?>
</div>

<?php
$data = $students;
$headers = ['Name', 'Date of Birth', 'Diagnosis', 'Enrolled', 'Status', 'Actions'];
$renderRow = function ($student) {
    ob_start();
?>
    <tr>
        <td>
            <div class='student-info'>
                <a href="<?= ROOT ?>/admin/view_student/<?= $student->id ?>" class='student-name'>
                    <?= esc( $student->first_name . ' ' . $student->last_name ) ?>
                </a>
            </div>
        </td>
        <td>
            <?= date( 'd-m-Y' , strtotime( $student->date_of_birth ) ) ?>
        </td>
        <td>
            <?= esc( $student->diagnosis ?? '—' ) ?>
        </td>
        <td>
            <?= date( 'd-m-Y' , strtotime( $student->enrollment_date ) ) ?>
        </td>
        <td>
            <span class="status-badge status-<?= $student->is_active ? 'active' : 'inactive' ?>">
                <?= $student->is_active ? 'Active' : 'Archived' ?>
            </span>
        </td>
        <td class='actions'>
            <a href="<?= ROOT ?>/admin/edit_student/<?= $student->id ?>" class='btn-icon btn-view' title='Edit'>✏️</a>
            <?php if ($student->is_active): ?>
                <form method='POST' action='<?= ROOT ?>/admin/archive_student' class='inline-form'
                    onsubmit="return confirm('Archive this student?')">
                    <input type='hidden' name='student_id' value="<?= $student->id ?>">
                    <button type='submit' class='btn-icon btn-deactivate' title='Archive'>📦</button>
                </form>
            <?php else: ?>
                <form method='POST' action='<?= ROOT ?>/admin/restore_student' class='inline-form'
                    onsubmit="return confirm('Restore this student?')">
                    <input type='hidden' name='student_id' value="<?= $student->id ?>">
                    <button type='submit' class='btn-icon btn-log' title='Restore'>♻️</button>
                </form>
            <?php endif;
            ?>
        </td>
    </tr>
<?php
    return ob_get_clean();
};

$emptyMessage = $showArchived ? 'No archived students.' : 'No active students found.';

require __DIR__ . '/../components/data_table.php';
?>

<?php require_once __DIR__ . '/../layouts/footer.php';
?>