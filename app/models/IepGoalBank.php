<?php
class IepGoalBank extends Model
{
  protected $table        = 'iep_goal_bank';
  protected $order_column = 'created_at';
  protected $order_type   = 'desc';

  public function getAll()
  {
    return $this->query(
      "SELECT id, category, goal_text, is_active, created_by, created_at
         FROM iep_goal_bank
        ORDER BY created_at DESC"
    );
  }
  public function getActive()
  {
    return $this->query(
      "SELECT id, category, goal_text
         FROM iep_goal_bank
        WHERE is_active = 1
        ORDER BY category ASC, created_at DESC"
    );
  }
  public function getActiveByCategory($category)
  {
    return $this->query(
      "SELECT id, category, goal_text
         FROM iep_goal_bank
        WHERE is_active = 1 AND category = :category
        ORDER BY created_at DESC",
      ['category' => $category]
    );
  }
  public function addEntry($category, $goalText, $createdBy)
  {
    return $this->query(
      "INSERT INTO iep_goal_bank (category, goal_text, is_active, created_by)
       VALUES (:category, :goal_text, 1, :created_by)",
      [
        'category'   => $category,
        'goal_text'  => $goalText,
        'created_by' => $createdBy,
      ]
    );
  }
  public function updateEntry($id, $category, $goalText)
  {
    return $this->query(
      "UPDATE iep_goal_bank
          SET category = :category, goal_text = :goal_text
        WHERE id = :id",
      [
        'id'        => $id,
        'category'  => $category,
        'goal_text' => $goalText,
      ]
    );
  }
  public function setActive($id, $isActive)
  {
    return $this->query(
      "UPDATE iep_goal_bank
          SET is_active = :is_active
        WHERE id = :id",
      [
        'id'        => $id,
        'is_active' => $isActive,
      ]
    );
  }
}
