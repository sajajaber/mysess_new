<?php
class IepMilestone extends Model
{
  protected $table        = 'iep_milestones';
  protected $order_column = 'created_at';
  protected $order_type   = 'asc';
  public function getForGoal($goalId)
  {
    return $this->query(
      "SELECT id, goal_id, description, is_achieved, achieved_at, created_at
         FROM iep_milestones
        WHERE goal_id = :goal_id
        ORDER BY created_at ASC",
      ['goal_id' => $goalId]
    );
  }
  public function addMilestone($goalId, $description)
  {
    return $this->query(
      "INSERT INTO iep_milestones (goal_id, description, is_achieved)
       VALUES (:goal_id, :description, 0)",
      [
        'goal_id'     => $goalId,
        'description' => $description,
      ]
    );
  }
  public function setAchieved($id, $isAchieved)
  {
    if ($isAchieved) {
      return $this->query(
        "UPDATE iep_milestones
            SET is_achieved = 1, achieved_at = NOW()
          WHERE id = :id",
        ['id' => $id]
      );
    }

    return $this->query(
      "UPDATE iep_milestones
          SET is_achieved = 0
        WHERE id = :id",
      ['id' => $id]
    );
  }
}
