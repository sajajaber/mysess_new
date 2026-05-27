<?php
class TableCard
{
  private $title;
  private $headers = [];
  private $rows = [];
  private $fields = [];
  private $limit = null;
  private $emptyMessage = "No records found";
  private $headerAction = null;
  private $rowActions = [];

  public function __construct($title)
  {
    $this->title = $title;
    return $this;
  }

  public function setHeaders($headers)
  {
    $this->headers = $headers;
    return $this;
  }

  public function setData($rows, $fields)
  {
    $this->rows = $rows;
    $this->fields = $fields;
    return $this;
  }

  public function setLimit($limit)
  {
    $this->limit = $limit;
    return $this;
  }

  public function setEmptyMessage($message)
  {
    $this->emptyMessage = $message;
    return $this;
  }

  public function addHeaderAction($text, $url)
  {
    $this->headerAction = ['text' => $text, 'url' => $url];
    return $this;
  }

  public function addRowAction($text, $url, $type = 'secondary')
  {
    $this->rowActions[] = ['text' => $text, 'url' => $url, 'type' => $type];
    return $this;
  }

  public function render()
  {
    // Apply limit
    $displayRows = $this->rows;
    if ($this->limit !== null && $this->limit > 0) {
      $displayRows = array_slice($displayRows, 0, $this->limit);
    }
?>
    <div class="card">
      <div class="card-header">
        <h2><?= htmlspecialchars($this->title) ?></h2>
        <?php if ($this->headerAction): ?>
          <a href="<?= $this->headerAction['url'] ?>" class="btn btn-sm">
            <?= htmlspecialchars($this->headerAction['text']) ?>
          </a>
        <?php endif; ?>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <?php foreach ($this->headers as $header): ?>
                  <th><?= htmlspecialchars($header) ?></th>
                <?php endforeach; ?>
                <?php if (!empty($this->rowActions)): ?>
                  <th>Actions</th>
                <?php endif; ?>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($displayRows)): ?>
                <tr>
                  <td colspan="<?= count($this->headers) + (!empty($this->rowActions) ? 1 : 0) ?>" class="text-center">
                    <?= htmlspecialchars($this->emptyMessage) ?>
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($displayRows as $row): ?>
                  <tr>
                    <?php foreach ($this->fields as $field): ?>
                      <td>
                        <?php
                        $value = $this->getNestedValue($row, $field);
                        echo htmlspecialchars($value);
                        ?>
                      </td>
                    <?php endforeach; ?>

                    <?php if (!empty($this->rowActions)): ?>
                      <td class="action-buttons">
                        <?php foreach ($this->rowActions as $action): ?>
                          <a href="<?= str_replace('{id}', $row['id'] ?? '', $action['url']) ?>"
                            class="btn-<?= $action['type'] ?> btn-sm">
                            <?= htmlspecialchars($action['text']) ?>
                          </a>
                        <?php endforeach; ?>
                      </td>
                    <?php endif; ?>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
<?php
  }

  private function getNestedValue($array, $path)
  {
    $parts = explode('.', $path);
    $value = $array;
    foreach ($parts as $part) {
      if (!is_array($value) || !isset($value[$part])) {
        return '';
      }
      $value = $value[$part];
    }
    return $value;
  }
}
?>