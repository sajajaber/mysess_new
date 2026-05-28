<?php
function esc($str)
{
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function statCard($number, $title, $color)
{
?>
    <style>
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: #e2e8f0 0px 4px 6px;
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-value {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 10px;
            color: <?php $color ?>;
        }

        .stat-sub {
            font-size: 14px;
            color: #999;
        }
    </style>
    <div class="stat-card">
        <div class="stat-value"> <?= esc($number); ?> </div>
        <div class="stat-sub"> <?= esc($title); ?></div>
    </div>
<?php
}

function renderTable($title, $columns, $data, $limit = null)
{
    if ($limit) {
        $data = array_slice($data, 0, $limit);
    }
?>
    <div class="card">
        <div class="card-header">
            <h2><?= htmlspecialchars($title) ?></h2>
        </div>
        <div class="card-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <?php foreach ($columns as $col): ?>
                            <th><?= htmlspecialchars($col) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="<?= count($columns) ?>">No records found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <?php foreach ($row as $value): ?>
                                    <td><?= htmlspecialchars($value) ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php }

function renderSelect($name, $options, $selected = null, $attributes = [])
{
    $attrs = '';
    foreach ($attributes as $key => $value) {
        $attrs .= " $key=\"$value\"";
    }
?>
    <select name="<?= htmlspecialchars($name) ?>" <?= $attrs ?>>
        <?php foreach ($options as $value => $label): ?>
            <option value="<?= htmlspecialchars($value) ?>" <?= ($selected == $value) ? 'selected' : '' ?>>
                <?= htmlspecialchars($label) ?>
            </option>
        <?php endforeach; ?>
    </select>
<?php
}
?>