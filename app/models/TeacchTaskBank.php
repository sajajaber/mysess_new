<?php
class TeacchTaskBank extends Model
{
  protected $table        = 'teacch_task_bank';
  protected $order_column = 'created_at';
  protected $order_type   = 'desc';
  public function getAll()
  {
    return $this->query(
      "SELECT id, category, title, is_active, created_by, created_at
         FROM teacch_task_bank
        ORDER BY created_at DESC"
    );
  }
  public function getActive()
  {
    return $this->query(
      "SELECT id, category, title
         FROM teacch_task_bank
        WHERE is_active = 1
        ORDER BY category ASC, created_at DESC"
    );
  }
  public function addEntry($category, $title, $createdBy)
  {
    return $this->query(
      "INSERT INTO teacch_task_bank (category, title, is_active, created_by)
       VALUES (:category, :title, 1, :created_by)",
      [
        'category'   => $category,
        'title'      => $title,
        'created_by' => $createdBy,
      ]
    );
  }
  public function updateEntry($id, $category, $title)
  {
    return $this->query(
      "UPDATE teacch_task_bank
          SET category = :category, title = :title
        WHERE id = :id",
      [
        'id'       => $id,
        'category' => $category,
        'title'    => $title,
      ]
    );
  }
  public function setActive($id, $isActive)
  {
    return $this->query(
      "UPDATE teacch_task_bank
          SET is_active = :is_active
        WHERE id = :id",
      [
        'id'        => $id,
        'is_active' => $isActive,
      ]
    );
  }
}
